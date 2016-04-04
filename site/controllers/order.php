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
 * Class CatalogueControllerOrder
 *
 */

class CatalogueControllerOrder extends JControllerForm
{

	public function __construct(array $config)
	{
		parent::__construct($config);
	}

	public function checkout()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		if (!CatalogueCart::$isEmpty && CatalogueCart::$amount > 0)
		{
			$app = JFactory::getApplication('site');

			$model = $this->getModel();
			// Set model state
			$model->setState('cart.id', CatalogueCart::$id);

			// Attempt to save the data.
			if (!$model->save())
			{
				// Redirect back to the edit screen.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
				$this->goBack();

				return false;
			}

			$context = "$this->option.edit.$this->context";

			$orderId = $model->getState('order.id');

			$table = $model->getTable();

			$checkin = property_exists($table, 'checked_out');

			// Attempt to check-out the new record for editing and redirect.
			if ($checkin && !$model->checkout($orderId))
			{
				// Check-out failed, display a notice but allow the user to see the record.
				$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()), 'error');
				$this->goBack();

				return false;
			}
			else
			{
				// Check-out succeeded, push the new record id into the session.
				$this->holdEditId($context, $orderId);
				$app->setUserState($context . '.data', null);

				$this->setRedirect(
					JRoute::_(
						'index.php?option=com_catalogue')
				);

				return true;
			}
		}

		return false;
	}

	protected function goBack($msg = null, $type = null)
	{
		$return = $this->input->server->get('HTTP_REFERER', JRoute::_('index.php'), 'string');
		$this->setRedirect($return, $msg, $type);
	}

	public function action1()
	{
		$form = $this->input->post->get('form', array(), 'array');

		echo json_encode(array('postData' => $form, 'redirect' => '/go/to/action1.php'));

		JFactory::getApplication()->close();
	}

	public function action2()
	{
		$form = $this->input->post->get('form', array(), 'array');

		echo json_encode(array('postData' => $form, 'redirect' => '/go/to/action2.php'));

		JFactory::getApplication()->close();
	}

}