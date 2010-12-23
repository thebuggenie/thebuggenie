<?php

	class BUGSmessages extends TBGModule 
	{

		protected $_module_version = '1.0';

		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('Messages'));
			$this->setConfigTitle($i18n->__('Messages'));
			$this->setDescription($i18n->__('Enables messaging functionality'));
		}

		protected function _addAvailableListeners()
		{
			$this->addAvailableListener('core', 'dashboard_left_top', 'listen_messagesSummary', 'Dashboard message summary');
			$this->addAvailableListener('core', 'useractions_bottom', 'listen_useractionsBottom', '"Send message" in user drop-down menu');
			$this->addAvailableListener('core', 'teamactions_bottom', 'listen_teamactionsBottom', '"Send message" in team drop-down menu');
		}

		protected function _install($scope)
		{
			$this->saveSetting('viewmode', 1, 0, $scope);
			$this->enableListenerSaved('core', 'dashboard_left_top', $scope);
			$this->enableListenerSaved('core', 'useractions_bottom', $scope);
			$this->enableListenerSaved('core', 'teamactions_bottom', $scope);
		}
		
		public function getCommentAccess($target_type, $target_id, $type = 'view')
		{
			
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
							$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getID())));

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
									$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getID())));
									
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
							$crit->addJoin(B2DB::getTable('TBGBuddiesTable'), TBGBuddiesTable::ID, TBGMessagesTable::FROM_USER, array(array(TBGBuddiesTable::UID, TBGContext::getUser()->getID())));
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
			$crit->addInsert(TBGMessageFoldersTable::UID, TBGContext::getUser()->getID());
			$crit->addInsert(TBGMessageFoldersTable::FOLDERNAME, $folder_name);
			$res = B2DB::getTable('TBGMessageFoldersTable')->doInsert($crit);
	
			return $res->getInsertID();
		}
	
		public function deleteFolder($folder_id, $force = false)
		{
			if (!$force)
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGMessageFoldersTable::UID, TBGContext::getUser()->getID());
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
				$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getID());
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
			$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getID());
			B2DB::getTable('TBGMessagesTable')->doUpdate($crit);
		}
	
		public function moveMessage($msg_id, $to_folder)
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGMessagesTable::FOLDER, $to_folder);
			$crit->addWhere(TBGMessagesTable::ID, $msg_id);
			$crit->addWhere(TBGMessagesTable::TO_USER, TBGContext::getUser()->getID());
			B2DB::getTable('TBGMessagesTable')->doUpdate($crit);
		}
	
		public function sendMessage($to_id, $to_team, $title, $content)
		{
			$now = NOW;
	
			$crit = new B2DBCriteria();
			$crit->addInsert(TBGMessagesTable::FROM_USER, TBGContext::getUser()->getID());
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
				$theTeam = TBGContext::factory()->TBGTeam($to_id);
				
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
			$crit->addWhere(TBGMessagesTable::TO_USER, (int) TBGContext::getUser()->getID());
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
		
		public function listen_teamactionsBottom(TBGEvent $event)
		{
			/*$tid = array_shift($vars);
			$closemenustring = array_shift($vars);
			$msgstring = '';
			if (TBGUser::isThisGuest() == false)
			{
				$retval = '<div style="padding: 2px;"><a href="javascript:void(0);" onclick="' . $msgstring . $closemenustring . '">' . TBGContext::getI18n()->__('Send a message to this team') . '</a></div>';
			}
			return $retval;*/
		}
		
		public function listen_useractionsBottom(TBGEvent $event)
		{
			//if (!is_array($vars)) throw new Exception('something went wrong');
			$user = $event->getSubject();
			$closemenustring = $event->getParameter('closemenu_string');
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
		
		public function listen_accountSettingsList(TBGEvent $event)
		{
			include_template('messages/accountsettingslist');
		}
		
		public function listen_accountSettings(TBGEvent $event)
		{
			/*if ($module != $this->getName()) return;
			if (TBGContext::getRequest()->getParameter('viewmode'))
			{
				TBGContext::getModule('messages')->saveSetting('viewmode', TBGContext::getRequest()->getParameter('viewmode'), TBGContext::getUser()->getID());
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
			<option value=1 <?php if (TBGSettings::get('viewmode', 'messages', null, TBGContext::getUser()->getID()) == 1) echo ' selected'; ?>><?php echo TBGContext::getI18n()->__('Standard layout'); ?></option>
			<option value=2 <?php if (TBGSettings::get('viewmode', 'messages', null, TBGContext::getUser()->getID()) == 2) echo ' selected'; ?>><?php echo TBGContext::getI18n()->__('Wide layout'); ?></option>
			</select>
			</td>
			</tr>
			<tr>
			<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo TBGContext::getI18n()->__('Save'); ?>"></td>
			</tr>
			</table>
			</form>
			<?php*/
		}

		public function listen_messagesSummary(TBGEvent $event)
		{
			include_template('messages/messagessummary', array('messages' => $this->countMessages(1)));
		}
		
	}
?>