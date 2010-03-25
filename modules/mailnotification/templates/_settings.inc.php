<div class="header"><?php echo __('Outgoing emails'); ?></div>
<div class="content"><?php echo __('This is the basic information about outgoing emails, used regardless of your email method or outgoing server'); ?></div>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" enctype="multipart/form-data" method="post">
<div class="rounded_box borderless" style="margin: 10px 0 0 0; width: 700px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 5px;">
		<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 200px; padding: 5px;"><label for="from_name"><?php echo __('Email "from"-name'); ?></label></td>
				<td style="width: auto;"><input type="text" name="from_name" id="from_name" value="<?php echo $module->getSetting('from_name'); ?>" style="width: 100%;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="from_address"><?php echo __('Email "from"-address'); ?></label></td>
				<td><input type="text" name="from_addr" id="from_address" value="<?php echo $module->getSetting('from_addr'); ?>" style="width: 100%;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('This is the name and email address email notifications from The Bug Genie will be sent from'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="headcharset"><?php echo __('Email header charset'); ?></label></td>
				<td><input type="text" name="headcharset" id="headcharset" value="<?php echo $module->getSetting('headcharset'); ?>" style="width: 100px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The character encoding used in outgoing emails'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="mail_type_php"><?php echo __('Mail configuration'); ?></label></td>
				<td>
					<input type="radio" name="mail_type" value="<?php echo TBGMailer::MAIL_TYPE_PHP; ?>" id="mail_type_php"<?php if ($module->getSetting('mail_type') != TBGMailer::MAIL_TYPE_B2M): ?> checked<?php endif; ?> onclick="$('mail_type_b2m_info').hide();">&nbsp;<label for="mail_type_php"><?php echo __('Use php settings'); ?></label><br>
					<input type="radio" name="mail_type" value="<?php echo TBGMailer::MAIL_TYPE_B2M; ?>" id="mail_type_b2m"<?php if ($module->getSetting('mail_type') == TBGMailer::MAIL_TYPE_B2M): ?> checked<?php endif; ?> onclick="$('mail_type_b2m_info').show();">&nbsp;<label for="mail_type_b2m"><?php echo __('Use custom settings'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('This setting determines whether The Bug Genie uses the built-in php email function, or a custom configuration'); ?></td>
			</tr>
		</table>
		<table style="width: 680px; margin-top: 10px;<?php if ($module->getSetting('mail_type') != TBGMailer::MAIL_TYPE_B2M): ?> display: none;<?php endif; ?>" class="padded_table" cellpadding=0 cellspacing=0 id="mail_type_b2m_info">
			<tr>
				<td style="width: 200px; padding: 5px;"><label for="smtp_host"><?php echo __('SMTP server address'); ?></label></td>
				<td style="width: auto;"><input type="text" name="smtp_host" id="smtp_host" value="<?php echo $module->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_port"><?php echo __('SMTP address port'); ?></label></td>
				<td><input type="text" name="smtp_port" id="smtp_port" value="<?php echo $module->getSetting('smtp_port'); ?>" style="width: 40px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="timeout"><?php echo __('SMTP server timeout'); ?></label></td>
				<td><input type="text" name="timeout" id="timeout" value="<?php echo $module->getSetting('timeout'); ?>" style="width: 40px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>><?php echo __('%number_of% seconds', array('%number_of%' => '')); ?></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('Connection information for the outgoing email server'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="ehlo"><?php echo __('Microsoft Exchange server'); ?></label></td>
				<td>
					<select name="ehlo" id="ehlo" style="width: 70px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>>
						<option value=1 <?php echo ($module->getSetting('ehlo') == 1) ? ' selected' : ''; ?>><?php echo __('No'); ?></option>
						<option value=0 <?php echo ($module->getSetting('ehlo') == 0) ? ' selected' : ''; ?>><?php echo __('Yes'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('For compatibility reasons, specify whether the SMTP server is a Microsoft Exchange server'); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_user"><?php echo __('SMTP username'); ?></label></td>
				<td><input type="text" name="smtp_user" id="smtp_user" value="<?php echo $module->getSetting('smtp_user'); ?>" style="width: 300px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The username used for sending emails'); ?></td>
			</tr>
			<tr>
				<td style="padding: 5px;"><label for="smtp_wd"><?php echo __('SMTP password'); ?></label></td>
				<td><input type="password" name="smtp_pwd" id="smtp_pwd" value="<?php echo $module->getSetting('smtp_pwd'); ?>" style="width: 150px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
			</tr>
			<tr>
				<td class="config_explanation" colspan="2"><?php echo __('The password used for sending emails'); ?></td>
			</tr>
			<tr>
				<td colspan="2" style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
		</table>
	</div>
	<?php if ($access_level != configurationActions::ACCESS_FULL): ?>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	<?php endif; ?>
</div>
<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
	<div class="rounded_box iceblue_borderless" style="margin: 0 0 5px 0; width: 700px;">
		<div class="xboxcontent" style="padding: 8px 5px 2px 5px; height: 23px;">
			<div style="float: left; font-size: 13px; padding-top: 2px;"><?php echo __('Click "%save%" to save email notification settings', array('%save%' => __('Save'))); ?></div>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
<?php endif; ?>
</form>
<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailnotification_test_email'); ?>" method="post">
	<div class="rounded_box borderless" style="margin: 10px 0 0 0; width: 700px;">
		<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
		<div class="xboxcontent" style="padding: 5px 5px 25px 5px;">
			<table style="width: 680px;" class="padded_table" cellpadding=0 cellspacing=0>
				<tr>
					<td style="width: 125px; padding: 5px;"><label for="test_email_to"><?php echo __('Send test email'); ?></label></td>
					<td style="width: auto;"><input type="text" name="test_email_to" id="test_email_to" value="" style="width: 300px;"<?php echo ($access_level != configurationActions::ACCESS_FULL) ? ' disabled' : ''; ?>></td>
				</tr>
				<tr>
					<td class="config_explanation" colspan="2" style="font-size: 13px;">
						<span class="faded_medium">
							<?php echo __('Enter an email address, and click "%send_test_email%" to check if the email module is configured correctly', array('%send_test_email%' => __('Send test email'))); ?>
						</span>
					</td>
				</tr>
			</table>
			<input type="submit" id="submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 13px; font-weight: bold;" value="<?php echo __('Send test email'); ?>">
		</div>
		<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
	</div>
</form>