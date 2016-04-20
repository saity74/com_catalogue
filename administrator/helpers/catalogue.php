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
 * CatalogueHelper
 *
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @since       3.0
 */
class CatalogueHelper extends JHelperContent
{

	public static $extension = 'com_catalogue';

	protected static $_items;

	/**
	 * Method to get options for list.
	 *
	 * @param   string  $vName  Alias of menu
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_CATALOGUE'),
			'index.php?option=com_catalogue&view=catalogue',
			$vName == 'catalogue'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_catalogue',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_ATTRDIRS'),
			'index.php?option=com_catalogue&view=attrdirs',
			$vName == 'attrdirs'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_ATTRS'),
			'index.php?option=com_catalogue&view=attrs',
			$vName == 'attrs'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_CARTS'),
			'index.php?option=com_catalogue&view=carts',
			$vName == 'carts'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CATALOGUE_SUBMENU_ORDERS'),
			'index.php?option=com_catalogue&view=orders',
			$vName == 'orders'
		);
	}

	/**
	 * Method to get options for list of items.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getItemsOptions()
	{

		$app = JFactory::getApplication();
		$id = $app->getUserState('com_catalogue.edit.item.id', 0);

		if (is_array($id))
		{
			$id = $id[0];
		}

		$options = [];

		if (!empty(static::$_items) && in_array($id, static::$_items))
		{
			$options = static::$_items[$id];
		}
		else
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('id As value, title As text');
			$query->from('#__catalogue_item AS i');

			if (!empty($id) && isset($id[0]))
			{
				$query->where('id <> ' . (int) $id[0]);
			}

			$query->order('i.title');

			// Get the options.
			$db->setQuery($query);

			try
			{
				$options = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}

			static::$_items[$id] = $options;
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getAttrDirsOptions()
	{
		$options = [];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id As value, title As text');
		$query->from('#__catalogue_attrdir AS d');
		$query->order('d.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	/**
	 * Method to get options for list.
	 *
	 * @return  array
	 *
	 * @since   12.2
	 */
	public static function getAttrsOptions()
	{
		$options = [];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('CONCAT(d.id, ":", a.id) AS value, a.attr_name AS text')
			->from('#__catalogue_attr AS a')
			->join('LEFT', '#__catalogue_attrdir AS d ON d.id = a.attrdir_id')
			->where('a.state = 1 AND d.state = 1')
			->order('d.title');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		return $options;
	}

	public static function getSeoRate($item, &$errors)
	{
		$result = 0;

		if ($item->title)
		{
			$result += 2;
			$title_len = mb_strlen($item->title);
			if ($title_len >= 15 && $title_len <= 55)
			{
				$result += 5;
			} else {
				$errors[] = "Слишком короткое или длинное название товара ($title_len сим.)";
			}
		} else {
			$errors[] = 'Отсутствует название товара';
		}

		$attribs = new \Joomla\Registry\Registry($item->attribs);

		if ($attribs)
		{
			$page_title = $attribs->get('page_title', '');

			if ($page_title)
			{
				$result += 2;
				$page_title_len = mb_strlen($page_title);
				if ($page_title_len >= 15 && $page_title_len <= 100)
				{
					$result += 7;
				} else {
					$errors[] = "Слишком короткий или длинный заголовок страницы ($page_title_len сим.)";
				}
			} else {
				$errors[] = 'Не прописан заголовок страницы (= названию)';
			}
		} else {
			$errors[] = 'Отсутствуют атрибуты';
			return 0;
		}

		if ($item->metadesc)
		{
			$result += 2;
			$metadesc_len = mb_strlen($item->metadesc);
			if ($metadesc_len >= 30 && $metadesc_len <= 150)
			{
				$result += 5;
			} else {
				$errors[] = "Слишком короткое или длинное описание страницы ($metadesc_len сим.)";
			}
		} else {
			$errors[] = 'Отсутствует meta-description';
		}

		if ($item->metakey)
		{
			$result += 2;
			$metakey_len = mb_strlen($item->metakey);
			if ($metakey_len >= 30 && $metakey_len <= 250)
			{
				$result += 5;
			} else {
				$errors[] = "Слишком много или мало ключевых слов ($metakey_len сим.)";
			}
		} else {
			$errors[] = 'Отсутствуют meta-keywords';
		}

		if ($item->introtext)
		{
			// description not empty
			$result += 2;
			$introtext_len = mb_strlen(strip_tags($item->introtext));
			if ($introtext_len > 150)
			{
				$result += 5;
			} else {
				$errors[] = "Короткий вступительный текст ($introtext_len сим.)";
			}

		} else {
			$errors[] = 'Отсутствует вступительный текст';
		}

		if ($item->fulltext)
		{
			// description not empty
			$result += 2;
			$fulltext_len = mb_strlen(strip_tags($item->fulltext));
			if ($fulltext_len > 450)
			{
				$result += 5;
			} else {
				$errors[] = "Короткое основное описание товара ($fulltext_len сим.)";
			}

		} else {
			$errors[] = 'Отсутствует основное описание товара';
		}

		$images = new \Joomla\Registry\Registry($item->images);
		if (count($images) > 0)
		{
			// images not empty
			$result += 2;
			if (count($images) > 1)
			{
				$result += 2;
			} else {
				$errors[] = 'Загружено только одно изображение';
			}
		} else {
			$errors[] = 'Отсутствуют изображения';
		}

		$similar_items = new \Joomla\Registry\Registry($item->similar_items);

		if (count($similar_items) > 0)
		{
			$result += 2;
			if (count($similar_items) > 4)
			{
				$result += 2;
			} else {
				$errors[] = 'Указано мало сопутствующих товаров (<=4)';
			}
		} else {
			$errors[] = 'Отсутствуют сопутствующие товары';
		}

		$assoc_items = new \Joomla\Registry\Registry($item->assoc_items);

		if (count($assoc_items) > 0)
		{
			$result += 3;
			if (count($assoc_items) > 4)
			{
				$result += 5;
			} else {
				$errors[] = 'Указано мало похожих товаров (<=4)';
			}
		} else {
			$errors[] = 'Отсутствуют похожие товары';
		}

		if ($item->price){
			$result += 5;
		} else {
			$errors[] = 'Отсутствует цена';
		}

		return $result;
	}
}
