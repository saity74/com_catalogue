<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$doc = JFactory::getDocument();

$doc->addStyleSheet('components/com_catalogue/assets/css/ion.rangeSlider.css');
$doc->addStyleSheet('components/com_catalogue/assets/css/ion.rangeSlider.skinFlat.css');
$doc->addScript('/components/com_catalogue/assets/js/ion.rangeSlider.min.js');

$app = JFactory::getApplication('Site');
$cid = $app->input->get('cid', 0);
$jform = $app->getUserState('com_catalogue.category.' . $cid . '.filter.jform');

$from = $displayData->data[0]->from_val;
$to = $displayData->data[0]->to_val;

if ($jform && isset($jform['filter']) && isset($jform['filter'][$displayData->data[0]->input_name]))
{
	list($from, $to) = explode(';', $jform['filter'][$displayData->data[0]->input_name]);
}

$script = "
jQuery(document).ready(function(){
        jQuery('#" . $displayData->data[0]->input_name . "').ionRangeSlider({
            min: " . $displayData->data[0]->from_val . ",
            max: " . $displayData->data[0]->to_val . ",
            from : '$from',
            to : '$to',
            type: 'double',
            postfix: ' р.',
            maxPostfix: '+',
            prettify: true,
            step: 10,
            hasGrid: true,
            gridMargin: 7,
            onFinish: function(obj) {
                var filter_wrapper = obj.slider.parent('.filter_wrap');
		var hint_top = filter_wrapper.position().top;
                
                window.ajaxSearch();
                
                jQuery('#catalogue_filter_hint').removeClass('hidden').css({'top' : hint_top, 'left' : '100%'});
            },
        });
    })
";
$doc->addScriptDeclaration($script);

?>
	<div class="filter-box">
		<h4 class="filter-label"><?php echo $displayData->title; ?></h4>

		<div class="filter_wrap">
			<input type="text" id="<?php echo $displayData->data[0]->input_name; ?>"
				   name="jform[filter][<?php echo $displayData->data[0]->input_name; ?>]" value=""/>
		</div>
	</div>
