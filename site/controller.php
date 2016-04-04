<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once(dirname(__FILE__) . DS . 'helper.php');

/**
 * Class CatalogueController
 *
 * @since  Joomla 1.5
 */
class CatalogueController extends JControllerLegacy
{

	public function __construct(array $config)
	{

		$this->input = JFactory::getApplication('site')->input;

		parent::__construct($config);
	}

	/**
	 * Method to display a search view.
	 *
	 * @return  JController		This object to support chaining.
	 *
	 * @since   1.5
	 */

	public function search()
	{
		$sphinx_search_model = $this->getModel('Search', 'CatalogueModel');
		$result = $sphinx_search_model->getItems();

		$app = JFactory::getApplication('site');
		$jinput = $app->input;
		$category_id = $jinput->get('cid');

		$ids = JArrayHelper::getColumn($result, 'id');

		$app->setUserState('com_catalogue.category.' . $category_id . '.filter.sphinx_ids', $ids);

		/*
		 * $result = array('result' => array('total' => count($ids)));
		 * echo json_encode($result);
		 * $app->close();
		 */

		$this->display();
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 *
	 * @since   1.5
	 */

	public function display($cachable = false, $urlparams = array())
	{

		$vName = $this->input->get('view');

		if ($vName == 'order' && CatalogueCart::$isEmpty)
		{
			$this->setRedirect(JRoute::_(CatalogueHelperRoute::getCartRoute()), false);
		}

		parent::display();

		return $this;
	}
}
