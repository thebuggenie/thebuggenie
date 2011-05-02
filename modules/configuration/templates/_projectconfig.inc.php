<div class="tab_menu">
	<ul id="project_config_menu">
		<li id="tab_information"<?php if ($selected_tab == 'info'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_edit.png', array('style' => 'float: left;')).__('Information'), array('onclick' => "switchSubmenuTab('tab_information', 'project_config_menu');")); ?></li>
		<li id="tab_settings"<?php if ($selected_tab == 'settings'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('cfg_icon_projectsettings.png', array('style' => 'float: left;')).__('Settings'), array('onclick' => "switchSubmenuTab('tab_settings', 'project_config_menu');")); ?></li>
		<li id="tab_hierarchy"<?php if ($selected_tab == 'hierarchy'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('cfg_icon_projecteditionsbuilds.png', array('style' => 'float: left;')).__('Hierarchy'), array('onclick' => "switchSubmenuTab('tab_hierarchy', 'project_config_menu');")); ?></li>
		<li id="tab_milestones"<?php if ($selected_tab == 'milestones'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('icon_milestones.png', array('style' => 'float: left;')).__('Milestones'), array('onclick' => "switchSubmenuTab('tab_milestones', 'project_config_menu');")); ?></li>
		<li id="tab_developers"<?php if ($selected_tab == 'developers'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('cfg_icon_project_devs.png', array('style' => 'float: left;')).__('Team'), array('onclick' => "switchSubmenuTab('tab_developers', 'project_config_menu');")); ?></li>
		<li id="tab_other"<?php if ($selected_tab == 'other'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('cfg_icon_datatypes.png', array('style' => 'float: left;')).__('Other'), array('onclick' => "switchSubmenuTab('tab_other', 'project_config_menu');")); ?></li>
		<li id="tab_permissions"<?php if ($selected_tab == 'permissions'): ?> class="selected"<?php endif; ?>><?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('style' => 'float: left;')).__('permissions'), array('onclick' => "switchSubmenuTab('tab_permissions', 'project_config_menu');")); ?></li>
	</ul>
</div>
<div id="project_config_menu_panes">
	<div id="tab_information_pane"<?php if ($selected_tab != 'info'): ?> style="display: none;"<?php endif; ?>>
		<?php include_template('configuration/projectinfo', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_settings_pane"<?php if ($selected_tab != 'settings'): ?> style="display: none;"<?php endif; ?>>
		<?php include_component('configuration/projectsettings', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_hierarchy_pane"<?php if ($selected_tab != 'hierarchy'): ?> style="display: none;"<?php endif; ?>>
		<?php include_template('configuration/projecthierarchy', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
	<div id="tab_milestones_pane"<?php if ($selected_tab != 'milestones'): ?> style="display: none;"<?php endif; ?>>
		<?php include_component('configuration/projectmilestones', array('access_level' => $access_level, 'project' => $project)); ?>
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
</div>