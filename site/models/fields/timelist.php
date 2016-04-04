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
class JFormFieldTimeList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'TimeList';

	protected $start = 9;

	protected $end = 20;

	protected $interval = 'PT3H';

	public function getOptions()
	{
		$options = array();



		return $options;
	}

}