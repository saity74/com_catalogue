<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$attrs = $displayData->attrs;
$item = $displayData->item;

if ($item->images) {
	$images = new \Joomla\Registry\Registry;
	$images->loadString($item->images);

	$tree = [];
	foreach ($images->toObject() as $key => $image) {

		$name = basename($image->name);
		$path = implode(DIRECTORY_SEPARATOR, ['images', $item->id, $name]);
		$path = JPath::clean($path);
		$image->src = CatalogueHelper::createThumb($item->id, $path, 0, 0, 'mid');

		list($attr_dir, $attr) = explode(':', $image->attrs);
		if (!isset($html[$attr_dir][$attr]))
		{
			$html[$attr_dir][$attr] = '<li><a href="' . $image->src . '" data-filter="[' .
				str_replace(':', '_', $image->attrs) . ']">' . $attrs[$attr]['attr_name'] . '</a></li>';
		}
	}
}
?>

<?php foreach($html as $dir => $list) : ?>
	<ul class="item-attr-list">
		<?php echo implode("\n", $list); ?>
	</ul>
<?php endforeach;
