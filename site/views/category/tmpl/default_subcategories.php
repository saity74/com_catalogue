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
?>

<div class="catalogue-categories">
	<div class="row">
		<?php foreach ($this->category->getChildren() as $category) : ?>
			<?php $category->params = new JRegistry($category->params); ?>
			<?php $clink = CatalogueHelperRoute::getCategoryRoute($category->id, $category->language); ?>
			<?php $img = json_decode($category->params)->image; ?>
			<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
				<div class="subcategory-wrapper">
					<div class="category-image-wrapper pull-right">
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
	<div class="clearfix"></div>
</div>
