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

<div class="open-item-price-wrap">
	<?php if (!$item->item_sale): ?>
		<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
			<?php if ($item->price)
			{
				echo number_format($item->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
			} ?>
			<meta itemprop="priceCurrency" content="RUB">
		</p>
	<?php else: ?>
		<?php $new_price = $item->price - (($item->price / 100) * $item->item_sale); ?>
		<div class="item-price-wrapper clearfix">
			<p class="item-old-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<?php echo number_format($item->price, 0, '.', ' '); ?>
			</p>

			<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<?php echo number_format($new_price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.'); ?>
			</p>
			<meta itemprop="priceCurrency" content="RUB">
		</div>
		<div class="discount-sum-wrap">
			<p>Экономия <span
					class="bold-text"><?php echo number_format((($item->price / 100) * $item->item_sale), 0, '.', ' '); ?></span>
				руб.</p>
		</div>
	<?php endif; ?>
</div>