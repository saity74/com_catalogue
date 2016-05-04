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

<a class="btn btn-small btn-success" href="<?php echo JRoute::_('index.php?option=com_catalogue&view=aggregion'); ?>">
	<span class="icon-arrow-down-4"></span>
	<?php echo JText::_('COM_CATALOGUE_AGGREGION_SYNC'); ?>
</a>
