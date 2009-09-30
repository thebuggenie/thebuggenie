<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			$settings_arr = array('smtp_host', 'smtp_port', 'smtp_user', 'timeout',
								'smtp_pwd', 'headcharset', 'from_name', 'from_addr', 'ehlo', 
								'returnfromlogin', 'returnfromlogout', 'showloginbox', 'limit_registration', 
								'showprojectsoverview', 'showprojectsoverview', 'cleancomments');
			foreach ($settings_arr as $setting)
			{
				if (BUGScontext::getRequest()->getParameter($setting) !== null)
				{
					BUGScontext::getModule('mailnotification')->saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
				}
			}
		}

		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure mail notification module'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Set up the mail notification module here.'); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" enctype="multipart/form-data" method="post" name="defaultscopeform">
		<input type="hidden" name="module" value="mailnotification">
		<table style="width: auto" cellpadding=0 cellspacing=0>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('SMTP server address'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="smtp_host" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The address BUGS 2 should use to send emails via'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('SMTP address port'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="smtp_port" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('smtp_port'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The port used by the SMTP server (default is 25)'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('SMTP server timeout'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="timeout" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('timeout'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('Timeout in seconds (default is 30)'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Server type'); ?></b></td>
				<td style="width: 250px;">
					<select name="ehlo" style="width: 100%"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>>
						<option value=1 <?php echo (BUGScontext::getModule('mailnotification')->getSetting('ehlo') == 1) ? ' selected' : ''; ?>>Standard SMTP server</option>
						<option value=0 <?php echo (BUGScontext::getModule('mailnotification')->getSetting('ehlo') == 0) ? ' selected' : ''; ?>>Microsoft Exchange server</option>
					</select>
				</td>
				<td style="width: auto; padding: 5px;"><?php echo __('Select whether the SMTP server is a MS Exchange server'); ?></td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('SMTP username'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="smtp_user" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('smtp_user'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The username used for sending emails'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('SMTP password'); ?></b></td>
				<td style="width: 250px;"><input type="password" name="smtp_pwd" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('smtp_pwd'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The password used for sending emails'); ?></td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Email "from"-name'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="from_name" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('from_name'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The name people will see when receiving emails from BUGS 2'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Email "from"-address'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="from_addr" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('from_addr'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The email-address people will see when receiving emails from BUGS 2'); ?></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Email header charset'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="headcharset" value="<?php echo BUGScontext::getModule('mailnotification')->getSetting('headcharset'); ?>" style="width: 100%;"<?php echo ($access_level != 'full') ? ' disabled' : ''; ?>></td>
				<td style="width: auto; padding: 5px;"><?php echo __('The charset used in outgoing emails'); ?></td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;">&nbsp;</td>
			</tr>
			<?php if ($access_level == 'full'): ?>
				<tr>
					<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Save'); ?>"></td>
				</tr>
			<?php endif; ?>
		</table>
		</form>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Test configuration'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('Enter an email address, and click "Send test email" to check if the email module is configured correctly'); ?><br>
						<?php echo __('The test output will appear below this form'); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="POST">
		<input type="hidden" name="module" value="mailnotification">
		<table>
			<tr>
				<td style="width: 125px; padding: 5px;"><b><?php echo __('Send test email to'); ?></b></td>
				<td style="width: 250px;"><input type="text" name="test_email" value="<?php echo BUGScontext::getRequest()->getParameter('test_email', ''); ?>" style="width: 100%;"></td>
			</tr>
			<tr>
				<td style="width: 125px; padding: 5px; text-align: right;"><input type="checkbox" value="1" name="debug" id="debug"></td>
				<td style="width: 25px; text-align: left;"><label for="debug">Show extra debug information</label></td>
			</tr>
			<tr>
				<td colspan=3 style="padding: 5px; text-align: right;"><input type="submit" value="<?php echo __('Send test email'); ?>"></td>
			</tr>
		</table>
		</form>
		<?php
		if (BUGScontext::getRequest()->getParameter('test_email'))
		{
			?><p><b><?php echo __('Test email output'); ?></b></p><?php
			try
			{
				$debug = (BUGScontext::getRequest()->getParameter('debug')) ? true : false;
				BUGScontext::getModule('mailnotification')->sendMail(BUGScontext::getRequest()->getParameter('test_email'), BUGScontext::getRequest()->getParameter('test_email'), 'Test email', 'Test successful!', 3, '', '', array(), $debug);
				echo 'Email sent!';
			}
			catch (Exception $e)
			{
				echo nl2br($e->getMessage()) . '<br>';
				if ($debug) echo nl2br($e->getTraceAsString());
			}
		}
			
	}
	
?>