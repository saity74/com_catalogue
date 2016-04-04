<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$viewName   = $this->getName();
$layout     = $this->getLayout();
$prefix     = "catalogue-$viewName-$layout-items";

$params     = $this->state->get('params');

$addprice   = $params->get('addprice', 0);
$slice_desc = $params->get('slice_desc', 0);
$slice_len  = $params->get('short_desc_len', 150);
$img_width  = $params->get('img_width', 220);
$img_height = $params->get('img_height', 155);

$item_show_description = $params->get('item_show_description', 0);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$num_columns = $params->get('num_columns', 3);

$items = array_chunk($this->items, $num_columns);

?>

<div class="<?php echo $prefix ?>">

	<?php if (!empty($this->category->description)): ?>
		<div class="category-desc-wrapper">
			<?php echo $this->category->description; ?>
		</div>
	<?php endif; ?>

	<?php foreach ($items as $i => $row) : ?>
		<div class="row">
			<?php foreach ($row as $j => $item) : ?>
				<?php
					$bootstrapSize = round(12 / $num_columns);
					$itemClass = "col-lg-$bootstrapSize col-md-$bootstrapSize col-sm-6 col-xs-12";
					$item->link = JRoute::_(CatalogueHelperRoute::getItemRoute($item->id, $item->catid));
				?>
				<div class="<?php echo $itemClass ?>">
					<div class="<?php echo $prefix . '-one'; ?>" itemscope="" itemtype="http://schema.org/Product">

						<div class="<?php echo $prefix . '-one-img'; ?>">
							<?php echo JLayoutHelper::render('catalogue.category.item.image', $item); ?>
						</div>

						<?php if ($this->params->get('show_short_desc', 0)) : ?>
							<div class="<?php echo $prefix . '-one-desc'; ?>" itemprop="description">
								<?php echo JLayoutHelper::render('catalogue.category.item.description', $item); ?>
							</div>
						<?php endif; ?>

						<?php echo JLayoutHelper::render('catalogue.category.item.name', $item); ?>

						<div class="<?php echo $prefix . '-one-price'; ?>">
							<?php echo JLayoutHelper::render('catalogue.category.item.price', $item); ?>
						</div>

						<?php echo JLayoutHelper::render('catalogue.category.item.button', $item); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>

	<div class="clearfix"></div>
	<?php if ($this->pagination->getPagesLinks()): ?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
