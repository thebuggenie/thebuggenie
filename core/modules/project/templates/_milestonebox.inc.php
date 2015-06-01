<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone_box <?php echo ($milestone->isVisibleRoadmap()) ? ' available' : ' unavailable'; ?> <?php echo ($milestone->isReached()) ? 'closed' : 'open'; ?>" data-milestone-id="<?php echo $milestone->getID(); ?>">
    <div class="planning_indicator" id="milestone_<?php echo $milestone->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
    <?php include_component('project/milestoneboxheader', compact('milestone', 'include_counts', 'include_buttons', 'board')); ?>
    <ul id="milestone_<?php echo $milestone->getID(); ?>_issues" class="milestone_issues jsortable intersortable collapsed <?php if ($milestone->countIssues() == 0) echo 'empty'; ?>"></ul>
    <div class="milestone_no_issues" style="<?php if ($milestone->countIssues() > 0): ?> display: none;<?php endif; ?>" id="milestone_<?php echo $milestone->getID(); ?>_unassigned"><?php echo __('No issues are assigned to this milestone'); ?></div>
</div>
