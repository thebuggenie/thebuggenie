<div class="sub_header"><?php echo __('Milestones finishing in the next 21 days'); ?></div>
<?php $milestone_cc = 0; ?>
<?php foreach (TBGContext::getCurrentProject()->getUpcomingMilestones(21) as $milestone): ?>
	<?php if ($milestone->isScheduled()): ?>
		<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
		<?php $milestone_cc++; ?>
	<?php endif; ?>
<?php endforeach; ?>
<?php if ($milestone_cc == 0): ?>
	<div class="faded_out"><?php echo __('This project has no upcoming milestones.'); ?></div>
<?php endif; ?>
<div class="sub_header"><?php echo __('Milestones starting in the next 21 days'); ?></div>
<?php $milestone_cc = 0; ?>
<?php foreach (TBGContext::getCurrentProject()->getStartingMilestones(21) as $milestone): ?>
	<?php if ($milestone->isStarting()): ?>
		<?php include_template('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
		<?php $milestone_cc++; ?>
	<?php endif; ?>
<?php endforeach; ?>
<?php if ($milestone_cc == 0): ?>
	<div class="faded_out"><?php echo __('This project has no upcoming milestones.'); ?></div>
<?php endif; ?>