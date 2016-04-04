<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('checkboxes');

/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @see    JFormFieldCheckbox
 * @since  11.1
 */
class JFormFieldCartItems extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'CartItems';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;

	/**
	 * The comma seprated list of checked checkboxes value.
	 *
	 * @var    mixed
	 * @since  3.2
	 */
	public $checkedOptions;

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
			case 'checkedOptions':
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
			case 'checkedOptions':
				$this->checkedOptions = (string) $value;
				break;

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


		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '<ul>';

		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			if (!isset($this->value) || empty($this->value))
			{
				$checked = (in_array((string) $option->value, (array) $checkedOptions) ? ' checked' : '');
			}
			else
			{
				$value = !is_array($this->value) ? explode(',', $this->value) : $this->value;
				$checked = (in_array((string) $option->value, $value) ? ' checked' : '');
			}

			$checked = empty($checked) && $option->checked ? ' checked' : $checked;

			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$disabled = !empty($option->disable) || $this->disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<li class="catalogue-cart-default-items-one">';
			$html[] = '<form action="' . JRoute::_('index.php') . '" method="post">';
			// start .row
			$html[] = '<div class="row">';

			$html[] = '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">';
			$html[] = JLayoutHelper::render('catalogue.cart.thumb', $option);
			$html[] = '</div>';

			$html[] = '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
			$html[] = '<span class="item-name">' . $option->text . '</span>';
			$html[] = '</div>';

			$html[] = '<div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">';
			$html[] = JLayoutHelper::render('catalogue.item.counter', $option);
			$html[] = '</div>';

			$html[] = '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4">';
			$html[] = '<span class="item-price">' .
				number_format(floatval($option->price), 2, '.', ' ') . ' ' .
				$params->get('catalogue_currency', 'руб.') . '</span>';
			$html[] = '</div>';

			$html[] = '<div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">';
			$html[] = '<button name="task" value="cart.delete" class="item-delete"><span class="icon icon-delete"></span></button>';
			$html[] = '<input type="hidden" name="jform[items][id][]" value="' . $option->value . '"></input>';
			$html[] = '<input type="hidden" name="option" value="com_catalogue">';
			$html[] = '<input type="hidden" name="return" value="" />';
			$html[] = JHtml::_('form.token');
			$html[] = '</div>';
			// end .row
			$html[] = '</div>';
			$html[] = '</form>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		return implode($html);
	}
}
