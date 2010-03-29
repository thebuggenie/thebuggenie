<div id="tab_general_pane" class="tab_pane">
	<p><?php echo __('These permissions control what you can do in The Bug Genie. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions%" tab.', array('%project_specific_permissions%' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
	<ul>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'general_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('general'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'user_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('user'), 'mode' => 'user', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'issues_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'project_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
	</ul>
</div>
<div id="tab_pages_pane" class="tab_pane" style="display: none;">
	<p><?php echo __('These permissions control which pages you can access in The Bug Genie. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions%" tab.', array('%project_specific_permissions%' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
	<ul>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'page_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('pages'), 'mode' => 'pages', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'configuration_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('configuration'), 'mode' => 'configuration', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
		<?php include_template('configuration/permissionsblock', array('base_id' => 'project_page_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => 0, 'module' => 'core', 'access_level' => $access_level)); ?>
	</ul>
</div>
<div id="tab_modules_pane" class="tab_pane" style="display: none;">
	<p><?php echo __('Module-specific permissions are also available from the "%configure_modules%" configuration page', array('%configure_modules%' => link_tag(make_url('configure_modules'), __('Configure modules')))); ?></p>
	<ul>
	<?php foreach (TBGContext::getModules() as $module_key => $module): ?>
		<li>
			<a href="javascript:void(0);" onclick="$('module_permission_details_<?php echo $module_key; ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $module->getLongName(); ?> <span class="faded_medium smaller"><?php echo $module_key; ?></span></a>
			<ul style="display: none;" id="module_permission_details_<?php echo $module_key; ?>">
				<?php include_template('configuration/permissionsblock', array('base_id' => 'module_' . $module_key . '_permissions', 'permissions_list' => $module->getAvailablePermissions(), 'mode' => 'module_permissions', 'target_id' => 0, 'module' => $module_key, 'access_level' => $access_level)); ?>
			</ul>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<div id="tab_projects_pane" class="tab_pane" style="display: none;">
	<p><?php echo __('These permissions control what you can do, and which pages you can access in The Bug Genie - on a project-specific basis. Some of these permissions are also available as site-wide permissions, from the "%general_permissions%" tab.', array('%general_permissions%' => '<i>'.__('General permissions').'</i>')); ?></p>
	<?php if (count(TBGProject::getAll()) > 0): ?>
		<ul>
			<?php foreach (TBGProject::getAll() as $project): ?>
				<li>
					<a href="javascript:void(0);" onclick="$('project_permission_details_<?php echo $project->getID(); ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $project->getName(); ?> <span class="faded_medium smaller"><?php echo $project->getKey(); ?></span></a>
					<ul style="display: none;" id="project_permission_details_<?php echo $project->getID(); ?>">
						<?php include_template('configuration/permissionsblock', array('base_id' => 'project_' . $project->getID() . '_project_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
						<?php include_template('configuration/permissionsblock', array('base_id' => 'project_' . $project->getID() . '_page_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
						<?php include_template('configuration/permissionsblock', array('base_id' => 'project_' . $project->getID() . '_issue_permissions', 'permissions_list' => TBGContext::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="faded_medium" style="padding: 2px;"><?php echo __('There are no projects'); ?></div>
	<?php endif; ?>
</div>