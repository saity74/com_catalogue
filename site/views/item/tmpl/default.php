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
<div id="item-open">
	<div class="page-header">
		<h1 class="item-name"><?php echo $item->item_name; ?></h1>
	</div>
	<section class="open-item-top-block white-box clearfix">
		<div class="top-left-block span3">
			<div class="open-item-img <?php echo ($item->item_sale) ? 'discount-label' : '' ?> id="zoom-gallery">
				<a href="<?php echo $item->item_image; ?>" rel="lightbox-<?php echo $item->id ?>"
				   data-source="<?php echo $item->item_image; ?>"
				   title="<?php echo $item->item_image_desc; ?>">
					<img
						src="<?php echo CatalogueHelper::createThumb($item->id, $item->item_image, 300, 300, 'max'); ?>"
						width="300px" height="300px">
				</a>
			</div>
		</div>
		<div class="top-center-block span5">
			<div class="open-item-price-wrap clearfix">
				<?php if (!$item->item_sale): ?>
					<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
						<?php if ($item->price)
						{
							echo number_format($item->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
						} ?>
						<meta itemprop="priceCurrency" content="RUB">
					</p>
				<?php else: ?>
					<?php $new_price = $item->price - (($item->price / 100) * $item->item_sale); ?>
					<div class="item-price-wrapper clearfix">
						<p class="item-old-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<?php echo number_format($item->price, 0, '.', ' '); ?>
						</p>

						<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<?php echo number_format($new_price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.'); ?>
						</p>
						<meta itemprop="priceCurrency" content="RUB">
					</div>
					<div class="discount-sum-wrap">
						<p>Экономия <span
								class="bold-text"><?php echo number_format((($item->price / 100) * $item->item_sale), 0, '.', ' '); ?></span>
							руб.</p>
					</div>
				<?php endif; ?>
			</div>
			<div class="open-item-top-desc" itemprop="description">
				<?php echo $item->introtext . $item->fulltext; ?>
			</div>
			<?php if (($params->get('catalague_order_type') == 1) || ($params->get('catalague_order_type') == 3)) : ?>
			<div class="open-item-top-order">
				<?php if (!CatalogueHelperCart::inCart($item->id)): ?>
					<a href="/index.php?option=com_catalogue&task=cart.add&tmpl=raw&id=<?php echo $item->id ?>&price=<?php echo $item->price ?>"
					   item-id="<?php echo $item->id; ?>"
					   item-price="<?php echo $item->price; ?>" item-count="1" class="orange-btn add-to-card-btn"
					   id="addToCart">Положить в корзину
						<div class="loader"></div>
					</a>
				<?php else: ?>
					<a href="<?php echo JRoute::_('index.php?option=com_catalogue&view=cart&Itemid=114'); ?>"
					   class="orange-btn add-to-card-btn disable" id="addToCart">В корзине</a>
				<?php endif; ?>
				<?php endif; ?>
				<?php if (($params->get('catalague_order_type') == 2) || ($params->get('catalague_order_type') == 3)) : ?>
					<a data-toggle="modal" data-target="#orderModal-<?php echo $item->id; ?>"
					   class="orange-link one-click-order" id="fastOrder">Купить в 1 клик</a>
					<hr>
					<div class="bottom-order-info">
						<p class="order-info"><?php echo $params->get('order_info'); ?></p>

						<p class="manager-phone"><?php echo $params->get('manager_phone'); ?></p>
					</div>
					<!-- Modal -->
					<div class="modal fade" id="orderModal-<?php echo $item->id; ?>" tabindex="-1" role="dialog"
						 aria-labelledby="orderModalLabel-<?php echo $item->id; ?>" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<a href="#" class="close" data-dismiss="modal" aria-hidden="true">×</a>
								</div>
								<div class="modal-body">
									<div class="form-wrapper">
										<div class="form-bg">
											<form action="<?php echo JRoute::_('index.php?option=com_catalogue'); ?>"
												  method="POST" id="orderForm" class="mail-form form-validate">
												<div class="form-lables">
													<div class="controlls">
														<input type="text" placeholder="Ваш E-mail"
															   data-validation="email"
															   class="field required" value="" name="email"/>
													</div>
													<div class="controlls">
														<input type="text" placeholder="Ваш телефон"
															   data-validation="custom"
															   data-validation-regexp="^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$"
															   class="field required" value="" name="phone"/>
													</div>
												</div>
												<div>
													<a href="#" class="orange-btn order-btn order-send"
														><?php echo $params->get('cart_form_btn', 'Заказать') ?></a>
												</div>
												<?php echo JHtml::_('form.token'); ?>
												<input type="hidden" name="id" value="<?php echo $item->id; ?>">
												<input type="hidden" name="task" value="cart.send">
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<section class="open-item-tabs-block">
		<ul class="nav nav-tabs clearfix" role="tablist">
			<li class="active"><a href="#item-techs" role="tab" data-toggle="tab">Тех. характеристики</a></li>
			<li><a href="#similar-items" role="tab" data-toggle="tab">Сопутствующие товары</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="item-techs">
				<?php $techs = json_decode($item->techs);
				if (!empty($techs)): ?>
					<table class="table table-striped">
						<tbody>
						<?php foreach ($techs as $tech): ?>
							<tr>
								<td><?php echo $tech->name; ?></td>
								<td><?php echo $tech->value; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
			<div class="tab-pane" id="similar-items">

				<?php foreach ($item->assoc as $similar): ?>
					<?php
					$ilink = JRoute::_(CatalogueHelperRoute::getItemRoute($similar->id, $similar->category_id));
					if (!empty($similar->item_image))
					{
						$src = CatalogueHelper::createThumb($similar->id, $similar->item_image, $img_width, $img_height, 'min');
					}
					else
					{
						$src = '/templates/blank_j3/img/imgholder.png';
					} ?>
					<div class="catalogue-one-item white-box" itemscope="" itemtype="http://schema.org/Product">
						<div class="catalogue-one-item-img">
							<a href="<?php echo $ilink; ?>" title="<?php echo $similar->item_name; ?>">
								<img src="<?php echo $src ?>" title="<?php echo $similar->item_name; ?>"
									 alt="<?php echo $similar->item_name; ?>" width="<?php echo $img_width; ?>px"
									 height="<?php echo $img_height; ?>px"
									 style="width: <?php echo $img_width; ?>px;height: <?php echo $img_height; ?>px"
									 itemprop="image"/>
							</a>
						</div>
						<div class="catalogue-one-item-desc">
							<h5 itemprop="name">
								<a class="product-name" href="<?php echo $ilink; ?>"
								   title="<?php echo $similar->item_name; ?>"
								   itemprop="url"><?php echo $similar->item_name; ?></a>
							</h5>

							<p class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
								<?php if ($similar->price)
								{
									echo number_format($item->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
								} ?>
								<!-- <meta itemprop="priceCurrency" content="RUB"> -->
							</p>
							<a href="#" class="orange-btn one-click-order">Купить в 1 клик</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</div>