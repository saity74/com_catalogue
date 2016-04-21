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
 * Aggregion items list field
 *
 * @since  1.0
 */
class JFormFieldAggitems extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Aggitems';

	/**
	 * Aggregion model
	 *
	 * @var CatalogueModelAggregion
	 */
	private $agg_model;

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   11.1
	 */
	public function __construct($form = null)
	{
		parent::__construct($form);

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_catalogue/models');

		$this->agg_model = JModelLegacy::getInstance('Aggregion', 'CatalogueModel');
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	public function getLabel()
	{
		return false;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	public function getInput()
	{
		JFactory::getDocument()
			->addStyleSheet('/administrator/components/com_catalogue/assets/css/admin-panel.css');

		$html   = '';
		$groups = $this->agg_model->getGroups();

		if ( empty($groups) )
		{
			JFactory::getApplication()
				->enqueueMessage(JText::sprintf('COM_CATALOGUE_AGGREGION_NO_GROUPS_WARNING'), 'warning');

			return '';
		}

		$groups_menu = $this->buildGroupsMenu($groups);

		// TODO: localization
		$html .= "
			<div class='row-fluid'>
				<div class='span12'>
					<div class='categories well'>
						<div class='span2 category-groups-list'>
							<span class='category-groups-header'>Пакеты</span>
							$groups_menu
						</div>
						<div class='span10 categories-fileds-container'>
							<div class='tab-content'>";

		foreach ($groups as $group)
		{
			// TODO: localization
			$html .= "			<div class='tab-pane' id='g$group->lp_id'>
									<ul class='nav nav-tabs category-content-links' role='tablist'>
										<li role='presentation'>
											<a data-toggle='tab' href='#category-fields-$group->lp_id' role='tab'>
												Custom fields
											</a>
										</li>
										<li role='presentation'>
											<a data-toggle='tab' href='#category-items-$group->lp_id' role='tab'>
												Catalogue items
											</a>
										</li>";

			if ($this->agg_model->getTags())
			{
				$html .= "				<li role='presentation'>
											<a data-toggle='tab' href='#category-tags-$group->lp_id' role='tab'>
												Tags
											</a>
										</li>";
			}

			$html .= "				</ul>";

			$fields_tab_html = $this->buildFieldsTabHtml($group->lp_id);
			$items_tab_html  = $this->buildItemsTabHtml($group->lp_id);
			$tags_tab_html   = $this->buildTagsTabHtml($group->lp_id);

			$html .= "				<div class='tab-content'>
										<div id='category-fields-$group->lp_id' class='category-fields tab-pane' role='tabpanel'>
											$fields_tab_html
										</div>
										<div id='category-items-$group->lp_id' class='category-items tab-pane' role='tabpanel'>
											$items_tab_html
										</div>
										<div id='category-tags-$group->lp_id' class='category-tags tab-pane' role='tabpanel'>
											$tags_tab_html
										</div>
									</div>";
			$html .= "			</div>";
		}

		$html .= '			</div>
      					</div>
					</div>
				</div>
			</div>';

		return $html;
	}

	/**
	 * Create HTML for Licence packages menu
	 *
	 * @param   Array  $groups  Array with Joomla groups
	 *
	 * @return string
	 */
	private function buildGroupsMenu($groups)
	{
		$html = '<ul id="aggGroupsMenu" class="nav nav-tabs">';

		foreach ($groups as $i => $group)
		{
			// TODO: overwrite Joomla active tabs behavior
			$html .= '
				<li>
					<a href="#g' . $group->lp_id . '" data-toggle="tab">' . $group->title . '</a>
				</li>';
		}

		return $html;
	}

	/**
	 * Create HTML for Fields tab
	 *
	 * @param   string  $lp_id  Licence package ID
	 *
	 * @return string
	 */
	private function buildFieldsTabHtml($lp_id)
	{
		$html = '';

		$fields = $this->agg_model->getFields();

		foreach ($fields as $field)
		{
			$label = $field->key;
			$html .= "<div class='category-fields-block'>
						<h4> $label </h4>";

			foreach ($field->values as $value)
			{
				$checkbox = (object) [
					'label' => $value,
					'value' => $value
				];
				$html .= $this->buildCheckboxHtml('fields', $checkbox, $lp_id);
			}

			$html .= "</div>";
		}

		return $html;
	}

	/**
	 * Create HTML for Items tab
	 *
	 * @param   string  $lp_id  Licence package ID
	 *
	 * @return string
	 */
	private function buildItemsTabHtml($lp_id)
	{
		$html = '';

		foreach ( $this->agg_model->getItems() as $item )
		{
			if ( $item->licensePackage === $lp_id)
			{
				$checkbox = (object) [
					'label' => $item->catalog->title->default,
					'value' => $item->id
				];

				$html .= $this->buildCheckboxHtml('items', $checkbox, $lp_id);
			}
		}

		return $html;
	}

	/**
	 * Create HTML for Tags tab
	 *
	 * @param   string  $lp_id  Licence package ID
	 *
	 * @return string
	 */
	private function buildTagsTabHtml($lp_id)
	{
		$html = '';

		$tags = $this->agg_model->getTags();

		foreach ($tags as $tag)
		{
			$checkbox = (object) [
				'label' => $tag,
				'value' => $tag
			];

			$html .= $this->buildCheckboxHtml('tags', $checkbox, $lp_id);
		}

		return $html;
	}

	/**
	 * Create HTML for one checkbox
	 *
	 * @param   string  $type   Type of checkbox
	 * @param   object  $item   Item with name and value
	 * @param   string  $lp_id  Licence package ID
	 *
	 * @return string
	 */
	private function buildCheckboxHtml($type, $item, $lp_id)
	{
		$html = "
			<label>
				<input  type='checkbox'
						class='checkbox'
						name='$this->name[$lp_id][$type][]'
						value='$item->value'>
				<span class='checkbox-value'> $item->label </span>
			</label>
		";

		return $html;
	}
}
