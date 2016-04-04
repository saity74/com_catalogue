<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var mixed $displayData */

/** @var array $images */
$images = $displayData;

?>

<ul class="catalogue-item-img-list">
	<?php foreach($images->toObject() as $k => $image) : ?>
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
