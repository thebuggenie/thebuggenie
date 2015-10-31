<?php

    \thebuggenie\core\framework\Context::loadLibrary('ui');
    $assigned_users = $project->getAssignedUsers();
    $assigned_teams = $project->getAssignedTeams();

?>
<h4><?php echo __('Assigned users'); ?></h4>
<div id="no_project_team_users" style="padding-left: 5px;<?php if (count($assigned_users) > 0): ?> display: none;<?php endif; ?> padding-top: 3px; color: #AAA;"><?php echo __('There are no users assigned to this project'); ?></div>
<table cellpadding=0 cellspacing=0 width="100%">
    <tbody id="project_team_users">
        <?php foreach ($assigned_users as $user): ?>
            <tr id="assignee_user_<?php echo $user->getID(); ?>_row" class="hoverable">
                <td style="width: 20px;">
                    <a href="javascript:void(0);" id="assignee_user_<?php echo $user->getID(); ?>_link" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove %username from this project?', array('%username' => $user->getNameWithUsername())); ?>', '<?php echo __('Please confirm that you want to remove this user from the project team'); ?>', {yes: {click: function() {TBG.Project.removeAssignee('<?php echo make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => 'user', 'assignee_id' => $user->getID())); ?>', 'user', <?php echo $user->getID(); ?>);TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('action_delete.png'); ?></a>
                    <?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_user_'.$user->getID().'_indicator', 'style' => 'float: left; display: none;')); ?>
                </td>
                <td style="vertical-align: top; font-size: 0.9em; width: 250px;">
                    <?php echo include_component('main/userdropdown', array('user' => $user)); ?>
                </td>
                <td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
                    <?php $roles = $project->getRolesForUser($user); ?>
                    <?php $role_names = array(); ?>
                    <?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
                    <?php echo join(', ', $role_names); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h4><?php echo __('Assigned teams'); ?></h4>
<div id="no_project_team_teams" style="padding-left: 5px;<?php if (count($assigned_teams) > 0): ?> display: none;<?php endif; ?> padding-top: 3px; color: #AAA;"><?php echo __('There are no teams assigned to this project'); ?></div>
<table cellpadding=0 cellspacing=0 width="100%">
    <tbody id="project_team_teams">
        <?php foreach ($assigned_teams as $team): ?>
            <tr id="assignee_team_<?php echo $team->getID(); ?>_row" class="hoverable">
                <td style="width: 20px;">
                    <a href="javascript:void(0);" id="assignee_team_<?php echo $team->getID(); ?>_link" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Remove %teamname from this project?', array('%teamname' => $team->getName())); ?>', '<?php echo __('Please confirm that you want to remove this team from the project team'); ?>', {yes: {click: function() {TBG.Project.removeAssignee('<?php echo make_url('configure_project_remove_assignee', array('project_id' => $project->getID(), 'assignee_type' => 'team', 'assignee_id' => $team->getID())); ?>', 'team', <?php echo $team->getID(); ?>);TBG.Main.Helpers.Dialog.dismiss();}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('action_delete.png'); ?></a>
                    <?php echo image_tag('spinning_16.gif', array('id' => 'remove_assignee_team_'.$team->getID().'_indicator', 'style' => 'float: left; display: none;')); ?>
                </td>
                <td style="vertical-align: top; font-size: 0.9em; width: 250px;">
                    <?php echo include_component('main/teamdropdown', array('team' => $team)); ?>
                </td>
                <td style="vertical-align: top; padding: 3px; font-size: 0.9em;">
                    <?php $roles = $project->getRolesForTeam($team); ?>
                    <?php $role_names = array(); ?>
                    <?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
                    <?php echo join(', ', $role_names); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
