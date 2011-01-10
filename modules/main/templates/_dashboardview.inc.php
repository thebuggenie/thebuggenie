<?php switch ($type):
		  case TBGDashboard::DASHBOARD_VIEW_PREDEFINED_SEARCH : ?>
	<?php case TBGDashboard::DASHBOARD_VIEW_SAVED_SEARCH : ?>
			<?php include_component('search/results_view',  array_merge($parameters, array('search' => true, 'default_message' => __('No issues in this list')))); ?>
	<?php break; ?>		
	
	<?php case TBGDashboard::DASHBOARD_VIEW_LOGGED_ACTION : ?>
		<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('What you\'ve done recently'); ?>
		</div>
		<div id="dashboard_<?php echo $id; ?>">
		<?php if (count($tbg_user->getLatestActions()) > 0): ?>
			<table cellpadding=0 cellspacing=0 style="margin: 5px;">
				<?php $prev_date = null; ?>
				<?php foreach ($tbg_user->getLatestActions() as $action): ?>
					<?php $date = tbg_formatTime($action['timestamp'], 5); ?>
					<?php if ($date != $prev_date): ?>
						<tr>
							<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
						</tr>
					<?php endif; ?>
					<?php include_component('main/logitem', array('log_action' => $action, 'include_project' => true, 'pad_length' => 60)); ?>
					<?php $prev_date = $date; ?>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<div class="faded_out" style="padding: 5px 5px 15px 5px;"><?php echo __("You haven't done anything recently"); ?></div>
		<?php endif; ?>
		</div>
	<?php break; ?>
	
	<?php case TBGDashboard::DASHBOARD_VIEW_LAST_COMMENTS : ?>
		<div class="rounded_box lightgrey borderless cut_bottom dashboard_view_header" style="margin-top: 5px;">
			<?php echo image_tag('collapse_small.png', array('id' => 'dashboard_'.$id.'_collapse', 'style' => 'float: left; margin: 3px 5px 0 2px;', 'onclick' => "\$('dashboard_{$id}').toggle(); this.src = (this.src == '" . image_url('collapse_small.png', false, 'core', false) . "') ? '" . image_url('expand_small.png', false, 'core', false) . "' : '" . image_url('collapse_small.png', false, 'core', false) . "'")); ?>
			<?php echo __('Recent comments'); ?>
		</div>
		<div id="dashboard_<?php echo $id; ?>">
		<?php $issues = TBGIssue::findIssues(array('state' => array('operator' => '=', 'value' => TBGIssue::STATE_OPEN))); ?>
		<?php if ($issues[1] > 0 && TBGComment::countComments(0, TBGComment::TYPE_ISSUE) > 0): ?>
			<?php $comments = TBGComment::getComments(0, TBGComment::TYPE_ISSUE, B2DBCriteria::SORT_DESC); ?>
			<table cellpadding=0 cellspacing=0 style="margin: 5px;">
				<?php $prev_date = null; ?>
				<?php $count = 1; ?>
				<?php foreach ($comments as $comment): ?>
					<?php if (!$comment->isPublic() && $tbg_user->getID() != $comment->getPostedByID() && !$tbg_user->hasPermission('canpostseeandeditallcomments')) continue; // skip private comments ?>
					<?php if ($comment->isSystemComment()) continue; // skip system comments ?>
					<?php $date = tbg_formatTime($comment->getPosted(), 5); ?>
					<?php if ($date != $prev_date): ?>
						<tr>
							<td class="latest_action_dates" colspan="2"><?php echo $date; ?></td>
						</tr>
					<?php endif; ?>
					<?php include_component('main/commentitem', array('comment' => $comment, 'include_project' => true, 'pad_length' => 60)); ?>
					<?php $prev_date = $date; ?>
					<?php if ($count++ == 10) break; // limit to 10 last comments ?>
				<?php endforeach; ?>
				<?php if ($count == 1): ?>
					<tr>
						<td><div class="faded_out" style="padding: 5px 5px 15px 5px;"><?php echo __('No issues recently commented'); ?></div></td>
					</tr>
				<?php endif; ?>
			</table>
		<?php else: ?>
			<div class="faded_out" style="padding: 5px 5px 15px 5px;"><?php echo __('No issues recently commented'); ?></div>
		<?php endif; ?>
		</div>
	<?php break; ?>
	
<?php endswitch;?>

<?php TBGEvent::createNew('core', 'dashboard_main_' . $id)->trigger(); ?>