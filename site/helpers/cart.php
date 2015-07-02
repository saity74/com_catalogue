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
 * Class CatalogueHelperCart
 *
 * @since  1.5
 */
abstract class CatalogueHelperCart
{
	/**
	 * getCartItems
	 *
	 * @return  bool|mixed
	 */
	public static function getCartItems()
	{
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_catalogue.cart');
		if ($data)
		{
			$cart_items = unserialize($data);
			if (!empty($cart_items))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query->select('*');
				$query->from('#__catalogue_item');
				$query->where('id IN (' . implode(',', array_keys($cart_items)) . ')');
				$query->order('item_name');
				$db->setQuery($query);
				$items = $db->loadObjectList();

				foreach ($items as $item)
				{
					$item->cart_info = $cart_items[$item->id];
				}

				return $items;
			}
		}
		return false;
	}

	/**
	 * item_form
	 *
	 * @param   int  $n  Num
	 *
	 * @return  mixed
	 */
	public static function item_form($n)
	{
		$forms = array('товар', 'товара', 'товаров');

		if ($n % 10 == 1 && $n % 100 != 11)
		{
			return $forms[0];
		}
		elseif ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20))
		{
			return $forms[1];
		}
		else
		{
			return $forms[2];
		}
	}

	/**
	 * Find item by item ID
	 *
	 * @param   int  $id  Item ID
	 *
	 * @return  bool
	 */
	public static function inCart($id)
	{
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_catalogue.cart');
		if ($data)
		{
			$cart_items = unserialize($data);
			return array_key_exists($id, $cart_items);
		}
		else
		{
			return false;
		}
	}
}
