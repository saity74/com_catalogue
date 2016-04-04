<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$form = $displayData->getForm();

$fields = $displayData->get('fields') ?: array(
	'similar_items'
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();

$html   = array();

foreach ($fields as $field)
{
	$field = is_array($field) ? $field : array($field);

	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->renderField($f);
			break;
		}
	}
}

echo implode('', $html);
