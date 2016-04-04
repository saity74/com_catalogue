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
 * Model class for handling lists of items.
 *
 * @since  12.2
 */
class CatalogueModelItems extends JModelList
{
	public $_context = 'com_catalogue.items';

	protected $_extension = 'com_catalogue';

	private $_items = null;

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
				'title', 'itm.title',
				'price', 'itm.price',
				'alias', 'itm.alias',
				'state', 'itm.state',
				'ordering', 'itm.ordering'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$categoryId = $this->getState('filter.category_id');

		// $price_cat = $this->getState('filters.price_cat', 0);

		if (is_numeric($categoryId))
		{
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'itm.catid ' . $type . (int) $categoryId;

			if ($includeSubcategories)
			{
				$levels = (int) $this->getState('filter.max_category_levels', '1');

				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true)
					->select('sub.id')
					->from('#__categories as sub')
					->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt')
					->where('this.id = ' . (int) $categoryId);

				if ($levels >= 0)
				{
					$subQuery->where('sub.level <= this.level + ' . $levels);
				}

				// Add the subquery to the main query
				$query->where('(' . $categoryEquals . ' OR itm.catid IN (' . $subQuery->__toString() . '))');

			}
			else
			{
				$query->where($categoryEquals);
			}
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);

			if (!empty($categoryId))
			{
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('itm.catid ' . $type . ' (' . $categoryId . ')');
			}
		}
		$params = $this->state->params;

		$params->get('catalogue_sort') == 1 ? $ordering = 'ordering' : $ordering = 'title';

		$query->select(
			$this->getState(
				'list.select',
				'itm.*, cat.title AS category_name, cat.description AS category_description'
			)
		)
			->from('`#__catalogue_item` AS `itm`')
			->join('LEFT', '`#__categories` as `cat` ON `itm`.`catid` = `cat`.`id`')
			->where('`itm`.`state` = 1');

		$price = $this->getState('filter.price');

		if (!empty($price))
		{
			if (strpos($price, '-') !== false)
			{
				@list($min, $max) = explode('-', $price);
				$query->where($this->_db->qn('itm.price') .' BETWEEN '. $this->_db->q((int) $min) .' AND '. $this->_db->q((int) $max));
			}
			else
			{
				$query->where($this->_db->qn('itm.price') .' >= '. $this->_db->q((int) $price));
			}
		}


		$sticker = $this->getState('filter.sticker', []);
		if ($sticker && !empty($sticker) && is_array($sticker))
		{
			$query->where('( itm.sticker IN (' . implode(',', $sticker) . ') AND itm.sticker > 0 )');
		}

		$query->order($this->getState('list.ordering', 'itm.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

		return $query;
	}

	/**
	 * Method get item by id
	 *
	 * @param   int  $id  ID of item
	 *
	 * @return  mixed|null
	 */
	public function getItem($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('itm.*');
		$query->from('#__catalogue_item AS itm');
		$query->where('itm.id = ' . $id);
		$db->setQuery($query);
		$this->_items = $db->loadObject();

		return $this->_items;
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

		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$orderCol = $app->input->get('filter_order', 'itm.ordering');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'itm.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$catid = $app->input->getUInt('cid');
		$this->setState('filter.category_id', $catid);

		$sphinx_ids = $app->input->getArray('sphinx_ids');
		$this->setState('filter.sphinx_ids', $sphinx_ids);

		$id = $app->input->getUInt('id');
		$this->setState('item.id', $id);

		$db = JFactory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->select('title AS category_name, category_description')
				->from('#__categories')
				->where('state = 1 AND id = ' . $catid)
		);
		$category = $db->loadObject();

		$this->setState('category.name', $category->category_name);
		$this->setState('category.desc', $category->category_description);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}
}
