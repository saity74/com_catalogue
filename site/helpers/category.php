<?php
/**
 * Catalogue Component Category Tree
 *
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Class CatalogueCategories
 *
 * @since  1.5
 */
class CatalogueCategories extends JCategories
{

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		$options['table'] = '#__catalogue_item';
		$options['extension'] = 'com_catalogue';
		$options['field'] = 'category_id';

		parent::__construct($options);
	}
}
