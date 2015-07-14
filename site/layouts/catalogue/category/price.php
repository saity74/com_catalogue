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

?>

<div class="catalogue-items-one-price">
	<?php if (!$item->item_sale): ?>
		<p class="catalogue-items-one-price-inner" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
			<?php if ($item->price)
			{
				echo number_format($item->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
			} ?>
			<meta itemprop="priceCurrency" content="0">
		</p>
	<?php else: ?>
		<?php $new_price = $item->price - (($item->price / 100) * $item->item_sale); ?>
		<p class="catalogue-items-one-old-price-inner" itemprop="offers" itemscope=""
		   itemtype="http://schema.org/Offer">
			<?php echo number_format($item->price, 0, '.', ' '); ?>
			<meta itemprop="priceCurrency" content="0">
		</p>
		<p class="catalogue-items-one-price-inner" itemprop="offers" itemscope=""
		   itemtype="http://schema.org/Offer">
			<?php echo number_format($new_price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.'); ?>
			<meta itemprop="priceCurrency" content="0">
		</p>
	<?php endif; ?>
</div>
