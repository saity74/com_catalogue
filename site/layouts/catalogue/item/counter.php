<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var mixed $displayData */
$item = $displayData;

$cart = CatalogueCart::getInstance();

?>
<div class="item-counter-wrap">
	<div class="counter">
		<input class="input-counter" name="jform[items][count][]" value="<?php echo $cart->get($item->id, 1); ?>" />
		<a class="input-btn counter-down" href="#"><span class="icon icon-minus"></span></a>
		<a class="input-btn counter-up" href="#"><span class="icon icon-plus"></span></a>
	</div>
</div>
