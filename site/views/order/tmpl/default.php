<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$params = $this->state->get('params');

?>

<div class="catalogue-order-default">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php') ?>" class="order-form" id="orderForm" method="post" >
		<div class="row">
			<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
				<div class="catalogue-order-default-form">

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('delivery_type') ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('delivery_type') ?>
						</div>

						<div class="delivery-info">
							<div class="control-label">
								<?php echo $this->form->getLabel('delivery_address') ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('delivery_address') ?>

								<?php echo $this->form->getInput('no_delivery_address') ?>
								<?php echo $this->form->getLabel('no_delivery_address') ?>
							</div>
						</div>

						<div class="delivery-point">
							<div class="control-label">
								<?php echo $this->form->getLabel('delivery_address') ?>
							</div>
							<p class="address"><?php echo JText::_('COM_CATALOGUE_DELIVERY_POINT_ADDRESS'); ?></p>
							<p class="address-desc"><?php echo JText::_('COM_CATALOGUE_DELIVERY_POINT_ADDRESS_DESC'); ?></p>
						</div>

					</div>

					<div class="control-group">
						<div class="control-label">
							<?php echo $this->form->getLabel('delivery_period') ?>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('delivery_period') ?>
						</div>
						<?php echo $this->form->getInput('delivery_datetime') ?>
					</div>

					<?php echo $this->form->renderField('payment_method') ?>

					<div class="control-group client-info">
						<div class="control-label">
							<?php echo JText::_('COM_CATALOGUE_CLIENT_INFO_LABEL'); ?>
						</div>
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4 cols-xs-12">
								<div class="controls">
									<?php echo $this->form->getInput('client_name') ?>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 cols-xs-12">
								<div class="controls">
									<?php echo $this->form->getInput('client_mail') ?>
								</div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4 cols-xs-12">
								<div class="controls">
									<?php echo $this->form->getInput('client_phone') ?>
								</div>
							</div>
						</div>
						<div class="controls">
							<?php echo $this->form->getInput('client_subscribe'); ?>
							<?php echo $this->form->getLabel('client_subscribe'); ?>
						</div>
					</div>

					<input type="hidden" name="option" value="com_catalogue" />


				</div>
			</div>
			<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
				<div class="catalogue-order-default-items">
					<?php echo $this->form->getInput('items') ?>
					<div class="catalogue-order-default-items-summary">
						<span class="order-count">
								<?php echo JText::plural('COM_CATALOGUE_CART_N_ITEMS_IN', CatalogueCart::$total); ?>
							</span>
						<span class="order-amount">
							<?php echo CatalogueCart::$amount; ?>
							<?php echo $params->get('catalogue_currency', 'руб.'); ?>
						</span>
					</div>
				</div>
			</div>
		</div>

		<button class="payment-button">
			<?php echo JText::_('COM_CATALOGUE_CART_BTN_PAYMENT') ?>
		</button>
	</form>
</div>
