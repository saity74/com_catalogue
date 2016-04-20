<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/catalogue.php';

/**
 * JFormFieldAttrsList
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAttrsList extends JFormFieldList
{

	protected static $options = array();

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'AttrsList';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	public function getOptions()
	{

		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();

			/** @noinspection PhpUndefinedClassInspection */
			$options = CatalogueHelper::getAttrsOptions();

			static::$options[$hash] = array_merge(static::$options[$hash], $options);
		}

		return static::$options[$hash];

	}
}
