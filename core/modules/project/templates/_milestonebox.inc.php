<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone_box <?php echo ($milestone->isVisibleRoadmap()) ? ' available' : ' unavailable'; ?> <?php echo ($milestone->isReached()) ? 'closed' : 'open'; ?>" data-milestone-id="<?php echo $milestone->getID(); ?>">
    <div class="planning_indicator" id="milestone_<?php echo $milestone->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
    <?php include_component('project/milestoneboxheader', compact('milestone', 'include_counts', 'include_buttons', 'board')); ?>
</div>
