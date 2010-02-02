<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		tbg_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if (TBGContext::getRequest()->getParameter('selectedscope'))
		{
			$theScope = TBGFactory::scopeLab((int) TBGContext::getRequest()->getParameter('selectedscope'));
		}
		else
		{
			$theScope = null;
		}

		if ($access_level == "full")
		{
			if (TBGContext::getRequest()->isAjaxCall())
			{
				if (TBGContext::getRequest()->getParameter('setscopeadmin'))
				{
					$theScope->setScopeAdmin(TBGContext::getRequest()->getParameter('id'));
				}
				if (TBGContext::getRequest()->getParameter('getscopeadmin'))
				{
					echo tbg_userDropdown($theScope->getScopeAdmin());
				}
				
				exit();
			}
			
			if (TBGContext::getRequest()->getParameter('setdefaultscope'))
			{
				$defaultscope = TBGContext::getRequest()->getParameter('defaultscope');
				TBGSettings::saveSetting('defaultscope' , $defaultscope);
			}
			
			if (TBGContext::getRequest()->getParameter('createnewscope'))
			{
				$newScope = true;
			}
			else
			{
				$newScope = false;
			}
	
			if (TBGContext::getRequest()->getParameter('createscope'))
			{
				$theErr = "";
				$theErr2 = "";
				$isUpdated = false;
				$newScope = true;
	
				$scopeShortname = TBGContext::getRequest()->getParameter('shortname');
				$scopeHostname = TBGContext::getRequest()->getParameter('hostname');
				$scopeName = TBGContext::getRequest()->getParameter('scopename');
				$scopeEnabled = (TBGContext::getRequest()->getParameter('enabled') == "1") ? 1 : 0;
				$scopeDescription = TBGContext::getRequest()->getParameter('description');
	
				if ($scopeName != "")
				{
					if ($scopeShortname != "")
					{
						try
						{
							$theScope = TBGScope::createNew($scopeShortname, $scopeName, $scopeEnabled, $scopeDescription, $scopeHostname);
							$isAdded = true;
							$newScope = false;
						}
						catch (Exception $e)
						{
							$theErr = $e->getMessage();
						}
					}
					else
					{
						$theErr = "shortname";
					}
				}
				else
				{
					$theErr = "scopename";
				}
			}
	
			if (TBGContext::getRequest()->getParameter('deletescope') && $theScope->getID() != TBGSettings::getDefaultScope()->getID())
			{
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGBuildsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGBuildsTable')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(TBGListTypesTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGListTypesTable')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(TBGEditionComponentsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGEditionComponentsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGEditionsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGEditionsTable')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(TBGGroupsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGGroupsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGIssuesTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGIssuesTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGIssueTypesTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGIssueTypesTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGModulePermissionsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGModulePermissionsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGPermissionsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGPermissionsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGProjectsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGProjectsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGSettingsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGSettingsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGTeamsTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGTeamsTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGUserStateTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGUserStateTable')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(TBGUsersTable::SCOPE, $theScope->getID());
				B2DB::getTable('TBGUsersTable')->doDelete($crit);
				
				B2DB::getTable('TBGScopesTable')->doDeleteById($theScope->getID());
				
				TBGContext::trigger('core', 'delete_scope', $theScope);
				
				$theScope = null;
			}
	
			if (TBGContext::getRequest()->getParameter('savescopesettings'))
			{
				$theErr = "";
				$isUpdated = false;
	
				$scopeShortname = TBGContext::getRequest()->getParameter('shortname');
				$scopeName = TBGContext::getRequest()->getParameter('scopename');
				$scopeHostname = TBGContext::getRequest()->getParameter('hostname');
				$scopeEnabled = (TBGContext::getRequest()->getParameter('enabled') == "1") ? 1 : 0;
				$scopeDescription = TBGContext::getRequest()->getParameter('description');
	
				if ($scopeName != "")
				{
					if ($scopeShortname != "")
					{
						$theScope->setName($scopeName);
						$theScope->setEnabled($scopeEnabled);
						$theScope->setShortname($scopeShortname);
						$theScope->setDescription($scopeDescription);
						$theScope->setHostname($scopeHostname);
						$isUpdated = true;
					}
					else
					{
						$theErr = "shortname";
					}
				}
				else
				{
					$theErr = "scopename";
				}
			}
		}
	}
	
?>