<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');

$app = JFactory::getApplication();

JFactory::getDocument()->addScriptDeclaration(
	"
		jQuery(function($) {
			window.initImport();
		});
	"
);

?>
<div id="j-main-container">
	<h1 class="aggimport-progress-title"><?php echo JText::_('COM_CATALOGUE_AGGREGION_IMPORT_HEADER_INIT'); ?></h1>

	<p id="aggimport-progress-message">
		<?php echo JText::_('COM_CATALOGUE_AGGREGION_IMPORT_MESSAGE_INIT'); ?>
	</p>

	<div id="progress" class="progress progress-striped active">
		<div id="progress-bar" class="bar bar-success" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<div class="agg-import-status">
		<span class="status-label"><?php echo JText::_('COM_CATALOGUE_AGGREGION_IMPORT_ITEMS_COUNT_LABEL'); ?></span>
		<span class="status-value" id="itemsCount">
			0
		</span>
	</div>

	<div class="agg-import-items">
		<span class="status-label"><?php echo JText::_('COM_CATALOGUE_AGGREGION_IMPORT_ITEMS'); ?></span>
		<span class="status-value" id="items">

		</span>
	</div>

	<form id="finishImportForm" action="<?php echo JRoute::_('index.php?option=com_catalogue&view=aggregion&task=aggregion.finishImport'); ?>"
		  method="post">
		<input id="aggimport-token" type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1" />
	</form>
</div>
