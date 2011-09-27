<div id="project_config_menu_panes">
	<div id="tab_information_pane"<?php if ($selected_tab != 'info'): ?> style="display: none;"<?php endif; ?>>
		<?php include_component('configuration/projectinfo', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_settings_pane"<?php if ($selected_tab != 'settings'): ?> style="display: none;"<?php endif; ?>>
		<?php include_component('configuration/projectsettings', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_hierarchy_pane"<?php if ($selected_tab != 'hierarchy'): ?> style="display: none;"<?php endif; ?>>
		<?php include_template('configuration/projecthierarchy', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_developers_pane"<?php if ($selected_tab != 'developers'): ?> style="display: none;"<?php endif; ?>>
		<?php include_template('configuration/projectdevelopers', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_other_pane"<?php if ($selected_tab != 'other'): ?> style="display: none;"<?php endif; ?>>
		<?php include_template('configuration/projectother', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_permissions_pane" style="text-align: left;<?php if ($selected_tab != 'permissions'): ?> display: none;<?php endif; ?>" class="permission_list" style="">
		<?php include_template('configuration/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<?php TBGEvent::createNew('core', 'config_project_panes')->trigger(array('selected_tab' => $selected_tab, 'access_level' => $access_level, 'project' => $project)); ?>
</div>