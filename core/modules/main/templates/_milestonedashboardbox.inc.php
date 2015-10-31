<div cellpadding="0" cellspacing="0" class="table rounded_box <?php if ($milestone->isReached()): ?>green<?php elseif ($milestone->isOverdue()): ?>red<?php else: ?>iceblue<?php endif; ?> milestone_box">
    <div class="tr">
        <div class="td">
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
        <div class="td percentage">
            <div class="numbers">
                <?php if ($milestone->isSprint()): ?>
                    <?php if ($milestone->countClosedIssues() == 1): ?>
                        <?php echo __('%num_closed story (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<span style="font-size: 1.5em;">'.$milestone->countClosedIssues().'</span>', '%closed_points' => $milestone->getPointsSpent(), '%num_assigned' => '<span style="font-size: 1.5em;">'.$milestone->countIssues().'</span>', '%assigned_points' => $milestone->getPointsEstimated())); ?>
                    <?php else: ?>
                        <?php echo __('%num_closed stories (%closed_points pts) closed of %num_assigned (%assigned_points pts) assigned', array('%num_closed' => '<span style="font-size: 1.5em;">'.$milestone->countClosedIssues().'</span>', '%closed_points' => $milestone->getPointsSpent(), '%num_assigned' => '<span style="font-size: 1.5em;">'.$milestone->countIssues().'</span>', '%assigned_points' => $milestone->getPointsEstimated())); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo __('%num_closed issue(s) closed of %num_assigned assigned', array('%num_closed' => '<span style="font-size: 1.5em;">'.$milestone->countClosedIssues().'</span>', '%num_assigned' => '<span style="font-size: 1.5em;">'.$milestone->countIssues().'</span>')); ?>
                <?php endif; ?>
            </div>
            <?php include_component('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 2)); ?>
        </div>
    </div>
</div>
