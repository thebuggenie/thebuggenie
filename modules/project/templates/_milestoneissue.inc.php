<tr class="milestone_issue_row<?php if ($issue->isClosed()): ?> issue_closed<?php endif; ?>" id="issue_<?php echo $issue->getID(); ?>">
	<?php if ($issue->canEditIssue()): ?>
		<td style="padding: 2px;"><input type="checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" onclick="TBG.Search.toggleCheckbox(this);" value="<?php echo $issue->getID(); ?>"></td>
	<?php endif; ?>
	<td>
		<?php if ($issue->canEditIssue()): ?>
			<input type="hidden" name="issue_id[<?php echo $issue->getID(); ?>]" value="<?php echo $issue->getID(); ?>">
		<?php endif; ?>
		<div id="issue_<?php echo $issue->getID(); ?>_draggable">
			<?php if ($issue->canEditIssue()): ?>
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
			<?php endif; ?>
			<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" <?php if ($issue->canEditIssue()): ?>onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();"<?php endif; ?> style="<?php if ($issue->canEditIssue()): ?>cursor: pointer; <?php endif; ?>background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
			<?php if ($issue->canEditIssue()): ?>
				<div class="draggable" id="issue_<?php echo $issue->getID(); ?>_handle">
					<span></span><span></span><span></span>
				</div>
				<input type="hidden" id="issue_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getID(); ?>">
			<?php endif; ?>
			<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getFormattedIssueNo(), 'project_key' => $issue->getProject()->getKey())), $issue->getFormattedTitle()); ?>
		</div>
	</td>
	<td><?php echo ($issue->isAssigned()) ? $issue->getAssignee()->getName() : '-' ?></td>
	<td class="sc_status"><span class="sc_status_name"><?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : '-' ?></span></td>
	<?php if ($issue->canEditIssue()): ?>
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
	<?php else: ?>
		<td>
			<?php echo ($issue->getPriority() instanceof TBGPriority) ? $issue->getPriority()->getName() : __('Not determined'); ?>
		</td>
		<td class="pointsandtime"><?php echo $issue->getEstimatedHours(); ?></td>
		<td class="pointsandtime"><?php echo $issue->getEstimatedPoints(); ?></td>
		<td class="pointsandtime"><?php echo $issue->getSpentHours(); ?></td>
		<td class="pointsandtime"><?php echo $issue->getSpentPoints(); ?></td>
	<?php endif; ?>
</tr>