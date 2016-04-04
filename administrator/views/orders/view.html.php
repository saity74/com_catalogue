<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('CatalogueHelper', JPATH_COMPONENT . '/helpers/catalogue.php');

/**
 * CatalogueViewCarts View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewOrders extends JViewLegacy
{

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		/** @noinspection PhpUndefinedClassInspection */
		CatalogueHelper::addSubmenu('orders');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);

		return true;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		/** @noinspection PhpIncludeInspection */
		require_once JPATH_COMPONENT . '/helpers/catalogue.php';

		$canDo = JHelperContent::getActions('com_catalogue', 'orders', $this->state->get('order.id'));

		JToolbarHelper::title(JText::_('COM_CATALOGUE_TITLE_ORDERS'), 'component.png');
		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('order.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('order.edit');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('orders.trash');
		}

	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'i.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
