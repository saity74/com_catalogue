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
?>

<div class="page-header">
    <h<?php echo $category_title_tag_size ?>>
        <?php echo $category->title; ?>
    </h<?php echo $category_title_tag_size ?>>
</div>
