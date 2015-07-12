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
require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'cart.php';

$config = JFactory::getConfig();
$params = $this->state->get('params');

$addprice = $params->get('addprice', 0);
$currency = $params->get('catalogue_currency', 'руб.');

$img_width = $params->get('img_width', 300);
$img_height = $params->get('img_height', 300);

$item = $this->item;
$app = JFactory::getApplication();
$doc = JFactory::getDocument();

?>
<div class="catalogue-item-open" itemscope itemtype="http://schema.org/Product">
	<?php if($item->params->get('show_title', 1)) : ?>
		<div class="item-header">
			<div class="row">
				<div class="col-lg-8 col-md-8 col-sm-8">
					<a href="#" onclick="window.history.go(-1); return false;" class="back-btn" alt="Назад"></a>
					<?php echo JLayoutHelper::render('catalogue.item.title', $item); ?>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2">
					<?php echo JLayoutHelper::render('catalogue.item.price', $item); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<section class="open-item-top-block white-box clearfix">
		<div class="top-left-block">
			<?php echo JLayoutHelper::render('catalogue.item.image', $item); ?>
		</div>
		<div class="top-right-block">

			<div class="item-top-desc" itemprop="description">
				<h4><?php echo JText::_('COM_CATALOGUE_ITEM_DESC_HEAD'); ?></h4>
				<?php echo $item->introtext . $item->fulltext; ?>
			</div>

			<div class="attrs">
				<?php echo JLayoutHelper::render('catalogue.item.attrs', $this); ?>
			</div>

			</div>
		</div>
	</section>
</div>
