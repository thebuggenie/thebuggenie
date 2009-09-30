<?php

if ($access_level == 'full' && BUGScontext::getRequest()->isAjaxCall())
{
	if (BUGScontext::getRequest()->getParameter('edit_milestone') == true && is_numeric(BUGScontext::getRequest()->getParameter('m_id')))
	{
		if (BUGScontext::getUser()->hasPermission('b2milestoneaccess', BUGScontext::getRequest()->getParameter('m_id'), 'core'))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tMilestones::NAME, BUGScontext::getRequest()->getParameter('m_name'));
			$crit->addUpdate(B2tMilestones::DESCRIPTION, BUGScontext::getRequest()->getParameter('description'));
			$scheduled_for = (BUGScontext::getRequest()->getParameter('sch_date') == 1) ? mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('sch_month'), BUGScontext::getRequest()->getParameter('sch_day'), BUGScontext::getRequest()->getParameter('sch_year')) : 0;
			$crit->addUpdate(B2tMilestones::SCHEDULED, $scheduled_for);
			B2DB::getTable('B2tMilestones')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('m_id'));
			$aMilestone = new BUGSmilestone(BUGScontext::getRequest()->getParameter('m_id'));
			$include_table = false;
			require BUGScontext::getIncludePath() . 'include/config/milestones_milestonebox.inc.php';
		}
	}
	elseif (BUGScontext::getRequest()->getParameter('setvisibility') && is_numeric(BUGScontext::getRequest()->getParameter('m_id')))
	{
		if (BUGScontext::getUser()->hasPermission('b2milestoneaccess', BUGScontext::getRequest()->getParameter('m_id'), 'core'))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tMilestones::VISIBLE, BUGScontext::getRequest()->getParameter('visible'));
			B2DB::getTable('B2tMilestones')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('m_id'));
			$include_table = false;
			$aMilestone = new BUGSmilestone(BUGScontext::getRequest()->getParameter('m_id'));
			require BUGScontext::getIncludePath() . 'include/config/milestones_milestonebox.inc.php';
		}
	}
	elseif (BUGScontext::getRequest()->getParameter('add_milestone') == true)
	{
		$crit = new B2DBCriteria();
		$crit->addInsert(B2tMilestones::NAME, BUGScontext::getRequest()->getParameter('m_name'));
		if (trim(BUGScontext::getRequest()->getParameter('description')) != '')
		{
			$crit->addInsert(B2tMilestones::DESCRIPTION, BUGScontext::getRequest()->getParameter('description'));
		}
		$scheduled_for = (BUGScontext::getRequest()->getParameter('sch_date') == 1) ? mktime(0, 0, 0, BUGScontext::getRequest()->getParameter('sch_month'), BUGScontext::getRequest()->getParameter('sch_day'), BUGScontext::getRequest()->getParameter('sch_year')) : 0;
		$crit->addInsert(B2tMilestones::SCHEDULED, $scheduled_for);
		$crit->addInsert(B2tMilestones::VISIBLE, 0);
		$crit->addInsert(B2tMilestones::SCOPE, BUGScontext::getScope()->getID());
		$crit->addInsert(B2tMilestones::PROJECT, BUGScontext::getRequest()->getParameter('p_id'));
		$result = B2DB::getTable('B2tMilestones')->doInsert($crit);
		BUGScontext::setPermission('b2milestoneaccess', $result->getInsertID(), 'core', 0, BUGScontext::getUser()->getGroup()->getID(), 0, true);
		$aMilestone = new BUGSmilestone($result->getInsertID());
		$include_table = true;
		require BUGScontext::getIncludePath() . 'include/config/milestones_milestonebox.inc.php';
	}
	elseif (BUGScontext::getRequest()->getParameter('delete_milestone') && is_numeric(BUGScontext::getRequest()->getParameter('m_id')))
	{
		if (BUGScontext::getUser()->hasPermission('b2milestoneaccess', BUGScontext::getRequest()->getParameter('m_id'), 'core'))
		{
			B2DB::getTable('B2tMilestones')->doDeleteById(BUGScontext::getRequest()->getParameter('m_id'));
			$theProject->doPopulateMilestones();
			if (count($theProject->getMilestones()) == 0)
			{
				echo '<script type="text/javascript">Effect.Appear(\'nomilestones\', { duration: 0.5 });</script>';
			}
		}
	}
}

?>