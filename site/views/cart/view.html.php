<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class CatalogueViewCart
 *
 * @since  Joomla 1.5
 */
class CatalogueViewCart extends JViewLegacy
{

	public $item;

	public $items;

	public $form;

	protected $state;

	protected $params;

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
		$app = JFactory::getApplication('site');

		$this->item = $this->get('Item');
		$this->items = $this->get('Items');
		$this->state = $this->get('State');
		$this->form = $this->get('Form');

		$this->params = $this->state->get('params');
		$active       = $app->getMenu()->getActive();

		// Check to see which parameters should take priority
		if ($active)
		{
			$this->params->merge($active->params);
		}

		parent::display($tpl);
	}

}
