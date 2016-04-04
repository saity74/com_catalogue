<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var $displayData */
$item = $displayData;


?>

<h5 itemprop="name">
	<a class="product-name item-head" href="<?php echo $item->link; ?>"
	   title="<?php echo $item->title; ?>"
	   itemprop="url"><?php echo $item->title; ?></a>
</h5>