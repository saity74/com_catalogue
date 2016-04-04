<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>

<div class="bar">
<?php if ($filters) : ?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<div class="filter-label">
			<?php echo $field->label; ?>
		</div>
		<div class="filter-controls">
			<?php echo $field->input; ?>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
</div>
