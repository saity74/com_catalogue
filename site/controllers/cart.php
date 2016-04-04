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
 * Class CatalogueControllerCart
 *
 */
class CatalogueControllerCart extends JControllerForm
{

	public function save($key = 'id', $urlVar = 'cart_id')
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $this->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";

		$recordId = $this->input->getInt($urlVar);
		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);

		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');

			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);

		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			// Redirect back to the edit screen.
			$this->goBack();

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_CATALOGUE_ADD_TO_CART_FAILED', $model->getError(), 'error'));

			$this->goBack();

			return false;
		}

		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setMessage(JText::sprintf('COM_CATALOGUE_CHECKIN_FAILED', $model->getError(), 'error'));

			$this->goBack();

			return false;
		}

		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_ADD_TO_CART_SUCCESS')
					? $this->text_prefix
					: 'COM_CATALOGUE') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_ADD_TO_CART_SUCCESS'
			)
		);

		$recordId = $model->getState($this->context . '.id');
		$this->holdEditId($context, $recordId);
		$app->setUserState($context . '.data', null);
		$model->checkout($recordId);

		// Redirect back previous page.
		$this->goBack();

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}


	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   12.2
	 */
	public function edit($key = 'id')
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();

		$context = "$this->option.edit.$this->context";

		// Get the previous record id (if any) and the current record id.

		if (CatalogueCart::$id && CatalogueCart::$count)
		{
			$this->setRedirect(
				JRoute::_(
					CatalogueHelperRoute::getCartRoute()
				)
			);

			return true;
		}

		$this->goBack();
		return false;

	}

	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$data = $this->input->get('jform', array(), 'array');

		// Get the model.
		$model = $this->getModel();
		$model->setState('cart.id', CatalogueCart::getInstance()->getProperty('id'));

		// Remove the items.
		if ($model->update($data))
		{
			$this->setMessage(JText::plural('COM_CATALOGUE_N_ITEMS_DELETED', count($data['items']['id'])));
		}
		else
		{
			$this->setMessage($model->getError(), 'error');
		}

		// Invoke the postDelete method to allow for the child class to access the model.
		//$this->postDeleteHook($model, $cid);

		$this->goBack();
	}

	public function allowAdd($data = array())
	{
		return false;
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	protected function goBack($msg = null, $type = null)
	{
		$return = $this->input->server->get('HTTP_REFERER', JRoute::_('index.php'), 'string');
		$this->setRedirect($return, $msg, $type);
	}
}
