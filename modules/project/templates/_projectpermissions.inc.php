<div class="permission_list" id="project_settings_roles">
	<h3>
		<div class="button button-green" style="float: right;" onclick="$('project_settings_roles').toggle();$('project_settings_advanced_permissions').toggle();"><?php echo __('Switch to advanded permissions'); ?></div>
		<?php echo __('Editing project roles and permissions'); ?>
	</h3>
	<div class="content faded_out">
		<p><?php echo __("These roles acts as permission templates and can be applied when assigning people (or teams) to the project. When people (or teams) are unassigned from the project they will keep all permissions applied by any roles until their last role in the project is unassigned. Read more about roles and permissions in the %online_documentation%", array('%online_documentation%' => link_tag('http://issues.thebuggenie.com/wiki/TheBugGenie:RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?></p>
	</div>
	<ul id="roles_list" class="simple_list" style="margin-top: 10px;">
		<?php foreach (TBGRole::getAll() as $role): ?>
			<?php include_template('configuration/role', array('role' => $role)); ?>
		<?php endforeach; ?>
		<?php foreach (TBGRole::getByProjectID($project->getID()) as $role): ?>
			<?php include_template('configuration/role', array('role' => $role)); ?>
		<?php endforeach; ?>
	</ul>
</div>
<div class="permission_list" id="project_settings_advanced_permissions" style="display: none;">
	<h3>
		<div class="button button-green" style="float: right;" onclick="$('project_settings_roles').toggle();$('project_settings_advanced_permissions').toggle();"><?php echo __('Switch to role-based permissions'); ?></div>
		<?php echo __('Editing advanced project permissions'); ?>
	</h3>
	<div class="content faded_out">
		<p><?php echo __('These permissions directly control what you can do, and which pages you can access in The Bug Genie - on a project-specific basis. Some of these permissions are also available as site-wide permissions in the %permissions_configuration% page. You may want to use roles and assignments instead of applying these permissions directly.', array('%permissions_configuration%' => '<b>'.link_tag(make_url('configure_permissions'), __('permissions configuration')).'</b>')); ?></p>
	</div>
	<ul id="project_permission_details_<?php echo $project->getID(); ?>" style="margin-top: 10px;">
		<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_project_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_page_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 0 . 'project_' . $project->getID() . '_issue_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
	</ul>
</div>