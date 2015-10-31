<?php

    $tbg_response->addBreadcrumb(__('Team overview'), make_url('project_team', array('project_key' => $selected_project->getKey())));
    $tbg_response->setTitle(__('"%project_name" project team', array('%project_name' => $selected_project->getName())));
    include_component('project/projectheader', array('selected_project' => $selected_project, 'subpage' => __('Team')));

?>
<div id="project_team" class="project_info_container">
    <div id="project_team_container">
        <div style="width: auto;" id="project_team_overview">
            <div class="project_team_list_container">
                <?php if (count($assigned_users) == 0): ?>
                    <div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no users assigned to this project'); ?></div>
                <?php else: ?>
                    <h3><?php echo __('Assigned users'); ?></h3>
                    <ul class="project_team_list usercard users">
                        <?php foreach ($assigned_users as $user): ?>
                            <li>
                                <div style="padding: 2px; width: 48px; height: 48px; text-align: center; background-color: #FFF; border: 1px solid #DDD; float: left; margin-right: 5px;">
                                    <?php echo image_tag($user->getAvatarURL(false), array('alt' => ' ', 'style' => "width: 48px; height: 48px;"), true); ?>
                                </div>
                                <div class="user_realname">
                                    <a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?>');"><?php echo $user->getRealname(); ?> <span class="user_username"><?php echo $user->getUsername(); ?></span></a>
                                    <div class="user_status"><?php echo tbg_get_userstate_image($user) . __($user->getState()->getName()); ?></div>
                                    <?php if ($user->isEmailPublic() || $tbg_user->canAccessConfigurationPage(\thebuggenie\core\framework\Settings::CONFIGURATION_SECTION_USERS)): ?>
                                        <div class="user_email"><?php echo link_tag('mailto:'.$user->getEmail(), $user->getEmail()); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="roles_list">
                                    <?php $roles = $selected_project->getRolesForUser($user); ?>
                                    <?php $role_names = array(); ?>
                                    <?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
                                    <?php echo join(', ', $role_names); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="project_team_list_container">
                <?php if (count($assigned_teams) == 0): ?>
                    <div style="padding: 5px; color: #AAA; font-size: 12px;"><?php echo __('There are no teams assigned to this project'); ?></div>
                <?php else: ?>
                    <h3><?php echo __('Assigned teams'); ?></h3>
                    <ul class="project_team_list teams">
                        <?php foreach ($assigned_teams as $team): ?>
                            <li>
                                <?php echo include_component('main/teamdropdown', array('team' => $team)); ?>
                                <div class="roles_list">
                                    <?php $roles = $selected_project->getRolesForTeam($team); ?>
                                    <?php $role_names = array(); ?>
                                    <?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
                                    <?php echo join(', ', $role_names); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <br style="clear: both;">
</div>
