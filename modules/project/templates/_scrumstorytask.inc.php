<div class="user_story_task" id="scrum_story_task_<?php echo $task->getID(); ?>">
	<div class="story_estimate">
		<?php echo __('%hours% hr(s)', array('%hours%' => '<span id="scrum_story_task_' . $task->getID() . '_hours">' . $task->getEstimatedHours() . '</span>')); ?>
	</div>
	<div class="story_title"><?php echo $task->getTitle(); ?></div>
</div>