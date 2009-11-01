<?php

	$bugs_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));
	$bugs_response->addJavascript('scrum');

?>
<?php echo bugs_successStrip(__('The user story has been added'), '', 'message_user_story_added', true); ?>
<?php echo bugs_successStrip(__('The user story has been updated'), '', 'message_user_story_assigned', true); ?>
<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="scrum">
	<tr>
		<td style="width: 210px; padding: 0 5px 0 5px;">
			<div class="header_div"><?php echo __('Actions'); ?></div>
		</td>
		<td style="width: auto; padding-right: 5px;">
			<div class="header_div"><?php echo __('Sprints overview'); ?></div>
			<?php foreach ($selected_project->getSprints() as $sprint): ?>
			<div class="rounded_box mediumgrey_borderless" style="margin-top: 5px;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 0 5px 5px 5px;">
					<div class="sprint_header">
						<a href="javascript: void(0);" onclick="$('scrum_sprint_<?php echo $sprint->getID(); ?>').toggle();"><?php echo $sprint->getName(); ?></a>
						&nbsp;&nbsp;<?php echo __('%number_of% issue(s)', array('%number_of%' => '<span style="font-weight: bold;" id="scrum_sprint_'.$sprint->getID().'_issues">'.$sprint->countIssues().'</span>')); ?>&nbsp;
						&nbsp;&nbsp;(<?php echo __('click to show/hide assigned issues'); ?>)
					</div>
					<ul id="scrum_sprint_<?php echo $sprint->getID(); ?>" style="display: none;">
						<?php foreach ($sprint->getIssues() as $issue): ?>
							<?php include_component('scrumcard', array('issue' => $issue)); ?>
						<?php endforeach; ?>
					</ul>
					<input type="hidden" id="scrum_sprint_<?php echo $sprint->getID(); ?>_id" value="<?php echo $sprint->getID(); ?>">
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_sprint_<?php echo $sprint->getID(); ?>_indicator">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
						</tr>
					</table>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
			<?php endforeach; ?>
		</td>
		<td id="scrum_unassigned">
			<div class="header_div"><?php echo __('Unassigned items'); ?></div>
			<form id="add_user_story_form" action="<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>" method="post" accept-charset="<?php echo BUGSsettings::getCharset(); ?>" onsubmit="addUserStory('<?php echo make_url('project_reportissue', array('project_key' => $project_key)); ?>');return false;">
				<div id="add_story">
					<label for="story_title"><?php echo __('Add user story'); ?></label>
					<input type="hidden" name="issuetype_id" value="<?php echo BUGSsettings::getIssueTypeUserStory(); ?>">
					<input type="hidden" name="return_format" value="scrum">
					<input type="text" id="story_title" name="title">
					<input type="submit" value="<?php echo __('Add'); ?>">
				</div>
			</form>
    		<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="user_story_add_indicator">
    			<tr>
    				<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
    				<td style="padding: 0px; text-align: left;"><?php echo __('Adding user story, please wait'); ?>...</td>
    			</tr>
    		</table>
			<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="scrum_unassigned_list_indicator">
				<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
					<td style="padding: 0px; text-align: left; font-size: 13px;"><?php echo __('Reassigning, please wait'); ?>...</td>
				</tr>
			</table>
			<ul id="scrum_unassigned_list">
				<?php foreach ($unassigned_issues as $issue): ?>
					<?php include_component('scrumcard', array('issue' => $issue)); ?>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" id="scrum_unassigned_list_id" value="0">
			<span id="scrum_sprint_0_issues" style="display: none;"></span>
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
	Droppables.add('scrum_unassigned_list', { hoverclass: 'highlighted', onDrop: function (dragged, dropped, event) { assignStory('<?php echo make_url('project_scrum_assign_story', array('project_key' => $selected_project->getKey())); ?>', dragged, dropped)}});
</script>