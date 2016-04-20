<?php

defined('JPATH_BASE') or die;

JHtml::_('behavior.core');
?>

<a class="btn" href="<?php echo JRoute::_($displayData->link); ?>">
    <span class="icon-<?php echo $displayData->icon ?>"></span>
    <?php echo JText::_($displayData->title); ?>
</a>
