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
<?php foreach ($item->similar_items as $similar) : ?>
	<?php
	$bootstrapSize = 3;
	$itemClass = "col-lg-$bootstrapSize col-md-$bootstrapSize col-sm-12 col-xs-12";
	$ilink = JRoute::_(CatalogueHelperRoute::getItemRoute($similar->id, $similar->catid));
	?>
	<div class="<?php echo $itemClass ?>">
		<div class="catalogue-one-item white-box" itemscope="" itemtype="http://schema.org/Product">
			<div class="catalogue-one-item-img <?php if ($similar->item_sale)
			{
				echo 'discount-label';
			} ?>">

				<?php echo JLayoutHelper::render('catalogue.category.image', $similar); ?>

			</div>
			<div class="catalogue-one-item-desc">
				<h5 itemprop="name">
					<a class="product-name item-head" href="<?php echo $ilink; ?>"
					   title="<?php echo $similar->title; ?>"
					   itemprop="url"><?php echo $similar->title; ?></a>
				</h5>

				<div class="item-shortdesc">
					<?php if (!empty($similar->introtext))
					{
						echo $similar->introtext;
					} ?>
				</div>
				<?php if (!$similar->item_sale): ?>
					<div class="item-price-wrapper">
						<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<?php if ($similar->price)
							{
								echo number_format($similar->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
							} ?>
							<meta itemprop="priceCurrency" content="0">
						</p>
					</div>
				<?php else: ?>
					<?php $new_price = $similar->price - (($similar->price / 100) * $similar->item_sale); ?>
					<div class="item-price-wrapper">
						<p class="item-old-price" itemprop="offers" itemscope=""
						   itemtype="http://schema.org/Offer">
							<?php echo number_format($similar->price, 0, '.', ' '); ?>
							<meta itemprop="priceCurrency" content="0">
						</p>
						<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<?php echo number_format($new_price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.'); ?>
							<meta itemprop="priceCurrency" content="0">
						</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
