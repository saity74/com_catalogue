<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/**
 * Class CatalogueViewCategory
 *
 * @since  Joomla 1.5
 */
class CatalogueViewCategory extends JViewCategory
{
	/**
	 * State data
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  3.2
	 */
	protected $state;

	/**
	 * Category items data
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $items;

	/**
	 * The category model object for this category
	 *
	 * @var    JModelCategory
	 * @since  3.2
	 */
	protected $category;

	/**
	 * The list of other categories for this extension.
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected $categories;

	/**
	 * Pagination object
	 *
	 * @var    JPagination
	 * @since  3.2
	 */
	protected $pagination;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   3.2
	 */
	public function display($tpl = null)
	{
		parent::commonCategoryDisplay();

		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->pagination = $this->get('Pagination');

		$this->filters = $this->get('Filters');

		$this->params = $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		parent::display($tpl);
	}
}
