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
 * The item controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 * @since       12.2
 */
class CatalogueControllerAggregion extends JControllerForm
{

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_list = 'catalogue';

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_catalogue.item.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_catalogue.item.' . $recordId))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return	void
	 *
	 * @since	3.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		return;
	}

	/**
	 * Depreceted
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function mapping()
	{
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$mapping = $jinput->get('mapping');

		$this->context = "$this->context.$mapping";

		$this->input->set('layout', $mapping);

		$context = "$this->option.edit.$this->context";

		// Access check.
		if (!$this->allowEdit())
		{
			// Set the internal error and also the redirect error.
			$this->setMessage(JText::_('JLIB_APPLICATION_ERROR_EDIT_RECORD_NOT_PERMITTED'), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=com_catalogue&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Clear the record edit information from the session.
		$app->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_catalogue&view=' . $this->view_item
				. $this->getRedirectToItemAppend(), false
			)
		);

		return true;
	}

	/**
	 * AJAX get aggregion items
	 *
	 * @return void
	 */
	public function getItemsAJAX()
	{
		if ($items = $this->getModel()->getItems())
		{
			echo json_encode($items);
		}

		JFactory::getApplication()->close();
	}

	/**
	 * Finish Aggregion import
	 *
	 * @return void
	 */
	public function finishImport()
	{
		// TODO: handle errors && warning

		$this->setMessage(JText::_('COM_CATALOGUE_AGGREGION_FINISH_IMPORT_SUCCESS'), 'success');
		$this->setRedirect(
			JRoute::_('index.php?option=com_catalogue&view=aggregion', false)
		);
	}

	/**
	 * Aggregion items import
	 *
	 * @return void
	 */
	public function import()
	{
		// TODO: check correct mapping
		$items = $this->getModel()->getItems();

		if ( ! $items )
		{
			$this->setMessage(JText::_('COM_CATALOGUE_AGGREGION_NO_ITEMS_ERROR'), 'error');
			$this->setRedirect(
				JRoute::_('index.php?option=com_catalogue&view=aggregion', false)
			);
		}

		$this->input->set('layout', 'import');
		$this->input->set('items', $items);

		parent::display();
	}

	/**
	 * AJAX import for one item
	 *
	 * @return void
	 */
	public function importItem()
	{
		$response = [
			'status'	=> 1,
			'msg'		=> 'Error'
		];

		if ( $agg_item = $this->input->post->get('item', false, 'json') )
		{
			if ( $agg_item = json_decode($agg_item) )
			{
				if ( $item = AggregionHelper::buildCatalogueItem($agg_item) )
				{
					$cover = $item['cover'];
					unset($item['cover']);

					$item_model = JModelLegacy::getInstance('Item', 'CatalogueModel');

					if ( $item_model->save($item) )
					{
						$item_id = isset($item['id']) ? $item['id'] : $item_model->getState($item_model->getName() . '.id');
						$item_images = [];
						$item_images['id'] = $item_id;

						AggregionHelper::getImages($item_images, $cover);

						if ( $item_model->save($item_images) )
						{
							$item['id'] = $item_id;
							$response['status'] = 0;
							$response['msg'] = 'Success';
							$response['item'] = $item;
						}
						else
						{
							$response['status'] = 6;
							$response['msg'] = 'Unable to save images';
							$response['item'] = $item;
						}
					}
					else
					{
						$response['status'] = 5;
						$response['msg'] = $item_model->getError();
					}
				}
				else
				{
					$response['status'] = 4;
					$response['msg'] = 'Item doesn\'t belong to any category';
				}
			}
			else
			{
				$response['status'] = 3;
				$response['msg'] = 'Bad JSON';
			}
		}
		else
		{
			$response['status'] = 2;
			$response['msg'] = 'No item';
		}

		echo json_encode($response);

		JFactory::getApplication()->close();
	}
}
