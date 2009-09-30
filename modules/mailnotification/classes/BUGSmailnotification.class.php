<?php

	class BUGSmailnotification extends BUGSmodule 
	{
		const BUGS_MAIL_LOGO = 1; // add The Bug Genie logo at top
		const BUGS_MAIL_FOOT = 2; // add The Bug Genie footer
		const BUGS_MAIL_TO_HTML = 4; // Try to convert plain text to HTML
		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_menu_title = BUGScontext::getI18n()->__("Mail notification");
			$this->_module_config_title = BUGScontext::getI18n()->__("Mail notification");
			$this->_module_config_description = BUGScontext::getI18n()->__('Set up email- and user notifications from this section.');
			$this->_module_version = "0.9";
			$this->addAvailableSection('core', 'user_registration', BUGScontext::getI18n()->__('Email when user registers'));
			$this->addAvailableSection('core', 'forgotten_password', BUGScontext::getI18n()->__('Email to reset password'));
			$this->addAvailableSection('core', 'password_reset', BUGScontext::getI18n()->__('Email when password is reset'));
			$this->addAvailableSection('core', 'BUGSIssue::update', BUGScontext::getI18n()->__('Email on issue update'));
			$this->addAvailableSection('core', 'BUGSIssue::createNew', BUGScontext::getI18n()->__('Email on new issues'));
			$this->addAvailableSection('core', 'BUGSComment::createNew', BUGScontext::getI18n()->__('Email when comments are posted'));
			$this->addAvailableSection('core', 'account_settings', BUGScontext::getI18n()->__('"My account" settings'));
			$this->addAvailableSection('core', 'account_settingslist', BUGScontext::getI18n()->__('"My account" drop-down settings'));
			BUGScontext::listenToTrigger('core', 'viewissue_top', array($this, 'section_issueTop'));
		}
		
		static public function install($scope = null)
		{
  			if ($scope === null)
  			{
  				$scope = BUGScontext::getScope()->getID();
  			}
			$module = parent::_install('mailnotification', 
  									  'Mail notification', 
  									  'Enables email notification functionality',
  									  'BUGSmailnotification',
  									  true, false, false,
  									  '0.9',
  									  true,
  									  $scope);
			$module->setPermission(0, 0, 0, true, $scope);
			$module->enableSection('core', 'user_registration', $scope);
			$module->enableSection('core', 'forgotten_password', $scope);
			$module->enableSection('core', 'password_reset', $scope);
			$module->enableSection('core', 'BUGSIssue::update', $scope);
			$module->enableSection('core', 'BUGSIssue::createNew', $scope);
			$module->enableSection('core', 'BUGSComment::createNew', $scope);
			$module->enableSection('core', 'account_settings', $scope);
			$module->enableSection('core', 'account_settingslist', $scope);
			$module->saveSetting('smtp_host', '');
			$module->saveSetting('smtp_port', 25);
			$module->saveSetting('smtp_user', '');
			$module->saveSetting('smtp_pwd', '');
			$module->saveSetting('headcharset', 'utf-8');
			$module->saveSetting('from_name', 'BUGS 2 Automailer');
			$module->saveSetting('from_addr', '');
			$module->saveSetting('ehlo', 1);
			
			try
			{
				self::loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}
			
		}
		
		static function loadFixtures($scope)
		{
			/*try
			{
				
			}
			catch (Exception $e)
			{
				
			}*/
		}
		
		public function uninstall($scope)
		{
			parent::uninstall($scope);
		}
					
		public function getCommentAccess($target_type, $target_id, $type = 'view')
		{
			
		}		

		public function enableSection($module, $identifier, $scope)
		{
			$function_name = '';
			switch ($module . '_' . $identifier)
			{
				case 'core_forgotten_password':
					$function_name = 'section_forgottenPassword';
					break;
				case 'core_password_reset':
					$function_name = 'section_passwordReset';
					break;
				case 'core_account_settingslist':
					$function_name = 'section_accountSettingsList';
					break;
				case 'core_user_registration':
					$function_name = 'section_registerUser';
					break;
				case 'core_account_settings':
					$function_name = 'section_accountSettings';
					break;
				case 'core_BUGSIssue::createNew':
					$function_name = 'section_issueCreate';
					break;
				case 'core_BUGSIssue::update':
					$function_name = 'section_issueUpdate';
					break;
				case 'core_BUGSComment::createNew':
					$function_name = 'section_bugsComment_createNew';
					break;
			}
			if ($function_name != '') parent::createSection($module, $identifier, $function_name, $scope);
		}
		
		public function section_accountSettingsList()
		{
			include_template('mailnotification/accountsettingslist');
		}
		
		public function section_registerUser($vars)
		{
			$user = array_shift($vars);
			$password = array_shift($vars);
	
			/* subject */
			$subject = "User account registered with BUGS 2";
	
			/* message */
			$message = "Hi, " . $user->getBuddyname() . "!<br>Someone registered the username '" . $user->getUname() . "' with BUGS 2.<br>";
			$message .= "Before you can use the new account, you need to confirm it, by visiting the following link:<br><a style=\"color: #00A400; text-decoration: none;\" href=\"" . BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . "login.php?verify_user=true&amp;uname=" . $user->getUname() . "&amp;verification_code=" . $user->getMD5Password() . "\">" . BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . "login.php?verify_user=true&amp;uname=" . $user->getUname() . "&amp;verification_code=" . $user->getMD5Password() . "</a><br><br>Your password is:<br><b>" . $password . "</b><br>and you can log in with this password from the link specified above.<br><br>(This email has been sent upon request to an email address specified by someone. If you did not register this username, or think you've received this email in error, please delete it. We are sorry for the inconvenience.)";
	
			// add nice font
			$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
	
			// and now mail it
			try
			{
				$this->sendMail($user->getBuddyname(), $user->getEmail(), $subject, $message);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		
		public function section_passwordReset($vars)
		{
			$to_users = array(array('id' => $vars[0]->getID()));
			$new_pwd = $vars[1];
			$subject = 'Password reset';
			$message = '<b>Your new password has been saved</b><br>Hi, %user_buddyname%!<br>A request was made to reset your password for your user account at %bugs_url%.<br>';
			$message .= 'The new password has been saved. Click this link:<br><a href="%bugs_url%">%bugs_url%</a><br>';
			$message .= 'and log in with the username <b>%user_username%</b>, and the password <b>' . $new_pwd . '</b>.';

			$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
			$this->sendToUsers($to_users, $subject, $message);
		}
		
		public function section_forgottenPassword($user)
		{
			$to_users = array(array('id' => $user->get(B2tUsers::ID)));
			$subject = 'Forgot your password?';
			$message = '<b>Forgot your password?</b><br>Hi, %user_buddyname%!<br>A request was made to reset your password for your user account at %bugs_url%.<br>';
			$message .= 'To change your password, click the following link:<br><a href="%link_to_reset_password%">%link_to_reset_password%</a><br>';
			$message .= "<br><i>If you didn't request this email, just disregard it. Nothing will be done unless you click the link in this email.";

			$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
			$this->sendToUsers($to_users, $subject, $message);
		}
		
		public function section_accountSettings($module)
		{
			if ($module != $this->getName()) return;
			if (BUGScontext::getRequest()->getParameter('forcenotification'))
			{
				BUGScontext::getModule('mailnotification')->saveSetting('forcenotification', BUGScontext::getRequest()->getParameter('forcenotification'), BUGScontext::getUser()->getUID());
			}
			if (BUGScontext::getRequest()->getParameter('hold_email_on_issue_update'))
			{
				BUGScontext::getModule('mailnotification')->saveSetting('hold_email_on_issue_update', BUGScontext::getRequest()->getParameter('hold_email_on_issue_update'), BUGScontext::getUser()->getUID());
			}
			
			?>
			<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 15px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-left: 4px; width: 20px;"><?php echo image_tag('cfg_icon_mailnotification.png'); ?></td>
			<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo BUGScontext::getI18n()->__('Notification settings'); ?></b></td>
			</tr>
			</table>
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="account.php" method="post">
			<input type="hidden" name="settings" value="<?php echo $this->getName(); ?>">
			<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: 200px;"><b><?php echo BUGScontext::getI18n()->__('Notification on own changes'); ?></b></td>
			<td style="width: 300px;"><select name="forcenotification" style="width: 100%;">
			<option value=1 <?php if (BUGSsettings::get('forcenotification', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Send notification email on my own changes'); ?></option>
			<option value=2 <?php if (BUGSsettings::get('forcenotification', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 2) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Only notify me when others are committing changes'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td style="width: 200px;"><b><?php echo BUGScontext::getI18n()->__('Always notify'); ?></b></td>
			<td style="width: 300px;"><select name="hold_email_on_issue_update" style="width: 100%;">
			<option value=0 <?php if (BUGSsettings::get('hold_email_on_issue_update', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 0) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Always send me an email whenever an issue changes'); ?></option>
			<option value=1 <?php if (BUGSsettings::get('hold_email_on_issue_update', 'mailnotification', null, BUGScontext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo BUGScontext::getI18n()->__('Stop sending emails until I open the issue'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo BUGScontext::getI18n()->__('Save'); ?>"></td>
			</tr>
			</table>
			</form>
			<?php
		}

		public function section_issueCreate(BUGSissue $theIssue)
		{
			if ($theIssue instanceof BUGSissue)
			{
				$to_users = $theIssue->getRelatedUIDs();
				$subject = 'New issue reported: ' . $theIssue->getFormattedIssueNo(false) . ' - ' . $theIssue->getTitle();
				$message = 'Hi, %user_buddyname%!<br>This email is to notify you that issue ' . $theIssue->getFormattedIssueNo(false) . ', "' . $theIssue->getTitle() . '" has been created.<br>';
				$message .= '<br><b>The issue was created with the following description:</b>';
				$message .= bugs_BBDecode($theIssue->getDescription());
				$message .= '<br><br>You can open the issue by clicking the following link:<br><a href="%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '">%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '</a>';
	
				$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
				$this->sendToUsers($to_users, $subject, $message);
			}
		}
		
		public function mustNotifyUserForIssue($issue_id, $user_id)
		{
			$dont_want_forced_notifications = $this->getSetting('hold_email_on_issue_update', $user_id);
			if (!$dont_want_forced_notifications)
			{
				return true;
			}
			else
			{
				if ($this->getSetting('notified_issue_'.$issue_id, $user_id))
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		
		public function sendToUsers($to_users, $subject, $message)
		{
			foreach ($to_users as $a_user)
			{
				if ($a_user != BUGScontext::getUser()->getUID() || BUGScontext::getModule('mailnotification')->getSetting('forcenotification', BUGScontext::getUser()->getUID()) == 1)
				{
					if (is_array($a_user) && isset($a_user['id'])) $a_user = $a_user['id'];
					if (is_array($a_user)) continue;
					$ntfyUser = BUGSfactory::userLab($a_user);
					if ($ntfyUser->isEnabled() && $ntfyUser->isActivated() && !$ntfyUser->isDeleted() && !$ntfyUser->isGuest())
					{
						$to = $ntfyUser->getEmail();
						$message = str_replace('%user_buddyname%', $ntfyUser->getBuddyname(), $message);
						$message = str_replace('%user_username%', $ntfyUser->getUsername(), $message);
						$message = str_replace('%bugs_url%', BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir'), $message);
						$message = str_replace('%link_to_issue%', BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . 'viewissue.php?issue_no=', $message);
						$message = str_replace('%link_to_reset_password%', BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . 'login.php?reset_password=true&username='.$ntfyUser->getUsername().'&key='.$ntfyUser->getPasswordMD5(), $message);
						
						try
						{
							$this->sendMail($ntfyUser->getBuddyname(), $to, $subject, $message);
						}
						catch (Exception $e) 
						{
							BUGSlogging::log('There was an error when trying to send email to ' . $to . ":\n" . $e->getMessage());
							throw $e;
						}
					}
				}
			}
		}
		
		public function section_bugsComment_createNew($comment)
		{
			if ($comment instanceof BUGSComment && $comment->getTargetType() == 1)
			{
				try
				{
					$theIssue = BUGSfactory::BUGSissueLab($comment->getTargetID());
					$title = $comment->getTitle();
					$content = $comment->getContent();
					$this->section_issueUpdate(array($theIssue, $title, $content, null, null));
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
		}
		
		public function section_issueTop($theIssue)
		{
			BUGSsettings::deleteSetting('notified_issue_'.$theIssue->getId(), 'mailnotification', '', 0, BUGScontext::getUser()->getId());
		}
		
		public function section_issueUpdate($vars)
		{
			$theIssue = array_shift($vars);
			$title = array_shift($vars);
			$content = array_shift($vars);
			$uid = array_shift($vars);
			$system = array_shift($vars);
			$to_users = array();
	
			if ($theIssue instanceof BUGSissue)
			{
				$to_users = $theIssue->getRelatedUIDs();
				$cc = 0;
				foreach ($to_users as &$a_user)
				{
					if (is_array($a_user) && isset($a_user['id'])) $a_user = $a_user['id'];
					if ($this->mustNotifyUserForIssue($theIssue->getID(), $a_user))
					{
						if ($this->getSetting('hold_email_on_issue_update', $a_user) == 1)
						{
							$this->saveSetting('notified_issue_'.$theIssue->getID(), 1, $a_user);
						}
					}
					else
					{
						unset($to_users[$cc]);
					}
					$cc++;
				}
				
				$subject = 'Issue ' . $theIssue->getFormattedIssueNo(false) . ' - ' . $theIssue->getTitle() . ' updated';
				$message = 'Hi, %user_buddyname%!<br>You are receiving this update because you are subscribing for updates.<br>This email is an update for issue ' . $theIssue->getFormattedIssueNo(false) . ' - ' . $theIssue->getTitle();
				$message .= '<br><br><b>' . $title . '</b>';
				$message .= '<br>' . bugs_BBDecode($content);
				$message .= '<br><br>You can open the issue by clicking the following link:<br><a href="%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '">%link_to_issue%' . $theIssue->getFormattedIssueNo(true) . '</a>';				
	
				$message = "<div style=\"font-family: \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-size: 11px; color: #646464;\">".$message."</div>";
				$this->sendToUsers($to_users, $subject, $message);
			}
		}
		
		public function sendMail($to_name, $to_addr, $subject, $message, $options = 3, $cc = '', $bcc = '', $attachments = array(), $debug = false)
		{
			$smtp_host = BUGScontext::getModule('mailnotification')->getSetting('smtp_host');
			$smtp_port = BUGScontext::getModule('mailnotification')->getSetting('smtp_port');
			$smtp_user = BUGScontext::getModule('mailnotification')->getSetting('smtp_user');
			$smtp_pwd = BUGScontext::getModule('mailnotification')->getSetting('smtp_pwd');

			$headcharset = BUGScontext::getModule('mailnotification')->getSetting('headcharset');
	   		
			$name = BUGScontext::getModule('mailnotification')->getSetting('from_name');
	   		$addr = BUGScontext::getModule('mailnotification')->getSetting('from_addr');
	
	   		if ($smtp_host == '' || $smtp_port == '' || $name == '' || $addr == '')
	   		{
	   			$e_msg = 'Please configure the mail notification module before trying to send an email';
	   			if ($smtp_host == '') $e_msg .= "\nMissing SMTP hostname";
	   			if ($smtp_port == '') $e_msg .= "\nMissing SMTP port (usually 25)";
	   			if ($name == '') $e_msg .= "\nMissing email \"From\"-name";
	   			if ($addr == '') $e_msg .= "\nMissing email \"From\"-address";
	   			throw new Exception($e_msg);
	   		}
	
		   	if ($options & 4)
		   	{
		   		$message = nl2br($message);
		   	}
	
		   	$buf = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html>';
		   	$buf .= '<head><meta http-equiv=Content-Type content="text/html; charset='.$headcharset.'"><title>BUGS 2 Automailer</title></head><body>';
	
		   	if ($options & 1)
		   	{
				$buf .= '<table cellpadding=0 cellspacing=0 width="100%" style="background-color: #381499; color: #FFF;table-layout: fixed;">';
				$buf .= '<tr>';
				$buf .= '<td style="width: 70px; height: 65px;" align="center" valign="middle">';
				$buf .= '<img src="' . BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . 'themes/' . BUGSsettings::getThemeName() . '/logo_48.png">';
				$buf .= '</td>';
				$buf .= '<td align="left" valign="middle" style="width: 300px;"><div style="font: 20px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; font-weight: bold; color: #FFF;">' . BUGSsettings::get('b2_name') . '</div><div style="font: 11px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif; color: #FFF;">' . BUGSsettings::get('b2_tagline') . '</div></td>';
				$buf .= '<td style="width: auto;">&nbsp;</td>';
				$buf .= '</tr>';
				$buf .= '</table><br>';
		   	}
	
		   	$buf .= $message;
	
		   	if ($options & 2)
		   	{
				$buf .= '<br><table cellpadding=0 cellspacing=0 style="table-layout: auto; background-color: #FFF; width: 100%; color: #999; border-top: 1px solid #DDD;" align="center">';
				$buf .= '<tr>';
				$buf .= '<td style="width: 30px; text-align: right; padding: 5px;">';
				$buf .= '<img src="' . BUGSsettings::get('url_host') . BUGSsettings::get('url_subdir') . 'themes/' . BUGSsettings::getThemeName() . '/footer_logo.png">';
				$bug .= '</td>';
				$buf .= '<td style="width: auto;"><div style="padding: 8px 0px 0px 0px; font: 11px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif;"><a style="color: #00A400; text-decoration: none;" href="http://www.thebuggenie.net/" target="_blank">BUGS - The Bug Genie</a>, Copyright 2002 &copy; 09 <a style="color: #00A400; text-decoration: none;" href="http://www.zegeniestudios.net" target="_blank">zegenie Studios</a></div><div style="padding: 0px 0px 0px 0px; font: 10px \'Trebuchet MS\', \'Liberation Sans\', \'Bitstream Vera Sans\', \'Luxi Sans\', Verdana, sans-serif;">Released under the MPL 1.1 only. Read the license at <a style="color: #00A400; text-decoration: none;" href="http://www.opensource.org/licenses/mozilla1.1.php" target="_blank">www.opensource.org</a>. Resistance is futile.</div>';
				$buf .= '</td>';
				$buf .= '</tr>';
				$buf .= '</table>';
		   	}
	
			$buf .= '</body></html>';
	
			$br_codes = array("<br>","<br>","<br>","<br>");
			$buf = str_replace($br_codes,"<br>\r\n",$buf);
	
			$mail = new BUGSmimemail($smtp_host, $smtp_port, $smtp_user, $smtp_pwd); // $name, $addr, $to_name, $to_addr, $subject, $buf);
			$mail->setDebug($debug);
			
			$mail->setFrom($name, $addr);
			$mail->addTo($to_name, $to_addr);
			$mail->setSubject($subject);
			$mail->setMessage($buf);
			
			if ($cc != '')
			{
				$mail->addCC($cc);
			}
			
			if ($bcc != '')
			{
				$mail->addBCC($bcc);
			}
			
			if (count($attachments) > 0)
			{
				foreach ($attachments as $attachment)
				{
					$mail->addAttachment($attachment['type'], $attachment['filename']);
				}
			}
			
			$mail->setCharset($headcharset);
			try
			{
				$retval = $mail->sendMail();
			}
			catch (Exception $e)
			{
				throw $e;
			}
			return $retval;
		}
		
	}

?>