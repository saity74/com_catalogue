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
 * Catalogue Component Query Helper
 *
 * @since  1.5
 */
class CatalogueHelperQuery
{
	/**
	 * Translate an order code to a field for primary category ordering.
	 *
	 * @param   string  $orderby  The ordering code.
	 *
	 * @return  string  The SQL field(s) to order by.
	 *
	 * @since   1.5
	 */
	public static function orderbyPrimary($orderby)
	{
		switch ($orderby)
		{
			case 'alpha' :
				$orderby = 'cat.path, ';
				break;

			case 'ralpha' :
				$orderby = 'cat.path DESC, ';
				break;

			case 'order' :
				$orderby = 'cat.lft, ';
				break;

			default :
				$orderby = '';
				break;
		}

		return $orderby;
	}

	/**
	 * Translate an order code to a field for secondary category ordering.
	 *
	 * @param   string  $orderby  The ordering code.
	 *
	 * @return  string  The SQL field(s) to order by.
	 *
	 * @since   1.5
	 */
	public static function orderbySecondary($orderby)
	{
		switch ($orderby)
		{
			case 'price' :
				$orderby = 'itm.price';
				break;

			case 'rprice' :
				$orderby = 'itm.price DESC';
				break;

			case 'alpha' :
				$orderby = 'itm.title';
				break;

			case 'ralpha' :
				$orderby = 'itm.title DESC';
				break;

			case 'hits' :
				$orderby = 'itm.hits DESC';
				break;

			case 'rhits' :
				$orderby = 'itm.hits';
				break;

			case 'order' :
				$orderby = 'itm.ordering';
				break;

			default :
				$orderby = 'itm.ordering';
				break;
		}

		return $orderby;
	}

	/**
	 * Get join information for the voting query.
	 *
	 * @param   \Joomla\Registry\Registry  $params  An options object for the article.
	 *
	 * @return  array  A named array with "select" and "join" keys.
	 *
	 * @since   1.5
	 */
	public static function buildVotingQuery($params = null)
	{
		if (!$params)
		{
			$params = JComponentHelper::getParams('com_catalogue');
		}

		$voting = $params->get('show_vote');

		if ($voting)
		{
			// Calculate voting count
			$select = ' , ROUND(v.rating_sum / v.rating_count) AS rating, v.rating_count';
			$join = ' LEFT JOIN #__catalogue_rating AS v ON a.id = v.item_id';
		}
		else
		{
			$select = '';
			$join = '';
		}

		$results = array ('select' => $select, 'join' => $join);

		return $results;
	}

}
