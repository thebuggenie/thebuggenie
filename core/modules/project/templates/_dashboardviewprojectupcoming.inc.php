<div class="dashboard_milestones">
<?php $milestone_cc = 0; ?>
<?php foreach ($upcoming_milestones as $milestone): ?>
    <?php if ($milestone->isScheduled()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php foreach ($starting_milestones as $milestone): ?>
    <?php if ($milestone->isStarting()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
</div>
<?php if ($milestone_cc == 0): ?>
    <div class="no-items">
        <?= fa_image_tag('calendar-plus'); ?>
        <span><?php echo __('Upcoming milestones appear here'); ?></span>
        <?php if ($tbg_user->hasProjectPageAccess('project_roadmap', $project)): ?>
            <?php echo link_tag(make_url('project_roadmap', array('project_key' => $project->getKey())), __('Open project roadmap'), ['class' => 'button button-silver']); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
