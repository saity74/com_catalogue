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

// Params

$item_cart_btn_text 	= $params->get('item_cart_btn_text', JText::_('COM_CATALOGUE_CATEGORY_CART_BTN_TEXT_DEFAULT'));
$item_cart_btn_class 	= $params->get('item_cart_btn_class', '');
$item_cart_btn_id 		= $params->get('item_cart_btn_id', '');
$item_cart_btn_onclick 	= $params->get('item_cart_btn_onclick', '');

$cart_link_attrs = [
	'id'	=> [ 'itemid-' . $item->id, $item_cart_btn_id ],
	'href'	=> [ "#" ],
	'class'	=> [ 'order-button', $item_cart_btn_class ],
	'onclick' => [$item_cart_btn_onclick]
];

// Cart data

$cart = CatalogueCart::getInstance();
$count = $cart->get($item->id);

if ($count > 0)
{
	$cart_link_attrs['class'][] = 'in-cart';
	$item_cart_btn_text = '<span class="icon icon-checkmark"></span> <span class="icon icon-cart"></span>';
}

$args = [];
foreach($cart_link_attrs as $attr_key => $attr_value)
{
	if (!empty($attr_value))
	{
		$args[] = $attr_key . '="' . implode(' ', $attr_value) . '"';
	}
}

?>

<?php if ($params->get('item_show_cart_btn', '1') == '1') : ?>
	<button <?php echo implode(' ', $args); ?> >
		<?php echo $item_cart_btn_text; ?>
	</button>
<?php endif;
