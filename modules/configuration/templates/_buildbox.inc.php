<?php $b_id = $build->getID(); ?>
<li id="show_build_<?php print $b_id; ?>" class="rounded_box invisible buildbox">
	<?php if ($tbg_user->canManageProjectReleases($build->getProject())): ?>
		<div class="build_buttons">
			<button class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_build', 'project_id' => $build->getProject()->getId(), 'build_id' => $build->getId())); ?>');"><?php echo __('Edit'); ?></button>
			<button class="button button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Delete this release?'); ?>', '<?php echo __('Do you really want to delete this release?').'<br>'.__('Deleting this release will make it unavailable for download, and remove it from any associated issue reports or feature requests.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {TBG.Project.Build.remove('<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'delete')); ?>', <?php print $b_id; ?>, '<?php echo ($build->isLocked()) ? "active" : "archived"; ?>');}}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});"><?php echo __('Delete'); ?></button>
		</div>
	<?php endif; ?>
	<?php echo image_tag('icon_build_medium.png', array('style' => 'float: left; margin: 3px 7px 0 0;')); ?>
	<span id="build_<?php echo $b_id; ?>name" class="build_name"><?php print $build->getName(); ?></span>
	<span class="faded_out">[<span id="build_<?php echo $b_id; ?>_version"><?php print $build->getVersion(); ?></span>]</span>
	<br>
	<div class="faded_out" style="font-size: 0.8em;">
		<?php if ($build->isReleased()): ?>
			<?php echo __('Released %release_date%', array('%release_date%' => '<span id="build_'.$b_id.'_release_date">'.tbg_formatTime($build->getReleaseDate(), 7).'</span>')); ?>
		<?php else: ?>
			<span class="faded_out" id="build_<?php echo $b_id; ?>_not_released"><?php echo __('Not released yet'); ?></span>
		<?php endif; ?>
		<?php if ($build->hasDownload()): ?>
			<?php echo __('%release_date%, download: %download_filename%', array('%release_date%' => '', '%download_filename%' => ($build->hasFile()) ? link_tag(make_url('downloadfile', array('id' => $build->getFile()->getID())), $build->getFile()->getOriginalFilename()) : link_tag($build->getFileURL()))); ?>
		<?php else: ?>
			<span class="faded_out" id="build_<?php echo $b_id; ?>_not_released"><?php echo __('%release_date%, no download available', array('%release_date%' => '')); ?></span>
		<?php endif; ?>
	</div>
</li>
<?php /* if ($tbg_user->canManageProjectReleases($build->getProject())): ?>
	<tr id="edit_build_<?php print $b_id; ?>" class="selected_green" style="display: none;">
		<td style="width: 20px; padding: 2px; padding-top: 10px;" valign="top"><?php echo image_tag('icon_edit_build.png'); ?></td>
		<td style="width: auto; padding: 2px;" colspan="2">
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'update')); ?>" method="post" id="edit_build_<?php print $b_id; ?>_form" onsubmit="TBG.Project.Build.update('<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'update')); ?>', <?php echo $b_id; ?>);return false;">
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
					<tr>
						<td style="width: 120px;"><label for="build_name_<?php echo $b_id; ?>"><?php echo __('Build / release name'); ?>:</label></td>
						<td style="width: auto;"><input type="text" name="build_name" name="build_name_<?php echo $b_id; ?>" style="width: 300px;" value="<?php print $build->getName(); ?>"></td>
						<td style="width: 100px; text-align: right;"><label for="ver_mj_<?php echo $b_id; ?>"><?php echo __('Ver: %version_number%', array('%version_number%' => '')); ?></label></td>
						<td style="width: 120px; text-align: right;"><input type="text" name="ver_mj" id="ver_mj_<?php echo $b_id; ?>" style="width: 25px; text-align: center;" value="<?php print $build->getVersionMajor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_mn" style="width: 25px; text-align: center;" value="<?php print $build->getVersionMinor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_rev" style="width: 25px; text-align: center;" value="<?php print $build->getVersionRevision(); ?>"></td>
					</tr>
					<tr>
					<td><label for="release_month_<?echo $b_id; ?>"><?php echo __('Release date'); ?>:</label></td>
					<td style="text-align: left;">
						<select style="width: 85px;" name="release_month" id="release_month_<?php print $b_id; ?>"<?php if (!$build->isReleased()): ?> disabled<?php endif; ?>>
						<?php for($cc = 1;$cc <= 12;$cc++): ?>
							<option value=<?php print $cc; ?><?php echo ($build->getReleaseDateMonth() == $cc) ? " selected" : "" ?>><?php echo tbg_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
						<?php endfor; ?>
						</select>
						<select style="width: 40px;" name="release_day" id="release_day_<?php print $b_id; ?>"<?php if (!$build->isReleased()): ?> disabled<?php endif; ?>>
						<?php for($cc = 1;$cc <= 31;$cc++): ?>
							<option value=<?php print $cc; ?><?php echo ($build->getReleaseDateDay() == $cc) ? " selected" : "" ?>><?php echo $cc; ?></option>
						<?php endfor; ?>
						</select>
						<select style="width: 55px;" name="release_year" id="release_year_<?php print $b_id; ?>"<?php if (!$build->isReleased()): ?> disabled<?php endif; ?>>
						<?php for($cc = 2000;$cc <= (date("Y") + 5);$cc++): ?>
							<option value=<?php print $cc; ?><?php echo ($build->getReleaseDateYear() == $cc) ? " selected" : "" ?>><?php echo $cc; ?></option>
						<?php endfor; ?>
						</select>
					</td>
					<td colspan="2" style="padding-top: 2px; text-align: right;">
						<a href="javascript:void(0);" onclick="$('edit_build_<?php print $b_id; ?>').hide();$('show_build_<?php print $b_id; ?>').show();" style="font-size: 12px;"><?php echo __('Cancel'); ?></a>
						&nbsp;&nbsp;<?php echo __('%cancel% or %save%', array('%save%' => '', '%cancel%' => '')); ?>&nbsp;&nbsp;
						<input type="submit" value="<?php echo __('Save changes'); ?>">
					</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
	<tr id="addtoopen_build_<?php print $b_id; ?>" style="display: none; background-color: #F5F5F5;">
		<td colspan=3 style="border-top: 1px solid #DDD; border-bottom: 1px solid #DDD; background-color: #F1F1F1; padding: 5px;">
			<strong><?php echo __('Please specify and confirm'); ?></strong><br>
			<?php echo __('You can specify a selection of issues to be updated, from the choices below'); ?>.
			<?php if ($build->isProjectBuild()): ?>
				<?php echo __('This build / release will then be added to the list of affected builds / releases on all open issues for this project'); ?>
			<?php else: ?>
				<?php echo __('This build / release will then be added to the list of affected builds / releases on all open issues for this edition'); ?>
			<?php endif; ?>.
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'addtoopen')); ?>" method="post" id="add_to_open_build_<?php print $b_id; ?>_form" onsubmit="addToOpenBuild('<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'addtoopen')); ?>', <?php echo $b_id; ?>);return false;">
				<table cellpadding=0 cellspacing=0 style="width: 100%;" class="padded_table">
					<tr>
						<td style="width: auto; margin-right: 10px; text-align: right;"><label for="build_<?php echo $b_id; ?>_status"><?php echo __('Status'); ?></label></td>
						<td style="width: auto;">
							<select name="status" id="build_<?php echo $b_id; ?>_status">
								<option value="" selected><?php echo __('All statuses'); ?></option>
								<?php foreach (TBGStatus::getAll() as $aStatus): ?>
									<option style="color: <?php echo $aStatus->getItemdata(); ?>" value=<?php echo $aStatus->getID(); ?>><?php echo $aStatus->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td style="width: auto; margin-right: 10px; text-align: right;"><label for="build_<?php echo $b_id; ?>_category"><?php echo __('Category'); ?></label></td>
						<td style="width: auto;">
							<select name="category" id="build_<?php echo $b_id; ?>_category">
								<option value="" selected><?php echo __('All categories'); ?></option>
								<?php foreach (TBGCategory::getAll() as $aCategory): ?>
									<option value=<?php echo $aCategory->getID(); ?>><?php echo $aCategory->getName(); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td style="width: auto; margin-right: 10px; text-align: right;"><label for="build_<?php echo $b_id; ?>_issuetype"><?php echo __('Issue type'); ?></label></td>
						<td style="width: auto;">
							<select name="issuetype" id="build_<?php echo $b_id; ?>_issuetype">
								<option value="" selected><?php echo __('All issue types'); ?></option>
								<?php if ($build->getProject() instanceof TBGProject): ?>
									<?php foreach (TBGIssuetype::getAll($build->getProject()->getID()) as $anIssuetype): ?>
										<option value=<?php echo $anIssuetype->getID(); ?>><?php echo $anIssuetype->getName(); ?></option>
									<?php endforeach; ?>
								<?php else: ?>
									<?php TBGLogging::log('Build ' . $build->getID() . ' does not belong to a project!', 'main', TBGLogging::LEVEL_WARNING_RISK); ?>
								<?php endif; ?>
							</select>
						</td>
					</tr>
				</table>
				<div style="text-align: right; padding: 3px;">
					<a href="javascript:void(0);" onclick="$('show_build_<?php print $b_id; ?>').removeClassName('selected_green');$('show_build_<?php print $b_id; ?>').addClassName('hover_highlight');$('addtoopen_build_<?php print $b_id; ?>').hide();" style="font-size: 12px;"><?php echo __('Cancel'); ?></a>
					&nbsp;&nbsp;<?php echo __('%cancel% or %save%', array('%save%' => '', '%cancel%' => '')); ?>&nbsp;&nbsp;
					<input type="submit" value="<?php echo __('Add to open issues'); ?>">
				</div>
			</form>
		</td>
	</tr>
	<tr id="del_build_<?php print $b_id; ?>" class="selected_red" style="display: none;">
		<td colspan=3 style="border-bottom: 1px solid #E55; padding: 5px; height: 33px; font-size: 12px;">
			<div style="float: right;">
				<a href="javascript:void(0);" onclick="$('show_build_<?php print $build->getID(); ?>').removeClassName('selected_red');$('show_build_<?php print $build->getID(); ?>').addClassName('hover_highlight');$('del_build_<?php print $b_id; ?>').hide();"><b><?php echo __('Cancel'); ?></b></a>
				&nbsp;<?php echo __('%cancel% or %delete%', array('%delete%' => '', '%cancel%' => '')); ?>&nbsp;
				<button onclick="TBG.Project.Build.remove('<?php echo make_url('configure_build_action', array('build_id' => $b_id, 'build_action' => 'delete')); ?>', <?php print $b_id; ?>);" style="font-size: 11px;"><?php echo __('Delete it'); ?></button>
			</div>
			<span style="padding-top: 2px; float: left;"><?php echo __('Please confirm that you really want to delete this build / release'); ?></span>
		</td>
	</tr>
	<tr id="build_<?php echo $build->getID(); ?>_permissions" style="display: none;">
		<td colspan="3">
			<div class="rounded_box white" style="margin: 5px 0 10px 0; padding: 3px; font-size: 12px;">
				<div class="header"><?php echo __('Permission details for "%itemname%"', array('%itemname%' => $build->getName())); ?></div>
				<div class="content">
					<?php echo __('Specify who can access this release'); ?>
					<?php include_component('configuration/permissionsinfo', array('key' => 'canseebuild', 'mode' => 'project_hierarchy', 'target_id' => $build->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
				</div>
			</div>
		</td>
	</tr>	
<?php endif; */ ?>