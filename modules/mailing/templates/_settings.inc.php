<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div style="margin-top: 5px; width: 750px; clear: both; height: 30px;" class="tab_menu">
	<ul id="mailing_settings_menu">
		<li class="selected" id="tab_outgoing_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_outgoing_settings', 'mailing_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_general.png', array('style' => 'float: left;')).__('Outgoing settings'); ?></a></li>
		<li id="tab_incoming_settings"><a onclick="TBG.Main.Helpers.tabSwitcher('tab_incoming_settings', 'mailing_settings_menu');" href="javascript:void(0);"><?php echo image_tag('cfg_icon_server.png', array('style' => 'float: left;')).__('Incoming settings'); ?></a></li>
	</ul>
</div>
<div id="mailing_settings_menu_panes">
	<div id="tab_outgoing_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<div class="content" style="padding-bottom: 10px;"><?php echo __('These are the settings for outgoing emails, such as notification emails and registration emails.'); ?></div>
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0 id="mailnotification_settings_table">
			<tr>
				<td style="width: 200px; padding: 5px;"><label for="enable_outgoing_notifications"><?php echo __('Enable outgoing email notifications'); ?></label></td>
				<td style="width: auto;">
					<select name="enable_outgoing_notifications" id="enable_outgoing_notifications" onchange="if ($(this).getValue() == 0) { $('mailnotification_settings_table').select('input').each(function (element, index) { element.disable(); }); } else { $('mailnotification_settings_table').select('input').each(function (element, index) { element.enable(); }); }">
						<option value="1"<?php if ($module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('Yes'); ?></option>
						<option value="0"<?php if (!$module->isOutgoingNotificationsEnabled()): ?> selected<?php endif; ?>><?php echo __('No'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="activation_needed"><?php echo __("Require email activation for new accounts"); ?></label></td>
				<td><input type="checkbox" name="activation_needed" id="activation_needed" value="1" <?php if ($module->isActivationNeeded()): ?>checked<?php endif; ?> style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __("If this option is ticked, new accounts will require activation by clicking a link in the email. If this is ticked, the user's password will also be provided in the email, instead of in the registration screen"); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="from_name"><?php echo __('Email "from"-name'); ?></label></td>
				<td><input type="text" name="from_name" id="from_name" value="<?php echo $module->getSetting('from_name'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="from_address"><?php echo __('Email "from"-address'); ?></label></td>
				<td><input type="text" name="from_addr" id="from_address" value="<?php echo $module->getSetting('from_addr'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('This is the name and email address email notifications from The Bug Genie will be sent from'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="no_dash_f"><?php echo __("Don't use sendmail '-f'"); ?></label></td>
				<td><input type="checkbox" name="no_dash_f" id="no_dash_f" value="1" <?php if ($module->getSetting('no_dash_f')): ?>checked<?php endif; ?> style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __("Don't use the '-f' sendmail parameter (some systems may not allow it)"); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="headcharset"><?php echo __('Email header charset'); ?></label></td>
				<td><input type="text" name="headcharset" id="headcharset" value="<?php echo $module->getSetting('headcharset'); ?>" style="width: 100px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The character encoding used in outgoing emails'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="use_queue"><?php echo __('Queue emails for batch processing'); ?></label></td>
				<td>
					<input type="radio" name="use_queue" value="0" id="use_queue_no"<?php if (!$module->usesEmailQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_no"><?php echo __('Send email notifications instantly'); ?></label><br>
					<input type="radio" name="use_queue" value="1" id="use_queue_yes"<?php if ($module->usesEmailQueue()): ?> checked<?php endif; ?> <?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="use_queue_yes"><?php echo __('Use email queueing'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __("If you're using a queue, outgoing emails will not slow down the system. Read more about how to set up email queueing in %email_queueing%", array('%email_queueing%' => link_tag(make_url('@publish_article?article_name=EmailQueueing'), 'EmailQueueing'))); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="mail_type_php"><?php echo __('Mail configuration'); ?></label></td>
				<td>
					<input type="radio" name="mail_type" value="<?php echo TBGMailer::MAIL_TYPE_PHP; ?>" id="mail_type_php"<?php if ($module->getSetting('mail_type') != TBGMailer::MAIL_TYPE_B2M): ?> checked<?php endif; ?> onclick="$('mail_type_b2m_info').hide();"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="mail_type_php"><?php echo __('Use php settings'); ?></label><br>
					<input type="radio" name="mail_type" value="<?php echo TBGMailer::MAIL_TYPE_B2M; ?>" id="mail_type_b2m"<?php if ($module->getSetting('mail_type') == TBGMailer::MAIL_TYPE_B2M): ?> checked<?php endif; ?> onclick="$('mail_type_b2m_info').show();"<?php echo ($access_level != TBGSettings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>&nbsp;<label for="mail_type_b2m"><?php echo __('Use custom settings'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('This setting determines whether The Bug Genie uses the built-in php email function, or a custom configuration'); ?></td>
			</tr>
		</table>
		<table style="width: 680px; margin-top: 10px;<?php if ($module->getSetting('mail_type') != TBGMailer::MAIL_TYPE_B2M): ?> display: none;<?php endif; ?>" class="padded_table" cellpadding=0 cellspacing=0 id="mail_type_b2m_info">
			<tr>
				<td style="width: 200px; padding: 5px;"><label for="smtp_host"><?php echo __('SMTP server address'); ?></label></td>
				<td style="width: auto;"><input type="text" name="smtp_host" id="smtp_host" value="<?php echo $module->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_port"><?php echo __('SMTP address port'); ?></label></td>
				<td><input type="text" name="smtp_port" id="smtp_port" value="<?php echo $module->getSetting('smtp_port'); ?>" style="width: 40px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="timeout"><?php echo __('SMTP server timeout'); ?></label></td>
				<td><input type="text" name="timeout" id="timeout" value="<?php echo $module->getSetting('timeout'); ?>" style="width: 40px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>><?php echo __('%number_of% seconds', array('%number_of%' => '')); ?></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('Connection information for the outgoing email server'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="ehlo_no"><?php echo __('Microsoft Exchange server'); ?></label></td>
				<td>
					<input type="radio" name="ehlo" id="ehlo_yes" value="1" <?php echo ($module->getSetting('ehlo') == 1) ? ' checked' : ''; ?>><label for="ehlo_yes"><?php echo __('No'); ?></label>&nbsp;
					<input type="radio" name="ehlo" id="ehlo_no" value="0" <?php echo ($module->getSetting('ehlo') == 0) ? ' checked' : ''; ?>><label for="ehlo_no"><?php echo __('Yes'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('For compatibility reasons, specify whether the SMTP server is a Microsoft Exchange server'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_user"><?php echo __('SMTP username'); ?></label></td>
				<td><input type="text" name="smtp_user" id="smtp_user" value="<?php echo $module->getSetting('smtp_user'); ?>" style="width: 300px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The username used for sending emails'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_wd"><?php echo __('SMTP password'); ?></label></td>
				<td><input type="password" name="smtp_pwd" id="smtp_pwd" value="<?php echo $module->getSetting('smtp_pwd'); ?>" style="width: 150px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The password used for sending emails'); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="tab_incoming_settings_pane" class="rounded_box borderless mediumgrey<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> cut_bottom<?php endif; ?>" style="margin: 10px 0 0 0; display: none; width: 700px;<?php if ($access_level == TBGSettings::ACCESS_FULL): ?> border-bottom: 0;<?php endif; ?>">
		<h4><?php echo __('Incoming email accounts'); ?></h4>
		<ul class="simple_list">
			<?php foreach ($module->getIncomingEmailAccounts() as $account): ?>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div class="rounded_box iceblue borderless cut_top" style="margin: 0 0 5px 0; width: 700px; border-top: 0; padding: 8px 5px 2px 5px; height: 25px;">
		<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save email notification settings', array('%save%' => __('Save'))); ?></div>
		<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
	</div>
<?php endif; ?>
</form>
<?php if ($module->isEnabled()): ?>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailing_test_email'); ?>" method="post">
		<div class="rounded_box borderless mediumgrey" style="margin: 10px 0 0 0; width: 700px; padding: 5px 5px 30px 5px;">
			<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 125px; padding: 5px;"><label for="test_email_to"><?php echo __('Send test email'); ?></label></td>
					<td style="width: auto;"><input type="text" name="test_email_to" id="test_email_to" value="" style="width: 300px;"<?php echo ($access_level != TBGSettings::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td class="config_explanation" colspan="2" style="font-size: 13px;">
						<span class="faded_out">
							<?php echo __('Enter an email address, and click "%send_test_email%" to check if the email module is configured correctly', array('%send_test_email%' => __('Send test email'))); ?>
						</span>
					</td>
				</tr>
			</table>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Send test email'); ?>">
		</div>
	</form>
<?php endif; ?>