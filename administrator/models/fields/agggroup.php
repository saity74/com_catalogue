<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * JFormFieldAttrsList
 * Supports a generic list of options.
 *
 * @since  11.1
 */
class JFormFieldAgggroup extends JFormFieldList
{

	protected static $options = [];

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Agggroup';

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

			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_catalogue/models/');
			$agg_model = JModelLegacy::getInstance('Aggregion', 'CatalogueModel');
			$agg_packages = $agg_model->getPackages();

			$options = [];

			if ($agg_packages)
			{
				foreach ($agg_packages as $package)
				{
					$options[$package->type . ':' . $package->id] = $package->name;
				}
			}

			static::$options[$hash] = array_merge(static::$options[$hash], $options);
		}

		return static::$options[$hash];

	}
}
