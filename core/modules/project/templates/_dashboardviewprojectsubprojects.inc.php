<div class="dashboard_subprojects">
    <?php if (count($subprojects) > 0): ?>
        <ul class="project_list simple_list" style="margin: 0;">
        <?php foreach ($subprojects as $project): ?>
            <li><?php include_component('project/project', array('project' => $project)); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="faded_out" style="font-weight: normal;"><?php echo __('This project has no subprojects'); ?></div>
    <?php endif; ?>
</div>
