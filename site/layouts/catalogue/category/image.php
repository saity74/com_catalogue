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

$img_width = $params->get('img_width', '240px');
$img_height = $params->get('img_height', '240px');

$item = $displayData;

if (!empty($item->images))
{
	$images = new JRegistry($item->images);
	$image = basename($images->get('0.name'));
	$path = implode(DIRECTORY_SEPARATOR, ['images', $item->id, $image]);
	$path = JPath::clean($path);
	$src = CatalogueHelper::createThumb($item->id, $path, $img_width, $img_height, 'min') ?:
		'images/placeholder.jpg';
	$ilink = JRoute::_(CatalogueHelperRoute::getItemRoute($item->id, $item->catid));
}

?>

<a href="<?php echo $ilink; ?>" title="<?php echo $item->title; ?>">
	<img src="<?php echo $src ?>" title="<?php echo $item->title; ?>"
		 alt="<?php echo $item->title; ?>" width="<?php echo $img_width; ?>"
		 height="<?php echo $img_height; ?>"
		 style="width: <?php echo $img_width; ?>;height: <?php echo $img_height; ?>"
		 itemprop="image"/>
</a>