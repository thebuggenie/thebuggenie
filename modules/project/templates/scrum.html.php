<?php

	$tbg_response->addBreadcrumb(__('Project sprint planning'));
	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));
	$tbg_response->addJavascript('scrum.js');

?>
		<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project, 'table_id' => 'scrum')); ?>
		<?php include_component('main/hideableInfoBox', array('key' => 'project_scrum_info', 'title' => __('Using the Sprint planning page'), 'content' => __('Administer your project backlog from this page.<br><ul><li>Create sprints from the "Add sprint" input area, or use the project "milestone" configuration page to add sprints</li><li>Use the "Add user story" input area to quickly add a user story to the backlog, or the "report issue"-wizard to add detailed user stories.</li><li>Drag user stories from the backlog to a sprint (or between sprints) to assign the user story there</li><li>Click the sprint header to show all stories in that sprint</li><li>Pause the mouse over a user story to show more actions, like opening the user story in a new window or editing it</li><li>Click the little square on the left side of the user story to colorize the story</li><li>To change estimated points for a user story, click the little card icon on the far right of the story</li></ul>'))); ?>
		<div id="scrum_sprints" class="scrum_container">
			<div class="header_div">
				<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
					<tr>
						<td><?php echo __('Sprints overview'); ?></td>
						<?php if ($tbg_user->canAddScrumSprints($selected_project)): ?>
							<td style="text-align: right;" id="sprint_add_button">
								<table align="right" border="0" cellpadding="0" cellspacing="0"><tr><td class="nice_button"><input type="button" onclick="$('sprint_add_div').toggle();" value="<?php echo __('Add new sprint'); ?>"></td></tr></table>
							</td>
						<?php endif; ?>
					</tr>
				</table>
			</div>
			<?php if ($tbg_user->canAddScrumSprints($selected_project)): ?>
				<div class="rounded_box lightyellow" style="margin-top: 5px; display: none; padding: 7px;" id="sprint_add_div">
					<form id="add_sprint_form" action="<?php echo make_url('project_scrum_add_sprint', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addSprint('<?php echo make_url('project_scrum_add_sprint', array('project_key' => $project_key)); ?>', '<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>');return false;">
						<div id="add_sprint">
							<label for="sprint_name"><?php echo __('Add sprint'); ?></label>
							<input type="text" id="sprint_name" name="sprint_name">
							<input type="submit" value="<?php echo __('Add'); ?>">
							<br style="clear: both;">
							<label for="sprint_starting_day"><?php echo __('Sprint starts'); ?></label>
							<select name="starting_day" id="sprint_starting_day">
								<?php for ($cc = 1;$cc <= 31;$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('d') == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
							</select>
							<select name="starting_month" id="sprint_starting_month">
								<?php for ($cc = 1;$cc <= 12;$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('m') == $cc) ? " selected" : ""); ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
								<?php endfor; ?>
							</select>
							<select name="starting_year" id="sprint_starting_year">
								<?php for ($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('Y') == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
							</select>
							<br style="clear: both;">
							<label for="sprint_scheduled_day"><?php echo __('Sprint ends'); ?></label>
							<select name="scheduled_day" id="sprint_scheduled_day">
								<?php for ($cc = 1;$cc <= 31;$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('d') == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
							</select>
							<select name="scheduled_month" id="sprint_scheduled_month">
								<?php for ($cc = 1;$cc <= 12;$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('m') == $cc) ? " selected" : ""); ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
								<?php endfor; ?>
							</select>
							<select name="scheduled_year" id="sprint_scheduled_year">
								<?php for ($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
									<option value=<?php echo $cc; ?><?php echo ((date('Y') == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
								<?php endfor; ?>
							</select>
							<br style="clear: both;">
						</div>
					</form>
				</div>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="sprint_add_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Adding sprint, please wait'); ?>...</td>
					</tr>
				</table>
			<?php endif; ?>
			<?php if ($tbg_user->canEditProjectDetails($selected_project)): ?><div class="faded_out" style="margin-top: 10px;"><?php echo __("Sprints are created as milestones for this project, which can be edited in the %project_configuration%.", array('%project_configuration%' => javascript_link_tag(__('project configuration'), array('onclick' => "showFadedBackdrop('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'section' => 'milestones', 'project_id' => $selected_project->getID()))."');")))); ?></div><?php endif; ?>
			<div class="faded_out" style="margin-top: 10px; font-size: 13px;<?php if (count($selected_project->getSprints()) > 0): ?> display: none;<?php endif; ?>" id="no_sprints"><?php echo __('No sprints have been defined for this project'); ?></div>
			<?php foreach ($selected_project->getSprints() as $sprint): ?>
				<?php include_template('sprintbox', array('sprint' => $sprint)); ?>
			<?php endforeach; ?>
		</td>
		<td id="scrum_unassigned" class="scrum_container">
			<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 7px;" id="scrum_sprint_0">
				<div class="header_div"><?php echo __('Unassigned items / project backlog'); ?></div>
				<?php if ($tbg_user->canAddScrumUserStories($selected_project)): ?>
					<div class="rounded_box white" style="margin-top: 5px; padding: 7px;">
						<form id="add_user_story_form" action="<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addUserStory('<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>');return false;">
							<div id="add_story">
								<label for="story_title"><?php echo __('Create a user story'); ?></label>
								<input type="hidden" name="issuetype_id" value="<?php echo TBGSettings::getIssueTypeUserStory(); ?>">
								<input type="hidden" name="return_format" value="scrum">
								<input type="text" id="story_title" name="title">
								<input type="submit" value="<?php echo __('Add'); ?>">
							</div>
						</form>
					</div>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="user_story_add_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Adding user story, please wait'); ?>...</td>
						</tr>
					</table>
				<?php endif; ?>
				<?php if ($tbg_user->canAssignScrumUserStories($selected_project)): ?>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_sprint_0_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
						</tr>
					</table>
				<?php endif; ?>
				<ul id="scrum_sprint_0_list" class="scrum_container">
					<?php foreach ($unassigned_issues as $issue): ?>
						<?php include_component('scrumcard', array('issue' => $issue)); ?>
					<?php endforeach; ?>
				</ul>
				<div class="faded_out" style="font-size: 13px;<?php if (count($unassigned_issues) > 0): ?> display: none;<?php endif; ?>" id="scrum_sprint_0_unassigned"><?php echo __('There are no items in the project backlog'); ?></div>
				<input type="hidden" id="scrum_sprint_0_id" value="0">
				<span id="scrum_sprint_0_issues" style="display: none;"></span>
				<span id="scrum_sprint_0_estimated_points" style="display: none;"></span>
				<span id="scrum_sprint_0_estimated_hours" style="display: none;"></span>
			</div>
		</div>
		<?php if ($tbg_user->canAssignScrumUserStories($selected_project)): ?>
			<script type="text/javascript">
				<?php foreach ($selected_project->getSprints() as $sprint): ?>
				Droppables.add('scrum_sprint_<?php echo $sprint->getID(); ?>', { hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) { assignStory('<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>', dragged, dropped)}});
					<?php foreach ($sprint->getIssues() as $issue): ?>
					new Draggable('scrum_story_<?php echo $issue->getID(); ?>', { revert: true });
					<?php endforeach; ?>
				<?php endforeach; ?>
				<?php foreach ($unassigned_issues as $issue): ?>
				new Draggable('scrum_story_<?php echo $issue->getID(); ?>', { revert: true });
				<?php endforeach; ?>
				Droppables.add('scrum_sprint_0', { hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) { assignStory('<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>', dragged, dropped)}});
			</script>
		<?php endif; ?>
		</td>
	</tr>
</table>