<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


/**
 * Item Model for an Item.
 *
 * @since  12.2
 */
class CatalogueModelBaseCart extends JModelAdmin
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
	public $typeAlias = 'com_catalogue.cart';

	public function __construct(array $config)
	{

		parent::__construct($config);
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
			return $user->authorise('core.delete', 'com_catalogue.cart.' . (int) $record->id);
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
			return $user->authorise('core.edit.state', 'com_catalogue.cart.' . (int) $record->id);
		}
		// Default to component settings if neither item nor category known.
		else
		{
			return parent::canEditState('com_catalogue');
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
	public function getTable($type = 'Cart', $prefix = 'CatalogueTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$form = $this->loadForm('com_catalogue.cart', 'cart', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput = JFactory::getApplication()->input;
		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('cart_id'))
		{
			$id = $jinput->get('cart_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('cart.id'))
		{
			$id = $this->getState('cart.id');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}
		$user = JFactory::getUser();
		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_catalogue.cart.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_catalogue')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is an item you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
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
		$data = $app->getUserState('com_catalogue.edit.cart.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			// Prime some default values.
			if ($this->getState('cart.id') == 0)
			{
				$filters = (array) $app->getUserState('com_catalogue.cart.filter');
			}
		}
		$this->preprocessData('com_catalogue.cart', $data);
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
		$app        = JFactory::getApplication();
		$input      = $app->input;
		$filter     = JFilterInput::getInstance();
		$table      = $this->getTable();
		$pk         = (int) $data[$table->getKeyName()];

		$table->load($pk);
		$items = json_decode($table->get('items', null), true) ?: array();

		$data['total'] = $table->get('total', 0);

		if (isset($data['items']) && is_array($data['items']) && !empty($data['items']))
		{
			array_map(
				function($id, $count) use (&$items, &$data)
				{
					$items[$id] += (int) $count;
					$data['total'] += (int) $count;
				},
				$data['items']['id'],
				$data['items']['count']
			);
		}

		$data['items'] = $items;

		if (isset($data['created_by_alias']))
		{
			$data['created_by_alias'] = $filter->clean($data['created_by_alias'], 'TRIM');
		}

		return parent::save($data);
	}

	public function update($data)
	{
		$recordId = $this->getState('cart.id');
		$table      = $this->getTable();
		$pk         = (int) $recordId;
		$table->load($pk);

		$items = json_decode($table->get('items', null), true) ?: array();

		$data['total'] = $table->get('total', 0);

		if (isset($data['items']) && is_array($data['items']) && !empty($data['items']))
		{
			array_map(
				function($id, $count) use (&$items, &$data)
				{
					$items[$id] -= (int) $count;
					$data['total'] -= (int) $count;
					if (empty($items[$id]) || is_null($items[$id]))
					{
						unset($items[$id]);
					}
				},
				$data['items']['id'],
				$data['items']['count']
			);
		}

		$data['items'] = $items;

		return parent::save($data);
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
		parent::cleanCache('com_catalogue');
	}

}