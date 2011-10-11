<div class="rounded_box <?php if ($milestone->isReached()): ?>green borderless<?php elseif ($milestone->isOverdue()): ?>red borderless<?php else: ?>iceblue borderless<?php endif; ?> milestone_box" style="vertical-align: middle; padding: 5px;">
	<div class="header"><?php echo link_tag(make_url('project_roadmap', array('project_key' => $milestone->getProject()->getKey())).'#roadmap_milestone_'.$milestone->getID(), $milestone->getProject()->getName() . ' - ' . $milestone->getName()); ?></div>
	<div class="date">
		<?php if ($milestone->getStartingDate()): ?>
			<?php
				echo tbg_formatTime($milestone->getStartingDate(), 20) . ' - ';
				if ($milestone->getScheduledDate() > 0): echo tbg_formatTime($milestone->getScheduledDate(), 20); else: echo __('No scheduled date specified'); endif;
			?>
		<?php else: ?>
			<?php if ($milestone->getScheduledDate() > 0): echo __('Scheduled for %scheduled_date%', array('%scheduled_date%' => tbg_formatTime($milestone->getScheduledDate(), 20))); else: echo __('No scheduled date specified'); endif; ?>
		<?php endif; ?>
	</div>
	<div class="percentage">
		<div class="numbers">
			<?php if ($milestone->isSprint()): ?>
				<?php if ($milestone->countClosedIssues() == 1): ?>
					<?php echo __('%num_closed% story (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
				<?php else: ?>
					<?php echo __('%num_closed% stories (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo __('%num_closed% issue(s) closed of %num_assigned% assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>')); ?>
			<?php endif; ?>
		</div>
		<?php include_template('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 14)); ?>
	</div>
	<?php if ($milestone->isReached()): ?>
		<div class="status">
			<?php if ($milestone->getType() == TBGMilestone::TYPE_REGULAR): ?>
				<?php echo __('This milestone has been reached'); ?>
			<?php else: ?>
				<?php echo __('This sprint is completed'); ?>
			<?php endif; ?>
		</div>
	<?php elseif ($milestone->isOverdue()): ?>
		<div class="status">
			<?php if ($milestone->getType() == TBGMilestone::TYPE_REGULAR): ?>
				<?php echo __('This milestone is overdue'); ?>
			<?php else: ?>
				<?php echo __('This sprint is overdue'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>