<div style="width: auto; padding: 5px; padding-top: 0px; padding-bottom: 0px;">
<table style="border-bottom: 1px solid #DDD; width: 100%; background-color: #F5F5F5; padding: 1px;">
<tr>
<?php if ($theIssue->canEditTexts() || $theIssue->canEditFields() || ($theIssue->getPostedBy()->getID() == TBGContext::getUser()->getID() && TBGContext::getUser()->isGuest() == false))
{
	?>
	<td style="width: 70px;"><div id="edit_issue_menu" class="menu_item" onmouseover="menuHover('edit_issue_menu');" onmouseout="menuUnhover('edit_issue_actions','');" onclick="showMenu('edit_issue')"><?php echo __('Edit issue'); ?></div>
	<div id="edit_issue_actions" style="display: none; width: 245px; background-color: #FFF; padding: 10px; border: 1px solid #DDD; position: absolute;">
	<table cellpadding=0 cellspacing=0 class="td1"><?php
	
	if ($theIssue->canEditTexts() || ($theIssue->getPostedBy()->getID() == TBGContext::getUser()->getID() && TBGContext::getUser()->isGuest() == false))
	{
		?><tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_title.png'); ?>
		<br>
		<div id="edit_title" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 400px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Enter the new title here, and press "Save" when you are finished.'); ?></div>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue_actions.php" enctype="multipart/form-data" method="post" name="issue_edit_title" id="issue_edit_title" onsubmit="return false">
		<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
			<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_newtitle" id="issue_newtitle_textbox" value="<?php echo $theIssue->getTitle(); ?>"></td>
			<td style="width: 40px; padding: 2px;"><button style="width: 100%;" onclick="submitNewTitle()"><?php echo __('Save'); ?></button></td>
		</tr>
		<tr>
			<td colspan=2 style="padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="showHide('edit_title');"><?php echo __('Close menu'); ?></a></td>
		</tr>
		</table>
		</form>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="Element.show('edit_title');$('issue_newtitle_textbox').focus();"><?php echo __('Edit title'); ?></a></td>
		</tr>
		<?php
	}
	
	if ($theIssue->canEditFields())
	{
		?>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_issuetypes.png'); ?>
		<br>
		<div id="edit_issuetype" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 250px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Select the issue type from the list below.'); ?></div>
		<span id="issuetype_table"><?php echo __('Loading list, please wait'); ?> ...</span>
		<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="showHide('edit_issuetype');"><?php echo __('Close menu'); ?></a></div>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="getIssueTypes();"><?php echo __('Set issue type'); ?></a></td>
		</tr>
		<?php
	}
	
	if ($theIssue->canEditTexts() || ($theIssue->getPostedBy()->getID() == TBGContext::getUser()->getID() && TBGContext::getUser()->isGuest() == false))
	{
		?>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_title.png'); ?>
		<br>
		<div id="edit_description" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 600px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Enter the new description here, and press "Save" when you are finished.'); ?></div>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_edit_description" id="issue_edit_description" onsubmit="return false">
		<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
			<td style="width: auto; padding: 2px;"><?php echo bugs_newTextArea('issue_newdescription', '150px', '100%', TBGContext::getRequest()->sanitize_input($theIssue->getDescription())); ?></td>
			<td style="width: 40px; padding: 2px;" valign="top"><button style="width: 100%;" onclick="submitNewDescription();"><?php echo __('Save'); ?></button></td>
		</tr>
		<tr>
			<td colspan=2 style="font-size: 10px; padding-top: 5px; text-align: right;"><a href="javascript:void(0)" onclick="Element.hide('edit_description');"><?php echo __('Close menu'); ?></a></td>
		</tr>
		</table>
		</form>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="showHide('edit_description');"><?php echo __('Edit description'); ?></a></td>
		</tr>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_title.png'); ?>
		<br>
		<div id="edit_repro" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 600px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Enter the new reproduction steps here, and press "Save" when you are finished.'); ?></div>
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_edit_repro" id="issue_edit_repro" onsubmit="return false">
		<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
		<table cellpadding=0 cellspacing=0 style="width: 100%;">
		<tr>
			<td style="width: auto; padding: 2px;"><?php echo bugs_newTextArea('issue_newrepro', '150px', '100%', TBGContext::getRequest()->sanitize_input($theIssue->getReproduction())); ?></td>
			<td style="width: 40px; padding: 2px;" valign="top"><button style="width: 100%;" onclick="submitNewRepro();"><?php echo __('Save'); ?></button></td>
		</tr>
		<tr>
			<td colspan=2 style="font-size: 10px; padding-top: 5px; text-align: right;"><a href="javascript:void(0)" onclick="Element.hide('edit_repro');">Close this menu</a></td>
		</tr>
		</table>
		</form>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="showHide('edit_repro');"><?php echo __('Edit reproduction steps'); ?></a></td>
		</tr>
		<?php
	}
	
	if ($theIssue->canEditFields())
	{
		?>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_category.png'); ?>
		<br>
		<div id="edit_category" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Select which category this issue should belong to, from the list of available categories below.'); ?></div>
		<span id="categories_table"><?php echo __('Please wait, loading list'); ?>...</span>
		<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_category');"><?php echo __('Close menu'); ?></a></div>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="Element.show('edit_category');getCategories();"><?php echo __('Set category'); ?></a></td>
		</tr>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_repro.png'); ?>
		<br>
		<div id="edit_reproducability" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
		<div style="padding: 2px;"><?php echo __('Select the level of reproducability for this issue, from the list of available levels below.'); ?></div>
		<span id="repros_table"><?php echo __('Please wait, loading list'); ?>...</span>
		<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_reproducability');"><?php echo __('Close menu'); ?></a></div>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="javascript:Element.show('edit_reproducability');getRepros();"><?php echo __('Set reproducability'); ?></a></td>
		</tr>
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_edition.png'); ?>
		<br>
		<div id="edit_affects" style="position: absolute; padding: 5px; display: <?php echo (TBGContext::getRequest()->getParameter('issue_addaffects')) ? "" : "none" ?>; border: 1px solid #DDD; width: 700px; background-color: #F1F1F1;">
		<div style="padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Affected edition(s) / build(s) / component(s)'); ?></b></div>
		<span id="affectslist_menu"><?php echo __('Please wait, loading list'); ?> ...</span>
		<table cellpadding=0 cellspacing=2 style="width: 100%;">
		<tr>
		<td style="width: 33%;" valign="top">
		<div style="margin-top: 10px; padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Add edition'); ?></b></div>
		<span id="editions_table"><?php echo __('Please wait, loading list'); ?> ...</span>
		</td>
		<td style="width: 33%;" valign="top">
		<div style="margin-top: 10px; padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Add build'); ?></b></div>
		<span id="builds_table"><?php echo __('Please wait, loading list'); ?> ...</span>
		</td>
		<td style="width: 34%;" valign="top">
		<div style="margin-top: 10px; padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Add component'); ?></b></div>
		<span id="components_table"><?php echo __('Please wait, loading list'); ?> ...</span>
		</td>
		</tr>
		</table>
		<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_affects');"><?php echo __('Close menu'); ?></a></div>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="Element.show('edit_affects');getAffectedInMenu();getEditions();getBuilds();getComponents();new Draggable('edit_affects');"><?php echo __('Edit edition(s) / build(s) / component(s)'); ?></a></td>
		</tr>
		<?php
	}
	?>
	</table>
	<div style="text-align: right; font-size: 9px;"><a href="javascript:void(0);" onclick="showMenu('edit_issue');menuUnhover('edit_issue_actions','');"><?php echo __('Close menu'); ?></a></div></div>
	</td>
	<?php
}
else
{
	?><td style="width: 70px;"><div id="edit_issue_menu" class="menu_item" style="color: #BBB; cursor: default;"><?php echo __('Edit issue'); ?></div>
	<div id="edit_issue_actions" style="display: none;">&nbsp;</div></td><?php
}

if ($theIssue->canEditFields())
{
	?>
	<td style="width: 110px;"><div id="progress_tracking_menu" class="menu_item" onmouseover="menuHover('progress_tracking_menu');" onmouseout="menuUnhover('progress_tracking_actions','');" onclick="showMenu('progress_tracking')"><?php echo __('Progress Tracking'); ?></div>
	<div id="progress_tracking_actions" style="display: none; width: 210px; background-color: #FFF; padding: 10px; border: 1px solid #DDD; position: absolute;">
	<?php
	
		if ($theIssue->canEditFields())
		{
			?>
			<table cellpadding=0 cellspacing=0 class="td1">
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_percent.png'); ?>
			<br>
			<div id="edit_percent" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 150px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Set percent completed'); ?></b></div>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_edit_percent" id="issue_edit_percent" onsubmit="return false">
			<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
			<table cellpadding=0 cellspacing=0 style="width: 100%;" id="issue_edit_percent">
			<tr>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setpercent" value="<?php echo $theIssue->getPercentCompleted(); ?>"></td>
				<td style="width: 40px; padding: 2px;"><button style="width: 100%;" onclick="submitNewPercent();"><?php echo __('Save'); ?></button></td>
			</tr>
			<tr>
				<td colspan=2 style="padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_percent');"><?php echo __('Close menu'); ?></a></td>
			</tr>
			</table>
			</form>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_percent');"><?php echo __('Set percent completed'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_time.png'); ?>
			<br>
			<div id="edit_estimated" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 260px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px; width: auto;"><?php echo __('Enter how much time it will take to complete this issue'); ?></div>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_edit_estimated" id="issue_edit_estimated" onsubmit="return false">
			<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
			<table cellpadding=0 cellspacing=0 style="width: 100%; table-layout: auto;">
			<tr>
				<td style="padding: 2px;"><b><?php echo __('Weeks'); ?></b></td>
				<td style="padding: 2px;"><b><?php echo __('Days'); ?></b></td>
				<td style="padding: 2px;"><b><?php echo __('Hours'); ?></b></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<?php $time_estimated = $theIssue->getTimeDetails($theIssue->getEstimatedTime()); ?>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setestimatedweeks" value="<?php echo $time_estimated['weeks']; ?>"></td>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setestimateddays" value="<?php echo $time_estimated['days']; ?>"></td>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setestimatedhours" value="<?php echo $time_estimated['hours']; ?>"></td>
				<td style="width: 40px; padding: 2px;"><button style="width: 100%;" onclick="submitNewEstimatedTime();"><?php echo __('Save'); ?></button></td>
			</tr>
			<tr>
				<td colspan=4 style="padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_estimated');"><?php echo __('Close menu'); ?></a></td>
			</tr>
			</table>
			</form>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_estimated');"><?php echo __('Set estimated time to complete'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_time.png'); ?>
			<br>
			<div id="edit_elapsed" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 260px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px; width: auto;"><?php echo __('Select how much time has been spent on this issue'); ?></div>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="viewissue.php" enctype="multipart/form-data" method="post" name="issue_edit_elapsed" id="issue_edit_elapsed" onsubmit="return false">
			<input type="hidden" name="issue_no" value="<?php echo $theIssue->getFormattedIssueNo(true); ?>">
			<table cellpadding=0 cellspacing=0 style="width: 100%; table-layout: auto;">
			<tr>
				<td style="padding: 2px;"><b><?php echo __('Weeks'); ?></b></td>
				<td style="padding: 2px;"><b><?php echo __('Days'); ?></b></td>
				<td style="padding: 2px;"><b><?php echo __('Hours'); ?></b></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<?php $time_elapsed = $theIssue->getTimeDetails($theIssue->getElapsedTime()); ?>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setelapsedweeks" value="<?php echo $time_elapsed['weeks']; ?>"></td>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setelapseddays" value="<?php echo $time_elapsed['days'] ?>"></td>
				<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="issue_setelapsedhours" value="<?php echo $time_elapsed['hours']; ?>"></td>
				<td style="width: 40px; padding: 2px;"><button style="width: 100%;" onclick="submitNewElapsedTime();"><?php echo __('Save'); ?></button></td>
			</tr>
			<tr>
				<td colspan=4 style="padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_elapsed');"><?php echo __('Close menu'); ?></a></td>
			</tr>
			</table>
			</form>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="javascript:showHide('edit_elapsed');"><?php echo __('Set time spent on this issue'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('issue_depend_add.png'); ?>
			<br>
			<div id="edit_dependant" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 700px; <?php echo (TBGContext::getRequest()->getParameter('find_dependant_issue')) ? "" : "display: none;" ?> background-color: #F1F1F1;">
			<div style="padding: 2px; width: auto;"><?php echo __('Select whether or not this issue is dependant on other issues, or whether other issues depends on this issue'); ?></div>
			<table style="table-layout: fixed; margin-top: 4px; width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="border-bottom: 1px solid #DFDFDF;"><b><?php echo __('Issue(s) which this issue depends on to be solved'); ?></b></td>
			<td style="width: 5px;"><b>&nbsp;</b></td>
			<td style="border-bottom: 1px solid #DFDFDF;"><b><?php echo __('Issue(s) which depends on this issue to be solved'); ?></b></td>
			</tr>
			<tr>
			<td id="related_p_issues_menu" valign="top"><?php echo __('Please wait, loading list'); ?> ...</td>
			<td valign="middle" style="width: 10px; text-align: center;">&nbsp;</td>
			<td id="related_c_issues_menu" valign="top"><?php echo __('Please wait, loading list'); ?> ...</td>
			</tr>
			<tr>
			<td style="padding-top: 10px;" valign="top" id="related_p_issues_search"><?php echo __('Please wait, loading search form'); ?> ...</td>
			<td>&nbsp;</td>
			<td style="padding-top: 10px;" valign="top" id="related_c_issues_search"><?php echo __('Please wait, loading search form'); ?> ...</td>
			</tr>
			</table>
			<div style="text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_dependant');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_dependant');getRelatedIssuesInMenu();getRelatedIssuesSearchBox(true, true);"><?php echo __('Add / remove dependant issues'); ?></a></td>
			</tr>
			</table>
			<?php
	
		}
	
	?>
	<div style="text-align: right; font-size: 9px;"><a href="javascript:void(0);" onclick="showMenu('progress_tracking');menuUnhover('progress_tracking_actions','');"><?php echo __('Close menu'); ?></a></div>
	</div>
	</td>
	<?php
}
else
{
	?><td style="width: 110px;"><div id="progress_tracking_menu" class="menu_item" style="color: #BBB; cursor: default;"><?php echo __('Progress Tracking'); ?></div>
	<div id="progress_tracking_actions" style="display: none;">&nbsp;</div></td><?php
}

if ($theIssue->canEditFields() || $theIssue->canEditUsers())
{
	?>
	<td style="width: 110px;"><div id="workflow_actions_menu" class="menu_item" onmouseover="menuHover('workflow_actions_menu');" onmouseout="menuUnhover('workflow_actions_actions','');" onclick="showMenu('workflow_actions')"><?php echo __('Workflow actions'); ?></div>
	<div id="workflow_actions_actions" style="display: none; width: 250px; background-color: #FFF; padding: 10px; border: 1px solid #DDD; position: absolute;">
	<table cellpadding=0 cellspacing=0 class="td1">
	<?php
	
		if ($theIssue->canEditUsers())
		{
			?>
			<tr>
			<td class="imgtd" valign="middle" ><?php echo image_tag('icon_user.png'); ?>
			<br>
			<span id="edit_owner" style="display: none;">
			<?php bugs_AJAXuserteamselector(__('Set owned by a user'), 
											__('Set owned by a team'),
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setowner=true&owned_type=1', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setowner=true&owned_type=2',
											'issue_owner', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getowner=true', 
											'issue_owner', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getowner=true',
											'edit_owner'
											); ?>
			</span>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_owner')"><?php echo __('Change owner'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_user.png'); ?>
			<br>
			<span id="edit_assignee" style="display: none;">
			<?php bugs_AJAXuserteamselector(__('Assign to a user'), 
											__('Assign to a team'),
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setassignee=true&assigned_type=1', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setassignee=true&assigned_type=2',
											'issue_assignee', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getassignee=true', 
											'issue_assignee', 
											'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getassignee=true',
											'edit_assignee'
											); ?>
			</span>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_assignee')"><?php echo __('Re/assign this issue'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_milestones.png'); ?>
			<br>
			<div id="edit_milestones" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><?php echo __('Unassign the issue from a milestone by clicking the "Remove" link. Assign the issue to a milestone by clicking its name in the list of available milestones.'); ?></div>
			<div style="padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Assigned to milestone'); ?></b></div>
			<span id="issue_assigned_milestones_menu"><table cellpadding=0 cellspacing=0 style="width: 100%;"><?php echo __('Please wait, loading list'); ?> ...</table></span>
			<div style="margin-top: 10px; padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php echo __('Available milestone(s)'); ?></b></div>
			<span id="issue_available_milestones_menu"><table cellpadding=0 cellspacing=0 style="width: 100%;"><?php echo __('Please wait, loading list'); ?> ...</table></span>
			<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_milestones');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_milestones');getMilestonesInMenu();"><?php echo __('Assign this issue to a milestone'); ?></a></td>
			</tr>
			<?php
		}
	
		if ($theIssue->canEditFields())
		{
			?>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_status.png'); ?>
			<br>
			<div id="edit_status" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><?php echo __('Select the status of this issue from the list below.'); ?></div>
			<span id="issue_status_menu"><?php echo __('Please wait, loading list'); ?> ...</span>
			<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="showHide('edit_status');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_status');getStatusList();"><?php echo __('Update status'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_resolution.png'); ?>
			<br>
			<div id="edit_resolution" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><?php echo __('Select the resolution of this issue, from the list below.'); ?></div>
			<span id="resolutions_table"><?php echo __('Please wait, loading list'); ?> ...</span>
			<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="showHide('edit_resolution');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_resolution');getResolutions();"><?php echo __('Set resolution'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_severity.png'); ?>
			<br>
			<div id="edit_severity" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><?php echo __('Select the severity of this issue, from the list below.'); ?></div>
			<span id="severities_table"><?php echo __('Please wait, loading list'); ?> ...</span>
			<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="showHide('edit_severity');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_severity');getSeverities();"><?php echo __('Set severity'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_priority.png'); ?>
			<br>
			<div id="edit_priority" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 300px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><?php echo __('Select the priority of this issue, from the list of available levels below.'); ?></div>
			<span id="priorities_table"><?php echo __('Please wait, loading list'); ?> ...</span>
			<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_priority');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_priority');getPriorities();"><?php echo __('Change priority'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_duplicate.png'); ?>
			<br>
			<div id="edit_duplicate" style="position: absolute; padding: 5px; display: none; border: 1px solid #DDD; width: 500px; background-color: #F1F1F1;">
			<div style="padding: 2px; width: auto;"><?php echo __('Select whether or not this issue is a duplicate of another issue'); ?></div>
			<table style="table-layout: fixed; margin-top: 4px; width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td id="duplicate_issue_menu" valign="top"><?php echo __('Please wait, loading list'); ?> ...</td>
			</tr>
			<tr>
			<td style="padding-top: 10px;" valign="top" id="duplicate_issues_search"><?php echo __('Please wait, loading search form'); ?> ...</td>
			</tr>
			</table>
			<div style="text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.hide('edit_duplicate');"><?php echo __('Close menu'); ?></a></div>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Element.show('edit_duplicate');getDuplicateOf();getDuplicateSearchBox();"><?php echo __('Un-/mark as duplicate of another issue'); ?></a></td>
			</tr>
			<tr>
			<td class="imgtd" valign="middle"><?php echo ($theIssue->isBlocking()) ? image_tag('icon_unblock.png') : image_tag('icon_block.png'); ?>&nbsp;</td>
			<td id="blocking_menu"><a href="javascript:void(0);" onclick="setBlocking(<?php echo ($theIssue->isBlocking()) ? 2 : 1; ?>);"><?php echo ($theIssue->isBlocking()) ? __('Mark as "Not blocking"') : __('Mark as "Blocking"'); ?></a></td>
			</tr>
			<?php
	
				if ($theIssue->getProject()->isTasksEnabled())
				{
					?>
					<tr>
					<td class="imgtd" valign="middle"><?php echo image_tag('icon_newtask.png'); ?>&nbsp;</td>
					<td><a href="javascript:void(0);" onclick="Element.show('new_task');showMenu('workflow_actions');menuUnhover('workflow_actions_actions','');"><?php echo __('Add a new task'); ?></a></td>
					</tr>
					<?php
				}
	
			?>
			<tr>
			<td class="imgtd" valign="middle"><?php echo ($theIssue->getState() == TBGIssue::STATE_CLOSED) ? image_tag('icon_reopen.png') : image_tag('icon_close.png'); ?>&nbsp;</td>
			<td><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;issue_setstate=<?php echo ($theIssue->isClosed()) ? TBGIssue::STATE_OPEN : TBGIssue::STATE_CLOSED; ?>"><?php echo ($theIssue->isClosed()) ? __('Reopen this issue') : __('Close this issue'); ?></a></td>
			</tr>
			<?php
		}
	
	?>
	</table>
	<div style="text-align: right; font-size: 9px;"><a href="javascript:void(0);" onclick="showMenu('workflow_actions');menuUnhover('workflow_actions_actions','');"><?php echo __('Close menu'); ?></a></div>
	</div>
	</td>
	<?php
}
else
{
	?><td style="width: 110px;"><div id="workflow_actions_menu" class="menu_item" style="color: #BBB; cursor: default;"><?php echo __('Workflow actions'); ?></div>
	<div id="workflow_actions_actions" style="display: none;">&nbsp;</div></td><?php
}

if ($theIssue->canEditUsers() || $theIssue->canDeleteIssue())
{
	?>
	<td style="width: 90px;"><div id="admin_actions_menu" class="menu_item" onmouseover="menuHover('admin_actions_menu');" onmouseout="menuUnhover('admin_actions_actions','');" onclick="showMenu('admin_actions');"><?php echo __('Admin actions'); ?></div>
	<div id="admin_actions_actions" style="display: none; width: 250px; height: auto; background-color: #FFF; padding: 10px; border: 1px solid #DDD; position: absolute;">
	<table cellpadding=0 cellspacing=0 class="td1">
		<tr>
		<td class="imgtd" valign="middle"><?php echo image_tag('icon_issueaccess.png'); ?>
		<br>
		<div id="edit_hideissue" style="position: absolute; padding: 5px; display: none; border: 1px solid #DDD; width: 500px; background-color: #F1F1F1;">
		<div style="padding: 2px; width: auto;"><?php echo __('Select whether or not users are disallowed access or explicitly allowed access to this specific issue.'); ?><br>
		<br><b><?php echo __('IMPORTANT! Make sure you grant your team(s)/group(s) or your user explicit access before you remove general access to the issue!') ?></b><br>
		<br>
		<b><?php echo __('This issue is hidden from'); ?></b><br>
		<div id="issue_hiddenfrom"><?php echo __('Please wait, loading list'); ?> ...</div>
		<span id="edit_hideissueselector" style="display: none; position: absolute;">
		<?php bugs_AJAXuserteamselector(__('Hide issue from a user'), 
										__('Hide issue from a team'),
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&sethidden=true&hidden_type=1', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&sethidden=true&hidden_type=2',
										'issue_hiddenfrom', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&gethiddenfrom=true', 
										'issue_hiddenfrom', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&gethiddenfrom=true',
										'edit_hideissueselector',
										__('Hide issue from a group'),
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&sethidden=true&hidden_type=3',
										'issue_hiddenfrom',
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&gethiddenfrom=true'
										); ?>
		</span>
		<a href="javascript:void(0);" onclick="Element.show('edit_hideissueselector')"><?php echo __('Remove access for a user / team / group'); ?></a><br>
		<br>
		<b><?php echo __('This issue is explicitly available to'); ?></b><br>
		<div id="issue_availableto"><?php echo __('Please wait, loading list'); ?> ...</div>
		<span id="edit_viewissueselector" style="display: none; position: absolute;">
		<?php bugs_AJAXuserteamselector(__('Grant explicit access to a user'), 
										__('Grant explicit access to a team'),
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setvisible=true&visible_type=1', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setvisible=true&visible_type=2',
										'issue_availableto', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getavailableto=true', 
										'issue_availableto', 
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getavailableto=true',
										'edit_viewissueselector',
										__('Grant explicit access to a group'),
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&setvisible=true&visible_type=3',
										'issue_availableto',
										'include/viewissue_actions.inc.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . '&getavailableto=true'
										); ?>
		</span>
		<a href="javascript:void(0);" onclick="Element.show('edit_viewissueselector')"><?php echo __('Grant explicit access to a user / team / group'); ?></a>	
		</div>
		<div style="text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="$('edit_hideissue').hide();"><?php echo __('Close menu'); ?></a></div>
		</div>
		</td>
		<td><a href="javascript:void(0);" onclick="Element.show('edit_hideissue');getHiddenFrom();getAvailableTo();"><?php echo __('Restrict access to this issue'); ?></a></td>
		</tr>
		<?php
	
		if ($theIssue->canDeleteIssue())
		{
			?>
			<tr>
			<td class="imgtd" valign="middle"><?php echo image_tag('icon_delete.png'); ?>
			<br>
			<div id="delete_issue" style="position: absolute; padding: 5px; border: 1px solid #DDD; width: 250px; display: none; background-color: #F1F1F1;">
			<div style="padding: 2px;"><b><?php echo __('Do you really want to delete this issue?'); ?></b></div>
			<table style="width: 100%;" cellpadding=0 cellspacing=0>
			<tr>
			<td style="width: auto; padding: 2px; text-align: right; padding-right: 5px;"><a href="viewissue.php?issue_no=<?php echo $theIssue->getFormattedIssueNo(true); ?>&amp;delete_issue=1"><?php echo __('Yes'); ?></a></td>
			<td style="width: 10px; padding: 2px; text-align: center;">|</td>
			<td style="width: auto; padding: 2px; text-align: left; padding-left: 5px;"><a href="javascript:void(0)" onclick="Effect.Fade('delete_issue', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></td>
			</tr>
			</table>
			</div>
			</td>
			<td><a href="javascript:void(0);" onclick="Effect.Appear('delete_issue', { duration: 0.5 });"><?php echo __('Delete this issue'); ?></a></td>
			</tr>
			<?php
		}
		
		?>
	</table>
	<div style="text-align: right; font-size: 9px;"><a href="javascript:void(0);" onclick="showMenu('admin_actions');menuUnhover('admin_actions_actions','');"><?php echo __('Close menu'); ?></a></div>
	</div>
	</td>
	<?php
}
else
{
	?><td style="width: 90px;"><div id="admin_actions_menu" class="menu_item" style="color: #BBB; cursor: default;"><?php echo __('Admin actions'); ?></div>
	<div id="admin_actions_actions" style="display: none;">&nbsp;</div></td><?php
}

?>
<td style="width: 110px;"><div id="log_menu" class="menu_item" onmouseover="menuHover('log_menu');" onmouseout="menuUnhover('log_actions','');" onclick="showMenu('log');getLogEntries();"><?php echo __('View history / log'); ?></div>
<div id="log_actions" style="display: none; width: 610px; height: 500px; overflow: auto; background-color: #FFF; padding: 10px; border: 1px solid #DDD; position: absolute;">
<?php echo __('Please wait, loading list'); ?> ...
</div>
</td>
<td style="width: auto;">&nbsp;</td>
</tr>
</table>
</div>