<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$category = $displayData;
$params = $category->params;

$category_title_tag_size = $params->get('category_title_tag_size', 2);
$category_title_after = $params->get('category_title_after', '');
$category_title_before = $params->get('category_title_before', '');

$category_title_text = $category_title_before ?
	$category_title_before . mb_strtolower($category->title) . $category_title_after :
	$category->title . $category_title_after;
?>

<div class="page-header">
    <h<?php echo $category_title_tag_size ?>>
        <?php echo $category_title_text; ?>
    </h<?php echo $category_title_tag_size ?>>
</div>
