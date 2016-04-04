<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


/**
 * Class CatalogueControllerCart
 *
 */

class CatalogueControllerCart extends JControllerForm
{

	public function action()
	{
		try
		{
			echo new JResponseJson('');
		}
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}
	}
}