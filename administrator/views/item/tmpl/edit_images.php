<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
?>
<h3>Основное изображение</h3>

<div>


	<div class="span2">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('item_image'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('item_image'); ?>
			</div>


		</div>
	</div>
	<div class="span3">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('item_image_desc'); ?>
			</div>

			<div class="controls">
				<?php echo $this->form->getInput('item_image_desc'); ?>
			</div>


		</div>
	</div>
</div>

<div class="clearfix"></div>


<h3>Дополнительные изображения</h3>

<?php if ($this->item->image_data)
{
	foreach ($this->item->image_data as $k => $img) : ?>

		<div class="control-group">
			<?php
			$this->form->setValue('item_image_data', null, $img['src']);
			$this->form->setFieldAttribute('item_image_data', 'mediaDesc', $img['desc']);

			$this->form->setFieldAttribute('item_image_data', 'id', 'attr_image_' . $k);
			echo $this->form->getInput('item_image_data');
			?>
		</div>
	<?php endforeach;
} ?>


<div class="control-group">
	<?php
	$this->form->setValue('item_image_data', null, '');
	$this->form->setFieldAttribute('item_image_data', 'mediaDesc', '');
	$this->form->setFieldAttribute('item_image_data', 'id', 'attr_image_' . count($this->item->image_data));
	echo $this->form->getInput('item_image_data');
	?>
</div>
