<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class CatalogueFilterHelper
 *
 * @since  1.5
 */
abstract class CatalogueFilterHelper
{
	/**
	 * Method to get data for filters
	 *
	 * @param   object  $filter  Filter object
	 *
	 * @return  mixed
	 */
	static public function getFilterData($filter)
	{
		$result = array();

		$db = JFactory::getDbo();
		switch ($filter->filter_field)
		{
			case 'attr' :
			{
				switch ($filter->filter_type)
				{
					case 'range' :

						break;
				}
				$db->setQuery(
					$db->getQuery(true)
						->select('*')
						->from('#__catalogue_attr')
						->where('published = 1 AND state = 1 AND attrdir_id = ' . (int) $filter->id)
						->order('ordering ASC')
				);
				$result = $db->loadObjectList();
			}
				break;

			case 'fl_price' :
			{
				$db->setQuery(
					$db->getQuery(true)
						->select($filter->id . ' as id, MIN(price) as from_val, MAX(price) as to_val')
						->from('#__catalogue_item')
						->where('published = 1 AND state = 1')
				);
				$result = $db->loadObjectList();
			}
				break;

			case 'fl_manufacturer' :
			{
				$db->setQuery(
					$db->getQuery(true)
						->select('id, manufacturer_name as attr_name')
						->from('#__catalogue_manufacturer')
						->where('published = 1 AND state = 1')
						->order('ordering')
				);
				$result = $db->loadObjectList();
			}
				break;
		}

		foreach ($result as $row)
		{
			$row->input_name = $filter->filter_field . '_' . $row->id;
		}

		return $result;
	}
}
