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
 * CatalogueTableCatalogue class
 *
 * @since  11.1
 */
class CatalogueTableCatalogue extends JTable
{
	public $id;

	public $introtext;

	public $fulltext;

	public $techs;

	public $modified;

	public $modified_by;

	public $created;

	public $created_by;

	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabaseDriver  &$_db  JDatabaseDriver object.
	 *
	 * @since   11.1
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__catalogue_item', 'id', $_db);
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $array   An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/bind
	 * @since   3.2
	 * @throws  UnexpectedValueException
	 */
	public function bind($array, $ignore = '')
	{

		if (isset($array['item_description']))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $array['item_description']);

			if ($tagPos == 0)
			{
				$this->introtext = $array['item_description'];
				$this->fulltext = '';
			}
			else
			{
				list($this->introtext, $this->fulltext) = preg_split($pattern, $array['item_description'], 2);
			}
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$tmp = array();
			foreach ($array['params']['attr'] as $key => $param)
			{
				$tmp['attr_' . $key] = $param;
			}
			$array['params'] = json_encode($tmp);
		}

		if (isset($array['attribs']) && is_array($array['attribs']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (isset($array['techs']) && is_array($array['techs']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['techs']);
			$array['techs'] = (string) $registry;
		}

		if (isset($array['similar_items']) && is_array($array['similar_items']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['similar_items']);
			$array['similar_items'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row in the database from the JTable instance properties.
	 * If a primary key value is set the row with that primary key value will be
	 * updated with the instance property values.  If no primary key value is set
	 * a new row will be inserted into the database with the properties from the
	 * JTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    https://docs.joomla.org/JTable/store
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		if ($this->techs && is_array($this->techs))
		{
			$registry = new JRegistry;
			$registry->loadArray($this->techs);
			$this->techs = (string) $registry;
		}

		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing item
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New item. A item created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		$result = parent::store($updateNulls);

		$values = array();

		$query_attr_price = 'DELETE FROM #__catalogue_attr_price WHERE item_id = ' . $this->id;
		$this->_db->setQuery($query_attr_price);
		$this->_db->execute();

		$attr_prices = array_map(array($this, '_restructDataPrice'), $jform['params']['attr_id'], $jform['params']['attr_price']);

		if (!empty($attr_prices))
		{
			$query_attr_prices = 'INSERT INTO #__catalogue_attr_price (`item_id`, `attr_id`, `attr_price`) VALUES ';

			foreach ($attr_prices as $attr_price)
			{
				if ($attr_price['attr_price'])
				{
					$values[] = '(' . $this->id . ',' . $attr_price['attr_id'] . ',' . $attr_price['attr_price'] . ')';
				}

			}

			if (is_array($values) && !empty($values))
			{
				$query_attr_prices .= implode(',', $values);
				$this->_db->setQuery($query_attr_prices);
				$this->_db->execute();
			}
		}

		// REVIEWS..
		$values = array();

		$query_reviews = 'DELETE FROM #__catalogue_item_review WHERE item_id = ' . $this->id;
		$this->_db->setQuery($query_reviews);
		$this->_db->execute();

		// $jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');
		if (!isset($jform['reviews']['published']))
		{
			$jform['reviews']['published'] = array();
		}
		if (!isset($jform['reviews']['ordering']))
		{
			$jform['reviews']['ordering'] = array();
		}

		$reviews = array_map(
			array($this, '_restructDataReview'),
			$jform['reviews']['review_date'], $jform['reviews']['review_fio'],
			$jform['reviews']['review_rate'], $jform['reviews']['review_text'],
			$jform['reviews']['ordering'], $jform['reviews']['published']
		);

		if (!empty($reviews))
		{
			$query_reviews = 'INSERT INTO #__catalogue_item_review (`id`, `item_id`, `item_review_date`, ' .
				'`item_review_fio`, `item_review_rate`, `item_review_text`, `ordering`, `published`) VALUES ';

			foreach ($reviews as $review)
			{
				$values[] = "(''," . $this->id . "," .
					escapeshellarg($review["review_date"]) . "," .
					escapeshellarg($review["review_fio"]) . "," .
					escapeshellarg($review["review_rate"]) . "," .
					escapeshellarg($review["review_text"]) . "," .
					$review["review_ordering"] . "," .
					$review["review_published"] . ")";
			}

			if (is_array($values) && !empty($values))
			{
				$query_reviews .= implode(',', $values);
				$this->_db->setQuery($query_reviews);
				$this->_db->execute();
			}
		}

		// ..REVIEWS

		return $result;
	}

	/**
	 * _restructDataParams
	 *
	 * @param   int  $attr_id     Assoc item ID
	 * @param   int  $attr_value  Attr value
	 *
	 * @return  array
	 */
	protected function _restructDataParams($attr_id, $attr_value = 0)
	{
		return array('attr_id' => (string) $attr_id, 'attr_value' => $attr_value);
	}

	/**
	 * _restructDataPrice
	 *
	 * @param   int  $attr_id     Assoc attr ID
	 * @param   int  $attr_price  Order
	 *
	 * @return  array
	 */
	protected function _restructDataPrice($attr_id, $attr_price = 0)
	{
		list($null, $id) = explode('_', $attr_id, 2);

		return array('attr_id' => (int) $id, 'attr_price' => (string) $attr_price);
	}

	/**
	 * _restructDataReview
	 *
	 * @param   string  $review_date       Assoc attr ID
	 * @param   string  $review_fio        Name
	 * @param   int     $review_rate       Review rate
	 * @param   string  $review_text       Review text
	 * @param   int     $review_ordering   Order
	 * @param   int     $review_published  Publish (0/1)
	 *
	 * @return  array
	 */
	protected function _restructDataReview($review_date, $review_fio, $review_rate, $review_text, $review_ordering = 0, $review_published = 0)
	{
		return array('review_date' => (string) $review_date,
			'review_fio' => (string) $review_fio,
			'review_rate' => (int) $review_rate,
			'review_text' => (string) $review_text,
			'review_ordering' => (int) $review_ordering,
			'review_published' => (int) $review_published
		);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$query = $this->_db->getQuery(true)
			->update($this->_db->quoteName($this->_tbl))
			->set($this->_db->quoteName('state') . ' = ' . (int) $state)
			->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);

		try
		{
			$this->_db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}

}
