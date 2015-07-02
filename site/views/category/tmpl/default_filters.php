<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @noinspection PhpIncludeInspection */
require_once(JPATH_COMPONENT . '/helpers/filter.php');
?>

<?php if ($this->filters) : ?>
	<div class="catalogue-form-wrapper col-lg-3 col-md-3 col-sm-3">
		<div id="catalogue_filter_hint" class="hidden">
			<a href="#">Показать результаты</a>
		</div>
		<?php

		foreach ($this->filters as $filter)
		{
			$filter->data = CatalogueFilterHelper::getFilterData($filter);
			echo JLayoutHelper::render('catalogue.' . $filter->filter_type, $filter, JPATH_COMPONENT . 'layouts');
		}

		?>
		<div class="submit-container">
			<input type="hidden" name="task" value="search"/>
			<input type="submit" value="Найти"/>
		</div>
	</div>
<?php endif;
