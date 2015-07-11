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

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$app = JFactory::getApplication();

$jinput = $app->input;
$view = $this->getName();
$layout = $this->getLayout();
$params = $this->state->get('params');
$menu = $this->menu;
$category = $this->category;

jimport('joomla.application.module.helper');

$modules	= JModuleHelper::getModules('catalogue-left');
$params		= array('style' => bootstrap);

?>
<div class="catalogue-<?php echo $view ?>-<?php echo $layout ?>">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <?php
		if ($category->params->get('show_page_heading'))
		{
			echo JLayoutHelper::render('catalogue.page.title', $category);
		}
	?>
	</div>
	<div class="row">
		<?php if ($modules) : ?>
			<div class="sidebar col-lg-3 col-md-3 col-sm-4">
				<?php
					foreach ($modules as $module)
					{
						echo JModuleHelper::renderModule($module, $params);
					}
				?>
			</div>
		<?php endif; ?>

		<div class="catalogue <?php echo ($modules ? 'col-lg-9 col-md-9 col-sm-8' : '') ?>">
			<?php
				if ($category->params->get('show_category_title', 1))
				{
					echo JLayoutHelper::render('catalogue.category.title', $category);
				}

				if ($category->params->get('show_subcategories_list', 0))
				{
					echo $this->loadTemplate('subcategories');
				}

				if ($category->params->get('show_description', 0))
				{
					echo JLayoutHelper::render('catalogue.category.description', $category);
				}
			?>

			<?php if (!empty($this->items)): ?>
				<form
					action="<?php echo JRoute::_(CatalogueHelperRoute::getCategoryRoute($this->state->get('category.id'))); ?>"
					method="post" id="catalogueForm">

					<?php echo $this->loadTemplate('filters'); ?>

					<?php echo $this->loadTemplate('items'); ?>

					<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
					<input type="hidden" name="option" value="com_catalogue"/>
				</form>
			<?php else: ?>

			<?php endif; ?>
		</div>
	</div>
</div>
