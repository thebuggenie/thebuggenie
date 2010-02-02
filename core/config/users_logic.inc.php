<?php

	if (!defined('THEBUGGENIE_PATH')) exit();
	
	if (TBGContext::getRequest()->getParameter('searchfor') || TBGContext::getRequest()->getParameter('adduser'))
	{
		if (TBGContext::getRequest()->getParameter('validate') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::ACTIVATED, 1);
			$res = B2DB::getTable('TBGUsersTable')->doUpdateById($crit, TBGContext::getRequest()->getParameter('uid'));
		}
		if (TBGContext::getRequest()->getParameter('unvalidate') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::ACTIVATED, 0);
			$res = B2DB::getTable('TBGUsersTable')->doUpdateById($crit, TBGContext::getRequest()->getParameter('uid'));
		}

		if (TBGContext::getRequest()->getParameter('suspend') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::ENABLED, 0);
			$res = B2DB::getTable('TBGUsersTable')->doUpdateById($crit, TBGContext::getRequest()->getParameter('uid'));
		}
		if (TBGContext::getRequest()->getParameter('enable') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::ENABLED, 1);
			$res = B2DB::getTable('TBGUsersTable')->doUpdateById($crit, TBGContext::getRequest()->getParameter('uid'));
		}

		if (TBGContext::getRequest()->getParameter('addteam') != "" && is_numeric(TBGContext::getRequest()->getParameter('uid')) && is_numeric(TBGContext::getRequest()->getParameter('addteam')))
		{
			$addtoTeam = TBGFactory::teamLab(TBGContext::getRequest()->getParameter('addteam'));
			$addtoTeam->addMember(TBGContext::getRequest()->getParameter('uid'));
		}
		if (TBGContext::getRequest()->getParameter('removeteam') != "" && is_numeric(TBGContext::getRequest()->getParameter('uid')) && is_numeric(TBGContext::getRequest()->getParameter('removeteam')))
		{
			$addtoTeam = TBGFactory::teamLab(TBGContext::getRequest()->getParameter('removeteam'));
			$addtoTeam->removeMember(TBGContext::getRequest()->getParameter('uid'));
		}

		if (TBGContext::getRequest()->getParameter('delete') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$uid = TBGContext::getRequest()->getParameter('uid');
			$row = B2DB::getTable('TBGUsersTable')->doSelectById($uid);
			if (TBGSettings::get('defaultuname') == $row->get(TBGUsersTable::UNAME))
			{
				$theMessage = __('This is the default user account. You cannot delete this');
			}
			else
			{
				$crit = new B2DBCriteria();
				$crit->addUpdate(TBGUsersTable::DELETED, 1);
				B2DB::getTable('TBGUsersTable')->doUpdateById($crit, $uid);
			}
		}
		if (TBGContext::getRequest()->getParameter('restore') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(TBGUsersTable::ENABLED, 0);
			$crit->addUpdate(TBGUsersTable::DELETED, 0);
			$res = B2DB::getTable('TBGUsersTable')->doUpdateById(TBGContext::getRequest()->getParameter('uid'));
		}

		if (TBGContext::getRequest()->getParameter('purge') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			B2DB::getTable('TBGUsersTable')->doDeleteById(TBGContext::getRequest()->getParameter('uid'));
			$uid = TBGContext::getRequest()->getParameter('uid');
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGIssuesTable::POSTED_BY, TBGContext::getRequest()->getParameter('uid'));
			B2DB::getTable('TBGIssuesTable')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGCommentsTable::POSTED_BY, TBGContext::getRequest()->getParameter('uid'));
			B2DB::getTable('TBGCommentsTable')->doDelete($crit);

			$crit = new B2DBCriteria();
			$crit->addWhere(TBGBuddiesTable::UID, $uid);
			$crit->addOr(TBGBuddiesTable::BID, $uid);
			B2DB::getTable('TBGBuddiesTable')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGTeamMembersTable::UID, $uid);
			B2DB::getTable('TBGTeamMembersTable')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGPermissionsTable::UID, $uid);
			B2DB::getTable('TBGPermissionsTable')->doDelete($crit);				
		}

		if (TBGContext::getRequest()->getParameter('pwd_1') != "" && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			if (TBGContext::getRequest()->getParameter('pwd_1') != "" && TBGContext::getRequest()->getParameter('pwd_2') == TBGContext::getRequest()->getParameter('pwd_1'))
			{
				$newPass = TBGContext::getRequest()->getParameter('pwd_1', null, false);
				$md5newPass = md5($newPass);
				$theUser = TBGFactory::userLab(TBGContext::getRequest()->getParameter('uid'));
				$theUser->changePassword($newPass);
				if (TBGSettings::get('defaultuname') == $theUser->getName())
				{
					TBGSettings::saveSetting("defaultpwd", $md5newPass);
				}
			}
			else
			{
				$theMessage = __('The new password can not be blank and you have to type it twice to change it');
			}
		}

		if (TBGContext::getRequest()->getParameter('randompassword') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$newPass = tbg_createPassword();
			$md5newPass = md5($newPass);
			$theUser = TBGFactory::userLab(TBGContext::getRequest()->getParameter('uid'));
			$theUser->changePassword($newPass);
		}

		if (TBGContext::getRequest()->getParameter('saveedituname') && is_numeric(TBGContext::getRequest()->getParameter('uid')))
		{
			$newUname = TBGContext::getRequest()->getParameter('uname');
			$newEmail = TBGContext::getRequest()->getParameter('email');
			$newRealname = TBGContext::getRequest()->getParameter('realname');
			$newBuddyname = TBGContext::getRequest()->getParameter('buddyname');
			$newGroup = (int) TBGContext::getRequest()->getParameter('group');
			
			$theUser = TBGFactory::userLab((int) TBGContext::getRequest()->getParameter('uid'));
			$theUser->updateUserDetails($newRealname, $newBuddyname, $theUser->getHomepage(), $newEmail, $newUname);
			
			if ($theUser->getGroup()->getID() != $newGroup)
			{
				$theUser->setGroup($newGroup);
			}
		}

		$isSearching = true;
		$searchTerm = TBGContext::getRequest()->getParameter('searchfor');

		if (TBGContext::getRequest()->getParameter('adduser'))
		{
			$newUname = TBGContext::getRequest()->getParameter('uname');
			$newEmail = TBGContext::getRequest()->getParameter('email');
			$newRealname = TBGContext::getRequest()->getParameter('realname');
			$newBuddyname = TBGContext::getRequest()->getParameter('buddyname');
			$newGroup = (int) TBGContext::getRequest()->getParameter('group');

			$crit = new B2DBCriteria();
			$crit->addWhere(TBGUsersTable::UNAME, $newUname);
			
			if (B2DB::getTable('TBGUsersTable')->doCount($crit) > 0)
			{
				$searchTerm = $newUname;
				$theMessage = __('This user already exists');
			}
			else
			{
				$newPass = tbg_createPassword();
				$md5newPass = md5($newPass);
				$theUser = TBGUser::createNew($newUname, $newRealname, $newBuddyname, TBGContext::getScope()->getID());
				$theUser->setEmail($newEmail);
				$theUser->setGroup($newGroup);
				$theUser->setEnabled(true);
				$theUser->setValidated(true);
				TBGContext::getRequest()->setParameter('uid', $theUser->getUID());
				$searchTerm = $newUname;
			}
		}

	}
	else
	{
		$isSearching = false;
	}

?>