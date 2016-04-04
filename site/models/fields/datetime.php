<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @see    JFormFieldList
 * @since  11.1
 */
class JFormFieldDateTime extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'DateTime';

	protected $isTomorrow;

	protected $selected;

	protected $tz;


	public function __get($name)
	{
		return parent::__get($name);
	}

	public function __set($name, $value)
	{

		switch ($name)
		{

			default:
				parent::__set($name, $value);
		}
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->tz = new DateTimeZone(JFactory::getApplication()->get('offset', 'UTC'));
		}

		return $return;
	}

	protected function getInput()
	{

		$required       = $this->required ? ' required aria-required="true"' : '';

		$options = $this->getOptions();

		$html = array();

		$html[] = '<div class="datetime-control">';

		$class = $this->class ? 'class="' . $this->class . '" ' : '';
		$name  = $this->name  ? 'name="' . $this->name  . '" ' : '';

		$html[] = '<input type="text" value="' . $this->value . '"' . $name . $class . '/>';

		$html[] = '<div class="time-control">';
		$html[] = '<a href="#" class="drop-toggle"><span class="icon icon-ctrl"></span></a>';
		$html[] = '<ul class="options">';

		foreach($options as $option)
		{
			$class = array();

			$class[] = $option->disabled ? 'disabled' : '';
			$class[] = $option->selected ? 'selected' : '';

			if (!empty($class))
				$class = 'class="' . implode($class) . '"';

			// Start list item
			$html[] = '<li ' . $class . '>';

			$start = $option->start->format('H:s', true);
			$end   = $option->end->format('H:s', true);

			if (!$option->disabled)
			{
				$html[] = '<a class="option" data-interval="' . $this->createValue($option) . '">' .
					$start . ' - ' . $end .
				'</a>';
			}
			else
			{
				$html[] = '<span class="option" data-interval="' . $this->createValue($option) . '">' .
					$start . ' - ' . $end .
					'</span>';
			}
			// End list item
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';

		$html[] = '</div>';

		if ($this->isTomorrow) {
			$html[] = '<p class="help-text"><span class="icon icon-info"></span> ' .
				JText::_('COM_CATALOGUE_DATETIME_HELP_TEXT') . '</p>';
		}

		return implode($html);
	}

	protected function getOptions()
	{
		$options   = parent::getOptions();

		if (empty($options))
		{
			throw new UnexpectedValueException(sprintf('%s no options.', get_class($this)));
		}

		$start        = explode('-', $options[0]->value);
		$startHour    = (int) strstr($start[0], ':', true);

		$end          = explode('-', $options[count($options) - 1]->value);
		$endHour      = (int) strstr($end[1], ':', true);
		$lastInterval = $endHour - (int) strstr($end[0], ':', true);

		if ($startHour > $endHour || $startHour < 0 || $startHour > 23 || $endHour < 0 || $endHour > 23)
		{
			throw new UnexpectedValueException(sprintf('%s has invalid date range.', get_class($this)));
		}

		// Get current date and time with timezone offset
		$now          = JFactory::getDate('now', $this->tz);
		// Get open shop time
		$startDate    = JFactory::getDate('now', $this->tz)->setTime($startHour, 0);
		$endDate      = JFactory::getDate('now', $this->tz)->setTime($endHour, 0);

		$beforeStart = (int) $startDate->diff($now)->format('%r%h');

		$this->isTomorrow = ($startDate->diff($endDate)->format('%r%h') <= ($beforeStart + $lastInterval));

		foreach ($options as $option)
		{
			@list($start, $end) = explode('-', $option->value);

			$option->start = JFactory::getDate('now', $this->tz)->setTime(strstr($start, ':', true), 0);
			$option->end = JFactory::getDate('now', $this->tz)->setTime(strstr($end, ':', true), 0);
			$option->disabled = $this->isTomorrow ? false : ($now->hour >= $option->start->hour);

			if (!$this->selected && !$option->disabled)
			{
				$option->selected = true;
				$this->selected = $option;
			}
		}

		$this->value = $this->createValue();

		reset($options);

		return $options;

	}

	protected function createValue($option = null)
	{

		if ($option === null)
		{
			$option = $this->selected;
		}

		if ($this->isTomorrow)
			$value = (string) JFactory::getDate('now +1 day', $this->tz)->format('d.m.Y', true);
		else
			$value = (string) JFactory::getDate('now', $this->tz)->format('d.m.Y', true);

		if ($option && isset($option->start) && isset($option->end))
			$value .= ' ' . $option->start->format('H:s', true) . '-' . $option->end->format('H:s', true);

		return $value;
	}

}