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
	<div class="span12">
		<div class="control-group">
			<div class="control-label">
				ФИО
			</div>
			<div class="controls">
				<input id="review_input_fio" type="text" name="jform[_reviews_][review_fio][]" value=""/>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				Рейтинг
			</div>
			<div class="controls">
				<input id="review_input_rate" type="text" name="jform[_reviews_][review_rate][]" value=""/>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				Дата
			</div>
			<div class="controls">
				<!-- <input class="review_input_date" type="text" name="jform[_reviews_][review_date][]" value=""/> -->
				<?php echo JHTML::_('calendar',
					$value = '' . date('Y-m-d') . '',
					$name = 'jform[_reviews_][review_date][]',
					$id = 'review_input_date',
					$format = '%Y-%m-%d',
					$attribs = array('size' => '8', 'maxlength' => '10','class' => ' validate[\'required\']')
				); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				Текст
			</div>
			<div class="controls">
				<textarea id="review_input_text" name="jform[_reviews_][review_text][]"></textarea>
				<a href="#" id="reviewTableAdd" class="btn btn-success">Добавить</a>
			</div>
		</div>
	</div>

	<table class="table table-stripped tablesorter" id="reviewsTable">
		<thead>
		<tr>
			<th>#</th>
			<th>ФИО</th>
			<th>Рейтинг</th>
			<th>Дата</th>
			<th>Текст</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->item->reviews as $review) : ?>
			<tr class="review_roww" id="review_row_<?php echo $review->id; ?>">
				<td width="1%"><input
						type="checkbox" <?php if ($review->published): ?> checked="checked" <?php endif; ?>/></td>
				<input type="hidden" name="jform[reviews][ordering][]" value="<?php echo $review->ordering; ?>"/>
				<input type="hidden" name="jform[reviews][published][]" value="<?php echo $review->published; ?>"
				       class="pub"/>
				<td><input type="text" name="jform[reviews][review_fio][]"
				           value="<?php echo $review->item_review_fio; ?>"/></td>
				<td><input type="text" name="jform[reviews][review_rate][]"
				           value="<?php echo $review->item_review_rate; ?>"/></td>
				<td><input type="text" name="jform[reviews][review_date][]"
				           value="<?php echo $review->item_review_date; ?>"/></td>
				<td><textarea name="jform[reviews][review_text][]"
				              value="<?php echo $review->item_review_text; ?>"><?php echo $review->item_review_text; ?></textarea>
				</td>
				<td class="span2"><a class="btn btn-danger btn-small reviewTableRemove">Удалить</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
