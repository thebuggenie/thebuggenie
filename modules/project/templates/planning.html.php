<?php

	$tbg_response->addBreadcrumb(__('Planning'), null, tbg_get_breadcrumblinks('project_summary', $selected_project));
	$tbg_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));

?>
		<?php include_template('project/projectheader', array('selected_project' => $selected_project)); ?>
		<?php include_template('project/projectinfosidebar', array('selected_project' => $selected_project, 'table_id' => 'project_planning')); ?>
		<div class="planning_container">
			<h3>
				<?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
					<?php echo javascript_link_tag(__('Add new milestone'), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'milestone', 'project_id' => $selected_project->getId()))."');", 'class' => 'button button-green')); ?>
				<?php endif; ?>
				<?php echo __('Project milestones'); ?>
			</h3>
			<?php if ($tbg_user->canManageProjectReleases($selected_project)): ?>
				<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="sprint_add_indicator">
					<tr>
						<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
						<td style="padding: 0px; text-align: left;"><?php echo __('Adding sprint, please wait'); ?>...</td>
					</tr>
				</table>
			<?php endif; ?>
			<div class="faded_out" style="margin-top: 10px; font-size: 13px;<?php if (count($selected_project->getMilestones()) > 0): ?> display: none;<?php endif; ?>" id="no_milestones"><?php echo __('No milestones have been created yet.'); ?></div>
			<div id="search_results">
				<?php if ($tbg_user->canEditProjectDetails($selected_project)) include_template('search/bulkactions', array('mode' => 'top')); ?>
				<div id="milestone_list">
					<?php foreach ($selected_project->getMilestones() as $milestone): ?>
						<?php include_template('milestonebox', array('milestone' => $milestone)); ?>
					<?php endforeach; ?>
					<?php include_template('milestonebox', array('milestone' => $unassigned_milestone)); ?>
				</div>
				<?php if ($tbg_user->canEditProjectDetails($selected_project)) include_template('search/bulkactions', array('mode' => 'bottom')); ?>
			</div>
		</div>
		<?php if ($tbg_user->canAssignScrumUserStories($selected_project)): ?>
			<script type="text/javascript">
				<?php foreach ($selected_project->getMilestones() as $milestone): ?>
					Droppables.add('milestone_<?php echo $milestone->getID(); ?>', { hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) { TBG.Project.Planning.assign('<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>', dragged, dropped)}});
				<?php endforeach; ?>
					<?php /* foreach ($milestone->getIssues() as $issue): ?>
					new Draggable('scrum_story_<?php echo $issue->getID(); ?>', { revert: true });
					<?php endforeach; ?>
				<?php foreach ($unassigned_issues as $issue): ?>
				new Draggable('scrum_story_<?php echo $issue->getID(); ?>', { revert: true });
				<?php endforeach; */ ?>
				Droppables.add('milestone_0', { hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) { TBG.Project.Planning.assign('<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>', dragged, dropped)}});
			</script>
		<?php endif; ?>
	</td>
	<?php 
	/*
	<td id="scrum_unassigned" class="milestone_issues_container">
		<div class="rounded_box lightgrey borderless" style="margin-top: 5px; padding: 7px;" id="scrum_sprint_0">
			<div class="header_div"><?php echo __('Unassigned items / project backlog'); ?></div>
			<?php if ($tbg_user->canAddScrumUserStories($selected_project)): ?>
				<div class="rounded_box white" style="margin-top: 5px; padding: 7px;">
					<form id="add_user_story_form" action="<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="addUserStory('<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>');return false;">
						<div id="add_story">
							<div class="add_story_header"><?php echo __('Create a user story'); ?></div>
							<select name="issuetype_id">
								<?php $c = 0; ?>
								<?php foreach ($selected_project->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
									<?php if ($issuetype->getIcon() == 'developer_report'): ?>
										<?php $c++; ?>
										<option value="<?php echo $issuetype->getID(); ?>"><?php echo $issuetype->getName(); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php if ($c == 0): ?>
									<option disabled="disabled"><?php echo __('No scrum-compatible issue types found'); ?></option>
								<?php endif; ?>
							</select>
							<br />
							<?php if ($c > 0): ?>
							<label for="story_title"><?php echo __('Story title:'); ?></label>
							<input type="hidden" name="return_format" value="scrum">
							<input type="text" id="story_title" name="title">
							<input type="submit" value="<?php echo __('Add'); ?>">
							<?php endif; ?>
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
			<ul id="scrum_sprint_0_list" class="milestone_issues_container">
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
		</td> */ ?>
	</tr>
</table>