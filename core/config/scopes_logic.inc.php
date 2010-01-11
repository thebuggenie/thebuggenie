<?php

	if (($access_level != "full" && $access_level != "read") || TBGContext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
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
					echo bugs_userDropdown($theScope->getScopeAdmin());
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
				$crit->addWhere(B2tBuilds::SCOPE, $theScope->getID());
				B2DB::getTable('B2tBuilds')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(B2tListTypes::SCOPE, $theScope->getID());
				B2DB::getTable('B2tListTypes')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(B2tEditionComponents::SCOPE, $theScope->getID());
				B2DB::getTable('B2tEditionComponents')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tEditions::SCOPE, $theScope->getID());
				B2DB::getTable('B2tEditions')->doDelete($crit);

				$crit = new B2DBCriteria();
				$crit->addWhere(B2tGroups::SCOPE, $theScope->getID());
				B2DB::getTable('B2tGroups')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tIssues::SCOPE, $theScope->getID());
				B2DB::getTable('B2tIssues')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tIssueTypes::SCOPE, $theScope->getID());
				B2DB::getTable('B2tIssueTypes')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tModulePermissions::SCOPE, $theScope->getID());
				B2DB::getTable('B2tModulePermissions')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tPermissions::SCOPE, $theScope->getID());
				B2DB::getTable('B2tPermissions')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tProjects::SCOPE, $theScope->getID());
				B2DB::getTable('B2tProjects')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tSettings::SCOPE, $theScope->getID());
				B2DB::getTable('B2tSettings')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tTeams::SCOPE, $theScope->getID());
				B2DB::getTable('B2tTeams')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tUserState::SCOPE, $theScope->getID());
				B2DB::getTable('B2tUserState')->doDelete($crit);
				
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tUsers::SCOPE, $theScope->getID());
				B2DB::getTable('B2tUsers')->doDelete($crit);
				
				B2DB::getTable('B2tScopes')->doDeleteById($theScope->getID());
				
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