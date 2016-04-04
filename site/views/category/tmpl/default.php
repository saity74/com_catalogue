<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('behavior.caption');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

$app        = JFactory::getApplication();
$jinput     = $app->input;
$view       = $this->getName();
$layout     = $this->getLayout();
$menu       = $this->menu;
$category   = $this->category;

jimport('joomla.application.module.helper');
$modules    = JModuleHelper::getModules('catalogue-left');
$params     = array('style' => 'xhtml');

$prefix = "catalogue-$view-$layout";
?>
<div class="<?php echo $prefix ?>">
	<?php
		if ($category->params->get('show_page_heading'))
		{
			echo JLayoutHelper::render('catalogue.page.title', $category);
		}
	?>

	<?php if ($modules) : ?>
		<div class="col-lg-3 col-md-3 col-sm-4 xs-hidden">
			<div class="sidebar">
				<?php
					foreach ($modules as $module)
					{
						echo JModuleHelper::renderModule($module, $params);
					}
				?>
			</div>
		</div>
		<div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
	<?php endif; ?>

	<?php

		echo JLayoutHelper::render('catalogue.category.title', $category);

		if ($category->params->get('show_subcategories_list', 0))
		{
			echo $this->loadTemplate('subcategories');
		}
		// TODO: Add check category description length TEXT
		if ($category->params->get('show_description', 0) && $category->description)
		{
			echo JLayoutHelper::render('catalogue.category.description', $category);
		}
	?>

	<div class="catalogue-category-default-filter">
		<form
			action="<?php echo JRoute::_(CatalogueHelperRoute::getCategoryRoute($this->state->get('category.id'))); ?>"
			method="post" id="catalogueForm" class="catalogue-category-default-filter-form">

			<?php echo JLayoutHelper::render('catalogue.filters.bar', array('view' => $this)); ?>

			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<input type="hidden" name="option" value="com_catalogue"/>
		</form>
	</div>

	<?php if (!empty($this->items)): ?>
		<?php echo $this->loadTemplate('items'); ?>
	<?php else: ?>

	<?php endif; ?>

	<?php if ($modules) : ?>
		</div>
	<?php endif; ?>
</div>

