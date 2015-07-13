<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$params = $app->getParams();

$num_columns = $params->get('num_columns', 3);
$categories = array_chunk($this->category->getChildren(), $num_columns);
?>

<div class="catalogue-subcategories">
	<?php foreach ($categories as $i => $row) : ?>
		<div class="row catalogue-category-row row-<?php echo $i; ?>">
			<?php foreach ($row as $category) : ?>
				<?php $category->params = new JRegistry($category->params); ?>
				<?php $clink = CatalogueHelperRoute::getCategoryRoute($category->id, $category->language); ?>
				<?php $img = json_decode($category->params)->image; ?>
				<?php
					$bootstrapSize = round(12 / $num_columns);
					$itemClass = "col-lg-$bootstrapSize col-md-$bootstrapSize col-sm-12 col-xs-12";
				?>
				<div class="<?php echo $itemClass; ?>">
					<div class="catalogue-category">
						<a href="<?php echo $clink; ?>">
							<div class="catalogue-category-image">
								<img src="<?php echo $img; ?>"/>
							</div>
							<h3 class="catalogue-category-title">
								<?php echo $category->title; ?>
							</h3>
						</a>
						<div class="catalogue-category-desc">
							<?php echo $category->description; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
	<div class="clearfix"></div>
</div>
