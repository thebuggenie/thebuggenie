<?php

	if (!defined('THEBUGGENIE_PATH')) exit();
	
	if (BUGScontext::getRequest()->getParameter('searchfor') || BUGScontext::getRequest()->getParameter('adduser'))
	{
		if (BUGScontext::getRequest()->getParameter('validate') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ACTIVATED, 1);
			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('uid'));
		}
		if (BUGScontext::getRequest()->getParameter('unvalidate') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ACTIVATED, 0);
			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('uid'));
		}

		if (BUGScontext::getRequest()->getParameter('suspend') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ENABLED, 0);
			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('uid'));
		}
		if (BUGScontext::getRequest()->getParameter('enable') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ENABLED, 1);
			$res = B2DB::getTable('B2tUsers')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('uid'));
		}

		if (BUGScontext::getRequest()->getParameter('addteam') != "" && is_numeric(BUGScontext::getRequest()->getParameter('uid')) && is_numeric(BUGScontext::getRequest()->getParameter('addteam')))
		{
			$addtoTeam = BUGSfactory::teamLab(BUGScontext::getRequest()->getParameter('addteam'));
			$addtoTeam->addMember(BUGScontext::getRequest()->getParameter('uid'));
		}
		if (BUGScontext::getRequest()->getParameter('removeteam') != "" && is_numeric(BUGScontext::getRequest()->getParameter('uid')) && is_numeric(BUGScontext::getRequest()->getParameter('removeteam')))
		{
			$addtoTeam = BUGSfactory::teamLab(BUGScontext::getRequest()->getParameter('removeteam'));
			$addtoTeam->removeMember(BUGScontext::getRequest()->getParameter('uid'));
		}

		if (BUGScontext::getRequest()->getParameter('delete') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$uid = BUGScontext::getRequest()->getParameter('uid');
			$row = B2DB::getTable('B2tUsers')->doSelectById($uid);
			if (BUGSsettings::get('defaultuname') == $row->get(B2tUsers::UNAME))
			{
				$theMessage = __('This is the default user account. You cannot delete this');
			}
			else
			{
				$crit = new B2DBCriteria();
				$crit->addUpdate(B2tUsers::DELETED, 1);
				B2DB::getTable('B2tUsers')->doUpdateById($crit, $uid);
			}
		}
		if (BUGScontext::getRequest()->getParameter('restore') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUsers::ENABLED, 0);
			$crit->addUpdate(B2tUsers::DELETED, 0);
			$res = B2DB::getTable('B2tUsers')->doUpdateById(BUGScontext::getRequest()->getParameter('uid'));
		}

		if (BUGScontext::getRequest()->getParameter('purge') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			B2DB::getTable('B2tUsers')->doDeleteById(BUGScontext::getRequest()->getParameter('uid'));
			$uid = BUGScontext::getRequest()->getParameter('uid');
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tIssues::POSTED_BY, BUGScontext::getRequest()->getParameter('uid'));
			B2DB::getTable('B2tIssues')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tComments::POSTED_BY, BUGScontext::getRequest()->getParameter('uid'));
			B2DB::getTable('B2tComments')->doDelete($crit);

			$crit = new B2DBCriteria();
			$crit->addWhere(B2tBuddies::UID, $uid);
			$crit->addOr(B2tBuddies::BID, $uid);
			B2DB::getTable('B2tBuddies')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tTeamMembers::UID, $uid);
			B2DB::getTable('B2tTeamMembers')->doDelete($crit);
			
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tPermissions::UID, $uid);
			B2DB::getTable('B2tPermissions')->doDelete($crit);				
		}

		if (BUGScontext::getRequest()->getParameter('pwd_1') != "" && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			if (BUGScontext::getRequest()->getParameter('pwd_1') != "" && BUGScontext::getRequest()->getParameter('pwd_2') == BUGScontext::getRequest()->getParameter('pwd_1'))
			{
				$newPass = BUGScontext::getRequest()->getParameter('pwd_1', null, false);
				$md5newPass = md5($newPass);
				$theUser = BUGSfactory::userLab(BUGScontext::getRequest()->getParameter('uid'));
				$theUser->changePassword($newPass);
				if (BUGSsettings::get('defaultuname') == $theUser->getName())
				{
					BUGSsettings::saveSetting("defaultpwd", $md5newPass);
				}
			}
			else
			{
				$theMessage = __('The new password can not be blank and you have to type it twice to change it');
			}
		}

		if (BUGScontext::getRequest()->getParameter('randompassword') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$newPass = bugs_createPassword();
			$md5newPass = md5($newPass);
			$theUser = BUGSfactory::userLab(BUGScontext::getRequest()->getParameter('uid'));
			$theUser->changePassword($newPass);
		}

		if (BUGScontext::getRequest()->getParameter('saveedituname') && is_numeric(BUGScontext::getRequest()->getParameter('uid')))
		{
			$newUname = BUGScontext::getRequest()->getParameter('uname');
			$newEmail = BUGScontext::getRequest()->getParameter('email');
			$newRealname = BUGScontext::getRequest()->getParameter('realname');
			$newBuddyname = BUGScontext::getRequest()->getParameter('buddyname');
			$newGroup = (int) BUGScontext::getRequest()->getParameter('group');
			
			$theUser = BUGSfactory::userLab((int) BUGScontext::getRequest()->getParameter('uid'));
			$theUser->updateUserDetails($newRealname, $newBuddyname, $theUser->getHomepage(), $newEmail, $newUname);
			
			if ($theUser->getGroup()->getID() != $newGroup)
			{
				$theUser->setGroup($newGroup);
			}
		}

		$isSearching = true;
		$searchTerm = BUGScontext::getRequest()->getParameter('searchfor');

		if (BUGScontext::getRequest()->getParameter('adduser'))
		{
			$newUname = BUGScontext::getRequest()->getParameter('uname');
			$newEmail = BUGScontext::getRequest()->getParameter('email');
			$newRealname = BUGScontext::getRequest()->getParameter('realname');
			$newBuddyname = BUGScontext::getRequest()->getParameter('buddyname');
			$newGroup = (int) BUGScontext::getRequest()->getParameter('group');

			$crit = new B2DBCriteria();
			$crit->addWhere(B2tUsers::UNAME, $newUname);
			
			if (B2DB::getTable('B2tUsers')->doCount($crit) > 0)
			{
				$searchTerm = $newUname;
				$theMessage = __('This user already exists');
			}
			else
			{
				$newPass = bugs_createPassword();
				$md5newPass = md5($newPass);
				$theUser = BUGSuser::createNew($newUname, $newRealname, $newBuddyname, BUGScontext::getScope()->getID());
				$theUser->setEmail($newEmail);
				$theUser->setGroup($newGroup);
				$theUser->setEnabled(true);
				$theUser->setValidated(true);
				BUGScontext::getRequest()->setParameter('uid', $theUser->getUID());
				$searchTerm = $newUname;
			}
		}

	}
	else
	{
		$isSearching = false;
	}

?>