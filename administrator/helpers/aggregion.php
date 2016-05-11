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
 * AggregionHelper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @since       3.0
 */
class AggregionHelper
{
	public static $extension = 'com_catalogue';

	/**
	 * Get aggregion groups mapping
	 *
	 * @return bool|array
	 */
	public static function getAggGroups()
	{
		$result = false;

		$db = JFactory::getDbo();

		// Get the options.
		$db->setQuery('SELECT * FROM #__catalogue_agg_groups');

		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $result;
	}

	/**
	 * Get Aggregion category mapping
	 *
	 * @return bool|array
	 */
	public static function getAggCategories()
	{
		$result = false;

		$db = JFactory::getDbo();

		// Get the options.
		$db->setQuery('SELECT * FROM #__catalogue_agg_categories');

		try
		{
			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		foreach ($result as &$cat)
		{
			if ($mapping = json_decode($cat->mapping))
			{
				$cat->mapping = $mapping;
			}
		}

		return $result;
	}

	/**
	 * Build Catalogue items from all mappings
	 *
	 * @param   object  $agg_item  Aggregion item
	 *
	 * @return bool|JObject
	 */
	public static function buildCatalogueItem($agg_item)
	{
		// Get Store Views
		$groups = self::getAggGroups();

		// Get category mapping
		$categories = self::getAggCategories();

		// TODO: fields mapping

		$agg_model = JModelLegacy::getInstance('Aggregion', 'CatalogueModel');
		$item_model = JModelLegacy::getInstance('Item', 'CatalogueModel');
		$item_table = $item_model->getTable();

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
		$access_model = JModelLegacy::getInstance('Level', 'UsersModel');
		$access_table = $access_model->getTable();

		if ( $cat_id = self::getCatId($agg_item, $categories) )
		{
			$item_table->load(['sku' => $agg_item->id]);
			$item = [];

			// TODO: langs

			if ( ! is_null($item_table->id) )
			{
				$item['id'] = $item_table->id;
			}

			$item['sku'] = $agg_item->id;
			$item['title'] = $agg_item->catalog->title->default;
			$item['fulltext'] = $agg_item->catalog->description->default;
			$item['price'] = $agg_item->cost;
			$item['cover'] = $agg_model->getResource($agg_item->catalog->cover);
			$item['catid'] = $cat_id;
			$item['state'] = $agg_item->cost == 0 ? '0' : '1';

			$access_level_id = false;

			foreach ($groups as $group)
			{
				if ($agg_item->licensePackage === $group->package_id)
				{
					$access_level_id = $group->group_id;
					break;
				}
			}

			if ( $access_level_id )
			{
				if ( $access_table->load(['rules' => "[$access_level_id]"]) )
				{
					$item['access'] = $access_table->id;
				}
			}

			return $item;
		}

		return false;
	}

	/**
	 * Build Catalogue items from all mappings
	 *
	 * @param   array  $groups      Groups mapping
	 * @param   array  $categories  Categories mapping
	 * @param   array  $fields      Fields
	 *
	 * @return bool|array
	 */
	public static function buildCatalogueItems($groups, $categories, $fields)
	{
		$items = [];

		$agg_model = JModelLegacy::getInstance('Aggregion', 'CatalogueModel');
		$item_model = JModelLegacy::getInstance('Item', 'CatalogueModel');

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
		$access_model = JModelLegacy::getInstance('Level', 'UsersModel');
		$access_table = $access_model->getTable();

		foreach ($agg_model->getItems() as $agg_item)
		{
			if ($cat_id = self::getCatId($agg_item, $categories))
			{
				$item = $item_model->getItem();

				// TODO: langs
				$item->title = $agg_item->catalog->title->default;
				$item->fulltext = $agg_item->catalog->description->default;

				$item->price = $agg_item->cost;

				$item->cover = $agg_model->getResource($agg_item->catalog->cover);

				$item->catid = $cat_id;

				$access_level_id = false;

				foreach ($groups as $group)
				{
					if ($agg_item->licensePackage === $group->package_id)
					{
						$access_level_id = $group->group_id;
						break;
					}
				}

				if ( $access_level_id )
				{
					if ( $access_table->load(['rules' => "[$access_level_id]"]) )
					{
						$item->access = $access_table->id;
					}
				}

				$items[] = $item;
			}
		}

		return $items;
	}

	/**
	 * Determine if this item have category id otherwise return false
	 *
	 * @param   object  $agg_item    Aggregion item
	 * @param   array   $categories  Categories mapping
	 *
	 * @return bool|int
	 */
	public static function getCatId($agg_item, $categories)
	{
		$item_options = $agg_item->catalog->options;

		foreach ($categories as $category)
		{
			// Find the category id
			if ( isset($category->mapping->{$agg_item->licensePackage}) )
			{
				$mapping = $category->mapping->{$agg_item->licensePackage};

				if ( isset($mapping->fields) )
				{
					// If some of the item options matches mapping of this category
					foreach ($mapping->fields as $label => $values)
					{
						if ( isset ($item_options->{$label}) && in_array($item_options->{$label}, $values) )
						{
							return $category->cat_id;
						}
					}
				}

				// If item id matches category items mapping
				if ( isset($mapping->items)
					&& in_array($agg_item->id, $mapping->items) )
				{
					return $category->cat_id;
				}

				// If some of the item tags matches category tabs mapping
				if ( isset($mapping->tags)
					&& ! empty(array_intersect($mapping->tags, $agg_item->catalog->tags)) )
				{
					return $category->cat_id;
				}
			}
		}

		return false;
	}

	/**
	 * Load item cover
	 *
	 * @param   array   &$item  Catalogue item
	 * @param   object  $cover  Aggregion image object
	 *
	 * @return void
	 */
	public static function getImages(&$item, $cover)
	{
		$image_folder = JPATH_SITE . '/images/' . $item['id'];

		if ( ! is_dir($image_folder) )
		{
			mkdir($image_folder, 0777, true);
		}

		file_put_contents($image_folder . '/' . JFile::makeSafe($cover->name), $cover->resource);

		$item['images'] = [];
		$item['images']['name']		= [JFile::makeSafe($cover->name)];
		$item['images']['size']		= [$cover->size];
		$item['images']['alt']		= [''];
		$item['images']['author']	= [$cover->owner];
		$item['images']['title']	= [$cover->name];
		$item['images']['attrs']	= [''];
	}
}
