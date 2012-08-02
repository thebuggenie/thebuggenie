<div class="header" id="milestone_<?php echo $milestone->getID(); ?>_header">
	<div class="button-group" style="float: right;">
		<?php echo javascript_link_tag(__('Add issue'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID()))."');", 'class' => 'button button-green')); ?>
		<?php echo javascript_link_tag(__('Issues'), array('onclick' => "TBG.Project.Planning.toggleIssues('".make_url('project_planning_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', {$milestone->getID()});", 'class' => 'button button-silver', 'title' => __('Click to show assigned stories for this milestone'))); ?>
		<?php if ($milestone->getID()): ?>
			<?php if ($tbg_user->canEditProjectDetails(TBGContext::getCurrentProject())): ?>
			<?php echo javascript_link_tag(__('More actions'), array('onclick' => "$('milestone_{$milestone->getID()}_moreactions').toggle();$(this).toggleClassName('button-pressed');", 'class' => 'button button-silver', 'id' => "milestone_{$milestone->getID()}_reload_button", 'title' => __('Click to show more actions for this milestone'))); ?>
			<?php else: ?>
				<?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), __('Show milestone overview'), array('class' => 'button button-silver')); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<ul class="rounded_box popup_box shadowed white milestone_moreactions more_actions_dropdown" id="milestone_<?php echo $milestone->getID(); ?>_moreactions" style="display: none;">
		<li style="display: none;" id="milestone_<?php echo $milestone->getID(); ?>_reload_button"><?php echo javascript_link_tag(__('Reload issues'), array('onclick' => "$('milestone_{$milestone->getID()}_moreactions').toggle();$(this).toggleClassName('button-pressed');$('milestone_{$milestone->getID()}_container').hide();$('milestone_{$milestone->getID()}_list').update('');TBG.Project.Planning.toggleIssues('".make_url('project_planning_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', {$milestone->getID()});", 'title' => __('Click to show assigned stories for this milestone'))); ?></li>
		<li><?php echo javascript_link_tag(__('Delete'), array('onclick' => "$('milestone_{$milestone->getID()}_moreactions').toggle();$(this).toggleClassName('button-pressed');TBG.Main.Helpers.Dialog.show('".__('Do you really want to delete this milestone?')."', '".__('Removing this milestone will unassign all issues from this milestone and remove it from all available lists. This action cannot be undone.')."', {yes: {click: function() { TBG.Project.Milestone.remove('".make_url('project_planning_milestone_remove', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', ".$milestone->getID()."); } }, no: {click: TBG.Main.Helpers.Dialog.dismiss} });")); ?></li>
		<li><?php echo javascript_link_tag(__('Edit'), array('onclick' => "$('milestone_{$milestone->getID()}_moreactions').toggle();$(this).toggleClassName('button-pressed');TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID()))."');")); ?></li>
		<li><?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), __('Show milestone overview')); ?></li>
	</ul>
	<?php echo image_tag('spinning_20.gif', array('id' => 'milestone_'.$milestone->getID().'_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
	<b><?php echo $milestone->getName(); ?></b>
	<span class="date">
		<?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
			(<?php echo tbg_formatTime($milestone->getStartingDate(), 22, true, true); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22, true, true); ?>)
		<?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
			(<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($milestone->getStartingDate(), 22, true, true))); ?>)
		<?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
			(<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 22, true, true))); ?>)
		<?php endif; ?>
	</span>
	&nbsp;&nbsp;<span class="counts"><?php echo __('%number_of% issue(s), %hours% hrs, %points% pts', array('%points%' => '<span id="milestone_'.$milestone->getID().'_estimated_points">' . $milestone->getPointsEstimated() . '</span>', '%hours%' => '<span id="milestone_'.$milestone->getID().'_estimated_hours">' . $milestone->getHoursEstimated() . '</span>', '%number_of%' => '<span id="milestone_'.$milestone->getID().'_issues">'.$milestone->countIssues().'</span>')); ?></span>&nbsp;
</div>