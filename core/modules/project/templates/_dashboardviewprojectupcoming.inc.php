<div class="dashboard_milestones">
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
</div>
<?php if ($milestone_cc == 0): ?>
    <div class="no-items">
        <?= fa_image_tag('calendar-plus-o'); ?>
        <?php echo __('Upcoming milestones appear here'); ?>
    </div>
<?php endif; ?>
