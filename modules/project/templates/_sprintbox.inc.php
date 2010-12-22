<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 6px;" id="scrum_sprint_<?php echo $sprint->getID(); ?>">
	<div class="sprint_header">
		<?php echo link_tag(make_url('project_scrum_sprint_details', array('project_key' => $sprint->getProject()->getKey(), 'sprint_id' => $sprint->getID())), image_tag('show_sprint_details.png', array('style' => 'float: left; margin-right: 5px;', 'title' => __('Show sprint overview and details')))); ?>
		<b><?php echo $sprint->getName(); ?></b>
		<span class="sprint_date">
			<?php if ($sprint->getStartingDate() && $sprint->isScheduled()): ?>
				(<?php echo tbg_formatTime($sprint->getStartingDate(), 22); ?> - <?php echo tbg_formatTime($sprint->getScheduledDate(), 22); ?>)
			<?php elseif ($sprint->getStartingDate() && !$sprint->isScheduled()): ?>
				<?php echo __('Starting %start_date%', array('%start_date%' => tbg_formatTime($sprint->getStartingDate(), 22))); ?>
			<?php elseif (!$sprint->getStartingDate() && $sprint->isScheduled()): ?>
				<?php echo __('Ends %end_date%', array('%end_date%' => tbg_formatTime($sprint->getScheduledDate(), 22))); ?>
			<?php endif; ?>
		</span>
		&nbsp;&nbsp;<a href="javascript: void(0);" class="issue_count" onclick="$('scrum_sprint_<?php echo $sprint->getID(); ?>_container').toggle();" title="<?php echo __('Click to show assigned stories for this sprint'); ?>"><?php echo __('%number_of% issue(s)', array('%number_of%' => '<span style="font-weight: bold;" id="scrum_sprint_'.$sprint->getID().'_issues">'.$sprint->countIssues().'</span>')); ?></a>&nbsp;
		<div class="sprint_points"><?php echo __('%hours% hrs / %points% pts', array('%points%' => '<span id="scrum_sprint_'.$sprint->getID().'_estimated_points">' . $sprint->getPointsEstimated() . '</span>', '%hours%' => '<span id="scrum_sprint_'.$sprint->getID().'_estimated_hours">' . $sprint->getHoursEstimated() . '</span>')); ?></div>
	</div>
	<div id="scrum_sprint_<?php echo $sprint->getID(); ?>_container" style="display: none;">
		<ul id="scrum_sprint_<?php echo $sprint->getID(); ?>_list" class="scrum_container">
			<?php foreach ($sprint->getIssues() as $issue): ?>
				<?php include_component('scrumcard', array('issue' => $issue)); ?>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" id="scrum_sprint_<?php echo $sprint->getID(); ?>_id" value="<?php echo $sprint->getID(); ?>">
		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_sprint_<?php echo $sprint->getID(); ?>_indicator">
			<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
				<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
			</tr>
		</table>
		<div class="faded_out" style="font-size: 13px;<?php if (count($sprint->getIssues()) > 0): ?> display: none;<?php endif; ?>" id="scrum_sprint_<?php echo $sprint->getID(); ?>_unassigned"><?php echo __('No user stories assigned to this sprint'); ?></div>
	</div>
</div>