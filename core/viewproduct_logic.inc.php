<?php

	if (!defined('BUGS2_INCLUDE_PATH')) exit();
	
	$allowed = false;
	if (BUGScontext::getRequest()->getParameter('project_id'))
	{
		try
		{
			$theProject = BUGSfactory::projectLab(BUGScontext::getRequest()->getParameter('project_id'));
		}
		catch (Exception $e) {}
		if ($theProject instanceof BUGSproject)
		{
			if (BUGScontext::getUser()->hasPermission("b2projectaccess", $theProject->getID(), "core"))
			{
				if (is_numeric(BUGScontext::getRequest()->getParameter('edition_id')))
				{
					if (BUGScontext::getUser()->hasPermission("b2editionaccess", BUGScontext::getRequest()->getParameter('edition_id'), "core"))
					{
						$theEdition = BUGSfactory::editionLab(BUGScontext::getRequest()->getParameter('edition_id'));
						$allowed = true;
					}
				}
				else
				{
					$allowed = true;
				}
			}
		}

		$theMilestone = null;
		if (is_numeric(BUGScontext::getRequest()->getParameter('milestone_id')))
		{
			if (BUGScontext::getUser()->hasPermission("b2milestoneaccess", BUGScontext::getRequest()->getParameter('milestone_id'), "core"))
			{
				$theMilestone = BUGSfactory::milestoneLab(BUGScontext::getRequest()->getParameter('milestone_id'));
			}
		}

		if ($allowed == false)
		{
			$theProject = null;
		}
	}

?>