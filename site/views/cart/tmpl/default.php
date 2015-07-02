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
/** @noinspection PhpIncludeInspection */
require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'cart.php');

$app = JFactory::getApplication();

$items = CatalogueHelperCart::getCartItems();
$img_width = 130;
$img_height = 95;
$cart_items = $app->getUserState('com_catalogue.cart');
$cart_items = unserialize($cart_items);
$i = 0;
$items_count = 0;
?>
<div class="cart-items">
	<div class="page-header">
		<h1 class="catalogue-head">Корзина</h1>
	</div>
	<?php if (!empty($items)): ?>
		<form action="cart.html" method="POST" id="orderForm" class="cart-form form-validate">
			<ul class="cart-list unstyled">
				<?php foreach ($items as $item): ?>
					<li class="white-box cart-item clearfix">
						<div class="cart-item-num"><?php echo ++$i; ?></div>
						<div class="cart-item-img <?php if ($item->item_sale)
						{
							echo 'discount-label';
						} ?>"
							 id="zoom-gallery<?php echo $item->id; ?>">
							<a href="<?php echo $item->item_image; ?>" data-source="<?php echo $item->item_image; ?>"
							   title="<?php echo $item->item_image_desc; ?>">
								<img
									src="<?php echo CatalogueHelper::createThumb($item->id, $item->item_image, $img_width, $img_height, 'cart'); ?>"
									width="<?php echo $img_width; ?>" height="<?php echo $img_height; ?>">
							</a>
						</div>
						<div class="cart-item-desc-wrap">
							<div class="item-name">
								<h4><?php echo $item->item_name; ?></h4>
							</div>
							<div class="cart-item-desc">
								<?php echo $item->item_shortdesc; ?>
							</div>
						</div>
						<div class="cart-item-count">
							<p class="cart-item-head">
								Кол-во:
							</p>
							<select class="count-item-list" name="" item-id="<?php echo $item->id; ?>">
								<?php foreach (range(1, 15) as $count) : ?>
									<?php $selected = ($cart_items[$item->id]['count'] == $count) ? 'selected' : '' ?>
									<option
										value="<?php echo $count ?>" <?php echo $selected ?>><?php echo $count; ?></option>
								<?php endforeach; ?>
							</select>
							<?php $items_count = $items_count + $cart_items[$item->id]['count']; ?>
						</div>
						<div class="cart-item-price">
							<p class="cart-item-head">
								Стоимость:
							</p>

							<p class="item-price"><?php echo number_format($item->price, 0, '.', ' ') . ' руб.'; ?></p>
						</div>
						<div class="cart-item-remove-wrap">
							<a href="/index.php?option=com_catalogue&task=cart.remove&tmpl=raw&id=<?php echo $item->id ?>"
							   item-id="<?php echo $item->id; ?>" title="Удалить товар из корзины"
							   class="close-icon removeItem">x</a>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="total-sum-wrap" id="totalSum">
				<p>Итого: <span class="bold-text"
								id="cartTotalItems"><?php echo $items_count . ' ' . CatalogueHelperCart::item_form($items_count); ?></span>
					на сумму <span class="bold-text" id="cartTotalSum"></span></p>
			</div>

			<div class="form-wrapper white-box cart-form" id="cartForm">
				<div class="form-header">
					<h4>Оформить заказ</h4>
				</div>
				<div class="form-lables">
					<div class="controlls">
						<input type="text" placeholder="Ваше имя" class="field" value="" name="name"/>
					</div>
					<div class="controlls">
						<input type="text" placeholder="Ваш телефон" data-validation="custom"
							   data-validation-regexp="^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$"
							   class="field required" value="" name="phone"/>
					</div>
					<div class="controlls">
						<input type="text" placeholder="Ваш E-mail" data-validation="email" class="field required"
							   value="" name="email"/>
					</div>
					<div class="controlls">
                        <textarea 
				title="Подробный адрес доставки" 
				placeholder="Подробный адрес доставки" 
				class="field" 
				name="address"></textarea>
					</div>
					<div class="controlls">
                        <textarea 
				title="Пожелания по доставке" 
				placeholder="Пожелания по доставке" 
				class="field"
				name="desc"></textarea>
					</div>
				</div>
				<div>
					<a href="#" class="orange-btn order-btn order-send">Оформить заказ</a>

					<div class="secure-wrap">
						Ваши данные останутся конфиденциальными
					</div>
				</div>
				<?php echo JHtml::_('form.token'); ?>
				<input type="hidden" name="task" value="cart.order">
			</div>
		</form>
		<p class="empty-cart" style="display: none;">Корзина пуста</p>
	<?php else: ?>
		<p class="empty-cart">Корзина пуста</p>
	<?php endif; ?>
</div>
