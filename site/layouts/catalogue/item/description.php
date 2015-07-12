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
$params = $item->params;

?>

<?php if (!$params->get('itemtext_head', '')) : ?>
	<h4><?php echo JText::_('COM_CATALOGUE_ITEM_DESC_HEAD'); ?></h4>
<?php else : ?>
	<h4><?php echo $params->get('itemtext_head') ?></h4>
<?php endif; ?>

<?php
if ($params->get('show_intro', 1))
{
	echo $item->introtext . $item->fulltext;
}
else
{
	echo $item->fulltext;
}
