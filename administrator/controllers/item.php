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
class CatalogueControllerItem extends JControllerForm
{
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_list = 'catalogue';

	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		$categoryId = JArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
		$allow = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow = $user->authorise('core.create', 'com_catalogue.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else
		{
			return $allow;
		}
	}

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
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Item', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_catalogue&view=catalogue' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Method to upload images
	 *
	 * @return	string
	 *
	 */
	public function imageUpload()
	{
		jimport('joomla.filesystem.folder');

		$app = JFactory::getApplication();
		$id = $app->getUserState('com_catalogue.edit.item.id');

		$params = JComponentHelper::getParams('com_media');

		$file        = $this->input->files->get('file', '', 'array');

		$this->folder = 'images/' . $id[0];

		// Create upload folder if not exist
		if (!JFolder::exists(JPATH_SITE . DIRECTORY_SEPARATOR . $this->folder))
		{
			JFolder::create(JPATH_SITE . DIRECTORY_SEPARATOR . $this->folder);
		}

		// Authorize the user
		if (!JFactory::getUser()->authorise('core.create', 'com_catalogue'))
		{
			header('HTTP/1.1 403 Restricred access!');
			return false;
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			echo 'File size exceed either \'upload_max_filesize\' or \'upload_maxsize\'';
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything

		$file['name']     = JFile::makeSafe($file['name']);
		$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(JPATH_SITE, $this->folder, $file['name'])));

		if (($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
			|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
		{
			// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
			echo 'File size exceed either \'upload_max_filesize\' or \'upload_maxsize\'';
		}
		elseif (!isset($file['name']))
		{
			echo 'no file name';
		} else {
			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
				header('HTTP/1.1 409 Conflict!');
				echo 'A file with this name already exists';
			} else {
				// Set FTP credentials, if given
				JClientHelper::setCredentialsFromRequest('ftp');

				// Trigger the onContentBeforeSave event.
				$object_file = new JObject($file);

				if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
				{
					// Error in upload
					echo 'can\'t upload file';
				} else {
					echo '1';
				}
			}
		}

		$app->close();
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
}
