<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$params = JComponentHelper::getParams('com_catalogue');
$img_width = $params->get('img_width', 326);
$img_height = $params->get('img_height', 326);

$items = $displayData;
$images = [];
foreach($items as $item)
{
	if ($item->images)
	{
		$imagesObj = new \Joomla\Registry\Registry;
		$imagesObj->loadString($item->images);
		$oneImage = $imagesObj->toObject()->{0};

		$name = basename($oneImage->name);
		$path = implode(DIRECTORY_SEPARATOR, ['images', $item->id, $name]);
		$path = JPath::clean($path);
		$oneImage->src = CatalogueHelper::createThumb($item->id, $path, $img_width, $img_height, 'mid');
		$oneImage->thumb = CatalogueHelper::createThumb($item->id, $path, 140, 140, 'thumb');
		$oneImage->attrs = explode(',', $oneImage->attrs);
		$oneImage->info = getimagesize(JPATH_SITE . DIRECTORY_SEPARATOR . $path);

		$images[] = $oneImage;
	}
}
?>
<?php if (!empty($images)) : ?>
	<div class="catalogue-item-img gallery <?php echo ($item->item_sale) ? 'discount-label' : '' ?>">
		<div itemscope itemtype="http://schema.org/ImageGallery">

			<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
				<a
					href="<?php echo $images[0]->name; ?>"
					data-size="<?php echo $images[0]->info[0] . 'x' . $images[0]->info[1] ?>"
					itemprop="contentUrl">
					<img
						id="item-image"
						src="<?php echo $images[0]->src; ?>"
						width="<?php echo $img_width ?>"
						height="<?php echo $img_height ?>" />
				</a>
			</figure>

			<?php foreach($images as $k => $image) : ?>
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

	<ul class="catalogue-item-img-list">
		<?php foreach($images as $k => $image) : ?>
			<li>
				<a href="<?php echo $image->src ?>"
				   data-orig="<?php echo $image->name ?>"
				   data-attrs="['<?php echo implode('\',\'', $image->attrs);	?>']"
					<?php if($k == 0 ) : ?> class="active" <?php endif;?>
				>
					<img src="<?php echo $image->thumb; ?>"/>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif;