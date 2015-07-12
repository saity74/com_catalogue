<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Model class for handling lists of items.
 *
 * @since  12.2
 */
class CatalogueModelItem extends JModelList
{
	public $_context = 'com_catalogue.item';

	protected $_extension = 'com_catalogue';

	/**
	 * Method to get item data.
	 *
	 * @param   integer  $pk  The id of the item.
	 *
	 * @return  mixed  Catalogue item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$user = JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('itm.*, cat.title AS category_name, man.manufacturer_description')
			->from('#__catalogue_item AS itm')
			->join('LEFT', '#__categories AS cat ON cat.id = itm.catid')
			->join('LEFT', '#__catalogue_manufacturer AS man ON man.id = itm.manufacturer_id');

		if ((!$user->authorise('core.edit.state', 'com_catalogue')) && (!$user->authorise('core.edit', 'com_catalogue')))
		{
			// Filter by start and end dates.
			$nullDate = $db->quote($db->getNullDate());
			$date = JFactory::getDate();

			$nowDate = $db->quote($date->toSql());

			$query->where('(itm.publish_up = ' . $nullDate . ' OR itm.publish_up <= ' . $nowDate . ')')
				->where('(itm.publish_down = ' . $nullDate . ' OR itm.publish_down >= ' . $nowDate . ')');
		}

		// Filter by published state.
		$published = $this->getState('filter.published');
		$archived = $this->getState('filter.archived');

		if (is_numeric($published))
		{
			$query->where('(itm.state = ' . (int) $published . ' OR itm.state =' . (int) $archived . ')');
		}

		$query->where('itm.id = ' . (int) $pk);

		$db->setQuery($query);

		$data = $db->loadObject();

		if (empty($data))
		{
			return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
		}

		// Check for published state if filter set.
		if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
		{
			return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
		}

		// Convert parameter fields to objects.
		$registry = new Registry;
		$registry->loadString($data->attribs);

		$data->params = clone $this->getState('params');
		$data->params->merge($registry);

		$query = $this->_db->getQuery(true);
		if (!empty($ids))
		{
			$query->select('p.*, a.attr_name')
				->from('#__catalogue_attr_price as p')
				->join('LEFT', '#__catalogue_attr as a ON a.published = 1 AND a.attrdir_id = 1 AND a.id = p.attr_id')
				->where('p.item_id in (' . implode(', ', $ids) . ')')
				->order('a.ordering');

			$this->_db->setQuery($query);

			$attrs = $this->_db->loadObjectList();

			foreach ($attrs as $attr)
			{
				$item_attrs[$attr->item_id][] = $attr;
			}
		}

		return $data;
	}

	/**
	 * Get attr dirs and attrs.
	 *
	 * @return  array  Array of objects.
	 */
	public function getAttrs()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('d.id AS dir_id, a.id AS attr_id, d.dir_name, a.attr_name')
			->from('#__catalogue_attr AS a')
			->join('LEFT', '#__catalogue_attrdir AS d ON d.id = a.attrdir_id')
			->where('a.state = 1 AND d.state = 1');
		$db->setQuery($query);

		$data = $db->loadAssocList('attr_id');

		return $data;
	}

	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer  $pk  Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('item.id');

			$table = JTable::getInstance('Catalogue', 'JTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		$id = $app->input->getUInt('id');
		$this->setState('item.id', $id);

		$db = JFactory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->select('title, introtext, `fulltext`, params, metadata')
				->from('#__catalogue_item')
				->where('state = 1 AND id = ' . $id)
		);

		$item = $db->loadObject();

		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		$this->setState('item.name', $item->title);
		$this->setState('item.params', $item->params);
		$this->setState('item.metadata', $item->metadata);
		$this->setState('item.desc', $item->item_description);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}
}
