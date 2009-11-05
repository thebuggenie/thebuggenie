<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;" id="scrum_sprint_<?php echo $sprint->getID(); ?>">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
		<div class="sprint_header">
			<a href="javascript: void(0);" onclick="$('scrum_sprint_<?php echo $sprint->getID(); ?>_list').toggle();"><?php echo $sprint->getName(); ?></a>
			<span class="sprint_date">
				<?php if ($sprint->getStartingDate() && $sprint->isScheduled()): ?>
					(<?php echo bugs_formatTime($sprint->getStartingDate(), 20); ?> - <?php echo bugs_formatTime($sprint->getScheduledDate(), 20); ?>)
				<?php elseif ($sprint->getStartingDate() && !$sprint->isScheduled()): ?>
					<?php echo __('Starting %start_date%', array('%start_date%' => bugs_formatTime($sprint->getStartingDate(), 20))); ?>
				<?php elseif (!$sprint->getStartingDate() && $sprint->isScheduled()): ?>
					<?php echo __('Ends %end_date%', array('%end_date%' => bugs_formatTime($sprint->getScheduledDate(), 20))); ?>
				<?php endif; ?>
			</span>
			&nbsp;&nbsp;<?php echo __('%number_of% issue(s)', array('%number_of%' => '<span style="font-weight: bold;" id="scrum_sprint_'.$sprint->getID().'_issues">'.$sprint->countIssues().'</span>')); ?>&nbsp;
			<span class="sprint_points"><?php echo __('%points% pts', array('%points%' => '<span id="scrum_sprint_'.$sprint->getID().'_estimated_points">' . $sprint->getPointsEstimated() . '</span>')); ?></span>
		</div>
		<ul id="scrum_sprint_<?php echo $sprint->getID(); ?>_list" style="display: none;">
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
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>