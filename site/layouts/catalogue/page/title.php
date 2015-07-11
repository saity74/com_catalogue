<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = $displayData->params;
$pageclass_sfx = $params->get('pageclass_sfx');
$item = $this->item;
?>

<div class="item-page<?php echo $pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Article">
	<meta
		itemprop="inLanguage"
		content="<?php echo ($item->language === '*') ? JFactory::getConfig()->get('language') : $item->language; ?>" />
	<div class="page-header">
		<h1> <?php echo $this->escape($params->get('page_heading')); ?> </h1>
	</div>
</div>
