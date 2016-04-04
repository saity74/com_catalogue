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
 * @see    JFormFieldList
 * @since  11.1
 */
class JFormFieldDateList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'DateList';

	protected $range = 14;

	protected $interval = 'P1D';

	public function getOptions()
	{
		$options = array();

		$interval = new DateInterval($this->interval);

		$today = JFactory::getDate();
		if ($today->hour > 20) {
			$today->add($interval);
		}

		for($i = 0;$i < $this->range; $i++)
		{
			$tmp = new stdClass();
			$tmp->value = (string) $today->toSql();
			$tmp->text = (string) $today->format('d.m.Y');
			$options[] = $tmp;

			$today->add($interval);
		}

		return $options;
	}

}