<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class Com_CatalogueInstallerScript
 *
 * @since  1.5
 */
class Com_CatalogueInstallerScript
{
	/**
	 * Method to install an extension.
	 *
	 * @return  void
	 */
	public function install()
	{
		// Create categories for our component
		$basePath = JPATH_ADMINISTRATOR . '/components/com_categories';

		/** @noinspection PhpIncludeInspection */
		require_once $basePath . '/models/category.php';
		$config = array('table_path' => $basePath . '/tables');
		$model = new CategoriesModelCategory($config);
		$data = array(
			'id' => 0,
			'parent_id' => 1,
			'level' => 1,
			'path' => 'uncategorised',
			'extension' => 'com_catalogue',
			'title' => 'Uncategorised',
			'alias' => 'uncategorised',
			'published' => 1,
			'language' => '*'
		);
		$status = $model->save($data);

		if (!$status)
		{
			JError::raiseWarning(500, JText::_('Unable to create default content category!'));
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @return  void
	 */
	public function uninstall()
	{
		echo '<p>' . JText::_('COM_CATALOGUE_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @param   object  $parent  Object
	 *
	 * @return  void
	 */
	public function update($parent)
	{
		echo '<p>' . JText::sprintf('COM_CATALOGUE_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}
}
