<?php

	if (BUGScontext::getRequest()->isAjaxCall())
	{
		if (BUGScontext::getRequest()->getParameter('makeissuetypedefaultfornewissues') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			BUGSsettings::saveSetting('defaultissuetypefornewissues', BUGScontext::getRequest()->getParameter('i_id'));
			$include_table = true;
			foreach (BUGSissuetype::getAll() as $aDatatype)
			{
				require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
			}
		}
		if (BUGScontext::getRequest()->getParameter('makeseveritydefaultfornewissues') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			BUGSsettings::saveSetting('defaultseverityfornewissues', BUGScontext::getRequest()->getParameter('i_id'));
			$include_table = true;
			foreach (BUGSdatatype::getAll(BUGSdatatype::SEVERITY) as $aDatatype)
			{
				$aDatatype = BUGSfactory::datatypeLab($aDatatype, BUGSdatatype::SEVERITY);
				require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
			}
		}
		if (BUGScontext::getRequest()->getParameter('makedefaultfortask') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTypes::IS_TASK, 0);
			$crit->addWhere(B2tIssueTypes::SCOPE, BUGScontext::getScope()->getID());
			B2DB::getTable('B2tIssueTypes')->doUpdate($crit);
			
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTypes::IS_TASK, 1);
			B2DB::getTable('B2tIssueTypes')->doUpdateById($crit, (int) BUGScontext::getRequest()->getParameter('i_id'));
			$include_table = true;
			foreach (BUGSissuetype::getAll() as $aDatatype)
			{
				require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
			}
		}
	
		if (BUGScontext::getRequest()->getParameter('delete_issuetype') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			$res = B2DB::getTable('B2tIssueTypes')->doDeleteById(BUGScontext::getRequest()->getParameter('i_id'));
		}
	
		if (BUGScontext::getRequest()->getParameter('add_issuetype'))
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tIssueTypes::NAME, BUGScontext::getRequest()->getParameter('issue_name'));
			$crit->addInsert(B2tIssueTypes::SCOPE, BUGScontext::getScope()->getID());
			$res = B2DB::getTable('B2tIssueTypes')->doInsert($crit);
			$include_table = true;
			$aDatatype = BUGSfactory::BUGSissuetypeLab($res->getInsertID(), BUGSdatatype::ISSUETYPE);
			require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
		}
	
		if (BUGScontext::getRequest()->getParameter('edit_issuetype') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tIssueTypes::NAME, BUGScontext::getRequest()->getParameter('issue_name'));
			$crit->addUpdate(B2tIssueTypes::APPLIES_TO, BUGScontext::getRequest()->getParameter('applies_to'));
			if (BUGScontext::getRequest()->getParameter('applies_to') != 0)
			{
				$crit->addUpdate(B2tIssueTypes::APPLIES_TYPE, 1);
			}
			else
			{
				$crit->addUpdate(B2tIssueTypes::APPLIES_TYPE, 0);
			}
			$crit->addWhere(B2tIssueTypes::ID, (int) BUGScontext::getRequest()->getParameter('i_id'));
			B2DB::getTable('B2tIssueTypes')->doUpdate($crit);
			$aDatatype = BUGSfactory::BUGSissuetypeLab(BUGScontext::getRequest()->getParameter('i_id'), BUGSdatatype::ISSUETYPE);
			require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
		}
		
		if (BUGScontext::getRequest()->getParameter('get_editissuetype') && is_numeric(BUGScontext::getRequest()->getParameter('i_id')))
		{
			$anIssueType = BUGSfactory::BUGSissuetypeLab(BUGScontext::getRequest()->getParameter('i_id'), BUGSdatatype::ISSUETYPE);
			?>
			<div id="edit_issuetype_<?php print $anIssueType->getID(); ?>" style="width: 100%;">
			<table style="margin-top: 0px; width: 100%;" class="configstrip" cellpadding=0 cellspacing=0>
			<tr>
			<td valign="middle" class="cleft" style="width: 20px;"><?php echo image_tag('icon_edit.png'); ?></td>
			<td valign="middle" class="cright" style="width: auto;"><b><?php echo __('Edit "%issue_type%"', array('%issue_type%' => $anIssueType)); ?></b></td>
			</tr>
			</table>
			<div style="padding-left: 10px;">
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="update_datatype_form" onsubmit="return false">
			<input type="hidden" name="module" value="core">
			<input type="hidden" name="section" value=4>
			<input type="hidden" name="subsection" value=1>
			<input type="hidden" name="edit_issuetype" value="true">
			<input type="hidden" name="i_id" value="<?php print $anIssueType->getID(); ?>">
			<table style="width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: 80px; padding: 3px;"><b><?php echo __('Name:'); ?></b></td>
			<td style="padding: 2px; padding-right: 0px; width: auto;"><input type="text" name="issue_name" value="<?php print $anIssueType->getName(); ?>" style="width: 100%;"></td>
			</tr>
			<tr>
			<td style="width: 80px; padding: 3px;"><b><?php echo __('Availability:') ?></b></td>
			<td style="padding: 2px; width: auto; padding-right: 0px;">
			<select name="applies_to" style="width: 100%;">
			<option value=0><?php echo __('Available for all projects'); ?></option>
			<?php

				foreach (BUGSproject::getAll() as $aProject)
				{
					$aProject = BUGSfactory::projectLab($aProject['id']);
					?><option value=<?php echo $aProject->getID(); echo ($anIssueType->getAppliesTo() instanceof BUGSproject && $anIssueType->getAppliesTo()->getID() == $aProject->getID()) ? ' selected' : ''; ?>><?php echo __('Available only for %item_name%', array('%item_name%' => $aProject)); ?></option><?php
				}
			
			?>
			</select>
			</td>
			</tr>
			<tr>
			<td colspan=2 style="text-align: right; padding: 2px; padding-right: 0px;"><button style="width: 15%;" onclick="updateIssuetype(<?php echo $anIssueType->getID(); ?>);"><?php echo __('Update'); ?></button></td>
			</tr>
			</table>
			</form>
			<div style="padding: 2px; border-bottom: 1px solid #CCC;"><b><?php echo __('Default issue type'); ?></b></div>
			<div style="padding: 3px;">
				<?php echo __('New issues gets assigned an issue type by default, unless changed in the reporting wizard'); ?>.
				<?php echo __('If you want to make all new issues from now on default to this issue type, click "Make default for new issues"'); ?>.<br>
				<div style="padding-top: 5px; text-align: right;"><a href="javascript:void(0);" onclick="makeIssueTypeDefaultForNewIssues(<?php echo $anIssueType->getID(); ?>);"><b><?php echo __('Make default for new issues'); ?></b></a></div>
			</div>
			<div style="padding: 2px; border-bottom: 1px solid #CCC;"><b><?php echo __('Task promotion'); ?></b></div>
			<div style="padding: 3px;">
				<?php echo __('Tasks that can be promoted to issues will automatically be assigned a specific issue type.'); ?>
				<?php echo __('If you want to make all tasks promoted to issues from now on default to this issue type, click "Make default for promoted tasks".'); ?><br>
				<div style="padding-top: 5px; text-align: right;"><a href="javascript:void(0);" onclick="makeDefaultForTask(<?php echo $anIssueType->getID(); ?>);"><b><?php echo __('Make default for promoted tasks'); ?></b></a></div>
			</div>
			<div style="padding-top: 5px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Fade('edit_issuetype_<?php echo $anIssueType->getID(); ?>', { duration: 0.5 });"><?php echo __('Never mind'); ?></a></div>
			</div>
			</div>
			<?php
		}
		
		if (BUGScontext::getRequest()->getParameter('get_editdatatype') && is_numeric(BUGScontext::getRequest()->getParameter('l_id')))
		{
			$aDatatype = new BUGSdatatype(BUGScontext::getRequest()->getParameter('l_id'), BUGScontext::getRequest()->getParameter('i_type'));
			$retval = '';
			if ($access_level == "full")
			{
				$retval .= '<form accept-charset="' . BUGScontext::getI18n()->getCharset() . '" action="config.php" method="post" id="edit_datatype_form" onsubmit="return false">';
				$retval .= '<input type="hidden" name="subsection" value=' . BUGScontext::getRequest()->getParameter('subsection') . '>';
				$retval .= '<input type="hidden" name="i_type" value=' . BUGScontext::getRequest()->getParameter('i_type') . '>';
				$retval .= '<input type="hidden" name="updatedatatype" value="true">';
				$retval .= '<input type="hidden" name="l_id" value="' . $aDatatype->getID() . '">';
				$retval .= '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
				$retval .= '<tr>';
				if (BUGScontext::getRequest()->getParameter('i_type') == BUGSdatatype::STATUS)
				{
					$retval .= '<td style="padding: 2px; width: 20px;"><select name="datatype_itemdata" style="width: 100%;">';
					foreach ($GLOBALS['BUGS_COLORS'] as $aColor)
					{
						$retval .= bugs_printColorOptions($aColor, $aDatatype->getItemdata());
					}
					$retval .= '</select></td>';
				}
				elseif (BUGScontext::getRequest()->getParameter('i_type') == BUGSdatatype::PRIORITY)
				{
					$retval .= '<td style="padding: 2px; width: 20px;"><input type="text" name="datatype_itemdata" value="' . $aDatatype->getItemdata() . '" style="width: 100%;"></td>';
				}
				$retval .= '<td style="padding: 2px; width: auto;"><input type="text" name="datatype_name" value="' . $aDatatype->getName() . '" style="width: 100%;"></td>';
				$retval .= '<td style="width: 50px;"><button onclick="updateDatatype(' . $aDatatype->getID() . ');" style="width: 100%;">' . __('Update') . '</button></td>';
				$retval .= '<td valign="middle" style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="getShowDatatype(' . $aDatatype->getID() . ', ' . BUGScontext::getRequest()->getParameter('subsection') . ', \'' . $aDatatype->getItemtype() . '\');" style="font-size: 9px;">' . __('Cancel') . '</a></td>';
				$retval .= '</tr>';
				$retval .= '</table>';
				$retval .= '</form>';
			}
			echo $retval;
		}
		
		if (BUGScontext::getRequest()->getParameter('get_showdatatype') && is_numeric(BUGScontext::getRequest()->getParameter('l_id')))
		{
			$aDatatype = BUGSfactory::datatypeLab(BUGScontext::getRequest()->getParameter('l_id'), BUGScontext::getRequest()->getParameter('i_type'));
			require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
		}
		
		if (BUGScontext::getRequest()->getParameter('updatedatatype') && is_numeric(BUGScontext::getRequest()->getParameter('l_id')))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tListTypes::NAME, BUGScontext::getRequest()->getParameter('datatype_name'));
			$crit->addUpdate(B2tListTypes::ITEMDATA, BUGScontext::getRequest()->getParameter('datatype_itemdata', ''));
			$crit->addWhere(B2tListTypes::SCOPE, BUGScontext::getScope()->getID());
			$res = B2DB::getTable('B2tListTypes')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('l_id'));
			
			$aDatatype = BUGSfactory::datatypeLab(BUGScontext::getRequest()->getParameter('l_id'), BUGScontext::getRequest()->getParameter('i_type'));
			require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
		}

		if (BUGScontext::getRequest()->getParameter('add_datatype') && BUGScontext::getRequest()->getParameter('datatype_name') != '' && BUGScontext::getRequest()->getParameter('datatype') != '')
		{
			$crit = new B2DBCriteria();
			$crit->addInsert(B2tListTypes::NAME, BUGScontext::getRequest()->getParameter('datatype_name'));
			$crit->addInsert(B2tListTypes::ITEMTYPE, BUGScontext::getRequest()->getParameter('datatype'));
			$crit->addInsert(B2tListTypes::ITEMDATA, BUGScontext::getRequest()->getParameter('datatype_itemdata', ''));
			$crit->addInsert(B2tListTypes::SCOPE, BUGScontext::getScope()->getID());
			$res = B2DB::getTable('B2tListTypes')->doInsert($crit);
			
			$aDatatype = BUGSfactory::datatypeLab($res->getInsertID(), BUGScontext::getRequest()->getParameter('datatype'));
			$include_table = true;
			require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
		}
		
		if (BUGScontext::getRequest()->getParameter('delete_datatype') && is_numeric(BUGScontext::getRequest()->getParameter('l_id')))
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tListTypes::ID, BUGScontext::getRequest()->getParameter('l_id'));
			$crit->addWhere(B2tListTypes::SCOPE, BUGScontext::getScope()->getID());
			$res = B2DB::getTable('B2tListTypes')->doDelete($crit);
		}
	}
		
	if (BUGScontext::getRequest()->getParameter('edit_userstate') && is_numeric(BUGScontext::getRequest()->getParameter('s_id')))
	{
		if (BUGScontext::getRequest()->getParameter('state_name') != '')
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tUserState::STATE_NAME, BUGScontext::getRequest()->getParameter('state_name'));
			$crit->addUpdate(B2tUserState::ABSENT, (BUGScontext::getRequest()->getParameter('check_absent') == 'on') ? 1 : 0);
			$crit->addUpdate(B2tUserState::BUSY, (BUGScontext::getRequest()->getParameter('check_busy') == 'on') ? 1 : 0);
			$crit->addUpdate(B2tUserState::MEETING, (BUGScontext::getRequest()->getParameter('check_meeting') == 'on') ? 1 : 0);
			$crit->addUpdate(B2tUserState::ONLINE, (BUGScontext::getRequest()->getParameter('check_online') == 'on') ? 1 : 0);
			$crit->addUpdate(B2tUserState::UNAVAILABLE, (BUGScontext::getRequest()->getParameter('check_unavailable') == 'on') ? 1 : 0);
			$crit->addUpdate(B2tUserState::COLOR, BUGScontext::getRequest()->getParameter('state_color'));
			B2DB::getTable('B2tUserState')->doUpdateById($crit, BUGScontext::getRequest()->getParameter('s_id'));
		}
	}
	
	if (BUGScontext::getRequest()->getParameter('delete_userstate') && is_numeric(BUGScontext::getRequest()->getParameter('s_id')))
	{
		B2DB::getTable('B2tUserState')->doDeleteById(BUGScontext::getRequest()->getParameter('s_id'));
	}
	
	if (BUGScontext::getRequest()->getParameter('add_userstate') && BUGScontext::getRequest()->getParameter('state_name') != '')
	{
		$crit = new B2DBCriteria();
		$crit->addInsert(B2tUserState::STATE_NAME, BUGScontext::getRequest()->getParameter('state_name'));
		$crit->addInsert(B2tUserState::ABSENT, (BUGScontext::getRequest()->getParameter('check_absent') == 'on') ? 1 : 0);
		$crit->addInsert(B2tUserState::BUSY, (BUGScontext::getRequest()->getParameter('check_busy') == 'on') ? 1 : 0);
		$crit->addInsert(B2tUserState::MEETING, (BUGScontext::getRequest()->getParameter('check_meeting') == 'on') ? 1 : 0);
		$crit->addInsert(B2tUserState::ONLINE, (BUGScontext::getRequest()->getParameter('check_online') == 'on') ? 1 : 0);
		$crit->addInsert(B2tUserState::UNAVAILABLE, (BUGScontext::getRequest()->getParameter('check_unavailable') == 'on') ? 1 : 0);
		$crit->addInsert(B2tUserState::COLOR, BUGScontext::getRequest()->getParameter('state_color'));
		$crit->addInsert(B2tUserState::SCOPE, BUGScontext::getScope()->getID());
		B2DB::getTable('B2tUserState')->doInsert($crit);
	}
	
?>