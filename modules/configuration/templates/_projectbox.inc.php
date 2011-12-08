<?php TBGContext::loadLibrary('ui'); ?>
<div class="rounded_box round_canhover lightgrey projectbox" style="margin: 10px 0px 10px 0px; width: 788px;">
	<div style="padding: 3px; font-size: 14px;">
		<?php if ($project->isArchived()): ?>
			<span class="faded_out"><?php echo __('ARCHIVED'); ?> </span>
		<?php endif; ?>
		<strong><?php echo link_tag(make_url('project_dashboard', array('project_key' => $project->getKey())), $project->getName()); ?></strong>&nbsp;(<?php echo $project->getKey(); ?>)
		<?php if ($project->usePrefix()): ?>
			&nbsp;-&nbsp;<i><?php echo $project->getPrefix(); ?></i>
		<?php endif; ?>
		<?php if ($project->hasParent()): ?>
			&nbsp;-&nbsp;<?php echo __('Subproject of'); ?> <i><?php echo $project->getParent()->getName(); ?></i>
		<?php endif; ?>
	</div>
	<table cellpadding=0 cellspacing=0 style="width: 778px; table-layout: auto;">
	<tr>
	<td style="padding-left: 3px; width: 80px;"><b><?php echo __('Owner: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
	<td style="padding-left: 3px; width: auto;">
		<?php if ($project->getOwner() != null): ?>
			<?php if ($project->getOwner() instanceof TBGUser): ?>
				<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
			<?php elseif ($project->getOwner() instanceof TBGTeam): ?>
				<?php echo include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
			<?php endif; ?>
		<?php else: ?>
			<div style="color: #AAA; padding: 2px; width: auto;"><?php echo __('None'); ?></div>
		<?php endif; ?>
	</td>
	</tr>
	<?php if ($project->hasDescription()): ?>
		<tr>
			<td colspan="2" style="padding: 3px;"><?php echo tbg_parse_text($project->getDescription()); ?></td>
		</tr>
	<?php endif; ?>
	<tr>
		<td colspan="2" style="border-top: 1px solid #DDD; padding: 5px; background-color: transparent;">
			<?php if (!$project->isArchived()): ?>
				<div style="float: right;">
					<span style="margin-right: 10px;"><strong><?php echo javascript_link_tag(image_tag('cfg_icon_projectsettings.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></strong></span>
				</div>
			<?php endif; ?>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php if ($project->isArchived()): ?>
					<div style="float: right; margin-right: 10px; display: none;" id="project_<?php echo $project->getID(); ?>_unarchive_indicator"><?php echo image_tag('spinning_16.gif'); ?></div><div style="float: right;" id="project_<?php echo $project->getID(); ?>_unarchive"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="TBG.Project.unarchive('<?php echo make_url('configure_project_unarchive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>)"><?php echo image_tag('icon_project_unarchive.png', array('title' => __('Unarchive project'), 'style' => 'float: left; margin-right: 5px;')) . __('Unarchive');?></a></span></div>
				<?php else: ?>
					<div style="float: right; margin-right: 10px; display: none;" id="project_<?php echo $project->getID(); ?>_archive_indicator"><?php echo image_tag('spinning_16.gif'); ?></div><div style="float: right;" id="project_<?php echo $project->getID(); ?>_archive"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="TBG.Main.Helpers.Dialog.show('<?php echo __('Archive this project?'); ?>', '<?php echo __('If you archive a project, it is placed into a read only mode, where the project and its issues can no longer be edited. This will also prevent you from creating new issues, and will hide it from project lists (it can be viewed from an Archived Projects list). This will not, however, affect any subprojects this one has.').'<br>'.__('If you need to reactivate this subproject, you can do this from projects configuration.'); ?>', {yes: {click: function() {TBG.Project.archive('<?php echo make_url('configure_project_archive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_project_archive.png', array('title' => __('Archive project'), 'style' => 'float: left; margin-right: 5px;')) . __('Archive');?></a></span></div>
				<?php endif; ?>
					<div style="float: right;"><span style="margin-right: 10px;"><a href="javascript:void(0)" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Really delete project?'); ?>', '<?php echo __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?>', {yes: {click: function() {TBG.Project.remove('<?php echo make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>); }}, no: { click: TBG.Main.Helpers.Dialog.dismiss }});"><?php echo image_tag('icon_delete.png', array('title' => __('Delete project'), 'style' => 'float: left; margin-right: 5px;')) . __('Delete');?></a></span></div>
			<?php endif; ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), array('onclick' => "$('project_{$project->getID()}_permissions').toggle();", 'style' => 'font-size: 12px;')); ?></span>
			</div>
			<br style="clear: both;">
		</td>
	</tr>
	</table>
	<div class="rounded_box white shadowed config_permissions" id="project_<?php echo $project->getID(); ?>_permissions" style="display: none;">
		<?php include_template('project/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
</div>