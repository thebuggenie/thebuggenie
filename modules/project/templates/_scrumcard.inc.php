<li class="story_card moveable" id="scrum_story_<?php echo $issue->getID(); ?>">
	<div style="display: none;" class="story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
		<div style="float: left;">
			<?php foreach ($colors as $color): ?>
				<div onclick="setStoryColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" class="story_color_selector_item c_sel_red_1" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
			<?php endforeach; ?>
		</div>
		<div style="float: left; position: relative;">
			<div class="header" style="margin-left: 5px;"><?php echo __('Pick a color for this user story'); ?></div>
			<div style="margin-left: 5px; width: 240px;"><?php echo __('Selecting a color makes the story easily recognizable'); ?>.</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
		</div>
	</div>
	<div class="story_estimate">
		<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
		<?php echo __('%hours%hr, %points%pt', array('%hours%' => '<span id="scrum_story_' . $issue->getID() . '_hours">' . $issue->getEstimatedHours() . '</span>', '%points%' => '<span id="scrum_story_' . $issue->getID() . '_points">' . $issue->getEstimatedPoints() . '</span>')); ?>
	</div>
	<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();" style="cursor: pointer; background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
	<div class="story_no"><?php echo $issue->getIssueNo(); ?></div>
	<div class="story_title"><?php echo $issue->getTitle(); ?></div>
	<input type="hidden" id="scrum_story_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getID(); ?>">
	<div class="rounded_box mediumgrey borderless story_estimation_div" id="scrum_story_<?php echo $issue->getID(); ?>_estimation" style="display: none;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
			<form id="scrum_story_<?php echo $issue->getID(); ?>_estimation_form" action="<?php echo make_url('project_scrum_story_setestimates', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="setStoryEstimates('<?php echo make_url('project_scrum_story_setestimates', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'scrum');return false;">
				<div class="header"><?php echo __('New story estimate'); ?></div>
				<?php echo image_tag('spinning_20.gif', array('id' => 'point_selector_'.$issue->getID().'_indicator', 'style' => 'display: none;')); ?><br>
				<input type="text" name="hours" value="<?php echo $issue->getEstimatedHours(); ?>" id="scrum_story_<?php echo $issue->getID(); ?>_hours_input"> hrs
				<input type="text" name="points" value="<?php echo $issue->getEstimatedPoints(); ?>" id="scrum_story_<?php echo $issue->getID(); ?>_points_input"> pts
				<input type="submit" value="<?php echo __('Set'); ?>">
				<?php echo __('%set% or %cancel%', array('%set%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'scrum_story_' . $issue->getID() . '_estimation\').toggle();">' . __('cancel') . '</a>')); ?>
			</form>
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
	<div class="actions">
		<label><?php echo __('Actions'); ?>:</label>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getIssueNo(), 'project_key' => $issue->getProject()->getKey())), image_tag('tab_new.png', array('title' => __('Open in new window'))), array('target' => '_blank')); ?>
		<?php /*<a href="javascript:void(0);" onclick="showUserStoryEdit('url', <?php echo $issue->getID(); ?>);"><?php echo image_tag('icon_edit.png', array('title' => __('Edit user story'))); ?></a>*/ ?>
		<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_add_task_div').toggle();"><?php echo image_tag('scrum_add_task.png', array('title' => __('Add a task to this user story'))); ?></a>
		<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_tasks').toggle();"><?php echo image_tag('view_list_details.png', array('title' => __('Show tasks for this user story'))); ?></a>&nbsp;<span class="task_count">(<span id="scrum_story_<?php echo $issue->getID(); ?>_tasks_count"><?php echo count($issue->getChildIssues()); ?></span>)</span>
		<div class="rounded_box borderless lightgrey" id="scrum_story_<?php echo $issue->getID(); ?>_add_task_div" style="margin: 5px 0 5px 0; display: none">
			<form id="scrum_story_<?php echo $issue->getID(); ?>_add_task_form" action="<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addUserStoryTask('<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'scrum');return false;">
				<div>
					<label for="scrum_story_<?php echo $issue->getID(); ?>_task_name_input"><?php echo __('Add task'); ?>&nbsp;</label>
					<input type="text" name="task_name" id="scrum_story_<?php echo $issue->getID(); ?>_task_name_input">
					<input type="submit" value="<?php echo __('Add task'); ?>">
					<?php echo __('%add_task% or %cancel%', array('%add_task%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'scrum_story_' . $issue->getID() . '_add_task_form\').toggle();">' . __('cancel') . '</a>')); ?>
					<?php echo image_tag('spinning_20.gif', array('id' => 'add_task_'.$issue->getID().'_indicator', 'style' => 'display: none;')); ?><br>
				</div>
			</form>
		</div>
	</div>
	<div style="clear: both; display: none;" id="scrum_story_<?php echo $issue->getID(); ?>_tasks">
		<?php foreach ($issue->getChildIssues() as $task_id => $task): ?>
			<?php if ($task->getIssueType()->isTask()): ?>
				<?php include_template('project/scrumstorytask', array('task' => $task)); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</li>