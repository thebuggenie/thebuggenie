<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if (BUGScontext::getScope()->getID() != BUGSsettings::getDefaultScope()->getID())
		{
			$access_level = 'read';
		}
		$isSaving = false;
		if ($access_level == 'full')
		{
			if (BUGScontext::getRequest()->isAjaxCall())
			{
				$module = BUGScontext::getModule(BUGScontext::getRequest()->getParameter('module_name'));
				if (is_numeric(BUGScontext::getRequest()->getParameter('enabled')))
				{
					if (BUGScontext::getRequest()->getParameter('enabled') == 1)
					{
						$module->enable();
					}
					else
					{
						$module->disable();
					}
					require BUGScontext::getIncludePath() . 'include/config/modulestrip.inc.php';
				}
				if (is_numeric(BUGScontext::getRequest()->getParameter('show_in_menu')))
				{
					if (BUGScontext::getRequest()->getParameter('show_in_menu') == 1)
					{
						$module->showInMenu();
					}
					else
					{
						$module->hideFromMenu();
					}
					require BUGScontext::getIncludePath() . 'include/config/modulestrip.inc.php';
				}
				if (is_numeric(BUGScontext::getRequest()->getParameter('show_in_usermenu')))
				{
					if (BUGScontext::getRequest()->getParameter('show_in_usermenu') == 1)
					{
						$module->showInUserMenu();
					}
					else
					{
						$module->hideFromUserMenu();
					}
					require BUGScontext::getIncludePath() . 'include/config/modulestrip.inc.php';
				}
				exit();
			}
		}
	}

?>