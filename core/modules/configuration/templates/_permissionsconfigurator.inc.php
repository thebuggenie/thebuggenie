<div style="margin: 10px 0 0 0; clear: both;" class="tab_menu inset">
    <ul id="permissions_<?php echo $mode; ?>_<?php echo $base_id; ?>_tabs">
        <li class="selected" id="<?php echo $mode.'_'.$base_id; ?>_tab_general"><a onclick="TBG.Main.Helpers.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_general', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);"><?php echo __('General permissions'); ?></a></li>
        <li id="<?php echo $mode.'_'.$base_id; ?>_tab_pages"><a onclick="TBG.Main.Helpers.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_pages', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);"><?php echo __('Page access permissions'); ?></a></li>
        <li id="<?php echo $mode.'_'.$base_id; ?>_tab_projects"><a onclick="TBG.Main.Helpers.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_projects', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);"><?php echo __('Project-specific permissions'); ?></a></li>
        <li id="<?php echo $mode.'_'.$base_id; ?>_tab_modules"><a onclick="TBG.Main.Helpers.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_modules', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);"><?php echo __('Module-specific permissions'); ?></a></li>
    </ul>
</div>
<div id="permissions_<?php echo $mode; ?>_<?php echo $base_id; ?>_tabs_panes" class="permission_list">
    <div id="<?php echo $mode.'_'.$base_id; ?>_tab_general_pane" class="tab_pane">
        <p><?php echo __('These permissions control what you can do in The Bug Genie. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions" tab.', array('%project_specific_permissions' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
        <ul>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'general_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('general'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'user_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('user'), 'mode' => 'user', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'issues_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'project_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
        </ul>
    </div>
    <div id="<?php echo $mode; ?>_<?php echo $base_id; ?>_tab_pages_pane" class="tab_pane" style="display: none;">
        <p><?php echo __('These permissions control which pages you can access in The Bug Genie. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions" tab.', array('%project_specific_permissions' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
        <ul>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'page_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('pages'), 'mode' => 'pages', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'configuration_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('configuration'), 'mode' => 'configuration', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'project_page_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
        </ul>
    </div>
    <div id="<?php echo $mode; ?>_<?php echo $base_id; ?>_tab_modules_pane" class="tab_pane" style="display: none;">
        <p><?php echo __('Module-specific permissions are also available from the "%configure_modules" configuration page', array('%configure_modules' => link_tag(make_url('configure_modules'), __('Configure modules')))); ?></p>
        <ul>
        <?php foreach (\thebuggenie\core\framework\Context::getModules() as $module_key => $module): ?>
            <li>
                <a href="javascript:void(0);" onclick="$('<?php echo $mode.'_'.$base_id; ?>_module_permission_details_<?php echo $module_key; ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $module->getLongName(); ?> <span class="faded_out smaller"><?php echo $module_key; ?></span></a>
                <ul style="display: none;" id="<?php echo $mode.'_'.$base_id; ?>_module_permission_details_<?php echo $module_key; ?>">
                    <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'module_' . $module_key . '_permissions', 'permissions_list' => $module->getAvailablePermissions(), 'mode' => 'module_permissions', 'target_id' => 0, 'module' => $module_key, 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
                </ul>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div id="<?php echo $mode.'_'.$base_id; ?>_tab_projects_pane" class="tab_pane" style="display: none;">
        <p><?php echo __('These permissions control what you can do, and which pages you can access in The Bug Genie - on a project-specific basis. Some of these permissions are also available as site-wide permissions, from the "%general_permissions" tab.', array('%general_permissions' => '<i>'.__('General permissions').'</i>')); ?></p>
        <?php if (count(\thebuggenie\core\entities\Project::getAll()) > 0): ?>
            <ul>
                <?php foreach (\thebuggenie\core\entities\Project::getAll() as $project): ?>
                    <li>
                        <a href="javascript:void(0);" onclick="$('<?php echo $base_id; ?>_project_permission_details_<?php echo $project->getID(); ?>').toggle();"><?php echo image_tag('expand_small.png', array('style' => 'float: left; margin-right: 5px; margin-top: 2px;')); ?><?php echo $project->getName(); ?> <span class="faded_out smaller"><?php echo $project->getKey(); ?></span></a>
                        <ul style="display: none;" id="<?php echo $base_id; ?>_project_permission_details_<?php echo $project->getID(); ?>">
                            <?php include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_project_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                            <?php include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_page_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                            <?php include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_issue_permissions', 'permissions_list' => \thebuggenie\core\framework\Context::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="faded_out" style="padding: 2px;"><?php echo __('There are no projects'); ?></div>
        <?php endif; ?>
    </div>
</div>
