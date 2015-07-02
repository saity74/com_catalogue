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
class CatalogueModelSearch extends JModelList
{
	public $_context = 'com_catalogue.search';

	protected $_extension = 'com_catalogue';

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
		/*
			$options = array('driver' => 'mysql', 'host' => '127.0.0.1:9306', 'user' => '', 'password' => '', 'database' => '', 'prefix' => '');

			try
			{
				$db = JDatabaseDriver::getInstance($options);
			}
			catch (RuntimeException $e)
			{
				if (!headers_sent())
				{
					header('HTTP/1.1 500 Internal Server Error');
				}

				jexit('Database Error: ' . $e->getMessage());
			}

			$config['dbo'] = $db;
		*/
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
		$this->setState('list.limit', 1000);

		$query = $this->_db->getQuery(true)
			->select('id')
			->from('#__catalogue_item');

		$category_id = $this->getState('filter.category_id', 0);
		if ($category_id)
		{
			$query->where('category_id = ' . $category_id);
		}

		$manufacturer_filter = $this->getState('filter.manufacturer_filter', array());
		if (!empty($manufacturer_filter))
		{
			$query->where('manufacturer_id IN (' . implode(',', $manufacturer_filter) . ')');
		}

		$params_filter = $this->getState('filter.params_filter', array());
		if (!empty($params_filter))
		{
			$query->where(implode(' AND ', $params_filter));
		}

		$price_min = $this->getState('filter.price_min', 0);
		$price_max = $this->getState('filter.price_max', 0);

		if ($price_min && $price_max)
		{
			$query->where('price BETWEEN ' . $price_min . ' AND ' . $price_max);
		}

		return $query;
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

		$category_id = $jinput->get('cid', 0);
		$this->setState('filter.category_id', $category_id);

		$jform = $jinput->post->get('jform', array(), 'array');

		$manufacturer_filter = array();
		$params_filter = array();
		$price_range = '';

		if (isset($jform['filter']) && !empty($jform['filter']))
		{
			foreach ($jform['filter'] as $key => $value)
			{
				$key_arr = explode('_', $key);
				$count = count($key_arr);

				if ($count > 2)
				{
					switch ($key_arr[1])
					{
						case 'manufacturer':
							$manufacturer_filter[] = $key_arr[2];
							break;
						case 'price':
							$price_range = $value;
							break;
						case 'listbox' :
							if ($value)
							{
								$params_filter[] = 'params.' . $value . ' = \'1\'';
							}
							break;
					}
				}
				else
				{
					// Like where params.attr_N = 1 AND params.attr_M = 1 ...
					$params_filter[] = 'params.' . $key . ' = ' . '\'' . $value . '\'';
				}
			}
		}

		$this->setState('filter.manufacturer_filter', $manufacturer_filter);
		$this->setState('filter.params_filter', $params_filter);

		if ($price_range && strpos($price_range, ';') !== false)
		{

			list($price_min, $price_max) = explode(';', $price_range, 2);
			$this->setState('filter.price_max', $price_max);
			$this->setState('filter.price_min', $price_min);
		}

		parent::populateState();
	}
}
