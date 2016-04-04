<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use \Joomla\Registry\Registry;

$params = JComponentHelper::getParams('com_catalogue');
$img_width = $params->get('img_width', 500);
$img_height = $params->get('img_height', 500);

/** @var mixed $displayData */
$item = $displayData;

if ($item->images)
{
	$images = new Registry($item->images);
	foreach ($images->toObject() as $image)
	{
		$name = basename($image->name);
		$path = implode(DIRECTORY_SEPARATOR, ['images', $item->id, $name]);
		$path = JPath::clean($path);
		$image->src 	= CatalogueHelper::createThumb($item->id, $path, $img_width, $img_height, 'lg');
		$image->thumb 	= CatalogueHelper::createThumb($item->id, $path, 140, 140, 'thumb');
		$image->attrs 	= explode(',', $image->attrs);
		$image->info 	= getimagesize(JPATH_SITE . DIRECTORY_SEPARATOR . $path);
	}
}

$sliderPosition = $params->get('catalogue_item_image_slider_position', 'bottom');
$img_count = count($images);
?>

<?php if ($item->images && $images) : ?>

	<?php if($sliderPosition === 'top' && $img_count > 1) : ?>
		<?php echo JLayoutHelper::render('catalogue.item.thumbs', $images); ?>
	<?php endif; ?>

	<div class="catalogue-item-default-img gallery <?php echo ($item->item_sale) ? 'discount-label' : '' ?>">
		<div itemscope itemtype="http://schema.org/ImageGallery">
			<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
				<a
					href="<?php echo $images->get('0.name'); ?>"
					data-size="<?php echo $images->get('0.info')[0] . 'x' . $images->get('0.info')[1] ?>"
					itemprop="contentUrl">
					<img
						id="item-image"
						class="img-responsive"
						src="<?php echo $images->get('0.src'); ?>"
						width="<?php echo $img_width ?>"
						height="<?php echo $img_height ?>" />
				</a>
			</figure>
			<?php foreach($images->toObject() as $k => $image) : ?>
				<?php if ($k != 0) : ?>
					<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
						<a
							href="<?php echo $image->name; ?>"
							itemprop="contentUrl"
							data-size="<?php echo $image->info[0] . 'x' . $image->info[1]; ?>">
							<figcaption itemprop="caption description"><?php $image->title; ?></figcaption>
						</a>
					</figure>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if($sliderPosition === 'bottom' && $img_count > 1) : ?>
		<?php echo JLayoutHelper::render('catalogue.item.thumbs', $images); ?>
	<?php endif; ?>


<?php endif;