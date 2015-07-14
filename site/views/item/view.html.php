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
 * HTML Item View class for the Catalogue component
 *
 * @since  1.5
 */
class CatalogueViewItem extends JViewLegacy
{
	protected $item;

	protected $state;

	protected $attrs;

	protected $category;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   12.2
	 */
	public function display($tpl = null)
	{
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->attrs = $this->get('Attrs');

		$item = $this->item;
		$offset = $this->state->get('list.offset');

		$dispatcher = JEventDispatcher::getInstance();

		// Process the content plugins.

		JPluginHelper::importPlugin('content');
		//$dispatcher->trigger('onContentPrepare', array ('com_catalogue.item', &$item, &$item->params, $offset));

		$item->event = new stdClass;
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_catalogue.item', &$item, &$item->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_catalogue.item', &$item, &$item->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_catalogue.item', &$item, &$item->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void.
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;
		$metadata = new JRegistry($this->state->get('item.metadata'));

		// Ссылка на активный пункт меню
		$menu = $menus->getActive();

		// Проверка привязана ли категория к меню
		$cid = (int) @$menu->query['cid'];
		$this->category = JCategories::getInstance('Catalogue')->get($cid);

		if ($menu && ($menu->query['option'] != 'com_catalogue' || $menu->query['view'] == 'category' || $cid != $this->category->id))
		{
			$pathway->addItem($this->item->title, '');
		}

		if ($menu && $cid == $this->state->get('item.id'))
		{
			// Если привязана к меню то берем TITLE из меню
			$title = $menu->params->get('page_title');
		}
		else
		{
			// Если нет то берем TITLE из настрое категории (по умолчанию название категории)
			$title = $metadata->get('metatitle', $this->state->get('item.name'));
		}

		// Установка <TITLE>

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		// Устновка метаданных

		if ($metadesc = $metadata->get('metadesc', ''))
		{
			$this->document->setDescription($metadesc);
		}
		elseif (!$metadesc && $menu && $menu->params->get('menu-meta_description'))
		{
			$this->document->setDescription($menu->params->get('menu-meta_description'));
		}

		if ($metakey = $metadata->get('metakey', ''))
		{
			$this->document->setMetadata('keywords', $metakey);
		}
		elseif (!$metakey && $menu && $menu->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $menu->params->get('menu-meta_keywords'));
		}

		if ($robots = $metadata->get('robots', ''))
		{
			$this->document->setMetadata('robots', $robots);
		}
	}
}
