<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @see    JFormFieldCheckbox
 * @since  11.1
 */
class JFormFieldOrderItems extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'OrderItems';


	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.2
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'forceMultiple':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->checkedOptions = (string) $this->element['checked'];
			$this->label = (string) $this->getLabel();
		}

		return $return;
	}

	public function getOptions()
	{
		JModelLegacy::addIncludePath(JPATH_SITE. '/components/com_catalogue/models');

		/** @var CatalogueModelCart $cartModel */
		$cartModel = JModelLegacy::getInstance('Cart', 'CatalogueModel', ['ignore_request' => true]);
		$cartModel->setState('list.select', 'i.id, i.id AS value, i.title AS text, i.catid, i.price, 0 AS checked, i.images');
		$options = $cartModel->getItems();

		return $options;
	}

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$params = JComponentHelper::getParams('com_catalogue');
		JLayoutHelper::$defaultBasePath =  JPATH_SITE . '/components/com_catalogue/layouts';

		$html = array();

		// Initialize some field attributes.
		$class          = !empty($this->class) ? ' class="checkboxes ' . $this->class . '"' : ' class="checkboxes"';
		$checkedOptions = explode(',', (string) $this->checkedOptions);
		$required       = $this->required ? ' required aria-required="true"' : '';
		$autofocus      = $this->autofocus ? ' autofocus' : '';
		$label          = $this->autofocus ? ' autofocus' : '';


		// Get the field options.
		$options = $this->getOptions();

		if ($this->label)
			$html[] = '<div class="control-label">' . JText::_($this->label) . '</div>';
		// Build the checkbox field output.
		$html[] = '<ul>';

		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

			$html[] = '<li class="catalogue-order-default-items-one">';
			// start .row
			$html[] = '<div class="row">';

			$html[] = '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">';
			$html[] = JLayoutHelper::render('catalogue.order.thumb', $option);
			$html[] = '</div>';

			$html[] = '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-4">';
			$html[] = '<span class="item-name">' . $option->text . '</span>';
			$html[] = '</div>';

			$html[] = '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
			$html[] = '<span class="item-price">' .
				number_format(floatval($option->price), 2, '.', ' ') . ' ' .
				$params->get('catalogue_currency', 'руб.') . '</span>';
			$html[] = '</div>';

			// end .row
			$html[] = '</div>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		return implode($html);
	}
}
