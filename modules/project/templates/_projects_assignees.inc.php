<?php 

	TBGContext::loadLibrary('ui'); 
	$assigned_users = $project->getAssignedUsers();
	$assigned_teams = $project->getAssignedTeams();

?>
<div class="config_header" style="border-bottom: 0; margin-top: 0; padding-top: 0;"><b><?php echo __('Assigned users'); ?></b></div>
<?php if (count($assigned_users) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no users assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assigned_users as $user): ?>
			<tr id="assignee_user_<?php echo $user->getID(); ?>_row" class="hoverable">
				<td style="width: 20px;">
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'onclick' => "TBG.Project.removeAssignee('".make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => 'user', 'assignee_id' => $user->getID()))."', 'user', {$user->getID()});", 'id' => 'assignee_user_'.$user->getID().'_link')); ?>
					<?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_user_'.$user->getID().'_indicator', 'style' => 'float: left; display: none;')); ?>
				</td>
				<td style="vertical-align: top; font-size: 0.9em;">
					<?php echo include_component('main/userdropdown', array('user' => $user)); ?>
				</td>
				<td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
					<?php $roles = $project->getRolesForUser($user); ?>
					<?php $role_names = array(); ?>
					<?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
					<?php echo join(',', $role_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
<div class="config_header" style="border-bottom: 0;"><b><?php echo __('Assigned teams'); ?></b></div>
<?php if (count($assigned_teams) == 0): ?>
	<div style="padding-left: 5px; padding-top: 3px; color: #AAA;"><?php echo __('There are no teams assigned to this project'); ?></div>
<?php else: ?>
	<table cellpadding=0 cellspacing=0 width="100%">
		<?php foreach ($assigned_teams as $team): ?>
			<tr id="assignee_team_<?php echo $team->getID(); ?>_row" class="hoverable">
				<td style="width: 20px;">
					<?php echo javascript_link_tag(image_tag('action_delete.png'), array('class' => 'image', 'onclick' => "TBG.Project.removeAssignee('".make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => 'team', 'assignee_id' => $team->getID()))."', 'team', {$team->getID()});", 'id' => 'assignee_team_'.$team->getID().'_link')); ?>
					<?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_team_'.$team->getID().'_indicator', 'style' => 'float: left; display: none;')); ?>
				</td>
				<td style="vertical-align: top; font-size: 0.9em;">
					<?php echo include_component('main/teamdropdown', array('team' => $team)); ?>
				</td>
				<td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
					<?php $roles = $project->getRolesForTeam($team); ?>
					<?php $role_names = array(); ?>
					<?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
					<?php echo join(',', $role_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>
