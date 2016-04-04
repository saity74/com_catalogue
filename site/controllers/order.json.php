<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2016 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;


/**
 * Class CatalogueControllerOrder
 *
 */

class CatalogueControllerOrder extends JControllerForm
{

	public function validate()
	{
		try
		{

			$result = null;
			$element = null;

			$data   = $this->input->get('jform', array(), 'array');

			if (!empty($data))
			{
				$name = key($data);
				$value = current($data);

				$model = $this->getModel();
				/** @var JForm $form */
				$form = $model->getForm($data);

				$fields = $form->getXml()->xpath('descendant::field[@name="' . $name . '"]');

				if (!empty($fields))
				{
					$element = $fields[0];
				}

				$type = $element->attributes()->validate;

				$rule = JFormHelper::loadRuleType($type);

				if (!$rule)
				{
					throw new UnexpectedValueException(sprintf('%s::validateField() rule `%s` missing.', get_class($form), $type));
				}

				// Run the field validation rule test.
				$valid = $rule->test($element, $value);

				// Check for an error in the validation test.
				if ($valid instanceof Exception)
				{
					throw $valid;
				}

				// Check if the field is valid.
				if ($valid === false)
				{
					// Does the field have a defined error message?
					$message = (string) $element['message'];

					if ($message)
					{
						$message = JText::_($element['message']);

						throw new UnexpectedValueException($message);
					}
					else
					{
						$message = JText::_($element['label']);
						$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', $message);

						throw new UnexpectedValueException($message);
					}
				}
				echo new JResponseJson($valid);
			}

		}
		catch(Exception $e)
		{
			echo new JResponseJson($e);
		}
	}
}