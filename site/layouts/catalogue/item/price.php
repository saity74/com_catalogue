<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$item = $displayData;
$params = JComponentHelper::getParams('com_catalogue');

?>

<span class="item-price" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
	<?php if ($item->price)
	{
		echo number_format($item->price, 0, '.', ' ') . ' ' . $params->get('catalogue_currency', 'руб.');
	} ?>
	<meta itemprop="priceCurrency" content="RUB">
</span>