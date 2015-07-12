<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$item = $displayData;
$params = $item->params;

$item_title_tag_size = $params->get('item_title_tag_size', 1);
?>

	<h<?php echo $item_title_tag_size ?> itemprop="name">
		<?php echo $item->title; ?>
	</h<?php echo $item_title_tag_size ?>>
