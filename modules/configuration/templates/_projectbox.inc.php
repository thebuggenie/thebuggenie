<?php TBGContext::loadLibrary('ui'); ?>
<div class="rounded_box round_canhover lightgrey projectbox" style="margin: 10px 0px 10px 0px; width: 690px;">
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
	<table cellpadding=0 cellspacing=0 style="width: 680px; table-layout: auto;">
	<tr>
	<td style="padding-left: 3px; width: 80px;"><b><?php echo __('Owner: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
	<td style="padding-left: 3px; width: auto;">
		<?php if ($project->getOwner() != null): ?>
			<?php if ($project->getOwnerType() == TBGIdentifiableClass::TYPE_USER): ?>
				<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
			<?php elseif ($project->getOwnerType() == TBGIdentifiableClass::TYPE_TEAM): ?>
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
		<?php if (!$project->isEditionsEnabled() && $project->isBuildsEnabled() && !$project->isArchived()): ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><strong><?php echo javascript_link_tag(image_tag('cfg_icon_builds.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'section' => 'hierarchy', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></strong></span>
			</div>
		<?php endif; ?>
			<?php if (!$project->isArchived()): ?>
				<div style="float: right;">
					<span style="margin-right: 10px;"><strong><?php echo javascript_link_tag(image_tag('cfg_icon_projectsettings.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project') : __('Show project details')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></strong></span>
				</div>
			<?php endif; ?>
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<?php
				if ($project->isArchived())
				{
					?>
				<div style="float: right; margin-right: 10px; display: none;" id="project_<?php echo $project->getID(); ?>_unarchive_indicator"><?php echo image_tag('spinning_16.gif'); ?></div><div style="float: right;" id="project_<?php echo $project->getID(); ?>_unarchive"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="TBG.Project.unarchive('<?php echo make_url('configure_project_unarchive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>)"><?php echo image_tag('icon_project_unarchive.png', array('title' => __('Unarchive project'), 'style' => 'float: left; margin-right: 5px;')) . __('Unarchive');?></a></span></div>
					<?php
				}
				else
				{
					?>
				<div style="float: right; margin-right: 10px; display: none;" id="project_<?php echo $project->getID(); ?>_archive_indicator"><?php echo image_tag('spinning_16.gif'); ?></div><div style="float: right;" id="project_<?php echo $project->getID(); ?>_archive"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="TBG.Main.Helpers.Dialog.show('<?php echo __('Archive this project?'); ?>', '<?php echo __('If you archive a project, it is placed into a read only mode, where the project and its issues can no longer be edited. This will also prevent you from creating new issues, and will hide it from project lists (it can be viewed from an Archived Projects list). This will not, however, affect any subprojects this one has.').'<br>'.__('If you need to reactivate this subproject, you can do this from projects configuration.'); ?>', {yes: {click: function() {TBG.Project.archive('<?php echo make_url('configure_project_archive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>);}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo image_tag('icon_project_archive.png', array('title' => __('Archive project'), 'style' => 'float: left; margin-right: 5px;')) . __('Archive');?></a></span></div>
					<?php
				}
				?>
				<div style="float: right;"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').show();"><?php echo image_tag('icon_delete.png', array('title' => __('Delete project'), 'style' => 'float: left; margin-right: 5px;')) . __('Delete');?></a></span></div>
			<?php endif; ?>
			<div style="float: right;">
				<span style="margin-right: 10px;"><?php echo javascript_link_tag(image_tag('cfg_icon_permissions.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions')), array('onclick' => "$('project_{$project->getID()}_permissions').toggle();", 'style' => 'font-size: 12px;')); ?></span>
			</div>
			<br style="clear: both;">
			<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
				<div id="project_delete_confirm_<?php echo($project->getID()); ?>" style="display: none; padding: 0 10px 5px 10px; margin-top: 5px;" class="rounded_box white shadowed">
					<h4><?php echo __('Really delete project?'); ?></h4>
					<span class="question_header"><?php echo __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?></span><br>
					<div style="text-align: right;" id="project_delete_controls_<?php echo($project->getID()); ?>"><a href="javascript:void(0)" class="xboxlink" onClick="TBG.Project.remove('<?php echo make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>)"><?php echo __('Yes'); ?></a> :: <a href="javascript:void(0)" class="xboxlink" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').hide();"><?php echo __('No'); ?></a></div>
					<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="project_delete_indicator_<?php echo($project->getID()); ?>">
						<tr>
							<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
							<td style="padding: 0px; text-align: left;"><?php echo __('Deleting project, please wait'); ?>...</td>
						</tr>
					</table>
					<div id="project_delete_error_<?php echo($project->getID()); ?>" style="display: none;"><b><?php echo __('System error when deleting project'); ?></b></div>
				</div>
			<?php endif; ?>
		<?php if ($project->hasEditions() && $project->isEditionsEnabled() && !$project->isArchived()): ?>
			<br style="clear: both;">
			<div class="config_header"><b><?php echo __('Project editions'); ?></b></div>
			<table cellpadding=0 cellspacing=0 style="width: 670px; table-layout: auto;">
			<?php foreach ($project->getEditions() as $edition): ?>
				<tr class="hover_highlight">
					<td style="width: auto; padding: 3px 0 3px 5px;">
						<div style="float: right;"><span style="margin-right: 10px;"><?php echo javascript_link_tag(image_tag('cfg_icon_builds.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'edition_id' => $edition->getID(), 'section' => 'releases', 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></span></div>
						<div style="float: right;"><span style="margin-right: 20px;"><?php echo javascript_link_tag(image_tag('cfg_icon_editiondetails.png', array('title' => (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit details') : __('Show details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == TBGSettings::ACCESS_FULL) ? __('Edit details') : __('Show details')), array('onclick' => "TBG.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'project_config', 'edition_id' => $edition->getID(), 'project_id' => $project->getID()))."');", 'style' => 'font-size: 12px;')); ?></span></div>
						<?php echo $edition->getName(); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</table>
		<?php endif; ?>
		</td>
	</tr>
	</table>
	<div class="rounded_box white shadowed config_permissions" id="project_<?php echo $project->getID(); ?>_permissions" style="display: none;">
		<?php include_template('configuration/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
	</div>
</div>