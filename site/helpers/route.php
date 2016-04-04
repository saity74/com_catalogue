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
				$needles['category'] = $catid;
				$needles['categories'] = array_reverse($category->getPath());
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
	 * Find an item ID.
	 *
	 * @param   array  $needles  An array of language codes.
	 *
	 * @return  mixed  The ID found or null otherwise.
	 *
	 * @since   1.5
	 */
	protected static function _findItem($needles = null)
	{
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		self::$lookup[$language] = array();

		$component  = JComponentHelper::getComponent('com_catalogue');

		$attributes = array('component_id');
		$values     = array($component->id);

		if ($language != '*')
		{
			$attributes[] = 'language';
			$values[]     = array($needles['language'], '*');
		}

		$items = $menus->getItems($attributes, $values);

		foreach ($items as $item)
		{
			if (isset($item->query) && isset($item->query['view']))
			{
				$view = $item->query['view'];

				if (!isset(self::$lookup[$language][$view]))
				{
					self::$lookup[$language][$view] = array();
				}

				if (isset($item->query['cid']))
				{
					/**
					 * Here it will become a bit tricky
					 * language != * can override existing entries
					 * language == * cannot override existing entries
					 */
					if (!isset(self::$lookup[$language][$view][$item->query['cid']]) || $item->language != '*')
					{
						self::$lookup[$language][$view][$item->query['cid']] = $item->id;
					}
				}
			}
		}
		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach ((array) $ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}

		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();

		if ($active
			&& $active->component == 'com_catalogue'
			&& ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
		{
			return $active->id;
		}

		// If not found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}

	/**
	 * Get the category route.
	 *
	 * @param   integer  $catid     The category ID.
	 * @param   integer  $language  The language code.
	 *
	 * @return  string  The article route.
	 *
	 * @since   1.5
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id       = $catid->id;
			$category = $catid;
		}
		else
		{
			$id       = (int) $catid;
			$category = JCategories::getInstance('Catalogue')->get($id);
		}

		if ($id < 1 || !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles               = array();
			$link                  = 'index.php?option=com_catalogue&view=category&cid=' . $id;
			$catids                = array_reverse($category->getPath());
			$needles['category']   = $id;
			$needles['categories'] = $catids;

			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
			{
				$link .= '&lang=' . $language;
				$needles['language'] = $language;
			}

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid=' . $item;
			}
		}

		return $link;
	}

	public static function getCartRoute($language = 0)
	{
		$needles = array();
		$link    = 'index.php?option=com_catalogue';

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			$link .= '&lang=' . $language;
			$needles['language'] = $language;
		}

		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		self::$lookup[$language] = array();

		$component  = JComponentHelper::getComponent('com_catalogue');

		$attributes = array('component_id');
		$values     = array($component->id);

		if ($language != '*')
		{
			$attributes[] = 'language';
			$values[]     = array($needles['language'], '*');
		}

		$items = $menus->getItems($attributes, $values);

		foreach ($items as $item) {
			if (isset($item->query)
				&& isset($item->query['view'])
				&& $item->query['view'] == 'cart')
			{
				$link .= '&Itemid=' . $item->id;
				break;
			}
		}

		return $link;
	}

	public static function getOrderRoute($language = 0)
	{
		$needles = array();
		$link    = 'index.php?option=com_catalogue';

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			$link .= '&lang=' . $language;
			$needles['language'] = $language;
		}

		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		self::$lookup[$language] = array();

		$component  = JComponentHelper::getComponent('com_catalogue');

		$attributes = array('component_id');
		$values     = array($component->id);

		if ($language != '*')
		{
			$attributes[] = 'language';
			$values[]     = array($needles['language'], '*');
		}

		$items = $menus->getItems($attributes, $values);

		foreach ($items as $item) {
			if (isset($item->query)
				&& isset($item->query['view'])
				&& $item->query['view'] == 'order')
			{
				$link .= '&Itemid=' . $item->id;
				break;
			}
		}

		return $link;
	}
}
