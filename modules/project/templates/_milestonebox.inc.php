<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone_box">
	<div class="header">
		<div class="button button-blue" style="float: right;">
			<?php echo link_tag(make_url('project_scrum_sprint_details', array('project_key' => $milestone->getProject()->getKey(), 'sprint_id' => $milestone->getID())), __('Open overview')); ?>
		</div>
		<div class="button button-blue" style="float: right;">
			<a onclick="$('scrum_sprint_<?php echo $milestone->getID(); ?>_container').toggle();" title="<?php echo __('Click to show assigned stories for this sprint'); ?>"><?php echo __('Show issues'); ?></a>
		</div>
		<b><?php echo $milestone->getName(); ?></b>
		<span class="sprint_date">
			<?php if ($milestone->getStartingDate() && $milestone->isScheduled()): ?>
				(<?php echo tbg_formatTime($milestone->getStartingDate(), 22); ?> - <?php echo tbg_formatTime($milestone->getScheduledDate(), 22); ?>)
			<?php elseif ($milestone->getStartingDate() && !$milestone->isScheduled()): ?>
				<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($milestone->getStartingDate(), 22))); ?>
			<?php elseif (!$milestone->getStartingDate() && $milestone->isScheduled()): ?>
				<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 22))); ?>
			<?php endif; ?>
		</span>
		&nbsp;&nbsp;<span class="issue_count"><?php echo __('%number_of% issue(s)', array('%number_of%' => '<span style="font-weight: bold;" id="milestone_'.$milestone->getID().'_issues">'.$milestone->countIssues().'</span>')); ?></span>&nbsp;
		<div class="milestone_estimates"><?php echo __('%hours% hrs / %points% pts', array('%points%' => '<span id="scrum_sprint_'.$milestone->getID().'_estimated_points">' . $milestone->getPointsEstimated() . '</span>', '%hours%' => '<span id="scrum_sprint_'.$milestone->getID().'_estimated_hours">' . $milestone->getHoursEstimated() . '</span>')); ?></div>
	</div>
	<div id="scrum_sprint_<?php echo $milestone->getID(); ?>_container" style="display: none;">
		<ul id="scrum_sprint_<?php echo $milestone->getID(); ?>_list" class="milestone_issues_container">
			<?php foreach ($milestone->getIssues() as $issue): ?>
				<?php include_component('scrumcard', array('issue' => $issue)); ?>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" id="scrum_sprint_<?php echo $milestone->getID(); ?>_id" value="<?php echo $milestone->getID(); ?>">
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_sprint_<?php echo $milestone->getID(); ?>_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
			</tr>
		</table>
		<div class="faded_out" style="font-size: 13px;<?php if (count($milestone->getIssues()) > 0): ?> display: none;<?php endif; ?>" id="scrum_sprint_<?php echo $milestone->getID(); ?>_unassigned"><?php echo __('No user stories assigned to this sprint'); ?></div>
	</div>
</div>