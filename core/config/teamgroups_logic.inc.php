<?php

	$theGroup = (is_numeric(TBGContext::getRequest()->getParameter('group')) && TBGContext::getRequest()->getParameter('group') > 0) ? TBGFactory::groupLab(TBGContext::getRequest()->getParameter('group')): null;
	$theTeam = (is_numeric(TBGContext::getRequest()->getParameter('team')) && TBGContext::getRequest()->getParameter('team') > 0) ? TBGFactory::teamLab(TBGContext::getRequest()->getParameter('team')) : null;

	if ($access_level == "full" && !TBGContext::getRequest()->hasParameter('access_level'))
	{
		if (TBGContext::getRequest()->getParameter('get_editgroup') && $theGroup instanceof TBGGroup)
		{
			$retval = '';
			if ($access_level == "full")
			{
				$retval .= '<form accept-charset="' . TBGContext::getI18n()->getCharset() . '" action="config.php" method="post" id="edit_group_' . $theGroup->getID() . '_form" onsubmit="return false">';
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
		
		if ((TBGContext::getRequest()->getParameter('get_showgroup') || TBGContext::getRequest()->getParameter('updatename')) && $theGroup instanceof TBGGroup)
		{
			$include_table = false;
			$aGroup = $theGroup;
			unset($theGroup);
			require TBGContext::getIncludePath() . 'include/config/teamgroups_groupbox.inc.php';
			$theGroup = $aGroup;
			unset($aGroup);
		}

		if (TBGContext::getRequest()->getParameter('get_editteam') && $theTeam instanceof TBGTeam)
		{
			$retval = '';
			if ($access_level == "full")
			{
				$retval .= '<form accept-charset="' . TBGContext::getI18n()->getCharset() . '" action="config.php" method="post" id="edit_team_' . $theTeam->getID() . '_form" onsubmit="return false">';
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
		
		if ((TBGContext::getRequest()->getParameter('get_showteam') || TBGContext::getRequest()->getParameter('updatename')) && $theTeam instanceof TBGTeam)
		{
			$include_table = false;
			$aTeam = $theTeam;
			unset($theTeam);
			require TBGContext::getIncludePath() . 'include/config/teamgroups_teambox.inc.php';
			$theTeam = $aTeam;
			unset($aTeam);
		}
		
		if (TBGContext::getRequest()->hasParameter('addgroup') && trim(TBGContext::getRequest()->getParameter('groupname')) != "")
		{
			$groupname = trim(TBGContext::getRequest()->getParameter('groupname'));
			$aGroup = TBGGroup::createNew($groupname);
			$include_table = true;
			require TBGContext::getIncludePath() . 'include/config/teamgroups_groupbox.inc.php';
		}
	
		if (TBGContext::getRequest()->hasParameter('addteam') && trim(TBGContext::getRequest()->getParameter('teamname')) != "")
		{
			$teamname = trim(TBGContext::getRequest()->getParameter('teamname'));
			$aTeam = TBGTeam::createNew($teamname);
			$include_table = true;
			require TBGContext::getIncludePath() . 'include/config/teamgroups_teambox.inc.php';
		}
		
		if ($theGroup instanceof TBGGroup)
		{
			if (TBGContext::getRequest()->getParameter('remove') && TBGContext::getRequest()->getParameter('confirm'))
			{
				$theGroup->delete();
				$theGroup = null;
				TBGContext::getRequest()->setParameter('group', null);
			}
		}
		
		if ($theTeam instanceof TBGTeam)
		{
			if (TBGContext::getRequest()->getParameter('remove') && TBGContext::getRequest()->getParameter('confirm'))
			{
				$theTeam->delete();
				$theTeam = null;
				TBGContext::getRequest()->setParameter('team', null);
			}
		}
	}

?>