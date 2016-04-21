<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JHtml::_('behavior.core');
?>

<a class="btn" href="<?php echo JRoute::_($displayData->link); ?>">
	<span class="icon-<?php echo $displayData->icon ?>"></span>
	<?php echo JText::_($displayData->title); ?>
</a>
