<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of items.
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewAggregion extends JViewLegacy
{
	protected $agg_user;

	protected $items;

	protected $state;

	protected $canDo;

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
		JFactory::getDocument()
			->addStyleSheet('/administrator/components/com_catalogue/assets/css/aggregion.css')
			->addScript('/administrator/components/com_catalogue/assets/js/aggregion.js');

		CatalogueHelper::addSubmenu('aggregion');

		// TODO: check correct mapping

		$this->agg_user = $this->get('AggregionUser');
		$this->canDo = JHelperContent::getActions('com_catalogue', 'item');

		if ( ! $this->agg_user )
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CATALOGUE_AGGREGION_NO_CONFIG'), 'error');
		}

		// Check for errors.
		if ( count($errors = $this->get('Errors')) )
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		if ($this->_layout === 'default')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		$this->addToolbar();

		parent::display($tpl);
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
		$user = JFactory::getUser();

		JToolbarHelper::title(JText::_('COM_CATALOGUE_AGGREGION_TITLE'), 'link');

		// Get the toolbar object instance
		if ( $this->agg_user )
		{
			$bar = JToolBar::getInstance('toolbar');

			$btn_layout = new JLayoutFile('toolbar.button', $basePath = JPATH_ROOT . '/components/com_catalogue/layouts');

			$actions = [];

			if ($this->_layout === 'default')
			{
				foreach (['import'] as $item)
				{
					$actions[$item] = (object) [
						'item'  => $item,
						'title' => 'COM_CATALOGUE_AGGREGION_' . strtoupper($item) . '_BUTTON_TITLE',
						'link'  => "index.php?option=com_catalogue&view=aggregion&task=aggregion.$item",
					];
				}

				$actions['import']->icon = 'arrow-down-4';
			}


			foreach ($actions as $action)
			{
				$bhtml = $btn_layout->render($action);
				$bar->appendButton('Custom', $bhtml, $action->item);
			}
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
		return [];
	}
}
