<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_catalogue');
$item = $displayData;
$item_short_desc_len = $params->get('item_short_desc_len', 150);

if (!empty($item->introtext)) {
	if ($params->get('slice_desc', 0) == '1'
		&& $slice_desc_len > 0
		&& mb_strlen(strip_tags($item->introtext)) > $slice_desc_len)
	{
		echo '<p>' . trim(mb_substr(strip_tags($item->introtext), 0, $slice_desc_len)) . '&#8230</p>';
	}
	else
	{
		echo $item->introtext;
	}
}

