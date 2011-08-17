<div class="rounded_box white borderless shadowed backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Update project icons'); ?>
	</div>
	<div id="backdrop_detail_content">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_build', array('project_id' => $project->getID())); ?>" method="post" id="build_form" onsubmit="$('add_release_indicator').show();return true;" enctype="multipart/form-data">
			<h4><?php echo __('Small icon'); ?></h4>
			<div style="text-align: center; padding: 30px;">
				<?php echo image_tag($project->getIcon(false), array(), $project->hasIcon(), 'core', !$project->hasIcon()); ?>
			</div>
			<div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
				<ul class="simple_list" style="margin-top: 0;" id="edit_build_download_options">
					<li><input type="radio" id="download_none" name="download" value="0"<?php if (!$project->hasIcon()) echo ' checked'; ?>><label for="download_none"><?php echo __('Leave as is %no_icon%', array('%no_icon%' => '<span class="faded_out">('.__('no icon').')</span>')).'</span>'; ?></label></li>
					<?php if ($project->hasIcon()): ?>
						<li><input type="radio" id="download_leave_file" name="download" value="leave_file" checked><label for="download_leave_file"><?php echo __('Use existing file %filename%', array('%filename%' => '<span class="faded_out" style="font-weight: normal;">('.$build->getFile()->getOriginalFilename().')</span>')); ?></label></li>
					<?php endif; ?>
					<?php if (TBGSettings::isUploadsEnabled()): ?>
						<li><input type="radio" id="download_upload" name="download" value="upload_file"><label for="download_upload"><?php echo __('Upload file for download'); ?>:</label>&nbsp;<input type="file" name="upload_file"></li>
					<?php else: ?>
						<li class="faded_out"><input type="radio" disabled><label><?php echo __('Upload file for download'); ?></label>&nbsp;<?php echo __('File uploads are not enabled'); ?></li>
					<?php endif; ?>
					<li><input type="radio" id="download_url" name="download" value="url"<?php if ($build->hasFileURL()) echo ' checked'; ?>><label for="download_url"><?php echo __('Specify download URL'); ?>:</label>&nbsp;<input type="text" style="width: 300px;" name="file_url"></li>
				</ul>
			</div>
			<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
						<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation">
							<?php echo __('When you are done, click "%update_icons%" to upload the new project icons', array('%update_icons%' => __('Update icons'))); ?>
						</div>
						<div class="button button-green" style="float: right;">
							<input type="submit" value="<?php echo __('Update icons'); ?>">
						</div>
						<span id="update_icons_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="backdrop_detail_footer">
		<?php echo javascript_link_tag(__('Close popup'), array('onclick' => 'TBG.Main.Helpers.Backdrop.reset();')); ?>
	</div>
</div>