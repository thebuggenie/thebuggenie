<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone_box">
	<div class="header">
		<?php if ($milestone->getID()): ?>
			<?php echo link_tag(make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())), __('Open overview'), array('class' => 'button button-silver')); ?>
		<?php endif; ?>
		<?php echo javascript_link_tag(__('Issues'), array('onclick' => "TBG.Project.Planning.toggleIssues('".make_url('project_planning_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()))."', {$milestone->getID()});", 'class' => 'button button-silver', 'title' => __('Click to show assigned stories for this sprint'))); ?>
		<?php echo image_tag('spinning_20.gif', array('id' => 'milestone_'.$milestone->getID().'_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
		<b><?php echo $milestone->getName(); ?></b>
		<span class="date">
			<?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
				(<?php echo tbg_formatTime($milestone->getStartingDate(), 22); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22); ?>)
			<?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
				(<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($milestone->getStartingDate(), 22))); ?>)
			<?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
				(<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 22))); ?>)
			<?php endif; ?>
		</span>
		&nbsp;&nbsp;<span class="counts"><?php echo __('%number_of% issue(s), %hours% hrs, %points% pts', array('%points%' => '<span id="milestone_'.$milestone->getID().'_estimated_points">' . $milestone->getPointsEstimated() . '</span>', '%hours%' => '<span id="milestone_'.$milestone->getID().'_estimated_hours">' . $milestone->getHoursEstimated() . '</span>', '%number_of%' => '<span id="milestone_'.$milestone->getID().'_issues">'.$milestone->countIssues().'</span>')); ?></span>&nbsp;
	</div>
	<div id="milestone_<?php echo $milestone->getID(); ?>_container" style="display: none;">
		<form action="<?php echo make_url('project_planning_update_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>" onsubmit="TBG.Project.Planning.updateIssues('<?php echo make_url('project_planning_update_milestone_issues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>);return false;" method="post" id="milestone_<?php echo $milestone->getID(); ?>_issues_form">
			<table cellpadding="0" cellspacing="0" class="milestone_issues">
				<thead>
					<tr>
						<th><?php echo __('Issue'); ?></th>
						<th><?php echo __('Status'); ?></th>
						<th><?php echo __('Priority'); ?></th>
						<th><?php echo __('Assigned to'); ?></th>
						<th class="pointsandtime"><?php echo __('Est. hrs'); ?></th>
						<th class="pointsandtime"><?php echo __('Est. pts'); ?></th>
						<th class="pointsandtime"><?php echo __('Spent hrs'); ?></th>
						<th class="pointsandtime"><?php echo __('Spent pts'); ?></th>
					</tr>
				</thead>
				<tbody id="milestone_<?php echo $milestone->getID(); ?>_list" class="milestone_issues_container"></tbody> 
			</table>
		</form>
		<input type="hidden" id="milestone_<?php echo $milestone->getID(); ?>_id" value="<?php echo $milestone->getID(); ?>">
		<div class="faded_out" style="font-size: 13px;<?php if ($milestone->countIssues() > 0): ?> display: none;<?php endif; ?>" id="milestone_<?php echo $milestone->getID(); ?>_unassigned"><?php echo __('No issues assigned to this milestone'); ?></div>
	</div>
</div>