<div class="rounded_box white borderless shadowed backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Configure project'); ?>
	</div>
	<div class="backdrop_detail_content">
		<div class="tab_menu">
			<ul>
				<li class="selected"><?php echo link_tag(make_url('configure_project_settings', array('project_id' => $project->getID())), image_tag('cfg_icon_projectsettings.png', array('style' => 'float: left;')).__('Information &amp; settings')); ?></li>
				<li><?php echo link_tag(make_url('configure_project_editions_components', array('project_id' => $project->getID())), image_tag('cfg_icon_projecteditionsbuilds.png', array('style' => 'float: left;')).__('Editions, components &amp; releases')); ?></li>
				<li><?php echo link_tag(make_url('configure_project_milestones', array('project_id' => $project->getID())), image_tag('icon_milestones.png', array('style' => 'float: left;')).__('Milestones')); ?></li>
				<li><?php echo link_tag(make_url('configure_project_developers', array('project_id' => $project->getID())), image_tag('cfg_icon_project_devs.png', array('style' => 'float: left;')).__('Related users')); ?></li>
				<li><?php echo link_tag(make_url('configure_project_other', array('project_id' => $project->getID())), image_tag('cfg_icon_datatypes.png', array('style' => 'float: left;')).__('Other')); ?></li>
			</ul>
		</div>
		<div id="configure_project_content">
			<?php include_template('configuration/projectinfo', array('access_level' => $access_level, 'project' => $project)); ?>
		</div>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>