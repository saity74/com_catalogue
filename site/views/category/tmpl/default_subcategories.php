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

<div class="catalogue-categories">
	<?php foreach ($categories as $i => $row) : ?>
		<div class="row clearfix row-<?php echo $i; ?>">
			<?php foreach ($row as $category) : ?>
				<?php $category->params = new JRegistry($category->params); ?>
				<?php $clink = CatalogueHelperRoute::getCategoryRoute($category->id, $category->language); ?>
				<?php $img = json_decode($category->params)->image; ?>
				<?php
					$bootstrapSize = round(12 / $num_columns);
					$itemClass = "col-lg-$bootstrapSize col-md-$bootstrapSize col-sm-12 col-xs-12";
				?>
				<div class="<?php echo $itemClass; ?>">
					<div class="subcategory-wrapper">
						<div class="category-image-wrapper">
							<a href="<?php echo $clink; ?>">
								<img src="<?php echo $img; ?>"/>
							</a>
						</div>
						<h3 class="category-link-wrapper">
							<a href="<?php echo $clink; ?>">
								<?php echo $category->title; ?>
							</a>
						</h3>
						<div class="category-desc-wrapper">
							<?php echo $category->description; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
	<div class="clearfix"></div>
</div>
