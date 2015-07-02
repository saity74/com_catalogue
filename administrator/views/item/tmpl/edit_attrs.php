<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
?>
<div class="row-fluid form-horizontal-desktop tabs-left">
	<?php

	echo JHtml::_('bootstrap.startTabSet', 'attrsTab', array('active' => 'tab_' . $this->item->attrdirs{0}->attrdir_id));
	echo JHtml::_('bootstrap.addTab', 'attrsTab', 'tab_' . $this->item->attrdirs{0}->attrdir_id, $this->item->attrdirs{0}->dir_name);
	echo '<table class="table table-stripped tablesorter attr-table"><thead><tr>' .
		'<th>Название фильтра</th>' .
		'<th>Значение</th>' .
		'<th>Цена</th>' .
		'<th>Изображение</th>' .
		'</tr></thead><tbody>';

	$current_dir = $this->item->attrdirs{0}->attrdir_id;

	foreach ($this->item->attrdirs as $attr_dir)
	{


		if ($current_dir != $attr_dir->attrdir_id)
		{
			$current_dir = $attr_dir->attrdir_id;

			echo '</tbody></table>';
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.addTab', 'attrsTab', 'tab_' . $current_dir, $attr_dir->dir_name);
			echo '<table class="table table-stripped tablesorter attr-table"><thead><tr>' .
				'<th>Название фильтра</th>' .
				'<th>Значение</th>' .
				'<th>Цена</th>' .
				'<th>Изображение</th>' .
				'</tr></thead><tbody>';
		}

		echo '<tr class="roww" id="row_' . $attr_dir->attr_id . '">

					<input type="hidden" name="jform[params][attr_id][]" value="attr_' . $attr_dir->attr_id . '"/>

					<td>' . $attr_dir->attr_name . '</td>';

		switch ($attr_dir->attr_type)
		{
			case 'integer' :
				echo '<td><input type="input" class="inputbox attr_value" name="jform[params][attr][' .
					$attr_dir->attr_id . ']" value="' . $attr_dir->attr_value . '"/></td>';
				break;
			case 'string' :
				echo '<td><input type="input" class="inputbox attr_value" name="jform[params][attr][' .
				$attr_dir->attr_id . ']" value="' .
				$attr_dir->attr_value ? $attr_dir->attr_value : $attr_dir->attr_default . '"/></td>';
				break;
			case 'bool' :
				echo '<td><input type="checkbox" class="inputbox attr_value" name="jform[params][attr][' .
					$attr_dir->attr_id . ']" value="' . ($attr_dir->attr_value ? 1 : -1) . '" ' .
					($attr_dir->attr_value == 1 ? 'checked="checked"' : '') . '/></td>';
				break;
		}

		$this->form->setValue('attr_image', null, $attr_dir->attr_image);
		$this->form->setFieldAttribute('attr_image', 'id', 'attr_image_' . $attr_dir->attr_id);

		echo '<td><input type="input" class="inputbox" name="jform[params][attr_price][]" value="' . $attr_dir->attr_price . '"/></td>
					<td>' . $this->form->getInput('attr_image') . '</td>

				</tr>';


	}

	echo '</tbody></table>';
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.endTabSet');

	?>
</div>
