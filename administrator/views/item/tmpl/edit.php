<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2015 Saity74 LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$this->hiddenFieldsets = array();
$this->hiddenFieldsets[0] = 'basic-limited';
$this->configFieldsets = array();
$this->configFieldsets[0] = 'editorConfig';

// Create shortcut to parameters.
$params = $this->state->get('params');

$app = JFactory::getApplication();
$input = $app->input;
$assoc = JLanguageAssociations::isEnabled();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$params = json_decode($params);
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
	$params->show_publishing_options = '1';
	$params->show_item_options = '1';
	$params->show_urls_images_backend = '0';
	$params->show_urls_images_frontend = '0';
}

// Check if the item uses configuration settings besides global. If so, use them.
if (isset($this->item->attribs['show_publishing_options']) && $this->item->attribs['show_publishing_options'] != '')
{
	$params->show_publishing_options = $this->item->attribs['show_publishing_options'];
}

if (isset($this->item->attribs['show_item_options']) && $this->item->attribs['show_item_options'] != '')
{
	$params->show_item_options = $this->item->attribs['show_item_options'];
}

if (isset($this->item->attribs['show_urls_images_frontend']) && $this->item->attribs['show_urls_images_frontend'] != '')
{
	$params->show_urls_images_frontend = $this->item->attribs['show_urls_images_frontend'];
}

if (isset($this->item->attribs['show_urls_images_backend']) && $this->item->attribs['show_urls_images_backend'] != '')
{
	$params->show_urls_images_backend = $this->item->attribs['show_urls_images_backend'];
}

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
    Joomla.submitbutton = function(task)
    {
        if (task == "item.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
        {
            ' . $this->form->getField('itemtext')->save() . '
            Joomla.submitform(task, document.getElementById("item-form"));
        }
    };
');

$doc->addScript('components/com_catalogue/assets/js/tablednd.js');
$doc->addScriptDeclaration('jQuery(function ($) { $("#techsTable").tableDnD() });');

?>

<form action="<?php echo JRoute::_('index.php?option=com_catalogue&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="item-form" class="form-validate">
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_CATALOGUE_ITEM_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<fieldset class="adminform">
					<?php echo $this->form->getInput('itemtext'); ?>
				</fieldset>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('edit.global', $this);?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php // Do not show the publishing options if the edit form is configured not to. ?>
		<?php if ($params->show_publishing_options == 1) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_CONTENT_FIELDSET_PUBLISHING', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</div>
				<div class="span6">
					<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'images', JText::_('COM_CATALOGUE_IMAGES', true)); ?>
		<?php echo $this->loadTemplate('images'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if ($assoc) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
			<?php echo $this->loadTemplate('associations'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php $this->show_options = $params->show_item_options; ?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php if ($this->canDo->get('core.admin')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_CONTENT_FIELDSET_RULES', true)); ?>
			<?php echo $this->form->getInput('rules'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'techs', JText::_('COM_CATALOGUE_ITEMTECHS', true)); ?>
		<?php echo $this->loadTemplate('techs'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'assoc', JText::_('COM_CATALOGUE_ASSOC', true)); ?>
		<?php echo $this->loadTemplate('assoc'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'reviews', JText::_('COM_CATALOGUE_REVIEWS', true)); ?>
		<?php echo $this->loadTemplate('reviews'); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attrs', JText::_('COM_CATALOGUE_ATTRS', true)); ?>
		<?php if($this->item->attrdirs) : ?>
			<?php echo $this->loadTemplate('attrs'); ?>
		<?php else : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php endif; ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php

// Load Dropzone template outside of the AdminForm
echo $this->loadTemplate('dropzone');
