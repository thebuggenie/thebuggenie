<?php

	$theGroup = (is_numeric(BUGScontext::getRequest()->getParameter('group')) && BUGScontext::getRequest()->getParameter('group') > 0) ? BUGSfactory::groupLab(BUGScontext::getRequest()->getParameter('group')): null;
	$theTeam = (is_numeric(BUGScontext::getRequest()->getParameter('team')) && BUGScontext::getRequest()->getParameter('team') > 0) ? BUGSfactory::teamLab(BUGScontext::getRequest()->getParameter('team')) : null;

	if ($access_level == "full" && !BUGScontext::getRequest()->hasParameter('access_level'))
	{
		if (BUGScontext::getRequest()->getParameter('get_editgroup') && $theGroup instanceof BUGSgroup)
		{
			$retval = '';
			if ($access_level == "full")
			{
				$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="config.php" method="post" id="edit_group_' . $theGroup->getID() . '_form" onsubmit="return false">';
				$retval .= '<input type="hidden" name="group" value=' . $theGroup->getID() . '>';
				$retval .= '<input type="hidden" name="updatename" value="true">';
				$retval .= '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
				$retval .= '<tr>';
				$retval .= '<td style="padding: 2px; width: auto;"><input type="text" name="groupname" value="' . $theGroup->getName() . '" style="width: 100%;"></td>';
				$retval .= '<td style="width: 50px;"><button onclick="updateGroup(' . $theGroup->getID() . ');" style="width: 100%;">' . __('Update') . '</button></td>';
				$retval .= '<td valign="middle" style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="getShowGroup(' . $theGroup->getID() . ');" style="font-size: 9px;">' . __('Cancel') . '</a></td>';
				$retval .= '</tr>';
				$retval .= '</table>';
				$retval .= '</form>';
			}
			echo $retval;
		}
		
		if (isset($_POST['updatename']) && $_POST['updatename'] == 'true' && isset($_POST['groupname']) && trim($_POST['groupname']) != "")
		{
			$theGroup->setName(trim($_POST['groupname']));
		}
		
		if ((BUGScontext::getRequest()->getParameter('get_showgroup') || BUGScontext::getRequest()->getParameter('updatename')) && $theGroup instanceof BUGSgroup)
		{
			$include_table = false;
			$aGroup = $theGroup;
			unset($theGroup);
			require BUGScontext::getIncludePath() . 'include/config/teamgroups_groupbox.inc.php';
			$theGroup = $aGroup;
			unset($aGroup);
		}

		if (BUGScontext::getRequest()->getParameter('get_editteam') && $theTeam instanceof BUGSteam)
		{
			$retval = '';
			if ($access_level == "full")
			{
				$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="config.php" method="post" id="edit_team_' . $theTeam->getID() . '_form" onsubmit="return false">';
				$retval .= '<input type="hidden" name="team" value=' . $theTeam->getID() . '>';
				$retval .= '<input type="hidden" name="updatename" value="true">';
				$retval .= '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
				$retval .= '<tr>';
				$retval .= '<td style="padding: 2px; width: auto;"><input type="text" name="teamname" value="' . $theTeam->getName() . '" style="width: 100%;"></td>';
				$retval .= '<td style="width: 50px;"><button onclick="updateTeam(' . $theTeam->getID() . ');" style="width: 100%;">' . __('Update') . '</button></td>';
				$retval .= '<td valign="middle" style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="getShowTeam(' . $theTeam->getID() . ');" style="font-size: 9px;">' . __('Cancel') . '</a></td>';
				$retval .= '</tr>';
				$retval .= '</table>';
				$retval .= '</form>';
			}
			echo $retval;
		}
		
		if (isset($_POST['updatename']) && $_POST['updatename'] == 'true' && isset($_POST['teamname']) && trim($_POST['teamname']) != "")
		{
			$theTeam->setName(trim($_POST['teamname']));
		}
		
		if ((BUGScontext::getRequest()->getParameter('get_showteam') || BUGScontext::getRequest()->getParameter('updatename')) && $theTeam instanceof BUGSteam)
		{
			$include_table = false;
			$aTeam = $theTeam;
			unset($theTeam);
			require BUGScontext::getIncludePath() . 'include/config/teamgroups_teambox.inc.php';
			$theTeam = $aTeam;
			unset($aTeam);
		}
		
		if (BUGScontext::getRequest()->hasParameter('addgroup') && trim(BUGScontext::getRequest()->getParameter('groupname')) != "")
		{
			$groupname = trim(BUGScontext::getRequest()->getParameter('groupname'));
			$aGroup = BUGSgroup::createNew($groupname);
			$include_table = true;
			require BUGScontext::getIncludePath() . 'include/config/teamgroups_groupbox.inc.php';
		}
	
		if (BUGScontext::getRequest()->hasParameter('addteam') && trim(BUGScontext::getRequest()->getParameter('teamname')) != "")
		{
			$teamname = trim(BUGScontext::getRequest()->getParameter('teamname'));
			$aTeam = BUGSteam::createNew($teamname);
			$include_table = true;
			require BUGScontext::getIncludePath() . 'include/config/teamgroups_teambox.inc.php';
		}
		
		if ($theGroup instanceof BUGSgroup)
		{
			if (BUGScontext::getRequest()->getParameter('remove') && BUGScontext::getRequest()->getParameter('confirm'))
			{
				$theGroup->delete();
				$theGroup = null;
				BUGScontext::getRequest()->setParameter('group', null);
			}
		}
		
		if ($theTeam instanceof BUGSteam)
		{
			if (BUGScontext::getRequest()->getParameter('remove') && BUGScontext::getRequest()->getParameter('confirm'))
			{
				$theTeam->delete();
				$theTeam = null;
				BUGScontext::getRequest()->setParameter('team', null);
			}
		}
	}

?>