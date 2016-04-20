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
 * CatalogueViewAttrDir View
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewAttrDir extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	protected $agg_fields;

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
		$agg_model = JModelLegacy::getInstance('Aggregion', 'CatalogueModel');
		$this->agg_fields = $agg_model->getitems();

		// Initialiase variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();
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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		// Since we don't track these assets at the item level, use the manufacturer id.
		JToolbarHelper::title($isNew ? JText::_('COM_CATALOGUE_MANAGER_ATTRDIR_NEW') : JText::_('COM_CATALOGUE_MANAGER_ATTRDIR_EDIT'));

		// If not checked out, can save the item.
		JToolbarHelper::apply('attrdir.apply');
		JToolbarHelper::save('attrdir.save');

		JToolbarHelper::save2new('attrdir.save2new');

		// If an existing item, can save to a copy.
		if (!$isNew)
		{
			JToolbarHelper::save2copy('attrdir.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('attrdir.cancel');
		}
		else
		{
			JToolbarHelper::cancel('attrdir.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();
	}
}
