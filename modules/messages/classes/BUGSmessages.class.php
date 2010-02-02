<?php

	class BUGSmessages extends TBGModule 
	{

	//define ('MESSAGES_NEWMSGWINDOWSTRING', "window.open('" . TBGContext::getTBGPath() . "modules/messages/newmessage.php?clear_sendto=true','mywindow','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=500');");
	//define ('MESSAGES_NEWMSGWINDOWSTRING_TOUSER_STRING', "window.open('" . TBGContext::getTBGPath() . "modules/messages/newmessage.php?set_sendto={uid}','mywindow','menubar=0,toolbar=0,location=0,status=0,scrollbars=0,width=600,height=500');");

		
		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(TBGContext::getI18n()->__('Messages'));
			$this->setMenuTitle(TBGContext::getI18n()->__('Messages'));
			$this->setConfigTitle(TBGContext::getI18n()->__('Messages'));
			$this->setDescription(TBGContext::getI18n()->__('Enables messaging functionality'));
			$this->setHasAccountSettings();
			$this->addAvailableListener('core', 'dashboard_left_top', 'section_messagesSummary', 'Dashboard message summary');
			$this->addAvailableListener('core', 'useractions_bottom', 'section_useractionsBottom', '"Send message" in user drop-down menu');
			$this->addAvailableListener('core', 'teamactions_bottom', 'section_teamactionsBottom', '"Send message" in team drop-down menu');
		}

		public function initialize()
		{

		}

		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;
			
			$module = parent::_install('messages', 'BUGSmessages', '1.0', true, false, true, $scope);

			$module->saveSetting('viewmode', 1, 0, $scope);
			$module->enableListenerSaved('core', 'dashboard_left_top', $scope);
			$module->enableListenerSaved('core', 'useractions_bottom', $scope);
			$module->enableListenerSaved('core', 'teamactions_bottom', $scope);

			if ($scope == TBGContext::getScope()->getID())
			{
				B2DB::getTable('TBGMessageFoldersTable')->setAutoIncrementStart(5);
				B2DB::getTable('TBGMessageFoldersTable')->create();
				B2DB::getTable('TBGMessagesTable')->create();
			}

			return true;
		}
		
		public function uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				B2DB::getTable('TBGMessageFoldersTable')->drop();
				B2DB::getTable('TBGMessagesTable')->drop();
			}
			parent::_uninstall();
		}
				
		public function getCommentAccess($target_type, $target_id, $type = 'view')
		{
			
		}
		
		public function enableSection($module, $identifier, $scope)
		{
			$function_name = '';
			switch ($module . '_' . $identifier)
			{
				case 'core_teamactions_bottom':
					$function_name = 'section_teamactionsBottom';
					break;
				case 'core_useractions_bottom':
					$function_name = 'section_useractionsBottom';
					break;
				case 'core_account_settingslist':
					$function_name = 'section_accountSettingsList';
					break;
				case 'core_account_settings':
					$function_name = 'section_accountSettings';
					break;
				case 'core_dashboard_left_top':
					$function_name = 'section_messagesSummary';
					break;
			}
			if ($function_name != '') parent::registerPermanentTriggerListener($module, $identifier, $function_name, $scope);
		}
		
		public function getMessages($gettype, $uid = 0, $folder = 0, $msg = 0, $tid = 0)
		{
			$returnmsg = array();
	
			switch($gettype)
			{
				case "details":
					$msgs = array();
					switch ($folder)
					{
						case 2:
							$crit = new B2DBCriteria();
							$crit->setFromTable(B2DB::getTable('TBGMessagesTable'));
							$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getUID())));

							if ($_SESSION['messages_filter'] != '')
							{
								$ctn = $crit->returnCriterion(TBGMessagesTable::BODY, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGMessagesTable::TITLE, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGMessagesTable::TITLE, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGUsersTable::BUDDYNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGUsersTable::UNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$crit->addWhere($ctn);
							}
							
							if ($_SESSION['unread_filter'] != '')
							{
								$crit->addWhere(TBGMessagesTable::IS_READ, $_SESSION['unread_filter']);
							}
							
							$crit->addWhere(TBGMessagesTable::FROM_USER, $uid);
							$crit->addWhere(TBGMessagesTable::DELETED_SENT, 0);
							if ($msg != 0) 
							{ 
								$res = B2DB::getTable('TBGMessagesTable')->doSelectById($msg, $crit);
								$msgs[] = $res;
							}
							else
							{
								$crit->addOrderBy(TBGMessagesTable::SENT, 'desc');
								$crit->addOrderBy(TBGMessagesTable::ID, 'desc');
								$res = B2DB::getTable('TBGMessagesTable')->doSelect($crit);
								$msgs = $res->getAllRows();
							}
							break;
						case 4:
							foreach (TBGContext::getUser()->getTeams() as $thetid)
							{
								if (($tid != 0 && $thetid == $tid) || $tid == 0)
								{
									$crit = new B2DBCriteria();
									$crit->setFromTable(B2DB::getTable('TBGMessagesTable'));
									$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getUID())));
									
									if ($_SESSION['messages_filter'] != '')
									{
										$ctn = $crit->returnCriterion(TBGMessagesTable::BODY, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
										$ctn->addOr(TBGMessagesTable::TITLE, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
										$ctn->addOr(TBGUsersTable::BUDDYNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
										$ctn->addOr(TBGUsersTable::UNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
										$crit->addWhere($ctn);
									}
									
									if ($_SESSION['unread_filter'] != '')
									{
										$crit->addWhere(TBGMessagesTable::IS_READ, $_SESSION['unread_filter']);
									}

									if ($tid != 0)
									{
										$crit->addWhere(TBGMessagesTable::TO_TEAM, $thetid, B2DBCriteria::DB_IN);
									}
									$crit->addWhere(TBGMessagesTable::FOLDER, $folder);
									$crit->addWhere(TBGMessagesTable::DELETED, 0);
									$crit->addWhere(TBGMessagesTable::TO_USER, $uid);
									if ($msg != 0) 
									{ 
										try
										{
											$res = B2DB::getTable('TBGMessagesTable')->doSelectById($msg, $crit);
											$msgs[] = $res;
										}
										catch (Exception $e)
										{
											throw $e;
										}
									}
									else
									{
										$crit->addOrderBy(TBGMessagesTable::SENT, 'desc');
										$crit->addOrderBy(TBGMessagesTable::ID, 'desc');
										$res = B2DB::getTable('TBGMessagesTable')->doSelect($crit);
										$msgs = $res->getAllRows();
									}
								}
							}
							break;
						default:
							$crit = new B2DBCriteria();
							$crit->setFromTable(B2DB::getTable('TBGMessagesTable'));
							$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getUID())));
							$crit->addWhere(TBGMessagesTable::TO_USER, $uid);
							$crit->addWhere(TBGMessagesTable::FOLDER, $folder);
							$crit->addWhere(TBGMessagesTable::DELETED, 0);
							$crit->addWhere(TBGMessagesTable::FOLDER, 2, B2DBCriteria::DB_NOT_EQUALS);

							if ($_SESSION['messages_filter'] != '')
							{
								$ctn = $crit->returnCriterion(TBGMessagesTable::BODY, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGMessagesTable::TITLE, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGUsersTable::BUDDYNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$ctn->addOr(TBGUsersTable::UNAME, '%'.$_SESSION['messages_filter'].'%', B2DBCriteria::DB_LIKE);
								$crit->addWhere($ctn);
							}
							
							if ($_SESSION['unread_filter'] != '')
							{
								$crit->addWhere(TBGMessagesTable::IS_READ, $_SESSION['unread_filter']);
							}
							
							if ($msg != 0) 
							{ 
								$res = B2DB::getTable('TBGMessagesTable')->doSelectById($msg, $crit);
								$msgs[] = $res;
							}
							else
							{
								$crit->addOrderBy(TBGMessagesTable::SENT, 'desc');
								$crit->addOrderBy(TBGMessagesTable::ID, 'desc');
								$res = B2DB::getTable('TBGMessagesTable')->doSelect($crit);
								$msgs = $res->getAllRows();
							}
							break;
	
					}
					return $msgs;
	
				default:
	
			}
		}
	
		public function getFolders($uid, $pid = null)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGMessageFoldersTable::UID, $uid);
			$crit->addOrderBy(TBGMessageFoldersTable::FOLDERNAME, 'asc');
			if ($pid !== null)
			{
				$crit->addWhere(TBGMessageFoldersTable::PARENT_FOLDER, $pid);
			}
			$res = B2DB::getTable('TBGMessageFoldersTable')->doSelect($crit);
			$mfs = array();
			while ($row = $res->getNextRow())
			{
				$mfs[] = array('id' => $row->get(TBGMessageFoldersTable::ID), 'foldername' => $row->get(TBGMessageFoldersTable::FOLDERNAME));
			}
			return $mfs;
		}
	
		public function addFolder($folder_name)
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGMessageFoldersTable::UID, TBGContext::getUser()->getUID());
			$crit->addInsert(TBGMessageFoldersTable::FOLDERNAME, $folder_name);
			$res = B2DB::getTable('TBGMessageFoldersTable')->doInsert($crit);
	
			return $res->getInsertID();
		}
	
		public function deleteFolder($folder_id, $force = false)
		{
			if (!$force)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGMessageFoldersTable::UID, TBGContext::getUser()->getUID());
				$crit->addWhere(TBGMessageFoldersTable::ID, $folder_id);
				$res = B2DB::getTable('TBGMessageFoldersTable')->doDelete($crit);
			}
			else
			{
				$res = B2DB::getTable('TBGMessageFoldersTable')->doDeleteById($folder_id);
			}
		}
	
		public function deleteMessage($msg_id, $force = false)
		{
			if (!$force)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getUID());
				$crit->addWhere(TBGMessagesTable::ID, $msg_id);
				$res = B2DB::getTable('TBGMessagesTable')->doDelete($crit);
			}
			else
			{
				$res = B2DB::getTable('TBGMessagesTable')->doDeleteById($msg_id);
			}
		}
	
		public function setRead($msg_id, $read)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGMessagesTable::IS_READ, $read);
			$crit->addWhere(TBGMessagesTable::ID, $msg_id);
			$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getUID());
			B2DB::getTable('TBGMessagesTable')->doUpdate($crit);
		}
	
		public function moveMessage($msg_id, $to_folder)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGMessagesTable::FOLDER, $to_folder);
			$crit->addWhere(TBGMessagesTable::ID, $msg_id);
			$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getUID());
			B2DB::getTable('TBGMessagesTable')->doUpdate($crit);
		}
	
		public function sendMessage($to_id, $to_team, $title, $content)
		{
			$now = $_SERVER["REQUEST_TIME"];
	
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGMessagesTable::FROM_USER, TBGContext::getUser()->getUID());
			$crit->addInsert(TBGMessagesTable::TITLE, $title);
			$crit->addInsert(TBGMessagesTable::BODY, $content);
			$crit->addInsert(TBGMessagesTable::SENT, $now);
			if ($to_team == 0)
			{
				$crit->addInsert(TBGMessagesTable::FOLDER, 1);
				$crit->addInsert(TBGMessagesTable::TO_USER, $to_id);
				B2DB::getTable('TBGMessagesTable')->doInsert($crit);
			}
			else
			{
				$theTeam = TBGFactory::teamLab($to_id);
				
				$crit->addInsert(TBGMessagesTable::FOLDER, 4);
				$crit->addInsert(TBGMessagesTable::TO_TEAM, $to_id);
				foreach ($theTeam->getMembers() as $anUid)
				{
					$crit2 = clone $crit;
					$crit2->addInsert(TBGMessagesTable::TO_USER, $anUid);
					B2DB::getTable('TBGMessagesTable')->doInsert($crit2);
				}
			}
		}
	
		public function countMessages($folder_id, $tid = null)
		{
			$crit = new B2DBCriteria();
			$crit->setDistinct();
			$crit->addWhere(TBGMessagesTable::FOLDER, (int) $folder_id);
			$crit->addWhere(TBGMessagesTable::DELETED, 0);
			$crit->addWhere(TBGMessagesTable::TO_USER, (int) TBGContext::getUser()->getUID());
			if ($tid !== null)
			{
				$crit->addWhere(TBGMessagesTable::TO_TEAM, $tid);
			}
			
			$unread_count = 0;
			$total_count = 0;
			$frombuds_count = 0;
			$urgent_count = 0;
			
			if ($res = B2DB::getTable('TBGMessagesTable')->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					if ($row->get(TBGMessagesTable::IS_READ) == 0)
					{
						$unread_count++;
						if (TBGContext::getUser()->isFriend($row->get(TBGMessagesTable::FROM_USER)))
						{
							$frombuds_count++;
						}
						if ($row->get(TBGMessagesTable::URGENT) == 1)
						{
							$urgent_count++;
						}
					}
					$total_count++;
				}
			}

			return array('total' => $total_count, 'unread' => $unread_count, 'frombuds' => $frombuds_count, 'urgent' => $urgent_count);
		}
		
		public function section_teamactionsBottom($vars)
		{
			if (!is_array($vars)) throw new Exception('something went wrong');
			$tid = array_shift($vars);
			$closemenustring = array_shift($vars);
			$msgstring = '';
			/*MESSAGES_NEWMSGWINDOWSTRING_TOUSER_STRING;
			$msgstring = str_replace("{uid}", $tid . "&sendto_team=1", $msgstring);*/
			if (TBGUser::isThisGuest() == false)
			{
				$retval = '<div style="padding: 2px;"><a href="javascript:void(0);" onclick="' . $msgstring . $closemenustring . '">' . TBGContext::getI18n()->__('Send a message to this team') . '</a></div>';
			}
			return $retval;
		}
		
		public function section_useractionsBottom($vars)
		{
			if (!is_array($vars)) throw new Exception('something went wrong');
			$user = array_shift($vars);
			$closemenustring = array_shift($vars);
			$msgstring = '';
			$retval = '';
			/*MESSAGES_NEWMSGWINDOWSTRING_TOUSER_STRING;
			$msgstring = str_replace("{uid}", $user->getID(), $msgstring);*/
			if (TBGUser::isThisGuest() == false && $user->isGuest() == false)
			{
				$retval = '<div style="padding: 2px;"><a href="javascript:void(0);" onclick="' . $msgstring . $closemenustring . '">' . TBGContext::getI18n()->__('Send a message to this user') . '</a></div>';
			}
			return $retval;
		}
		
		public function section_accountSettingsList()
		{
			include_template('messages/accountsettingslist');
		}
		
		public function section_accountSettings($module)
		{
			if ($module != $this->getName()) return;
			if (TBGContext::getRequest()->getParameter('viewmode'))
			{
				TBGContext::getModule('messages')->saveSetting('viewmode', TBGContext::getRequest()->getParameter('viewmode'), TBGContext::getUser()->getUID());
			}
			?>
			<table style="table-layout: fixed; width: 100%; background-color: #F1F1F1; margin-top: 15px; border: 1px solid #DDD;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-left: 4px; width: 20px;"><?php echo image_tag('mail_nonew.png'); ?></td>
			<td style="border: 0px; width: auto; padding: 3px; padding-left: 7px;"><b><?php echo TBGContext::getI18n()->__('Messages settings'); ?></b></td>
			</tr>
			</table>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="account.php" method="post">
			<input type="hidden" name="settings" value="<?php echo $this->getName(); ?>">
			<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: 200px;"><b><?php echo TBGContext::getI18n()->__('Message center layout'); ?></b></td>
			<td style="width: 200px;"><select name="viewmode" style="width: 100%;">
			<option value=1 <?php if (TBGSettings::get('viewmode', 'messages', null, TBGContext::getUser()->getUID()) == 1) echo ' selected'; ?>><?php echo TBGContext::getI18n()->__('Standard layout'); ?></option>
			<option value=2 <?php if (TBGSettings::get('viewmode', 'messages', null, TBGContext::getUser()->getUID()) == 2) echo ' selected'; ?>><?php echo TBGContext::getI18n()->__('Wide layout'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo TBGContext::getI18n()->__('Save'); ?>"></td>
			</tr>
			</table>
			</form>
			<?php
		}

		public function section_messagesSummary()
		{
			include_template('messages/messagessummary', array('messages' => $this->countMessages(1)));
		}
		
		public function section_messagesBox()
		{
			if (TBGUser::isThisGuest() == false)
			{
				?>
				<table class="b2_section_miniframe" cellpadding=0 cellspacing=0>
				<tr>
				<td class="b2_section_miniframe_header"><?php echo TBGContext::getI18n()->__('Message central'); ?></td>
				</tr>
				<tr>
				<td class="td1">
				<?php
	
					$messages = $this->countMessages(1);

				?>
				<table cellpadding=0 cellspacing=0 style="width: 100%;">
				<tr>
				<td class="imgtd">
				<?php 
				
				if ($messages['unread'] >= 1)
				{
					echo image_tag('mail_new.png');
				}
				else
				{
					echo image_tag('mail_nonew.png');
				}

				?>
				</td>
				<td><a href="modules/messages/messages.php?select_folder=1"><b><?php echo TBGContext::getI18n()->__('Inbox:'); ?></b></a>&nbsp;<?php print $messages['total']; ?>&nbsp;<?php print ($messages['unread'] > 0) ? "<b>(" . TBGContext::getI18n()->__('%number_of% new', array('%number_of%' => $messages['unread'])) . ")</b>" : ""; ?></td>
				</tr>
				<?php
				if ($messages['urgent'] > 0 || $messages['frombuds'] > 0)
				{
					?>
					<tr>
					<td>&nbsp;</td>
					<td><?php
	
					if ($messages['urgent'] > 0)
					{
						echo '<b style="color: #A55;">' . TBGContext::getI18n()->__('%number_of% urgent message(s)', array('%number_of%' => $messages['urgent'])) . "</b><br>";
					}
					if ($messages['frombuds'] > 0)
					{
						echo TBGContext::getI18n()->__('%number_of% message(s) from people you know', array('%number_of%' => $messages['frombuds']));
					}
	
					?></td>
					</tr>
					<?php
				}
	
				if (count(TBGContext::getUser()->getTeams()) >= 1)
				{
					$teammessages = $this->countMessages(4);
					?>
					<tr>
					<td class="imgtd">
					<?php
					
					if ($teammessages['unread'] > 0)
					{
						echo image_tag('mail_new.png');
					}
					else
					{
						echo image_tag('mail_nonew.png');
					}

					?>
					</td>
					<td><a href="modules/messages/messages.php?select_folder=4&amp;team_id=0"><b><?php echo TBGContext::getI18n()->__('Team inbox:'); ?></b></a>&nbsp;<?php print $teammessages['total']; ?>&nbsp;<?php print ($teammessages['unread'] > 0) ? "<b>(" . TBGContext::getI18n()->__('%number_of% new', array('%number_of%' => $teammessages['unread'])) . ")</b>" : ""; ?></td>
					</tr>
					<?php
					foreach (TBGContext::getUser()->getTeams() as $tid)
					{
						$teammessages = $this->countMessages(4, $tid);
						if ($teammessages['unread'] > 0)
						{
							$team = TBGFactory::teamLab($tid);
							?>
							<tr>
							<td>&nbsp;</td>
							<td><?php echo TBGContext::getI18n()->__('%teamname%: %number_of% new', array('%teamname%' => $team->getName(), '%number_of%' => $teammessages['unread'])); ?><br></td>
							</tr>
							<?php
						}
					}
				}
				?>
				<tr>
				<td class="imgtd" style="padding-top: 15px;"><?php echo image_tag('msg_unknown.png'); ?></td>
				<td style="padding-top: 15px;"><a href="javascript:void(0);" onclick="<?php //print MESSAGES_NEWMSGWINDOWSTRING; ?>"><?php echo TBGContext::getI18n()->__('Write a new message'); ?></a></td>
				</tr>
				</table>
				</td>
				</tr>
				</table>
				<?php
			}
		}
		
	}
?>