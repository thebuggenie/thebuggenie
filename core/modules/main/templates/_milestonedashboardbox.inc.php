<div class="rounded_box <?php if ($milestone->isReached()): ?>green<?php elseif ($milestone->isOverdue()): ?>red<?php else: ?>iceblue<?php endif; ?> milestone_box" style="vertical-align: middle; padding: 0 10px 10px 20px; background-color: rgba(243, 241, 241, 0.5); padding-left: 20px; border-width: 1px; border-style: solid;">
	<div class="rounded_box <?php if ($milestone->isReached()): ?>green borderless<?php elseif ($milestone->isOverdue()): ?>red borderless<?php else: ?>iceblue borderless<?php endif; ?>" style="position: absolute; width: 10px; height: 100%; border-radius: 0; margin-bottom: 0; margin-left: -20px; padding: 0;"></div>
    <div class="header"><?php echo link_tag(make_url('project_roadmap', array('project_key' => $milestone->getProject()->getKey())).'#roadmap_milestone_'.$milestone->getID(), $milestone->getProject()->getName() . ' - ' . $milestone->getName()); ?></div>
    <div class="date">
        <?php if ($milestone->getStartingDate()): ?>
            <?php
                echo tbg_formatTime($milestone->getStartingDate(), 20, true, true) . ' - ';
                if ($milestone->getScheduledDate() > 0): echo tbg_formatTime($milestone->getScheduledDate(), 20, true, true); else: echo __('No scheduled date specified'); endif;
            ?>
        <?php else: ?>
            <?php if ($milestone->getScheduledDate() > 0): echo __('Scheduled for %scheduled_date', array('%scheduled_date' => tbg_formatTime($milestone->getScheduledDate(), 20, true, true))); else: echo __('No scheduled date specified'); endif; ?>
        <?php endif; ?>
    </div>
    <div class="percentage">
        <div class="numbers">
            <?php if ($milestone->isSprint()): ?>
                <?php if ($milestone->countClosedIssues() == 1): ?>
                    <?php echo __('%num_closed story (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
                <?php else: ?>
                    <?php echo __('%num_closed stories (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
                <?php endif; ?>
            <?php else: ?>
                <?php echo __('%num_closed issue(s) closed of %num_assigned assigned', array('%num_closed' => '<b>'.$milestone->countClosedIssues().'</b>', '%num_assigned' => '<b>'.$milestone->countIssues().'</b>')); ?>
            <?php endif; ?>
        </div>
        <?php include_component('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 4)); ?>
    </div>
    <?php if ($milestone->isReached()): ?>
        <div class="status">
            <?php if ($milestone->getType() == \thebuggenie\core\entities\Milestone::TYPE_REGULAR): ?>
                <?php echo __('This milestone has been reached'); ?>
            <?php else: ?>
                <?php echo __('This sprint is completed'); ?>
            <?php endif; ?>
        </div>
    <?php elseif ($milestone->isOverdue()): ?>
        <div class="status">
            <?php if ($milestone->getType() == \thebuggenie\core\entities\Milestone::TYPE_REGULAR): ?>
                <?php echo __('This milestone is overdue'); ?>
            <?php else: ?>
                <?php echo __('This sprint is overdue'); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
