<div class="backdrop_box large">
	<div class="backdrop_detail_header">
		<?php echo __('Update project icons'); ?>
	</div>
	<div id="backdrop_detail_content">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_projects_icons', array('project_id' => $project->getID())); ?>" method="post" id="build_form" onsubmit="$('update_icons_indicator').show();return true;" enctype="multipart/form-data">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="width: 460px; padding-right: 10px;">
						<h4><?php echo __('Small icon'); ?></h4>
						<div style="text-align: center; padding: 30px; height: 60px;">
							<?php echo image_tag($project->getSmallIconName(), array('style' => 'width: 16px; height: 16px;'), $project->hasSmallIcon()); ?>
						</div>
						<div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
							<ul class="simple_list" style="margin-top: 0;">
								<li><input type="radio" id="small_no_change" name="small_icon_action" value="0"<?php if (!$project->hasSmallIcon()) echo ' checked'; ?>><label for="small_no_change"><?php echo __('Leave as is %no_icon%', array('%no_icon%' => '<span class="faded_out">('.__('Generic project icon').')</span>')).'</span>'; ?></label></li>
								<?php if ($project->hasSmallIcon()): ?>
									<li><input type="radio" id="small_clear_icon" name="small_icon_action" value="clear_file" checked><label for="small_clear_icon"><?php echo __('Clear small icon'); ?></label></li>
								<?php endif; ?>
								<?php if (TBGSettings::isUploadsEnabled()): ?>
									<li><input type="radio" id="small_upload" name="small_icon_action" value="upload_file"><label for="small_upload"><?php echo __('Upload new icon'); ?>:</label><br><input type="file" name="small_icon"></li>
								<?php else: ?>
									<li class="faded_out" style="padding: 2px; font-style: italic;"><?php echo __('Enable file uploads to upload project icons'); ?></li>
								<?php endif; ?>
							</ul>
						</div>
					</td>
					<td style="width: 460px;">
						<h4><?php echo __('Large icon'); ?></h4>
						<div style="text-align: center; padding: 30px; height: 60px;">
							<?php echo image_tag($project->getLargeIconName(), array('style' => 'width: 32px; height: 32px;'), $project->hasLargeIcon()); ?>
						</div>
						<div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
							<ul class="simple_list" style="margin-top: 0;">
								<li><input type="radio" id="large_no_change" name="large_icon_action" value="0"<?php if (!$project->hasLargeIcon()) echo ' checked'; ?>><label for="large_no_change"><?php echo __('Leave as is %no_icon%', array('%no_icon%' => '<span class="faded_out">('.__('Generic project icon').')</span>')).'</span>'; ?></label></li>
								<?php if ($project->hasLargeIcon()): ?>
									<li><input type="radio" id="large_clear_icon" name="large_icon_action" value="clear_file" checked><label for="large_clear_icon"><?php echo __('Clear large icon'); ?></label></li>
								<?php endif; ?>
								<?php if (TBGSettings::isUploadsEnabled()): ?>
									<li><input type="radio" id="large_upload" name="large_icon_action" value="upload_file"><label for="large_upload"><?php echo __('Upload new icon'); ?>:</label><br><input type="file" name="large_icon"></li>
								<?php else: ?>
									<li class="faded_out" style="padding: 2px; font-style: italic;"><?php echo __('Enable file uploads to upload project icons'); ?></li>
								<?php endif; ?>
							</ul>
						</div>
					</td>
				</tr>
			</table>
			<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="2" style="padding: 10px 0 10px 10px; text-align: right;">
						<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation">
							<?php echo __('When you are done, click "%update_icons%" to upload the new project icons', array('%update_icons%' => __('Update icons'))); ?>
						</div>
						<input class="button button-green" style="float: right;" type="submit" value="<?php echo __('Update icons'); ?>">
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