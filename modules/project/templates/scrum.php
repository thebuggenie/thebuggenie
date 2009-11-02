<?php

	$bugs_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));
	$bugs_response->addJavascript('scrum.js');

?>
<?php echo bugs_successStrip(__('The sprint has been added'), '', 'message_sprint_added', true); ?>
<?php echo bugs_successStrip(__('The user story has been added'), '', 'message_user_story_added', true); ?>
<?php echo bugs_successStrip(__('The user story has been updated'), '', 'message_user_story_assigned', true); ?>
<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
<?php include_component('main/hideableInfoBox', array('key' => 'project_scrum_info', 'title' => __('Using the Scrum planning page'), 'content' => __('Something something'))); ?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="scrum">
	<tr>
		<td style="width: 210px; padding: 0 5px 0 5px;">
			<div class="header_div"><?php echo __('Actions'); ?></div>
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_burndown.png'); ?></td>
					<td style="padding: 3px 0 0 2px; text-align: left; font-size: 12px; font-weight: bold;"><?php echo link_tag('#', __('Show burndown'), array('class' => 'faded_medium')); ?></td>
				</tr>
			</table>
		</td>
		<td style="width: auto; padding-right: 5px;" id="scrum_sprints">
			<div class="header_div"><?php echo __('Sprints overview'); ?></div>
			<div class="rounded_box lightgreen_borderless" style="margin-top: 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<form id="add_sprint_form" action="<?php echo make_url('project_scrum_add_sprint', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo BUGSsettings::getCharset(); ?>" onsubmit="addSprint('<?php echo make_url('project_scrum_add_sprint', array('project_key' => $project_key)); ?>', '<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>');return false;">
						<div id="add_sprint">
							<label for="sprint_name"><?php echo __('Add sprint'); ?></label>
							<input type="text" id="sprint_name" name="sprint_name">
							<input type="submit" value="<?php echo __('Add'); ?>">
						</div>
					</form>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="sprint_add_indicator">
				<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
					<td style="padding: 0px; text-align: left;"><?php echo __('Adding sprint, please wait'); ?>...</td>
				</tr>
			</table>
			<?php foreach ($selected_project->getSprints() as $sprint): ?>
				<?php include_template('sprintbox', array('sprint' => $sprint)); ?>
			<?php endforeach; ?>
		</td>
		<td id="scrum_unassigned">
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;" id="scrum_sprint_0">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<div class="header_div"><?php echo __('Unassigned items'); ?></div>
					<div class="rounded_box lightgreen_borderless" style="margin-top: 5px;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
							<form id="add_user_story_form" action="<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo BUGSsettings::getCharset(); ?>" onsubmit="addUserStory('<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>');return false;">
								<div id="add_story">
									<label for="story_title"><?php echo __('Add user story'); ?></label>
									<input type="hidden" name="issuetype_id" value="<?php echo BUGSsettings::getIssueTypeUserStory(); ?>">
									<input type="hidden" name="return_format" value="scrum">
									<input type="text" id="story_title" name="title">
									<input type="submit" value="<?php echo __('Add'); ?>">
								</div>
							</form>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="user_story_add_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Adding user story, please wait'); ?>...</td>
						</tr>
					</table>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_sprint_0_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
						</tr>
					</table>
					<ul id="scrum_sprint_0_list">
						<?php foreach ($unassigned_issues as $issue): ?>
							<?php include_component('scrumcard', array('issue' => $issue)); ?>
						<?php endforeach; ?>
					</ul>
					<div class="faded_medium" style="font-size: 13px;<?php if (count($unassigned_issues) > 0): ?> display: none;<?php endif; ?>" id="scrum_no_unassigned"><?php echo __('There are no unassigned user stories'); ?></div>
					<input type="hidden" id="scrum_sprint_0_id" value="0">
					<span id="scrum_sprint_0_issues" style="display: none;"></span>
					<span id="scrum_sprint_0_points" style="display: none;"></span>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		</td>
	</tr>
</table>
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