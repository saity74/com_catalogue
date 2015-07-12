<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
?>
<div class="row-fluid form-horizontal-desktop">
	<table id="techsTable" class="table table-stripped span12">
		<tr>
			<th>Показать в кратком</th>
			<th>Характеристика</th>
			<th>Значение</th>
			<th><a href="#" id="techsTableAdd" class="btn btn-success btn-small">Добавить</a></th>
		</tr>
		<?php foreach ($this->item->techs as $tech) : ?>
			<tr>
				<td width="1%">
					<input title=""
					       type="checkbox" <?php if ($tech['show_short']): ?> checked="checked" <?php endif; ?>/>
					<input type="hidden" name="jform[techs][show_short][]"
					       value="<?php echo $tech['show_short']; ?>" class="pub"/>
				</td>
				<td class="span2">
					<input title="" type="text" class="inputBox required" name="jform[techs][name][]"
					       value="<?php echo $tech['name'] ?>"/>
				</td>
				<td class="span1">
					<input title="" type="text" class="inputBox required" name="jform[techs][value][]"
					       value="<?php echo $tech['value'] ?>"/>
				</td>
				<td class="span2"><a href="#" class="techsTableRemove btn btn-danger btn-small">Удалить</a></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
