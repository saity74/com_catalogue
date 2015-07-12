<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('CatalogueHelper', JPATH_ADMINISTRATOR . '/components/com_catalogue/helpers/catalogue.php');

/**
 * Item Model for an Item.
 *
 * @since  12.2
 */
class CatalogueModelItem extends JModelAdmin
{
	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_CATALOGUE';

	/**
	 * The type alias for this content type (for example, 'com_catalogue.item').
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_catalogue.item';

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$newIds = array();

		if (!parent::checkCategoryId($categoryId))
		{
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);

					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $this->table->alias, $this->table->title);
			$this->table->title = $data['0'];
			$this->table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// Reset hits because we are making a copy
			$this->table->hits = 0;

			// Unpublish because we are making a copy
			$this->table->state = 0;

			// New category ID
			$this->table->catid = $categoryId;

			// TODO: Deal with ordering?
			// $table->ordering	= 1;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());
				return false;
			}

			parent::createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return false;
			}
			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_catalogue.item.' . (int) $record->id);
		}

		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing item.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_catalogue.item.' . (int) $record->id);
		}
		// New item, so check against the category.
		elseif (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_catalogue.category.' . (int) $record->catid);
		}
		// Default to component settings if neither item nor category known.
		else
		{
			return parent::canEditState('com_catalogue');
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		// Set the publish date to now
		$db = $this->getDbo();

		if ($table->state == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}

		if ($table->state == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $db->getNullDate();
		}

		// Reorder the items within the category so the new item is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($type = 'Catalogue', $prefix = 'CatalogueTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItem($pk = null)
	{

		if ($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new Registry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new Registry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			$techs = new Registry;
			$techs->loadString($item->techs);
			$item->techs = $techs->toArray();

			// Convert the images field to an array.
			if (isset($item->images) && !empty($item->images))
			{
				$registry = new Registry;
				$registry->loadString($item->images);
				$item->images = $registry->toArray();
				foreach ($item->images as &$image)
				{
					$image['url'] = $image['name'];
					$image['dir'] = dirname($image['name']);
					$image['name'] = basename($image['name']);
				}
			}

			$item->itemtext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
			if(isset($item->similar_items)) {
				$similar_items = new Registry;
				$similar_items->loadString($item->similar_items);
				$item->similar_items = $similar_items->toArray();
			}

			// Load reviews..
			$query = $this->_db->getQuery(true);
			$query->select('rv.*')
				->from('#__catalogue_item_review as rv')
				->where('rv.item_id = ' . (int) $item->id)
				->order('rv.ordering ASC');
			$this->_db->setQuery($query);

			$item->reviews = $this->_db->loadObjectList();

			// ..load reviews

			$query = $this->_db->getQuery(true);

			$query->select('a.id as attr_id, a.attr_name')
				->from('#__catalogue_attr as a')
				->where('a.published = 1');

			$this->_db->setQuery($query);

			$item->attrs = $this->_db->loadAssocList('attr_id');

			$query = $this->_db->getQuery(true);

			$query->select('a.id as attr_id,
				a.attrdir_id,
				d.dir_name,
				a.attr_name,
				a.attr_type,
				a.attr_default,
				0 as attr_value,
				0 as attr_price,
				\'\' as attr_image'
			)
				->from('#__catalogue_attr as a')
				->join('LEFT', '#__catalogue_attrdir as d ON d.id = a.attrdir_id')
				->where('a.published = 1 AND d.published = 1')
				// ->order('a.attrdir_id ASC')
				->order('a.attrdir_id ASC, a.attr_name ASC')
				->group('a.id');

			$this->_db->setQuery($query);

			$item->attrdirs = $this->_db->loadObjectList();

			$query = $this->_db->getQuery(true);

			$query->select('p.attr_id, p.attr_price')
				->from('#__catalogue_attr_price as p')
				->where('p.item_id = ' . (int) $item->id);

			$this->_db->setQuery($query);

			$attr_prices = $this->_db->loadAssocList('attr_id');

			$query = $this->_db->getQuery(true);

			$query->select('i.attr_id, i.attr_image')
				->from('#__catalogue_attr_image as i')
				->where('i.item_id = ' . (int) $item->id);

			$this->_db->setQuery($query);

			$attr_images = $this->_db->loadAssocList('attr_id');

			foreach ($item->attrdirs as $attr_dir)
			{

				if (isset($item->params['attr_' . $attr_dir->attr_id]))
				{
					$attr_dir->attr_value = $item->params['attr_' . $attr_dir->attr_id];
				}

				if (isset($attr_prices[$attr_dir->attr_id]))
				{
					$attr_dir->attr_price = $attr_prices[$attr_dir->attr_id]['attr_price'];
				}

				if (isset($attr_images[$attr_dir->attr_id]))
				{
					$attr_dir->attr_image = $attr_images[$attr_dir->attr_id]['attr_image'];
				}

			}
			$item->attrs = JArrayHelper::toObject($item->attrs);
		}

		// Load associated catalogue items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_catalogue', '#__catalogue_item', 'com_catalogue.item', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	/**
	 * Method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   12.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_catalogue.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('item.id'))
		{
			$id = $this->getState('item.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own items in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_catalogue.item.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_catalogue')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an item you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Prevent messing with item language and category when editing existing item with associations
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		// Check if item is associated
		if ($this->getState('item.id') && $app->isSite() && $assoc)
		{
			$associations = JLanguageAssociations::getAssociations('com_catalogue', '#__catalogue_item', 'com_catalogue.item', $id);

			// Make fields read only
			if ($associations)
			{
				$form->setFieldAttribute('language', 'readonly', 'true');
				$form->setFieldAttribute('catid', 'readonly', 'true');
				$form->setFieldAttribute('language', 'filter', 'unset');
				$form->setFieldAttribute('catid', 'filter', 'unset');
			}
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array    The default data is an empty array.
	 *
	 * @since   12.2
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_catalogue.edit.item.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('item.id') == 0)
			{
				$filters = (array) $app->getUserState('com_catalogue.items.filter');
				$filterCatId = isset($filters['category_id']) ? $filters['category_id'] : null;

				$data->set('catid', $app->input->getInt('catid', $filterCatId));
			}
		}

		$this->preprocessData('com_catalogue.item', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   12.2
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$filter  = JFilterInput::getInstance();

		$techs = array_map(array($this, '_restructTechData'), $data['techs']['name'], $data['techs']['value'], $data['techs']['show_short']);

		$registry = new JRegistry($techs);
		$data['techs'] = $registry->toString();

		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		if (isset($data['images']) && is_array($data['images']))
		{
			$root = JUri::root(true);
			$id = $input->getInt('id');

			// Restruct images data
			$images = array_map(
				function($name, $size, $alt, $author, $title, $attrs) use ($root, $id)
				{
					$name = $root . '/images/' . $id . '/' . $name;
					return [
						'name'    => $name,
						'size'    => $size,
						'alt'     => $alt,
						'author'  => $author,
						'title'   => $title,
						'attrs'   => $attrs
					];
				},
				$data['images']['name'],
				$data['images']['size'],
				$data['images']['alt'],
				$data['images']['author'],
				$data['images']['title'],
				$data['images']['attrs']
			);
			$registry = new Registry;
			$registry->loadArray($images);
			$data['images'] = (string) $registry;
		}
		else
		{
			$data['images'] = '{}';
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (int) $input->get('id') == 0)
		{
			if ($data['alias'] == null)
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['title']);
				}

				$table = JTable::getInstance('Catalogue', 'CatalogueTable');

				if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])))
				{
					$msg = JText::_('COM_CONTENT_SAVE_WARNING');
				}

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		if (parent::save($data))
		{

			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_CONTENT_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_catalogue.item'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);

					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_catalogue.item') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);
						return false;
					}
				}
			}

			return true;
		}

		return false;
	}
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   object  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 *
	 * @since   1.6
	 */

	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since    3.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_CONTENT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;

			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_article');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_content');
	}

	/**
	 * _restructData
	 *
	 * @param   int  $name   Name
	 * @param   int  $price  Price
	 * @param   int  $count  Count
	 *
	 * @return  array
	 */
	protected function _restructData($name, $price, $count)
	{
		if ($name && $price && $count)
		{
			return array('name' => $name, 'price' => $price, 'count' => $count);
		}

		return array();
	}

	/**
	 * _restructData
	 *
	 * @param   int  $name        Name
	 * @param   int  $value       Price
	 * @param   int  $show_short  Count
	 *
	 * @return  array
	 */
	protected function _restructTechData($name, $value, $show_short)
	{
		if ($name && $value)
		{
			return array('name' => $name, 'value' => $value, 'show_short' => $show_short);
		}

		return array();
	}
}
