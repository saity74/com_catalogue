<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/** @noinspection PhpIncludeInspection */

$config = JFactory::getConfig();
$params = $this->state->get('params');

$currency = $params->get('catalogue_currency', 'руб.');

$img_width = $params->get('img_width', 300);
$img_height = $params->get('img_height', 300);

$viewName = $this->getName();
$layout = $this->getLayout();

$prefix = "catalogue-$viewName-$layout";

?>

<div class="<?php echo $prefix; ?>" >
	<section itemscope itemtype="http://schema.org/Product">
		<form action="<?php JRoute::_('index.php') ?>" method="post">
			<div class="left-block">
				<?php echo JLayoutHelper::render('catalogue.item.image', $this->item); ?>
			</div>
			<div class="right-block">
				<!-- Product title -->
				<?php if($this->item->params->get('show_title', 1)) : ?>
					<div class="item-header">
						<?php echo JLayoutHelper::render('catalogue.item.title', $this->item); ?>
					</div>
				<?php endif; ?>
				<!-- ..end Product title -->
				<div class="item-order-wrapper">
					<?php echo JLayoutHelper::render('catalogue.item.price', $this->item); ?>
					<?php echo JLayoutHelper::render('catalogue.item.counter', $this->item); ?>
					<?php echo JLayoutHelper::render('catalogue.item.button', $this->item); ?>
				</div>
				<div class="item-top-desc" itemprop="description">
					<?php echo JLayoutHelper::render('catalogue.item.description', $this->item); ?>
				</div>
			</div>
			<input type="hidden" name="jform[items][id][]" value="<?php echo $this->item->id; ?>" />
			<input type="hidden" name="task" value="cart.save" />
			<input type="hidden" name="return" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</section>

	<section>
		<?php if ($params->get('show_similar_items', 1)) : ?>
			<h3><?php echo JText::_('COM_CATALOGUE_ITEM_SIMILAR_HEAD'); ?></h3>
			<?php echo JLayoutHelper::render('catalogue.item.similar', $item); ?>
		<?php endif; ?>
	</section>
</div>

<?php echo $this->item->event->afterDisplayContent;
