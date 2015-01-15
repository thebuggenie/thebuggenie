<div class="user_story_task" id="scrum_story_task_<?php echo $task->getID(); ?>" style="position: relative;">
    <div class="story_estimate">
        <?php if ($task->canEditEstimatedTime()): ?>
            <a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $task->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
            <?php include_component('project/quickestimate', array('issue' => $task, 'show_hours' => true)); ?>
        <?php endif; ?>
        <?php echo __('%hours hr(s)', array('%hours' => '<span id="scrum_story_' . $task->getID() . '_hours">' . $task->getEstimatedHours() . '</span>')); ?>
    </div>
    <div class="story_title"><?php echo $task->getTitle(); ?></div>
</div>
