<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_catalogue
 *
 * @copyright   Copyright (C) 2012 - 2015 Saity74, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app        = JFactory::getApplication();
$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$archived   = $this->state->get('filter.published') == 2 ? true : false;
$trashed    = $this->state->get('filter.published') == -2 ? true : false;
$saveOrder  = $listOrder == 'i.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_catalogue&task=catalogue.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$assoc = JLanguageAssociations::isEnabled();
?>

<form action="<?php echo JRoute::_('index.php?option=com_catalogue&view=catalogue'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="itemList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'i.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'i.state', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" style="min-width:250px">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'i.title', $listDirn, $listOrder); ?>
						</th>
						<th class="hidden-phone" width="1%">
							<?php echo JHtml::_('grid.sort', 'COM_CATALOGUE_HEADING_INTRO', 'i.fulltext', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'COM_CATALOGUE_HEADING_MANUFACTURER', 'mf.manufacturer_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'COM_CATALOGUE_HEADING_PRICE', 'i.price', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'i.access', $listDirn, $listOrder); ?>
						</th>
					<?php if ($assoc) : ?>
						<th width="5%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_CONTENT_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
						</th>
					<?php endif;?>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort',  'JAUTHOR', 'i.created_by', $listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'i.hits', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'i.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
					$ordering   = ($listOrder == 'i.ordering');
					$canCreate  = $user->authorise('core.create',     'com_catalogue.category.' . $item->catid);
					$canEdit    = $user->authorise('core.edit',       'com_catalogue.item.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own',   'com_catalogue.item.' . $item->id) && $item->created_by == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_catalogue.item.' . $item->id) && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'catalogue.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php
								// Create dropdown items
								$action = $archived ? 'unarchive' : 'archive';
								JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'catalogue');

								$action = $trashed ? 'untrash' : 'trash';
								JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'catalogue');

								// Render dropdown list
								echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
								?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'catalogue.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($item->language == '*'):?>
									<?php $language = JText::alt('JALL', 'language'); ?>
								<?php else:?>
									<?php $language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>

								<?php endif;?>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="hasTooltip"
									   href="<?php echo JRoute::_('index.php?option=com_catalogue&task=item.edit&id=' . $item->id); ?>"
									   title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>">
										<?php echo $this->escape($item->title); ?>
									</span>
								<?php endif; ?>
								<?php
									switch ($item->sticker)
									{
										case 1:
											echo '<span class="label label-important">hot</span>';
											break;
										case 2:
											echo '<span class="label label-warning">new</span>';
											break;
										case 3:
											echo '<span class="label label-success">sale</span>';
											break;
									}
								?>
								<br/>
								<span class="small break-word">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
								</div>
							</div>
						</td>
						<td class="hidden-phone">
							<?php if ($this->escape($item->fulltext)) : ?>
								<span class="label">есть</span>
							<?php else : ?>
								<span class="label label-warning">Добавьте описание</span>
							<?php endif; ?>
						</td>
						<td class="hidden-phone">
							<?php echo $item->manufacturer_name; ?>
							<div class="small">
								<?php echo $item->country_name; ?>
							</div>
						</td>
						<td class="center hidden-phone">
							<?php echo $item->price; ?>
						</td>

						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<?php if ($assoc) : ?>
							<td class="hidden-phone">
								<?php if ($item->association) : ?>
									<?php echo JHtml::_('contentadministrator.association', $item->id); ?>
								<?php endif; ?>
							</td>
						<?php endif;?>
						<td class="small hidden-phone">
							<?php if ($item->modified_by) : ?>
								<a class="hasTooltip"
								   href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->modified_by); ?>"
								   title="<?php echo JText::_('JAUTHOR'); ?>">
									<?php echo $this->escape($item->editor_name); ?></a>
							<?php else : ?>
								<a class="hasTooltip"
								   href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>"
								   title="<?php echo JText::_('JAUTHOR'); ?>">
									<?php echo $this->escape($item->author_name); ?></a>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->hits; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php echo $this->pagination->getListFooter(); ?>

		<?php // Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>