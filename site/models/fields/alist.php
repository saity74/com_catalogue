<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

JFormHelper::loadFieldClass('list');

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @see    JFormFieldAList
 * @since  11.1
 */
class JFormFieldAList extends JFormFieldList
{

	protected function getInput()
	{
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= !empty($this->name) ? ' name="' . $this->name  . '" ' : '';
		$attr .= !empty($this->hint) ? 'placeholder="' . JText::_($this->hint)  . '" ' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$attr .= ' readonly="readonly"';
		}

		if ((string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		$options = $this->getOptions();

		$html = [];
		$html[] = '<div class="alternative-select">';

		$html[] = '<input type="text" value="' . $this->value . '"' . trim($attr) .  '/>';

		$html[] = '<div class="alternative-select-inner">';
		$html[] = '<a href="#" class="list-toggle"><span class="icon icon-ctrl"></span></a>';
		$html[] = '<ul class="list-options">';

		foreach($options as $option)
		{
			$optionClass = [];
			$optionClass[] = $option->disabled ? 'disabled' : '';
			$optionClass[] = ($this->default == $option->value) ? 'selected' : '';

			// Start list item
			$html[] = '<li ' . (!empty($optionClass) ? implode($optionClass) : '')  . '>';

			if (!$option->disabled)
			{
				$html[] = '<a class="option" data-value="' . $option->value . '">' . $option->text . '</a>';
			}
			else
			{
				$html[] = '<span class="option">' . $option->text  . '</span>';
			}
			// End list item
			$html[] = '</li>';
		}
		// ..end alternative-select-list-options
		$html[] = '</ul>';
		// ..end alternative-select-list
		$html[] = '</div>';
		// ..end alternative-select
		$html[] = '</div>';

		return implode($html);
	}
}