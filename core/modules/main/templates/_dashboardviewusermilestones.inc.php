<?php if (count($tbg_user->getAssociatedProjects()) > 0): ?>
    <div class="dashboard_milestones">
        <?php $milestone_cc = 0; ?>
        <?php foreach ($tbg_user->getAssociatedProjects() as $project): ?>
            <?php foreach ($project->getUpcomingMilestones() as $milestone): ?>
                <?php if ($milestone->isScheduled()): ?>
                    <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
                    <?php $milestone_cc++; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <?php if ($milestone_cc == 0): ?>
        <div class="faded_out" style="padding: 5px;"><?php echo __('There are no upcoming milestones for any of your associated projects'); ?></div>
    <?php endif; ?>
<?php else: ?>
    <div class="faded_out" style="font-size: 0.9em; padding: 5px 5px 10px 5px;"><?php echo __('You are not associated with any projects'); ?></div>
<?php endif; ?>
