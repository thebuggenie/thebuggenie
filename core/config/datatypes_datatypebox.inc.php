<?php

	
	if (!$aDatatype instanceof BUGSdatatype)
	{
		exit();
	}

	$retval = '';
	if ($include_table)
	{
		$retval .= '<span id="show_datatype_' . $aDatatype->getID() . '">';
	}
	$retval .= '<table style="width: 100%;" cellpadding=0 cellspacing=0>';
	
	switch($aDatatype->getItemtype())
	{
		case BUGSdatatype::ISSUETYPE:
			$retval .= '<tr>';
			$retval .= '<td style="padding: 2px; width: auto;">';
			$retval .= '<b>' . $aDatatype->getName() . '</b>';
			if ($aDatatype->appliesToProject())
			{
				$retval .= '<div style="color: #999;">' . __('Available only for %item_name%', array('%item_name%' => $aDatatype->getAppliesTo()->getName())) . '</div>';
			} 
			if ($aDatatype->isTask())
			{
				$retval .= '<div style="color: #999;">' . __('This is the default issue type when promoting a task') . '</div>';
			} 
			if ($aDatatype->isDefaultForIssues())
			{
				$retval .= '<div style="color: #999;">' . __('This is the default issue type when creating a new issue') . '</div>';
			} 
			$retval .= '</td>';
			if ($access_level == "full")
			{
				$retval .= '<td valign="middle" style="width: 15px;"><a href="javascript:void(0);" onclick="getIssuetypeEdit(' . $aDatatype->getID() . ');" class="image">' . image_tag('icon_edit.png') . '</a></td>';
				$retval .= '<td valign="middle" align="right" style="width: 20px;"><a href="javascript:void(0);" onclick="Effect.Appear(\'delete_datatype_' . $aDatatype->getID() . '\', {duration: 0.5});" class="image">' . image_tag('icon_delete.png') . '</a>';
				$retval .= '<div style="position: absolute; display: none; background-color: #FFF; border: 1px solid #DDD; padding: 5px;" id="delete_datatype_' . $aDatatype->getID() . '">';
				$retval .= '<b>' . __('Please confirm') . '</b><br>' . __('Are you sure you want to delete this item?');
				$retval .= '<br>';
				$retval .= '<a href="javascript:void(0);" onclick="deleteIssuetype(' . $aDatatype->getID() . ');">' . __('Yes') . '</a> | <a href="javascript:void(0);" onclick="Effect.Fade(\'delete_datatype_' . $aDatatype->getID() . '\', {duration: 0.5});"><b>' . __('No') . '</b></a>';
				$retval .= '</div>';
				$retval .= '</td>';
				$retval .= '</tr>';
			}
			else
			{
				$retval .= '</tr>';
			}
			break;
		case BUGSdatatype::USERSTATE:
			echo 'mustfix';
			break;
		default:
			$retval .= '<tr>';
			if ($aDatatype->getItemtype() == BUGSdatatype::STATUS)
			{
				$retval .= '<td style="padding: 2px; width: 20px;"><div style="border: 1px solid #AAA; background-color: ' . $aDatatype->getItemdata() . '; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>';
				$retval .= '<td style="padding: 2px; width: auto;">' . $aDatatype->getName() . '</td>';
			}
			elseif ($aDatatype->getItemtype() == BUGSdatatype::PRIORITY)
			{
				$retval .= '<td style="padding: 2px; width: auto;">' . $aDatatype->getItemdata() . ' - ' . $aDatatype->getName() . '</td>';
			}
			elseif ($aDatatype->getItemtype() == BUGSdatatype::SEVERITY)
			{
				$retval .= '<td style="padding: 2px; width: auto;">' . $aDatatype->getName();
				if (BUGSsettings::get('defaultseverityfornewissues') == $aDatatype->getID())
				{
					$retval .= '&nbsp;(' . __('default for new issues') . ')';
				}
				$retval .= '</td>';
			}
			else
			{
				$retval .= '<td style="padding: 2px; width: auto;">' . $aDatatype->getName() . '</td>';
			}
			if ($access_level == "full")
			{
				if ($aDatatype->getItemtype() == BUGSdatatype::SEVERITY && BUGSsettings::get('defaultseverityfornewissues') != $aDatatype->getID())
				{
					$retval .= '<td valign="middle" style="width: 15px;"><a href="javascript:void(0);" onclick="makeSeverityDefaultForNewIssues(' . $aDatatype->getID() . ', ' . BUGScontext::getRequest()->getParameter('subsection') . ', \'' . $aDatatype->getItemtype() . '\');" class="image">' . image_tag('action_ok.png') . '</a></td>';
				}
				$retval .= '<td valign="middle" style="width: 15px;"><a href="javascript:void(0);" onclick="getEditDatatype(' . $aDatatype->getID() . ', ' . BUGScontext::getRequest()->getParameter('subsection') . ', \'' . $aDatatype->getItemtype() . '\');" class="image">' . image_tag('icon_edit.png') . '</a></td>';
				$retval .= '<td valign="middle" align="right" style="width: 20px;"><a href="javascript:void(0);" onclick="Effect.Appear(\'delete_datatype_' . $aDatatype->getID() . '\', { duration: 0.5 });" class="image">' . image_tag('icon_delete.png') . '</a>';
				$retval .= '<div id="delete_datatype_' . $aDatatype->getID() . '" style="width: 200px; position: absolute; display: none; background-color: #FFF; border: 1px solid #DDD; padding: 5px;">';
				$retval .= '<b>' . __('Please confirm') . '</b><br>' . __('Are you sure you want to delete this item?');
				$retval .= '<br><div style="text-align: center; padding: 5px;"><a href="javascript:void(0);" onclick="deleteDatatype(' . $aDatatype->getID() . ')">' . __('Yes') . '</a> | <a href="javascript:void(0);" onclick="Effect.Fade(\'delete_datatype_' . $aDatatype->getID() . '\', { duration: 0.5 });">' . __('No') . '</a></div>';
				$retval .= '</td>';
				$retval .= '</tr>';
			}
			$retval .= '</tr>';
			break;
	}
	$retval .= '</table>';
	
	if ($include_table)
	{
		$retval .= '</span>';
	}
	
	echo $retval;

?>