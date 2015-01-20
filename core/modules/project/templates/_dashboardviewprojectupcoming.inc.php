<?php $milestone_cc = 0; ?>
<?php foreach (\thebuggenie\core\framework\Context::getCurrentProject()->getUpcomingMilestones(21) as $milestone): ?>
    <?php if ($milestone->isScheduled()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php foreach (\thebuggenie\core\framework\Context::getCurrentProject()->getStartingMilestones(21) as $milestone): ?>
    <?php if ($milestone->isStarting()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($milestone_cc == 0): ?>
    <div class="faded_out"><?php echo __('This project has no upcoming milestones.'); ?></div>
<?php endif; ?>
