<?php BUGScontext::loadLibrary('ui'); ?>
<div class="rounded_box round_canhover" style="margin: 10px 0px 10px 0px; width: 700px;" id="project_box_<?php echo $project->getID();?>">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px 10px 5px 10px;">
		<div style="padding: 3px; font-size: 14px;">
			<strong><?php echo $project->getName(); ?></strong>&nbsp;(<?php echo $project->getKey(); ?>)
			<?php if ($project->usePrefix()): ?>
				&nbsp;-&nbsp;<i><?php echo $project->getPrefix(); ?></i>
			<?php endif; ?>
		</div>
		<table cellpadding=0 cellspacing=0 style="width: 680px; table-layout: auto;">
		<tr>
		<td style="padding-left: 3px; width: 80px;"><b><?php echo __('Owner: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
		<td style="padding-left: 3px; width: auto;">
			<?php if ($project->getOwner() != null): ?>
				<table cellpadding=0 cellspacing=0 width="100%">
					<?php if ($project->getOwnerType() == BUGSidentifiableclass::TYPE_USER): ?>
						<?php echo include_component('main/userdropdown', array('user' => $project->getOwner())); ?>
					<?php elseif ($project->getOwnerType() == BUGSidentifiableclass::TYPE_TEAM): ?>
						<?php echo include_component('main/teamdropdown', array('team' => $project->getOwner())); ?>
					<?php endif; ?>
				</table>
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
			<td colspan="2" style="border-top: 1px solid #DDD; padding: 5px; background-color: #F9F9F9;">
			<?php if (!$project->isEditionsEnabled() && $project->isBuildsEnabled()): ?>
				<div style="float: right;"><span style="margin-right: 10px;"><strong><?php echo link_tag(make_url('configure_project_editions_components', array('project_id' => $project->getID())), image_tag('cfg_icon_builds.png', array('title' => (($access_level == configurationActions::ACCESS_FULL) ? __('Manage releases') : __('Show releases')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == configurationActions::ACCESS_FULL) ? __('Manage releases') : __('Show releases'))); ?></strong></span></div>
			<?php endif; ?>
				<div style="float: right;"><span style="margin-right: 10px;"><strong><?php echo link_tag(make_url('configure_project_settings', array('project_id' => $project->getID())), image_tag('cfg_icon_projectsettings.png', array('title' => (($access_level == configurationActions::ACCESS_FULL) ? __('Edit project') : __('Show project details')), 'style' => 'float: left; margin-right: 5px;')) . (($access_level == configurationActions::ACCESS_FULL) ? __('Edit project') : __('Show project details'))); ?></strong></span></div>
				<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
					<div style="float: right;"><span style="margin-right: 10px;"><a href="javascript:void(0)" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').show();"><?php echo image_tag('icon_delete.png', array('title' => __('Delete project'), 'style' => 'float: left; margin-right: 5px;')) . __('Delete');?></a></span></div>
					<div id="project_delete_confirm_<?php echo($project->getID()); ?>" style="display: none;" class="rounded_box white">
						<br style="clear: both;"><br style="clear: both;">
						<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
						<div class="xboxcontent" style="padding: 0 10px 5px 10px;">
							<h4><?php echo __('Really delete project?'); ?></h4>
							<span class="xboxlarge"><?php echo __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?></span><br>
							<div style="text-align: right;" id="project_delete_controls_<?php echo($project->getID()); ?>"><a href="javascript:void(0)" class="xboxlink" onClick="removeProject('<?php echo make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>)"><?php echo __('Yes'); ?></a> :: <a href="javascript:void(0)" class="xboxlink" onClick="$('project_delete_confirm_<?php echo($project->getID()); ?>').hide();"><?php echo __('No'); ?></a></div>
							<table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="project_delete_indicator_<?php echo($project->getID()); ?>">
								<tr>
									<td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
									<td style="padding: 0px; text-align: left;"><?php echo __('Deleting project, please wait'); ?>...</td>
								</tr>
							</table>
							<div id="project_delete_error_<?php echo($project->getID()); ?>" style="display: none;"><b>System error when deleting project</b></div>
						</div>
						<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
					</div>
				<?php endif; ?>
			<?php if ($project->hasEditions() && $project->isEditionsEnabled()): ?>
				<br style="clear: both;">
				<div class="config_header noborder nobg"><b><?php echo __('Project editions'); ?></b></div>
				<table cellpadding=0 cellspacing=0 style="width: 670px; table-layout: auto;">
				<?php foreach ($project->getEditions() as $edition): ?>
					<tr class="canhover_dark">
						<td style="width: auto; padding: 3px 0 3px 5px;">
							<div style="float: right;"><span style="margin-right: 10px;"><?php echo link_tag(make_url('configure_project_edition', array('project_id' => $project->getID(), 'edition_id' => $edition->getID(), 'mode' => 'releases')), image_tag('cfg_icon_builds.png', array('title' => __('Manage edition releases'), 'style' => 'float: left; margin-right: 5px;')) . __('Manage releases')); ?></span></div>
							<div style="float: right;"><span style="margin-right: 20px;"><?php echo link_tag(make_url('configure_project_edition', array('project_id' => $project->getID(), 'edition_id' => $edition->getID(), 'mode' => 'general')), image_tag('cfg_icon_editiondetails.png', array('title' => __('Edit details'), 'style' => 'float: left; margin-right: 5px;')) . __('Edit details')); ?></span></div>
							<?php echo $edition->getName(); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php endif; ?>
			</td>
		</tr>
		</table>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>
<?php if (BUGScontext::getRequest()->isAjaxCall()): ?>
	<script type="text/javascript">new Effect.Pulsate('project_box_<?php echo $project->getID(); ?>');</script>
<?php endif; ?>
