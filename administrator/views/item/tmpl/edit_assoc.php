<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
?>
<div class="row-fluid">
	<div class="span12">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('item_id'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('item_id'); ?>
				<a href="#" id="assocTableAdd" class="btn btn-success">Добавить</a>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-stripped tablesorter" id="assocTable">
			<thead>
			<tr>
				<th>#</th>
				<th>Название</th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->item->assoc as $assoc) : ?>
				<tr class="roww" id="row_<?php echo $assoc->assoc_id; ?>">
					<td width="1%"><input
							type="checkbox" <?php if ($assoc->published): ?> checked="checked" <?php endif; ?>/></td>
					<input type="hidden" name="jform[assoc][assoc_id][]" value="<?php echo $assoc->assoc_id; ?>"/>
					<input type="hidden" name="jform[assoc][ordering][]" value="<?php echo $assoc->ordering; ?>"/>
					<input type="hidden" name="jform[assoc][published][]" value="<?php echo $assoc->published; ?>"
					       class="pub"/>
					<td><?php echo $assoc->assoc_name; ?></td>
					<td class="span2"><a class="btn btn-danger btn-small assocTableRemove">Удалить</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
