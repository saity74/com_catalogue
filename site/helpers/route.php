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
 * Class CatalogueHelperRoute
 *
 * @since  1.5
 */
abstract class CatalogueHelperRoute
{
	protected static $lookup = array();

	protected static $lang_lookup = array();

	/**
	 * Build uri by item id and catid
	 *
	 * @param   int  $id     Item ID
	 * @param   int  $catid  Category ID
	 *
	 * @return  string
	 *
	 * @internal param The $integer route of the content item
	 */
	public static function getItemRoute($id, $catid = 0)
	{
		$needles = array(
			'item' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_catalogue&view=item&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Catalogue');
			$category = $categories->get((int) $catid);

			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&cid=' . $catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Find menu item ID
	 *
	 * @param   array  $needles  Search params
	 *
	 * @return  null
	 */
	protected static function _findItem($needles = null)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();

		$component = JComponentHelper::getComponent('com_catalogue');

		$attributes = array('component_id');
		$values = array($component->id);

		$items = $menus->getItems($attributes, $values);

		foreach ($items as $item)
		{
			if ($item->query['cid'] == $needles['category'])
			{
				return $item->id;
			}
		}

		if ($active && $active->component == 'com_catalogue')
		{
			return $active->id;
		}

		$default = $menus->getDefault();

		return !empty($default->id) ? $default->id : null;
	}

	/**
	 * Method to get url for category
	 *
	 * @param   int  $catid  Category ID
	 *
	 * @return  string
	 */
	public static function getCategoryRoute($catid)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('Catalogue')->get($id);
		}

		if ($id < 1 || !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles = array();
			$link = 'index.php?option=com_catalogue&view=category&cid=' . $id;
			$catids = array_reverse($category->getPath());
			$needles['category'] = $id;
			$needles['categories'] = $catids;

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}
}
