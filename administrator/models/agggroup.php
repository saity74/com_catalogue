<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * User group model.
 *
 * @since  1.6
 */
class CatalogueModelAgggroup extends JModelAdmin
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		$config = array_merge(
			array(
				'event_after_delete'  => 'onCatalogueAfterDeleteAgggroup',
				'event_after_save'    => 'onCatalogueAfterSaveAgggroup',
				'event_before_delete' => 'onCatalogueBeforeDeleteAgggroup',
				'event_before_save'   => 'onCatalogueBeforeSaveAgggroup',
				'events_map'          => array('delete' => 'catalogue', 'save' => 'catalogue')
			), $config
		);

		parent::__construct($config);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable	A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Agggroup', $prefix = 'CatalogueTable', $config = array())
	{

		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_catalogue/tables');
		$return = JTable::getInstance($type, $prefix, $config);

		return $return;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm	A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_catalogue.agggroup', 'agggroup', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get Aggregion group mapping record by Joomla group ID
	 *
	 * @param   int  $group_id  Joomla group id
	 *
	 * @return  bool|object
	 *
	 * @throws  Exception
	 */
	public function getByGroupId($group_id)
	{
		$table = $this->getTable();

		if ($group_id > 0)
		{
			// Attempt to load the row.
			$return = $table->load($group_id);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new Registry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}

		return $item;
	}
}
