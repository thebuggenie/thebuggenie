<tr class="milestone_issue_row<?php if ($issue->isClosed()): ?> issue_closed<?php endif; ?>" id="issue_<?php echo $issue->getID(); ?>">
	<td style="padding: 2px;"><input type="checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" onclick="TBG.Search.toggleCheckbox(this);" value="<?php echo $issue->getID(); ?>"></td>
	<td>
		<input type="hidden" name="issue_id[<?php echo $issue->getID(); ?>]" value="<?php echo $issue->getID(); ?>">
		<div id="issue_<?php echo $issue->getID(); ?>_draggable">
			<div style="display: none;" class="rounded_box shadowed white story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
				<div>
					<div class="header" style="margin-left: 5px;"><?php echo __('Pick a planning color for this issue'); ?></div>
					<div style="margin-left: 5px;"><?php echo __('Selecting a color makes the issue easily recognizable in the planning view'); ?>.</div>
					<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
				</div>
				<div class="color_items">
					<?php foreach ($colors as $color): ?>
						<div <?php if ($issue->canEditIssue()): ?>onclick="TBG.Project.Scrum.Story.setColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" <?php endif; ?>class="story_color_selector_item" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
					<?php endforeach; ?>
				</div>
				<br style="clear: both;">
				<div style="margin: 5px;">
					<?php echo javascript_link_tag(__('%color_list% or keep the current color', array('%color_list%' => '')), array('onclick' => "$('color_selector_{$issue->getID()}').toggle()")); ?>
				</div>
			</div>
			<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" <?php if ($issue->canEditIssue()): ?>onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();"<?php endif; ?> style="cursor: pointer; background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
			<div class="draggable" id="issue_<?php echo $issue->getID(); ?>_handle">
				<span></span><span></span><span></span>
			</div>
			<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle()); ?>
			<input type="hidden" id="issue_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getID(); ?>">
		</div>
	</td>
	<td><?php echo ($issue->isAssigned()) ? $issue->getAssignee()->getName() : '-' ?></td>
	<td class="sc_status"><span class="sc_status_name"><?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : '-' ?></span></td>
	<td class="milestoneupdateable first">
		<select name="priority[<?php echo $issue->getID(); ?>]" id="priority_selector_<?php echo $issue->getID(); ?>">
			<option value="0"<?php if (!$issue->getPriority() instanceof TBGPriority): ?> selected<?php endif; ?>><?php echo __('Not prioritized'); ?></option>
			<?php foreach (TBGPriority::getAll() as $p_id => $priority): ?>
				<option value="<?php echo $p_id; ?>"<?php if ($issue->getPriority() instanceof TBGPriority && $issue->getPriority()->getID() == $p_id): ?> selected<?php endif; ?>><?php echo $priority->getName(); ?></option>
			<?php endforeach; ?>
		</select>
	</td>
	<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getEstimatedHours(); ?>" name="estimated_hours[<?php echo $issue->getID(); ?>]"></td>
	<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getEstimatedPoints(); ?>" name="estimated_points[<?php echo $issue->getID(); ?>]"></td>
	<td class="milestoneupdateable pointsandtime"><input type="text" value="<?php echo $issue->getSpentHours(); ?>" name="spent_hours[<?php echo $issue->getID(); ?>]"></td>
	<td class="milestoneupdateable pointsandtime last"><input type="text" value="<?php echo $issue->getSpentPoints(); ?>" name="spent_points[<?php echo $issue->getID(); ?>]"></td>
	<?php
	/*
	<div style="display: none;" class="story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
		<div style="float: left;">
			<?php foreach ($colors as $color): ?>
				<div <?php if ($issue->canEditIssue()): ?>onclick="setStoryColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" <?php endif; ?>class="story_color_selector_item c_sel_red_1" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
			<?php endforeach; ?>
		</div>
		<div style="float: left; position: relative;">
			<div class="header" style="margin-left: 5px;"><?php echo __('Pick a color for this user story'); ?></div>
			<div style="margin-left: 5px; width: 240px;"><?php echo __('Selecting a color makes the story easily recognizable'); ?>.</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
		</div>
	</div>
	<div class="story_estimate">
		<?php if ($issue->canEditEstimatedTime()): ?>
			<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
		<?php endif; ?>
		<?php echo __('%points%pt(s)', array('%points%' => '<span id="scrum_story_' . $issue->getID() . '_points">' . $issue->getEstimatedPoints() . '</span>')); ?>
	</div>
	<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" <?php if ($issue->canEditIssue()): ?>onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();"<?php endif; ?> style="cursor: pointer; background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
	<div class="story_no"><?php echo $issue->getFormattedIssueNo(); ?></div>
	<div class="story_title"><?php echo $issue->getTitle(); ?></div>
	<input type="hidden" id="scrum_story_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getID(); ?>">
	<?php if ($issue->canEditEstimatedTime()): ?>
		<?php include_template('project/quickestimate', array('issue' => $issue)); ?>
	<?php endif; ?>
	<div class="actions">
		<label><?php echo __('Actions'); ?>:</label>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), image_tag('tab_new.png', array('title' => __('Open in new window'))), array('target' => '_blank')); ?>
		<?php <a href="javascript:void(0);" onclick="showUserStoryEdit('url', <?php echo $issue->getID(); ?>);"><?php echo image_tag('icon_edit.png', array('title' => __('Edit user story'))); ?></a> ?>
		<?php if ($issue->canAddRelatedIssues()): ?>
			<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_add_task_div').toggle();"><?php echo image_tag('scrum_add_task.png', array('title' => __('Add a task to this user story'))); ?></a>
		<?php endif; ?>
		<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_tasks').toggle();"><?php echo image_tag('view_list_details.png', array('title' => __('Show tasks for this user story'))); ?></a>&nbsp;<span class="task_count">(<span id="scrum_story_<?php echo $issue->getID(); ?>_tasks_count"><?php echo count($issue->getChildIssues()); ?></span>)</span>
		<?php if ($issue->canAddRelatedIssues()): ?>
			<?php include_template('project/quickaddtask', array('issue' => $issue, 'mode' => 'scrum')); ?>
		<?php endif; ?>
	</div>
	<div style="clear: both; display: none;" id="scrum_story_<?php echo $issue->getID(); ?>_tasks" class="story_task_list">
		<?php $hastasks = false; ?>
		<?php foreach ($issue->getChildIssues() as $task_id => $task): ?>
			<?php if ($task->getIssueType()->isTask()): ?>
				<?php $hastasks = true; ?>
				<?php include_template('project/scrumstorytask', array('task' => $task)); ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="faded_out" id="no_tasks_<?php echo $issue->getID(); ?>"<?php if ($hastasks): ?> style="display: none;"<?php endif; ?>><?php echo __("This story doesn't have any tasks"); ?></div>
	</div>
	*/ ?>
</tr>