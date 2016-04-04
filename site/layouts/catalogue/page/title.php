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
?>

<?php if($params->get('show_page_heading', 0)) : ?>
<div class="page-header">
	<h1> <?php echo $this->escape($params->get('page_heading')); ?> </h1>
</div>
<?php endif;