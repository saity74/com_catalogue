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
 * View class for a list of items.
 *
 * Class holding methods for displaying presentation data.
 *
 * @since  12.2
 */
class CatalogueViewAggregion extends JViewLegacy
{
    protected $agg_user;

    protected $items;

    protected $state;

    protected $canDo;

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
        CatalogueHelper::addSubmenu('aggregion');

        $this->agg_user = $this->get('AggregionUser');
        $this->canDo = JHelperContent::getActions('com_catalogue', 'item');
        $this->items = [];

        foreach(['fields', 'licence_packages', 'categories'] as $item)
        {
            $this->items[$item] = (object) [
                'item'  => $item,
                'title' => 'COM_CATALOGUE_AGGREGION_' . strtoupper($item) . '_BUTTON_TITLE',
                'link'  => "index.php?option=com_catalogue&task=aggregion.mapping&mapping=${item}",
            ];
        }

        $this->items['fields']->icon = 'grid';
        $this->items['licence_packages']->icon = 'book';
        $this->items['categories']->icon = 'list-2';

        if ( ! $this->agg_user )
        {
            JFactory::getApplication()
                ->enqueueMessage(JText::_('COM_CATALOGUE_AGGREGION_NO_CONFIG'), 'error');
        }

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        $this->sidebar = JHtmlSidebar::render();
        if ($this->_layout === 'default') {
            $this->addToolbar();
        } else {

            switch($this->_layout) {
                case 'fields':
                    $title = 'COM_CATALOGUE_AGGREGION_FIELDS_TITLE';
                    $icon = 'grid';

                    $this->items = $this->get('Fields');
                    break;

                case 'licence_packages':
                    $title = 'COM_CATALOGUE_AGGREGION_LICENCE_PACKAGES_TITLE';
                    $icon = 'book';
                    break;

                case 'categories':
                    $title = 'COM_CATALOGUE_AGGREGION_CATEGORIES_TITLE';
                    $icon = 'list-2';
                    break;
                default:
                    $title = '';
                    $icon = '';
            }

            $this->addEditButtons($title, $icon);
        }

        $doc = JFactory::getDocument();

        parent::display($tpl);
    }


    public function addEditButtons($title, $icon)
    {
        // Built the actions for new and existing records.
        $canDo = $this->canDo;

        JToolbarHelper::title(JText::_($title), $icon);

        JFactory::getApplication()->input->set('hidemainmenu', true);

        // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId))
        {
            JToolbarHelper::apply('aggregion.apply');
            JToolbarHelper::save('aggregion.save');
        }


        JToolbarHelper::cancel('aggregion.cancel', 'JTOOLBAR_CLOSE');
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        $user       = JFactory::getUser();
        $userId     = $user->get('id');

        JToolbarHelper::title(JText::_('COM_CATALOGUE_AGGREGION_TITLE'), 'link');

        // Get the toolbar object instance
        if ( $this->agg_user ) {
            $bar = JToolBar::getInstance('toolbar');

            $btn_layout = new JLayoutFile('toolbar.button', $basePath = JPATH_ROOT .'/components/com_catalogue/layouts');

            foreach ($this->items as $i => $item)
            {
                $bhtml = $btn_layout->render($item);
                $bar->appendButton('Custom', $bhtml, $item->item);
            }
        }

        if ($user->authorise('core.admin', 'com_catalogue') || $user->authorise('core.options', 'com_catalogue'))
        {
            JToolbarHelper::preferences('com_catalogue');
        }

    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return [];
    }

}
