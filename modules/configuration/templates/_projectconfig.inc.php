<div class="rounded_box white borderless shadowed backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Configure project'); ?>
	</div>
	<div class="backdrop_detail_content">
		<div class="tab_menu">
			<ul id="project_config_menu">
				<li id="tab_information" class="selected"><?php echo javascript_link_tag(image_tag('icon_edit.png', array('style' => 'float: left;')).__('Information'), array('onclick' => "switchSubmenuTab('tab_information', 'project_config_menu');")); ?></li>
				<li id="tab_settings"><?php echo javascript_link_tag(image_tag('cfg_icon_projectsettings.png', array('style' => 'float: left;')).__('Settings'), array('onclick' => "switchSubmenuTab('tab_settings', 'project_config_menu');")); ?></li>
				<li id="tab_hierarchy"><?php echo javascript_link_tag(image_tag('cfg_icon_projecteditionsbuilds.png', array('style' => 'float: left;')).__('Hierarchy'), array('onclick' => "switchSubmenuTab('tab_hierarchy', 'project_config_menu');")); ?></li>
				<li id="tab_milestones"><?php echo javascript_link_tag(image_tag('icon_milestones.png', array('style' => 'float: left;')).__('Milestones'), array('onclick' => "switchSubmenuTab('tab_milestones', 'project_config_menu');")); ?></li>
				<li id="tab_users"><?php echo javascript_link_tag(image_tag('cfg_icon_project_devs.png', array('style' => 'float: left;')).__('Related users'), array('onclick' => "switchSubmenuTab('tab_users', 'project_config_menu');")); ?></li>
				<li id="tab_other"><?php echo javascript_link_tag(image_tag('cfg_icon_datatypes.png', array('style' => 'float: left;')).__('Other'), array('onclick' => "switchSubmenuTab('tab_other', 'project_config_menu');")); ?></li>
			</ul>
		</div>
		<div id="project_config_menu_panes">
			<div id="tab_information_pane">
				<?php include_template('configuration/projectinfo', array('access_level' => $access_level, 'project' => $project)); ?>
			</div>
			<div id="tab_settings_pane" style="display: none;">
				settings
			</div>
			<div id="tab_hierarchy_pane" style="display: none;">
				hierarchy
			</div>
			<div id="tab_milestones_pane" style="display: none;">
				milestones
			</div>
			<div id="tab_users_pane" style="display: none;">
				users
			</div>
			<div id="tab_other_pane" style="display: none;">
				other
			</div>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>