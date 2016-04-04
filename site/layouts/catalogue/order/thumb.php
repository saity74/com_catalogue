<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use \Joomla\Registry\Registry;

$params = JComponentHelper::getParams('com_catalogue');

$cart_thumb_width = $params->get('order_thumb_width', 56);
$cart_thumb_height = $params->get('order_thumb_height', 56);

/** @var mixed $displayData */
$item = $displayData;
$image = null;

if ($item->images)
{
	$images = new Registry($item->images);
	$image = $images->toArray()[0];
	$name = basename($image['name']);
	$path = implode(DIRECTORY_SEPARATOR, ['images', $item->id, $name]);
	$path = JPath::clean($path);
	$image['src'] = CatalogueHelper::createThumb($item->id, $path, $cart_thumb_width, $cart_thumb_height, 'order');
	$image['attrs'] = explode(',', $image['attrs']);
}

?>

<?php if ($item->images && $image) : ?>

	<img
		class="item-image"
		src="<?php echo $image['src']; ?>"
		width="<?php echo $cart_thumb_width ?>"
		height="<?php echo $cart_thumb_width ?>" />

<?php endif;