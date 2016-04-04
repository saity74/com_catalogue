<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_catalogue/models/base.cart.php';

use Joomla\Registry\Registry;

/**
 * Catalogue Component Article Model
 *
 * @since  1.5
 */

class CatalogueModelCart extends CatalogueModelBaseCart
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */

	protected $cache = array();

	protected $_context = 'com_catalogue.cart';

	protected $cart;

	protected $item;

	public function __construct(array $config)
	{
		$this->cart = CatalogueCart::getInstance();

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{

		$this->setState('cart.id', $this->cart->getProperty('id'));

		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);

		// TODO: Tune these values based on other permissions.
		$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_catalogue')) && (!$user->authorise('core.edit', 'com_catalogue')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

	}

	/**
	 * Method to get cart data.
	 *
	 * @param   integer  $pk  The id of the cart.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = (object) $this->cart->getProperties();
		return $item;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = serialize($this->cart);

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

	public function getItems()
	{
		$id = $this->cart->array_keys();

		if (!empty($id)) {
			$db = $this->_db;
			$query = $db->getQuery(true)
				->select(
					$this->getState('list.select',
						'i.id, i.catid, i.title, i.price, i.introtext, i.fulltext, i.state, i.images, i.attribs'
					)
				)
				->from('#__catalogue_item AS i')
				->where('i.id IN (' . implode(',', $id) . ')');

			$db->setQuery($query);

			$items = $db->loadObjectList('id');

			foreach ($items as $key => $item)
			{
				$item->count = $this->cart[$key];
				$item->amount = $this->cart[$key] * $item->price;
			}

			return $items;
		}

		return null;
	}

	public function update($data)
	{
		parent::update($data);
	}

	public function delete()
	{
		return false;
	}
}