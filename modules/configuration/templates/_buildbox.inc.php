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
