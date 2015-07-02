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
class CatalogueModelItem extends JModelList
{
	public $_context = 'com_catalogue.item';

	protected $_extension = 'com_catalogue';

	private $_items = null;

	/**
	 * Method get one item
	 *
	 * @return  mixed
	 */
	public function getItem()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->getInt('id', 0);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('itm.*, cat.title AS category_name, man.manufacturer_description');
		$query->from('#__catalogue_item AS itm');
		$query->join('LEFT', '#__categories AS cat ON cat.id = itm.category_id');
		$query->join('LEFT', '#__catalogue_manufacturer AS man ON man.id = itm.manufacturer_id');
		$query->where('itm.state = 1  && itm.published = 1 && itm.id=' . $id);

		$db->setQuery($query);
		$this->_items = $db->loadObject();

		$query = $this->_db->getQuery(true);
		$query->select('i.*')
			->from('#__catalogue_assoc as a')
			->join('LEFT', '#__catalogue_item as i ON i.id = a.assoc_id')
			->where('item_id = ' . (int) $this->_items->id)
			->order('ordering ASC');
		$this->_db->setQuery($query);

		$this->_items->assoc = $this->_db->loadObjectList();

		// Load assoc attr size..

		$ids = array_map(
			function($el){
				return $el->id;
			},
			$this->_items->assoc
		);

		$query = $this->_db->getQuery(true);
		if (!empty($ids))
		{
			$query->select('p.*, a.attr_name')
				->from('#__catalogue_attr_price as p')
				->join('LEFT', '#__catalogue_attr as a ON a.published = 1 AND a.attrdir_id = 1 AND a.id = p.attr_id')
				->where('p.item_id in (' . implode(', ', $ids) . ')')
				->order('a.ordering');

			$this->_db->setQuery($query);

			$attrs = $this->_db->loadObjectList();

			foreach ($attrs as $attr)
			{
				$item_attrs[$attr->item_id][] = $attr;
			}

			foreach ($this->_items->assoc as $item)
			{
				if (isset($item_attrs[$item->id]))
				{
					$item->sizes = $item_attrs[$item->id];
				}
			}
		}
		// ..Load assoc attr size

		// Load reviews
		$query = $this->_db->getQuery(true);
		$query->select('rv.*')
			->from('#__catalogue_item_review as rv')
			->where('rv.published = 1 AND item_id = ' . (int) $this->_items->id)
			->order('item_review_date');
		$this->_db->setQuery($query);

		$this->_items->reviews = $this->_db->loadObjectList();

		$this->_addToSimilarList($this->_items);

		// Load attr size..
		$query = $this->_db->getQuery(true);
		$query->select('p.*, a.attr_name')
			->from('#__catalogue_attr_price as p')
			->join('LEFT', '#__catalogue_attr as a ON a.published = 1 AND a.attrdir_id = 1 AND a.id = p.attr_id')
			->where('p.item_id = ' . $id)
			->order('a.ordering');

		$this->_db->setQuery($query);
		$attrs = $this->_db->loadObjectList();

		$this->_items->sizes = $attrs;

		return $this->_items;
	}

	/**
	 * Method get similar items list item
	 *
	 * @param   JTable  $item  Object of JTable
	 *
	 * @return  mixed
	 */
	private function _addToSimilarList($item)
	{
		$app = JFactory::getApplication();

		$ctn = 3;
		$id = $item->id;
		$query = $this->_db->getQuery(true);
		$query->select('itm.*');
		$query->from('#__catalogue_item AS itm');
		$query->where('itm.state = 1')
			->where('itm.published = 1', 'AND')
			->where('itm.id <> ' . $id, 'AND')
			->where('itm.category_id = ' . $item->category_id, 'AND')
			->order('itm.price DESC');
		$this->_db->setQuery($query);
		$similar = $this->_db->loadAssocList('id');

		$count_similar = count($similar);
		$similar_items = array();

		$num = 1;
		$direct = 0;
		$i = 1;
		if ($count_similar > $ctn)
		{
			while (count($similar_items) != $ctn)
			{
				$direct == 0 ? $cur = @$similar[$id - $num] : $cur = @$similar[$id + $num];
				if ($cur != null)
				{
					$similar_items[] = $cur;
				}
				$i % 2 ? $num++ : $num;
				$direct == 0 ? $direct = 1 : $direct = 0;
				$i++;
			}
		}
		else
		{
			$similar_items = $similar;
		}

		$list = serialize($similar_items);
		$app->setUserState('com_catalogue.similaritems', $list);
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

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		$id = $app->input->getUInt('id');
		$this->setState('item.id', $id);

		$db = JFactory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->select('item_name, item_description, params, metadata')
				->from('#__catalogue_item')
				->where('state = 1 AND published AND id = ' . $id)
		);

		$item = $db->loadObject();

		$this->setState('item.name', $item->item_name);
		$this->setState('item.params', $item->params);
		$this->setState('item.metadata', $item->metadata);
		$this->setState('item.desc', $item->item_description);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}
}
