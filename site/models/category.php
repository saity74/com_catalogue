<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * CatalogueModelCategory
 *
 * Model class for handling lists of items.
 *
 * @since  12.2
 */
class CatalogueModelCategory extends JModelList
{

	public $_context = 'com_catalogue.category';

	protected $_extension = 'com_catalogue';

	protected $_item = null;

	protected $_items = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;

	protected $_pagination = null;

	protected $_leftsibling = null;

	protected $_rightsibling = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'itm.id',
				'catid', 'itm.catid', 'category_title',
				'state', 'itm.state',
				'price', 'itm.price',
				'color',
				'hits', 'itm.hits',
				'ordering', 'itm.ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		$limit = $this->getState('list.limit');

		if ($this->_items === null && $category = $this->getCategory())
		{

			$model = JModelLegacy::getInstance('Items', 'CatalogueModel', array('ignore_request' => true));
			$model->setState('params', JComponentHelper::getParams('com_catalogue'));
			$model->setState('filter.category_id', $category->id);
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('filter.language', $this->getState('filter.language'));
			$model->setState('filter.price', $this->getState('filter.price'));
			$model->setState('list.ordering', $this->_buildItemsOrderBy());
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.filter', $this->getState('list.filter'));

			// Filter.subcategories indicates whether to include articles from subcategories in the list or blog
			$model->setState('filter.subcategories', $this->getState('filter.subcategories'));
			$model->setState('filter.max_category_levels', $this->getState('filter.max_category_levels'));

			if ($limit >= 0)
			{
				$this->_items = $model->getItems();

				if ($this->_items === false)
				{
					$this->setError($model->getError());
				}
			}
			else
			{
				$this->_items = array();
			}

			$this->_pagination = $model->getPagination();
		}

		return $this->_items;
	}

	/**
	 * Method to get object of JCategory
	 *
	 * @return  JCategory
	 */
	public function getCategory()
	{
		if (!is_object($this->_item))
		{
			if (isset($this->state->params))
			{
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_items', 1) || !$params->get('show_empty_categories_cat', 0);
			}
			else
			{
				$options['countItems'] = 0;
			}

			$categories = JCategories::getInstance('Catalogue', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->_item))
			{
				$user = JFactory::getUser();
				$asset = 'com_catalogue.category.' . $this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset))
				{
					$this->_item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;

				if ($this->_item->getParent())
				{
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else
			{
				$this->_children = false;
				$this->_parent = false;
			}
		}

		return $this->_item;
	}

	/**
	 * Get the parent category.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function getParent()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function &getLeftSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function &getRightSibling()
	{
		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @return  mixed  An array of categories or false if an error occurs.
	 *
	 * @since   1.6
	 */
	public function &getChildren()
	{

		if (!is_object($this->_item))
		{
			$this->getCategory();
		}

		// Order subcategories

		if (count($this->_children))
		{
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha')
			{
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return  JPagination  A JPagination object for the data set.
	 *
	 * @since   12.2
	 */
	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			return null;
		}

		return $this->_pagination;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');
		$jinput = $app->input;

		$pk = $jinput->getInt('cid');

		$this->setState('category.id', $pk);

		$sphinx_ids = $app->getUserState('com_catalogue.category.' . $pk . '.filter.sphinx_ids', array());
		$this->setState('filter.sphinx_ids', $sphinx_ids);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();
		$menuParams = new Registry;

		if ($menu = $app->getMenu()->getActive())
		{
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);

		$user = JFactory::getUser();

		// Create a new query object.

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		if ((!$user->authorise('core.edit.state', 'com_catalogue')) && (!$user->authorise('core.edit', 'com_catalogue')))
		{
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);

			// Filter by start and end dates.
			$nullDate = $db->quote($db->getNullDate());
			$nowDate = $db->quote(JFactory::getDate()->toSQL());

			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')')
				->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}
		else
		{
			$this->setState('filter.published', array(0, 1, 2));
		}

		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				// Exclude if blacklisted
				if (in_array($name, $this->filter_fields))
				{
					$this->setState('filter.' . $name, $value);
				}
			}
		}

		// Filter.order

		$orderCol = $app->getUserStateFromRequest('com_catalogue.category.list.' . $pk . '.filter_order', 'filter_order', 'itm.price', 'string');
		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'itm.price';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_catalogue.category.list.' . $pk . '.filter_order_Dir', 'filter_order_Dir', 'ASC', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));

		$limit = $app->getUserStateFromRequest('com_catalogue.category.list.' . $pk . '.limit', 'limit', 12, 'uint');

		$this->setState('list.limit', $limit);

		// Set the depth of the category query based on parameter
		$showSubcategories = $params->get('show_subcategory_content', '0');

		if ($showSubcategories)
		{
			$this->setState('filter.max_category_levels', $params->get('show_subcategory_content', '1'));
			$this->setState('filter.subcategories', true);
		}

		$this->setState('layout', $app->input->getString('layout'));
	}

	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string   $key        The key of the user state variable.
	 * @param   string   $request    The name of the variable passed in a request.
	 * @param   string   $default    The default value for the variable if not found. Optional.
	 * @param   string   $type       Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 * @param   boolean  $resetPage  If true, the limitstart in request is set to zero
	 *
	 * @return  The request user state.
	 *
	 * @since   12.2
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $input->get($request, null, $type);

		if (($cur_state != $new_state) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null)
		{
			$app->setUserState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Build the orderby for the query
	 *
	 * @return  string	$orderby part of query
	 *
	 * @since   1.5
	 */
	protected function _buildItemsOrderBy()
	{
		$app       = JFactory::getApplication('site');
		$db        = $this->getDbo();
		$params    = $this->state->params;
		$cid    = $app->input->get('id', 0, 'int') . ':' . $app->input->get('cid', 0, 'int');
		$orderCol  = $app->getUserStateFromRequest('com_catalogue.category.list.' . $cid . '.filter_order', 'filter_order', '', 'string');
		$orderDirn = $app->getUserStateFromRequest('com_catalogue.category.list.' . $cid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$orderby   = ' ';

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = null;
		}

		if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', '')))
		{
			$orderDirn = 'ASC';
		}

		if ($orderCol && $orderDirn)
		{
			$orderby .= $db->escape($orderCol) . ' ' . $db->escape($orderDirn) . ', ';
		}

		$itemOrderby   = $params->get('orderby_sec', '');
		$categoryOrderby  = $params->def('orderby_pri', '');
		$secondary        = CatalogueHelperQuery::orderbySecondary($itemOrderby) . ', ';
		$primary          = CatalogueHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby .= $primary . ' ' . $secondary . ' itm.ordering ';

		return $orderby;
	}
}
