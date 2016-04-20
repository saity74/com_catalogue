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
 * CatalogueTableAttrDir class
 *
 * @since  11.1
 */
class CatalogueTableAttrDir extends JTable
{
	public $id;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabaseDriver  &$_db  JDatabaseDriver object.
	 *
	 * @since   11.1
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__catalogue_attrdir', 'id', $_db);

		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/store
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$result = parent::store($updateNulls);

		if ($result)
		{
			$values = array();

			$query = 'DELETE FROM #__catalogue_attrdir_category WHERE attrdir_id = ' . (int) $this->id;

			$this->_db->setQuery($query);
			$this->_db->execute();

			$keyName = $this->getKeyName();
			$this->load($this->$keyName);

			$jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');

			$cat_ids = $jform['category_id'];

			if (!empty($cat_ids))
			{
				$insert_query = 'INSERT INTO #__catalogue_attrdir_category (`attrdir_id`, `category_id`) VALUES ';

				foreach ($cat_ids as $category_id)
				{
					$values[] = '(' . (int) $this->id . ',' . (int) $category_id . ')';
				}

				if (is_array($values) && !empty($values))
				{
					$insert_query .= implode(',', $values);
					$this->_db->setQuery($insert_query);
					$this->_db->execute();
				}
			}
		}

		return $result;
	}
}
