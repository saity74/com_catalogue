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
 * CatalogueViewAttrDirs View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewAttrDirs extends JViewLegacy
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
		/** @noinspection PhpUndefinedClassInspection */
		CatalogueHelper::addSubmenu('attrdirs');

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

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
		$canDo = JHelperContent::getActions('com_catalogue', 'attr_dir', $this->state->get('filter.attr_dir_id'));
		$user  = JFactory::getUser();

		JToolbarHelper::title(JText::_('COM_CATALOGUE_ATTRDIRS_MANAGER_TITLE'), 'book');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('attrdir.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolbarHelper::editList('attrdir.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('attrdir.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('attrdir.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('attrdir.archive');
			JToolbarHelper::checkin('attrdir.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'attrdirs.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('attrdirs.trash');
		}

		if ($user->authorise('core.admin', 'com_catalogue') || $user->authorise('core.options', 'com_catalogue'))
		{
			JToolbarHelper::preferences('com_catalogue');
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
			'd.ordering'	=> JText::_('JGRID_HEADING_ORDERING'),
			'd.title'	=> JText::_('COM_CATALOGUE_ATTR_NAME'),
			'd.id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}
}
