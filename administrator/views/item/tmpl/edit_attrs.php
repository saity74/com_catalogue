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
	echo JHtml::_('bootstrap.startTabSet', 'attrsTab', array('active' => 'tab_' . $this->item->attrdirs[0]->attrdir_id));

	$current_dir = 0;

	foreach ($this->item->attrdirs as $attr_dir)
	{
		if ($current_dir != $attr_dir->attrdir_id)
		{

			if ($current_dir)
			{
				echo '</tbody></table>';
				echo JHtml::_('bootstrap.endTab');
			}
			echo JHtml::_('bootstrap.addTab', 'attrsTab', 'tab_' . $attr_dir->attrdir_id, $attr_dir->title);
			echo '<table class="table table-stripped tablesorter attr-table"><thead><tr>' .
				'<th style="width: 60%">Название фильтра</th>' .
				'<th style="width: 20%">Значение</th>' .
				'</tr></thead><tbody>';

			$current_dir = $attr_dir->attrdir_id;
		}

		echo '<tr class="roww" id="row_' . $attr_dir->attr_id . '"><td>' . $attr_dir->attr_name . '</td>';

		switch ($attr_dir->attr_type)
		{
			case 'integer' :
				echo '<td><input type="text" class="inputbox attr_value" name="jform[params][attr][' . $attr_dir->attr_id . ']" value="' .
					($attr_dir->attr_value ?: $attr_dir->attr_default) . '"/></td>';
				break;
			case 'string' :
				echo '<td><input type="text" class="inputbox attr_value" name="jform[params][attr][' . $attr_dir->attr_id . ']" value="' .
					($attr_dir->attr_value ?: $attr_dir->attr_default) . '"/></td>';
				break;
			case 'date' :
				echo '<td><input type="text" class="inputbox attr_value" name="jform[params][attr][' . $attr_dir->attr_id . ']" value="' .
					($attr_dir->attr_value ?: $attr_dir->attr_default) . '"/></td>';
				break;
			case 'bool' :
				echo '<td><input type="checkbox" class="inputbox attr_value" name="jform[params][attr][' . $attr_dir->attr_id . ']" value="' .
					($attr_dir->attr_value ? 1 : -1) . '" ' . ($attr_dir->attr_value ? 'checked="checked"' : '') . '/></td>';
				break;
		}
		echo '<input type="hidden" class="inputbox" name="jform[params][attr_id][' . $attr_dir->attr_id . ']" value="' . (int) $attr_dir->attr_id . '"/>';
		echo '</tr>';
	}

	echo '</tbody></table>';
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.endTabSet');

	?>
</div>
