<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

?>

	<div class="row-fluid">
		<div class="span9">
			<?php echo $this->form->getInput('images'); ?>
		</div>
		<div class="span3">
			<div id="attrForm" class="form-vertical">
				<h4><?php echo JText::_('COM_CATALOGUE_IMAGE_ATTR_MODAL_HEAD'); ?></h4>
				<?php echo JLayoutHelper::render('edit.image', $this); ?>
			</div>
		</div>
	</div>

