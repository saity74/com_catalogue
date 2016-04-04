<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use \Joomla\Registry\Registry;

$item = $displayData;
$params = new Registry($item->params);

if (!class_exists('CatalogueHelperRoute'))
{
	require_once JPATH_SITE . '/components/com_catalogue/helpers/route.php';
	if (!is_callable('CatalogueHelperRoute::getCartRoute'))
	{
		JFactory::getApplication()->enqueueMessage('Класс CatalogueHelperRoute не найден', 'warning');
	}
}

// Params

$item_cart_btn_text 	= $params->get('item_cart_btn_text', JText::_('COM_CATALOGUE_CATEGORY_CART_BTN_TEXT_DEFAULT'));
$item_cart_btn_class 	= $params->get('item_cart_btn_class', '');
$item_cart_btn_id 		= $params->get('item_cart_btn_id', '');
$item_cart_btn_onclick 	= $params->get('item_cart_btn_onclick', '');

$cart_button_attrs = [
	'id'      => [ 'itemid-' . $item->id, $item_cart_btn_id ],
	'name'    => 'task',
	'value'   => 'cart.save',
	'class'   => [ 'order-button', $item_cart_btn_class ],
	'onclick' => $item_cart_btn_onclick
];

// Cart data

$cart = CatalogueCart::getInstance();
$count = $cart->get($item->id);

if ($count > 0)
{
	$cart_button_attrs['class'][] = 'in-cart';
	$cart_button_attrs['value'] = 'cart.edit';

	$item_cart_btn_text = '<span class="icon icon-checkmark"></span> <span class="icon icon-cart"></span>';
}

$form_action = $count ? CatalogueHelperRoute::getCartRoute() : 'index.php';

$args = [];
foreach($cart_button_attrs as $attr_key => $attr_value)
{
	if (!empty($attr_value))
	{
		if (is_array($attr_value))
		{
			$args[] = $attr_key . '="' . implode(' ', $attr_value) . '"';
		}
		else
		{
			$args[] = $attr_key . '="' . $attr_value . '"';
		}
	}
}

?>

<?php if ($params->get('item_show_cart_btn', '1') == '1') : ?>
	<form action="<?php echo JRoute::_($form_action); ?>" method="post" enctype="multipart/form-data" >
		<button <?php echo implode(' ', $args); ?> >
			<?php echo $item_cart_btn_text; ?>
		</button>
		<input type="hidden" name="jform[items][id][]" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="jform[items][count][]" value="1" />
		<input type="hidden" name="option" value="com_catalogue">
		<input type="hidden" name="return" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
<?php endif;
