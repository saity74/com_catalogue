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
<div class="row">
<?php if ($item->similar_items) : ?>
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
						<a class="product-name item-head"
						   href="<?php echo $ilink; ?>"
						   title="<?php echo $similar->title; ?>"
						   itemprop="url"><?php echo $similar->title; ?></a>
					</h5>

					<div class="item-shortdesc">
						<?php if (!empty($similar->introtext))
						{
							echo $similar->introtext;
						} ?>
					</div>
					<?php echo JLayoutHelper::render('catalogue.item.price', $similar); ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
</div>
