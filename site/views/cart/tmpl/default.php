<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$params = $this->state->get('params');

// TODO: Remove this

//$data = unserialize('a:2:{i:0;a:10:{s:13:"grade_average";s:6:"7.6000";s:13:"grade_quality";s:1:"7";s:21:"grade_professionalism";s:1:"8";s:10:"grade_cost";s:1:"7";s:14:"grade_schedule";s:1:"8";s:18:"grade_connectivity";s:1:"8";s:14:"review_comment";s:0:"";s:11:"review_time";s:19:"2015/05/07 16:42:14";s:4:"from";a:9:{s:3:"url";s:53:"https://freelancehunt.com/profile/show/Eruntales.html";s:7:"url_api";s:48:"https://api.freelancehunt.com/profiles/Eruntales";s:10:"profile_id";s:6:"102429";s:5:"login";s:9:"Eruntales";s:5:"fname";s:0:"";s:5:"sname";s:0:"";s:6:"avatar";s:63:"http://content.freelancehunt.com/profile/photo/50/Eruntales.png";s:6:"rating";s:3:"268";s:15:"rating_position";s:3:"854";}s:7:"project";a:12:{s:10:"project_id";s:5:"63377";s:4:"name";s:0:"";s:11:"description";s:0:"";s:16:"description_html";s:0:"";s:3:"url";s:99:"https://freelancehunt.com/project/novaya-verstkaplussverstat-dlya-mobilnogo-planshetnogo/63377.html";s:7:"url_api";s:44:"https://api.freelancehunt.com/projects/63377";s:19:"is_personal_project";i:0;s:15:"is_safe_project";i:0;s:11:"status_name";s:0:"";s:13:"budget_amount";s:4:"3000";s:20:"budget_currency_code";s:3:"UAH";s:15:"currency_symbol";s:0:"";}}i:1;a:5:{s:13:"grade_average";N;s:14:"review_comment";s:0:"";s:11:"review_time";s:19:"2014/10/31 22:22:02";s:4:"from";a:5:{s:13:"grade_average";N;s:14:"review_comment";s:0:"";s:11:"review_time";s:19:"2014/10/31 22:22:02";s:4:"from";a:5:{s:13:"grade_average";N;s:14:"review_comment";s:0:"";s:11:"review_time";s:19:"2014/10/31 22:22:02";s:4:"from";N;s:7:"project";a:12:{s:10:"project_id";s:5:"37072";s:4:"name";s:0:"";s:11:"description";s:0:"";s:16:"description_html";s:0:"";s:3:"url";s:82:"https://freelancehunt.com/project/nuzhno-dorabotat-sayt-oplata-po-faktu/37072.html";s:7:"url_api";s:44:"https://api.freelancehunt.com/projects/37072";s:19:"is_personal_project";i:0;s:15:"is_safe_project";i:0;s:11:"status_name";s:0:"";s:13:"budget_amount";s:4:"5000";s:20:"budget_currency_code";s:3:"RUB";s:15:"currency_symbol";s:0:"";}}s:7:"project";a:12:{s:10:"project_id";s:5:"37072";s:4:"name";s:0:"";s:11:"description";s:0:"";s:16:"description_html";s:0:"";s:3:"url";s:82:"https://freelancehunt.com/project/nuzhno-dorabotat-sayt-oplata-po-faktu/37072.html";s:7:"url_api";s:44:"https://api.freelancehunt.com/projects/37072";s:19:"is_personal_project";i:0;s:15:"is_safe_project";i:0;s:11:"status_name";s:0:"";s:13:"budget_amount";s:4:"5000";s:20:"budget_currency_code";s:3:"RUB";s:15:"currency_symbol";s:0:"";}}s:7:"project";a:12:{s:10:"project_id";s:5:"37072";s:4:"name";s:70:"Нужно доработать сайт! Оплата по факту";s:11:"description";s:0:"";s:16:"description_html";s:0:"";s:3:"url";s:82:"https://freelancehunt.com/project/nuzhno-dorabotat-sayt-oplata-po-faktu/37072.html";s:7:"url_api";s:44:"https://api.freelancehunt.com/projects/37072";s:19:"is_personal_project";i:0;s:15:"is_safe_project";i:0;s:11:"status_name";s:0:"";s:13:"budget_amount";s:4:"5000";s:20:"budget_currency_code";s:3:"RUB";s:15:"currency_symbol";s:8:" руб.";}}}');
//
//$reviews = null;
//
//function traverseStructure(&$array, $recursive) {
//	while($recursive)
//	{
//		@list($key, $value) = each($array);
//		yield $value;
//		$recursive = ($key !== null);
//		$array = $value;
//	}
//};

//$user_format = 'd.m.Y H:i';
//$dates = [
//	['rewiew_date' => '2015/05/07 16:42:14'],
//	['rewiew_date' => '2014/10/31 22:22:02']
//];
//
//foreach( $dates as $val )
//{
//	$val['rewiew_date'] = JFactory::getDate($val['rewiew_date'])->format('d.m.Y H:i');
//}
//
//var_dump($dates);

//
//$date_arr = $p->get('date_arr');
//foreach($date_arr as &$date)
//{
//	$date = (string) JFactory::getDate($date)->format($user_format, true);
//}
//$p->set('date_arr', $date_arr);
//
//var_dump($p); die();
//
//echo JFactory::getDate('2015-05-07T16:42:14+03:00')->format('d.m.Y H:i');
//echo '<br/>';
//echo date_format(date_create('2015-05-07T16:42:14+03:00', $tz), "d.m.Y H:i");

?>

<div class="catalogue-cart-default">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<?php if ($total = CatalogueCart::getInstance()->getProperty('total', 0)) : ?>
		<div class="catalogue-cart-default-items">
			<div class="cart-head">
				<div class="row">
					<div class="col-lg-push-1 col-lg-10 col-lg-pull-1 col-md-push-1 col-md-10 col-md-pull-1 col-sm-push-1 col-sm-10 col-sm-pull-1">
						<div class="row">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
								<?php echo JText::_('COM_CATALOGUE_CART_HEAD_PHOTO'); ?>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
								<?php echo JText::_('COM_CATALOGUE_CART_HEAD_TITLE'); ?>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
								<?php echo JText::_('COM_CATALOGUE_CART_HEAD_COUNT'); ?>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">
								<?php echo JText::_('COM_CATALOGUE_CART_HEAD_PRICE'); ?>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
								<?php echo JText::_('COM_CATALOGUE_CART_HEAD_DELETE'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-push-1 col-lg-10 col-lg-pull-1 col-md-push-1 col-md-10 col-md-pull-1 col-sm-push-1 col-sm-10 col-sm-pull-1">
					<?php echo $this->form->getInput('items') ?>
				</div>
			</div>
		</div>

		<div class="cart-info">
			<div class="row">
				<div class="col-lg-push-6 col-lg-6 col-md-push-6 col-md-6 col-md-push-6 col-sm-push-1 col-sm-10 col-sm-pull-1">
					<span class="cart-count">
						<?php echo JText::plural('COM_CATALOGUE_CART_N_ITEMS_IN', $total); ?>
					</span>
					<span class="amount">
						<?php echo CatalogueCart::getInstance()->getProperty('amount', 0); ?>
						<?php echo $params->get('catalogue_currency', 'руб.'); ?>
					</span>
				</div>
			</div>
		</div>

		<div class="cart-buttons">
			<div class="row">
				<div class="col-lg-push-6 col-lg-6 col-md-push-6 col-md-6 col-md-push-6 col-sm-push-1 col-sm-10 col-sm-pull-1">
					<form action="<?php echo JRoute::_(CatalogueHelperRoute::getOrderRoute()); ?>" method="post">
						<button class="order-button">
							<?php echo JText::_('COM_CATALOGUE_CART_BTN_CHECKOUT') ?>
						</button>
						<input type="hidden" name="task" value="order.checkout" />
						<input type="hidden" name="option" value="com_catalogue" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
				</div>
			</div>
		</div>
	<?php else : ?>
		<p><?php echo JText::_('COM_CATALOGUE_CART_EMPTY'); ?></p>
	<?php endif; ?>
</div>
