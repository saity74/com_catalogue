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
 * CatalogueHelper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @since       3.0
 */
class CatalogueHelper
{

	public static $extension = 'com_catalogue';

	/**
	 * Method to get options for list.
	 *
	 * @param   string  $vName  Alias of menu
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_CATALOGUE'),
			'index.php?option=com_catalogue&view=catalogue',
			$vName == 'catalogue'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_catalogue',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_MANUFACTURERS'),
			'index.php?option=com_catalogue&view=manufacturers',
			$vName == 'manufacturers'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_COUNTRIES'),
			'index.php?option=com_catalogue&view=countries',
			$vName == 'countries'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_ATTRDIRS'),
			'index.php?option=com_catalogue&view=attrdirs',
			$vName == 'attrdirs'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_ATTRS'),
			'index.php?option=com_catalogue&view=attrs',
			$vName == 'attrs'
		);
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getItemsOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id As value, title As text');
		$query->from('#__catalogue_item AS i');
		$query->order('i.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getCountriesOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id As value, country_name As text');
		$query->from('#__catalogue_country AS ctr');
		$query->order('ctr.country_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getManufacturersOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id As value, manufacturer_name As text');
		$query->from('#__catalogue_manufacturer AS ctr');
		$query->order('ctr.manufacturer_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getAttrDirsOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id As value, dir_name As text');
		$query->from('#__catalogue_attrdir AS d');
		$query->order('d.dir_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getAttrsOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('CONCAT(d.id, ":", a.id) AS value, a.attr_name AS text')
			->from('#__catalogue_attr AS a')
			->join('LEFT', '#__catalogue_attrdir AS d ON d.id = a.attrdir_id')
			->where('a.state = 1 AND d.state = 1')
			->order('d.dir_name');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}
}
