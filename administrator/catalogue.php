<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_catalogue'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Register classes to be able to autoload files if class is instantiated.

JLoader::register('CatalogueHelper', __DIR__ . '/helpers/catalogue.php');
JLoader::register('AggregionHelper', __DIR__ . '/helpers/aggregion.php');
JLoader::register('HttpHelper', __DIR__ . '/helpers/http.php');

$controller = JControllerLegacy::getInstance('Catalogue');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
