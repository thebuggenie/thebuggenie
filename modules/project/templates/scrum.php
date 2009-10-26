<?php

	$bugs_response->setTitle(__('"%project_name%" project planning', array('%project_name%' => $selected_project->getName())));
	$bugs_response->addJavascript('scrum');

?>
<?php echo bugs_successStrip(__('The user story has been added'), '', 'message_user_story_added', true); ?>
<?php echo bugs_failureStrip('', '', 'message_failed', true); ?>
<table style="width: 100%;" cellpadding="0" cellspacing="0" id="scrum">
	<tr>
		<td style="width: 210px; padding: 0 5px 0 5px;">
			<div class="header_div"><?php echo __('Actions'); ?></div>
		</td>
		<td style="width: auto; padding-right: 5px;">
			<div class="header_div"><?php echo __('Sprints overview'); ?></div>
			<ul id="scrum_sprint_1">
				
			</ul>
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
			<ul id="scrum_unassigned_list">
				<?php foreach ($unassigned_issues as $issue): ?>
					<?php include_component('scrumcard', array('issue' => $issue)); ?>
				<?php endforeach; ?>
				<?php /*<li id="scrum_story_1" style="background: url('<?php echo BUGScontext::getTBGPath() . '/themes/' . BUGSsettings::getThemeName() . '/scrum_storycard.png'; ?>') repeat-x; background-color: #FFF;">
					<div class="story_color" style="background-color: #FFDD00;">&nbsp;</div>
					<div class="header">Story 1</div>
					<div class="story_no">1</div>
					<div class="content">As user #1 I'd like to perform an action, obviously, so that I can be a performer of actions</div>
					<div class="story_tags"><b><?php echo __('Tags'); ?></b>: permissions, actions</div>
					<div class="story_owner faded_dark"><?php echo __('Not claimed'); ?></div>
					<div class="story_estimate"><b><?php echo __('Estim'); ?>: </b>3</div>
				</li>
				<li id="scrum_story_2" style="background: url('<?php echo BUGScontext::getTBGPath() . '/themes/' . BUGSsettings::getThemeName() . '/scrum_storycard.png'; ?>') repeat-x; background-color: #FFF;">
					<div class="story_color" style="background-color: #00BF00;">&nbsp;</div>
					<div class="header">Story 2</div>
					<div class="story_no">2</div>
					<div class="content" style="display: none;">As user #2 I should not be able to perform actions, so no unprivileged users can perform actions</div>
					<div class="story_tags" style="display: none;"><b><?php echo __('Tags'); ?></b>: security, login, permissions</div>
					<div class="story_owner faded_dark"><?php echo __('Not claimed'); ?></div>
					<div class="story_estimate"><b><?php echo __('Estim'); ?>: </b>3</div>
				</li>
				<li id="scrum_story_3" style="background: url('<?php echo BUGScontext::getTBGPath() . '/themes/' . BUGSsettings::getThemeName() . '/scrum_storycard.png'; ?>') repeat-x; background-color: #FFF;">
					<div class="story_color" style="background-color: #80B3FF;">&nbsp;</div>
					<div class="header">Story 3</div>
					<div class="story_no">3</div>
					<div class="content">As an admin I should be able to remove user #1 and user #2, in case they violate TOS</div>
					<div class="story_tags"><b><?php echo __('Tags'); ?></b>: security, users, permissions</div>
					<div class="story_owner faded_dark"><?php echo __('Not claimed'); ?></div>
					<div class="story_estimate"><b><?php echo __('Estim'); ?>: </b>3</div>
				</li> */ ?>
			</ul>
		</td>
	</tr>
</table>
<script type="text/javascript">
	<?php foreach ($selected_project->getMilestones() as $milestone): ?>
	Droppables.add('scrum_sprint_<?php echo $milestone->getID(); ?>', { hoverclass: 'highlighted' });
	<?php endforeach; ?>
	<?php foreach ($selected_project->getIssuesWithoutMilestone() as $issue): ?>
	new Draggable('scrum_story_<?php echo $issue->getID(); ?>', { revert: true });
	<?php endforeach; ?>
	Droppables.add('scrum_unassigned', { hoverclass: 'highlighted' });
</script>