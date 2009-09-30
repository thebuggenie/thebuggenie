<div style="background-color: #F5F5F5; padding: 5px; width: auto; border-bottom: 1px solid #DDD;"><b><?php echo __('STEP 4'); ?></b> - <?php echo __('attach files and / or add link(s)'); ?></div>
<?php

	if ($step1_set == false || $step2_set == false || $step3_set == false)
	{
		?>
		<div style="padding: 5px; background-color: #F9F9F9; color: #AAA;" id="step4_main_content"><?php echo __('Please complete all the above steps first'); ?></div>
		<?php
	}
	else
	{
		?>
		<div style="width: auto; padding: 5px;<?php print ($step4_set == true) ? " background-color: #F9F9F9;" : ""; ?>" id="step4_main_content">
			<div style="width: 330px; padding: 2px; float: left;">
				<div style="width: auto; padding: 2px; border-bottom: 1px solid #DDD;"><b><?php echo __('Attached links'); ?></b></div>
				<div id="step4_linklist"><?php require BUGScontext::getIncludePath() . 'include/report_issue/linklist.inc.php'; ?></div>
				<?php
		
				if ($step4_set == false)
				{
					?>
					<div style="margin-top: 10px; width: auto; padding: 2px; border-bottom: 1px solid #DDD;"><b><?php print (isset($_SESSION['rni_step4_links'])) ? __('Attach another link') : __('Attach a link'); ?></b></div>
					<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_step4_add_link" id="rni_step4_add_link" onsubmit="return false;">
					<table style="width: 100%;" cellpadding=0 cellspacing=0>
					<tr>
					<td style="width: 70px; padding: 2px;"><b><?php echo __('URL:'); ?></b></td>
					<td style="width: auto; padding: 2px;"><input type="text" name="rni_step4_link_url" style="width: 100%;"></td>
					</tr>
					<tr>
					<td style="width: 70px; padding: 2px;"><b><?php echo __('Description:'); ?></b></td>
					<td style="width: auto; padding: 2px;"><input type="text" name="rni_step4_link_desc" style="width: 100%;"></td>
					</tr>
					<tr>
					<td style="padding: 2px; text-align: right;" colspan=3><input type="submit" value="<?php echo __('Attach'); ?>" onclick="addLink();" style="width: 80px;"></td>
					</tr>
					</table>
					</form>
					<?php
				}
				
				?>
			</div>
			<div style="width: 330px; padding: 2px; float: left;">
				<div style="width: auto; padding: 2px; border-bottom: 1px solid #DDD;"><b><?php echo __('Attached files'); ?></b></div>
				<div id="step4_filelist"><?php require BUGScontext::getIncludePath() . 'include/report_issue/filelist.inc.php'; ?></div>
				<?php
		
				if ($step4_set == false)
				{
					if (BUGSsettings::get('enable_uploads'))
					{
						?>
						<div style="margin-top: 10px; width: auto; padding: 2px; border-bottom: 1px solid #DDD;"><b><?php print (isset($_SESSION['rni_step4_links'])) ? __('Attach another file') : __('Attach a file'); ?></b></div>
						<div style="padding: 3px; color: #AAA;">
						<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" enctype="multipart/form-data" action="reportissue.php" method="POST">
						<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo (BUGSsettings::get('max_file_size') * 1024 * 1024); ?>" />
						<table style="width: 100%;" cellpadding=0 cellspacing=0>
						<tr>
						<td style="width: 70px; padding: 2px;"><b><?php echo __('File:'); ?></b></td>
						<td style="width: auto; padding: 2px;"><input type="file" name="rni_step4_file" style="width: 100%;"></td>
						</tr>
						<tr>
						<td style="width: 70px; padding: 2px;"><b><?php echo __('Description:'); ?></b></td>
						<td style="width: auto; padding: 2px;"><input type="text" name="rni_step4_file_desc" style="width: 100%;"></td>
						</tr>
						<tr>
						<td style="padding: 2px;" colspan=2>
						<?php if (isset($upload_error)): ?>
							<div style="padding-bottom: 10px;"><b><?php echo __('There was an error with your upload: %error%', array('%error%' => $upload_error)); ?></b></div>
						<?php endif; ?>
						<?php echo __('You cannot upload files bigger than %max_size% MB', array('%max_size%' => '<b>' . BUGSsettings::get('max_file_size') . '</b>')); ?>.<br>
						<?php if (BUGSsettings::get('uploads_blacklist') && BUGSsettings::get('uploads_filetypes') != ''): ?>
							<?php echo __('Also remember that you can not upload these filetypes: %list%', array('%list%' => '<i>' . join(', ', explode(',', BUGSsettings::get('uploads_filetypes'))) . '</i>')); ?>
						<?php elseif (BUGSsettings::get('uploads_filetypes') != ''): ?>
							<?php echo __('Also remember that the only allowed filetypes are: %list%', array('%list%' => '<i>' . join(', ', explode(',', BUGSsettings::get('uploads_filetypes'))) . '</i>')); ?>
						<?php elseif (!BUGSsettings::get('uploads_blacklist')): ?>
							<?php echo __('Unfortunately, there are no allowed filetypes'); ?>.
						<?php endif; ?>
						</td>
						<tr>
						<td style="padding: 2px; text-align: right;" colspan=2><input type="submit" value="<?php echo __('Attach'); ?>" style="width: 80px;"></td>
						</tr>
						</table>
	    				</form>
						</div>
						<?php
					}
					else
					{
						?><div style="margin-top: 10px; width: auto; padding: 2px;"><?php echo __('File attachments are not enabled'); ?>.</div><?php
					}
				}
		
				?>
			</div>
			<div style="width: auto; padding-right: 2px; text-align: right; clear: both;">
				<?php
		
				if ($step4_set == false)
				{
					?>
					<button onclick="setStep(4);$('step4_button').hide();" id="step4_button"><?php echo __('Confirm'); ?></button>
					<?php
				}
				elseif ($step4_set == true)
				{
					?>
					<a href="javascript:void(0);" onclick="unsetStep(4);"><?php echo __('Click here to change your issue details'); ?></a>
					<?php
				}
	
				?>
			</div>
		</div>
		<?php
	}

?>