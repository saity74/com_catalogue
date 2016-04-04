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
 * CatalogueControllerOrders
 *
 * Controller (controllers are where you put all the actual code) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @since  12.2
 */
class CatalogueControllerOrders extends JControllerAdmin
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerAdmin
	 * @since   12.2
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   12.2
	 */
	public function getModel($name = 'Order', $prefix = 'CatalogueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to redirect.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function edit()
	{
		$id = $this->input->getInt('id', 0);
		$this->setRedirect('index.php?option=com_catalogue&view=order&layout=edit&id=' . $id);
	}
}
