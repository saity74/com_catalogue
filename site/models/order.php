<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_catalogue/models/base.order.php';

use Joomla\Registry\Registry;

/**
 * Catalogue Component Order Model
 *
 * @since  1.5
 */

class CatalogueModelOrder extends CatalogueModelBaseOrder
{
	protected $cache = array();

	protected $_context = 'com_catalogue.order';

	protected $item;

	public function __construct(array $config)
	{
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
		$app = JFactory::getApplication('site');

		$orderId = $app->getUserState($this->_context . '.id');
		$this->setState('order.id', $orderId);

		//$this->setState('cart.id', CatalogueCart::getInstance()->getProperty('id'));

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

	public function getItem($pk = null)
	{
		return parent::getItem($pk);
	}

	public function getForm($data = array(), $loadData = true)
	{
		return parent::getForm($data, $loadData);
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = JFactory::getApplication();
		$data = $app->getUserState('com_catalogue.edit.order.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('order.id') == 0)
			{
				$orderId = $app->getUserState('com_catalogue.order.id');
				return false;
			}
		}
		$this->preprocessData('com_catalogue.order', $data);
		return $data;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$tz = new DateTimeZone(JFactory::getApplication()->get('offset'));

		$dp_label = JText::_($form->getFieldAttribute('delivery_period', 'label'));

		$dp_label .= JText::sprintf(
			JText::_('COM_CATALOGUE_CURRENT_DATETIME'),
			JFactory::getDate('now', $tz)->format('d M H:i', true)
		);

		$form->setFieldAttribute('delivery_period', 'label', $dp_label);

		parent::preprocessForm($form, $data, $group); // TODO: Change the autogenerated stub
	}

	public function save($data = null)
	{
		// if checkout only create $data for new row with CartID
		if ($data === null)
		{
			$data['cart_id'] = $this->getState('cart.id');
		}

		if (!parent::save($data))
		{

			return false;
		};

		return true;
	}
}