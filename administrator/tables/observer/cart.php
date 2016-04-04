<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class CatalogueTableCartObserver extends JTableObserver
{

	/**
	 * Creates the associated observer instance and attaches it to the $observableObject
	 *
	 * @param   JObservableInterface $observableObject The observable subject object
	 * @param   array $params Params for this observer
	 *
	 * @return  JObserverInterface
	 *
	 * @since   3.1.2
	 */
	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);
		return $observer;
	}

	public function onBeforeLoad($keys, $reset)
	{
		return true;
	}

	public function onBeforeStore($updateNulls, $tableKey)
	{
		$cartItems = json_decode($this->table->get('items'), true);

		if (!$cartItems) return false;

		$pk = array_keys($cartItems);

		if ($pk && !empty($pk))
		{
			$db = $this->table->getDbo();

			$query = $db->getQuery(true)
				->select('id, price')
				->from('#__catalogue_item')
				->where('id IN (' . implode(',', $pk) . ')');

			$prices = $db->setQuery($query)->loadObjectList('id');

			$amount = 0;
			array_map(function($count, $item) use (&$amount) {
				$amount += $item->price * (int) $count;
			}, $cartItems, $prices);

			$this->table->set('amount', $amount);
		}

		return true;

	}

}
