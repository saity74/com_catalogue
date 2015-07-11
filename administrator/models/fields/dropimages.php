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
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @since  11.1
 */
class JFormFieldDropimages extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Dropimages';

	/**
	 * The allowable src of img field.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $src;

	/**
	 * The allowable upload_url.
	 *
	 * @var    string
	 */
	protected $upload_url;

	/**
	 * JSON with images path.
	 *
	 * @var    string
	 */
	protected $images;

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
			case 'upload_url':
			case 'images':
			case 'src':
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
			case 'upload_url':
			case 'images':
			case 'src':
				$this->$name = (string) $value;
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
			$this->src    = (string) $this->element['src'] ? (string) $this->element['src'] : '';
			$this->upload_url    = (string) $this->element['upload_url'] ? (string) $this->element['upload_url'] : '';
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Build the attributes array.
		$attributes = array();

		empty($this->src)          ? null : $attributes['src'] = $this->src;
		empty($this->class)        ? null : $attributes['class'] = $this->class;
		empty($this->upload_url)   ? null : $attributes['upload_url'] = $this->upload_url;
		empty($this->images)       ? null : $attributes['images'] = $this->images;

		// Get some system objects.
		$config = JFactory::getConfig();
		$user = JFactory::getUser();

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

		$doc = JFactory::getDocument();
		$doc->addStyleSheet('components/com_catalogue/assets/css/dropzone.css');
		$doc->addScript('components/com_catalogue/assets/js/dropzone.js');
		$doc->addScript('components/com_catalogue/assets/js/jquery-ui.min.js');
		$doc->addScript('components/com_catalogue/assets/js/init.js');

		$js[] = 'jQuery( document ).ready(function( $ ) {';
		$js[] = '	Dropzone.options.imagesContainer = {';
		$js[] = '		url: "' . $this->upload_url . '",';
		$js[] = '		previewTemplate: document.querySelector(\'#template-container\').innerHTML,';
		$js[] = '		paramName: "file",';
		$js[] = '		createImageThumbnails: "true",';
		$js[] = '		maxFilesize: 1,';
		$js[] = '		thumbnailWidth: 128,';
		$js[] = '		thumbnailHeight: 90,';
		$js[] = '		init: function() {';
		$js[] = '			var that = this;';
		$js[] = '			this.on("addedfile", function(file) {';
		$js[] = '				jQuery(file.previewElement).parent().sortable();';
		$js[] = '				console.log(file);';
		$js[] = '				file.previewElement.querySelector(".filename").value = file.name;';
		$js[] = '				file.previewElement.querySelector(".filesize").value = file.size;';
		$js[] = '				file.previewElement.querySelector(".title").value = file.title ? file.title : \'\';';
		$js[] = '				file.previewElement.querySelector(".alt").value = file.alt ? file.alt : \'\';';
		$js[] = '				file.previewElement.querySelector(".author").value = file.author ? file.author : \'\';';
		$js[] = '				file.previewElement.querySelector(".attrs").value = file.attrs ? file.attrs : \'\';';
		$js[] = '			});';
		$js[] = '			var files = JSON.parse(\'' . json_encode($this->value) . '\');';
		$js[] = '			files.each(function(file){';
		$js[] = '				that.emit("addedfile", file);';
		$js[] = '				that.emit("thumbnail", file, file.url);';
		$js[] = '				that.emit("complete", file);';
		$js[] = '			});';
		$js[] = '		}';
		$js[] = '	};';
		$js[] = '});';

		$doc->addScriptDeclaration(implode("\n", $js));

		$html = [];
		$html[] = '<ul id="imagesContainer" class="dropzone unstyled">';
		$html[] = '	<div class="dz-message">';
		$html[] = '		Перетащи файл сюда или кликни.';
		$html[] = '	</div>';
		$html[] = '</ul>';

		return implode("\n", $html);
	}
}
