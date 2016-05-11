<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

define('DS', DIRECTORY_SEPARATOR);

/** @noinspection PhpIncludeInspection */
require_once JPATH_COMPONENT . '/helpers/route.php';
require_once JPATH_COMPONENT . '/helpers/query.php';
JLoader::register('HttpHelper', JPATH_ADMINISTRATOR . '/helpers/http.php');

$doc = JFactory::getDocument();
$doc->addStyleSheet('/components/com_catalogue/assets/css/catalogue.css');

$controller = JControllerLegacy::getInstance('Catalogue');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
