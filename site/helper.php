<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 20012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once('thumbnail.php');

/**
 * Class CatalogueHelper
 *
 * @since  1.5
 */
class CatalogueHelper
{

	/**
	 * Method to get items by array of ID's
	 *
	 * @param   array  $ids  Array of ID's
	 *
	 * @return  mixed
	 */
	public static function getItemsByIds($ids)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__catalogue_items');
		$query->where('id IN (' . implode(',', $ids) . ')');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;
	}

	/**
	 * Method to get items by ID
	 *
	 * @param   int  $id  Catalogue item ID
	 *
	 * @return  mixed
	 */
	public static function getItemById($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__catalogue_item');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$item = $db->loadObject();

		return $item;
	}

	/**
	 * Create image thumb function
	 *
	 * @param   int     $id      Catalogue item ID
	 * @param   string  $image   Image relative path
	 * @param   int     $width   Width of image
	 * @param   int     $height  Height if image
	 * @param   string  $suffix  Additional suffix for image filename
	 *
	 * @return  bool|string
	 */
	public static function createThumb($id, $image, $width, $height, $suffix = 'min')
	{
		$resized_folder = 'resized';

		// Check for the existence of source image
		$abs_source_img = JPATH_BASE . DS . $image;
		if (!file_exists($abs_source_img))
		{
			return false;
		}

		$images_dir = dirname($image);

		$abs_images_dir = JPATH_BASE . DS . $images_dir;

		$abs_new_img_dir = $abs_images_dir . DS . $resized_folder . DS . $id . DS . $suffix;

		if (!file_exists($abs_images_dir . DS . $resized_folder))
		{
			mkdir($abs_images_dir . DS . $resized_folder, 0777);
		}

		if (!file_exists($abs_images_dir . DS . $resized_folder . DS . $id))
		{
			mkdir($abs_images_dir . DS . $resized_folder . DS . $id, 0777);
		}

		if (!file_exists($abs_new_img_dir))
		{
			mkdir($abs_new_img_dir, 0777);
		}

		$sizeOptions = array(
			'width' => $width,
			'height' => $height,
			'method' => THUMBNAIL_METHOD_SCALE_MIN,
		);

		$p = str_replace('.jpg', '-' . $suffix . '.jpg', $abs_new_img_dir . DS . basename($image));

		if (!file_exists($p))
		{
			$thumb = new Thumbnail;

			$resizeImage = $thumb->render($image, $sizeOptions);
			@imageJpeg($resizeImage, $p, 90);
			@imagedestroy($resizeImage);
		}

		return $images_dir . DS . $resized_folder . DS . $id . DS . $suffix . DS . str_replace('.jpg', '-' . $suffix . '.jpg', basename($image));
	}

}
