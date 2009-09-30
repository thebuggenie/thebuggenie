<table style="width: 700px;" cellpadding=0 cellspacing=0>
<tr>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projects.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 180px;"><a href="config.php?module=core&amp;section=10"><?php echo __('Select a different project'); ?></a></td>
<td style="border-bottom: 1px solid #DDD; width: auto;">&nbsp;</td>
<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projectsettings.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 150px;"><b><?php echo __('Project information'); ?></b></td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_projecteditionsbuilds.png', '', __('Edit settings'), __('Edit settings')); ?></td>
<td style="border-bottom: 1px solid #DDD; padding: 4px; width: <?php print ($theProject->isBuildsEnabled()) ? 210 : 180; ?>px;"><a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $theProject->getID(); ?>&amp;edit_editions=true"><?php echo ($theProject->isBuildsEnabled()) ? __('Editions, builds and components') : __('Editions and components'); ?></a></td>
</tr>
</table>
<?php

if ($access_level == "full")
{
	?>
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" onsubmit="return false" id="project_details">
	<input type="hidden" name="module" value="core">
	<input type="hidden" name="section" value="10">
	<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
	<input type="hidden" name="update_project" value="true">
	<input type="hidden" name="edit_settings" value="true">
	<?php
}

?>
<table style="width: 700px;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: auto; padding: 10px; padding-left: 0px; vertical-align: top;">
<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px;"><b><?php echo __('PROJECT DETAILS'); ?></b></div>
<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 120px; padding: 2px;"><b><?php echo __('Project name'); ?></b></td>
<td style="padding: 2px; width: 250px;">
<?php

	if ($access_level == "full")
	{
		?><input type="text" name="project_name" value="<?php print $theProject->getName(); ?>" style="width: 100%;"><?php
	}

?>
</td>
<td style="width: auto; padding: 2px; text-align: right;"><b><?php echo __('Use prefix'); ?></b></td>
<td style="padding: 2px; width: 50px;">
<?php

	if ($access_level == "full")
	{
		?>
		<select name="use_prefix" style="width: 100%;">
		<option value=1 <?php echo ($theProject->usePrefix()) ? 'selected' : ''; ?>><?php echo __('Yes'); ?></option>
		<option value=0 <?php echo (!$theProject->usePrefix()) ? 'selected' : ''; ?>><?php echo __('No'); ?></option>
		</select>
		<?php
	}

?>
</td>
<td style="width: auto; padding: 2px; text-align: right;"><b><?php echo __('Prefix:'); ?></b></td>
<td style="padding: 2px; width: 50px;">
<?php

	if ($access_level == "full")
	{
		?><input type="text" name="prefix" value="<?php print $theProject->getPrefix(); ?>" style="width: 100%;"><?php
	}

?>
</td>
<td style="padding: 2px; width: 30px; text-align: center;"><?php echo bugs_helpBrowserHelper('prefix&prev_topic=config_projects', image_tag('help.png')); ?></td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Description:'); ?></b></td>
<td style="padding: 2px;" colspan=4>
<?php

	if ($access_level == "full")
	{
		?><input type="text" name="description" value="<?php print $theProject->getDescription(); ?>" style="width: 100%;"><?php
	}

?>
</td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Homepage: %link_to_homepage%', array('%link_to_homepage%' => '')); ?></b></td>
<td style="padding: 2px;" colspan=4>
<?php

	if ($access_level == "full")
	{
		?><input type="text" name="homepage" value="<?php print $theProject->getHomepage(); ?>" style="width: 100%;"><?php
	}

?>
</td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Documentation: %link_to_documentation%', array('%link_to_documentation%' => '')); ?></b></td>
<td style="padding: 2px;" colspan=4>
<?php

	if ($access_level == "full")
	{
		?><input type="text" name="doc_url" value="<?php print $theProject->getDocumentationURL(); ?>" style="width: 100%;"><?php
	}

?>
</td>
</tr>
</table>
</div>
<?php

	if ($access_level == "full")
	{
		?>
		<div style="padding: 10px; padding-right: 2px; text-align: right;">
		<button onclick="submitProjectDetails();"><?php echo __('Save'); ?></button>
		</div>
		</form>
		<?php
	}

?>
<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-top: 10px;"><b><?php echo __('PROJECT SETTINGS'); ?></b></div>
<table style="width: auto;" cellpadding=0 cellspacing=0 id="leadby_table">
<tr>
<td style="padding: 2px; width: 120px;"><b><?php echo __('Lead by: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
<td style="padding: 2px; width: 200px;" id="project_leadby">
<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0>
<?php

	if ($theProject->getLeadType() == BUGSidentifiableclass::TYPE_USER)
	{
		print bugs_userDropdown($theProject->getLeadBy()->getID());
	}
	elseif ($theProject->getLeadType() == BUGSidentifiableclass::TYPE_TEAM)
	{
		print bugs_teamDropdown($theProject->getLeadBy()->getID());
	}
	else
	{
		echo '<tr><td style="color: #AAA;">' . __('Not assigned') . '</td></tr>';
	}
	
?>
</table>
</td>
<td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('edit_leadby', { duration: 0.5 })"><?php echo image_tag('icon_switchassignee.png', '', __('Change'), __('Change'), 0, 12, 12); ?></a></td>
<td style="padding: 2px;" colspan=2>&nbsp;</td>
</tr>
</table>
<span id="edit_leadby" style="display: none;">
<?php bugs_AJAXuserteamselector(__('Set lead by a user'), 
								__('Set lead by a team'),
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&setleadby=true&lead_type=1', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&setleadby=true&lead_type=2',
								'project_leadby', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&getleadby=true',
								'project_leadby', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&getleadby=true',
								'edit_leadby'
								); ?>
</span>
<table style="width: auto;" cellpadding=0 cellspacing=0 id="qa_table">
<tr>
<td style="padding: 2px; width: 120px;"><b><?php echo __('QA by: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
<td style="padding: 2px; width: 200px;" id="project_qa">
<table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0>
<?php

	if ($theProject->getQAType() == BUGSidentifiableclass::TYPE_USER)
	{
		print bugs_userDropdown($theProject->getQA());
	}
	elseif ($theProject->getQAType() == BUGSidentifiableclass::TYPE_TEAM)
	{
		print bugs_teamDropdown($theProject->getQA());
	}
	else
	{
		echo '<tr><td style="color: #AAA;">' . __('Not assigned') . '</td></tr>';
	}

?>
</table>
</td>
<td style="width: 20px;"><a href="javascript:void(0);" class="image" onclick="Effect.Appear('edit_qa', { duration: 0.5 })"><?php echo image_tag('icon_switchassignee.png', '', __('Change'), __('Change'), 0, 12, 12); ?></a></td>
<td style="padding: 2px;" colspan=2>&nbsp;</td>
</tr>
</table>
<span id="edit_qa" style="display: none;">
<?php bugs_AJAXuserteamselector(__('Set QA\'ed by a user'), 
								__('Set QA\'ed by a team'),
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&setqa=true&qa_type=1', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&setqa=true&qa_type=2',
								'project_qa', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&getqa=true',
								'project_qa', 
								'config.php?module=core&section=10&p_id=' . $theProject->getID() . '&edit_settings=true&getqa=true',
								'edit_qa'
								); ?>
</span>
<?php

	if ($access_level == "full")
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" onsubmit="return false" id="project_settings">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="10">
		<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
		<input type="hidden" name="update_project_settings" value="true">
		<input type="hidden" name="edit_settings" value="true">
		<?php
	}

?>
<table style="width: 100%; margin-top: 5px;" cellpadding=0 cellspacing=0>
<tr>
<td style="padding: 2px; width: 250px;"><b><?php echo __('Use tasks in issue reports'); ?></b></td>
<td style="padding: 2px; width: 200px; text-align: right;">
<select name="enable_tasks" style="width: 50px;">
<option value=1<?php print ($theProject->isTasksEnabled()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
<option value=0<?php print (!$theProject->isTasksEnabled()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
</select>
</td>
<td style="padding: 2px; padding-left: 10px; width: auto;"><b><?php echo __('Allow voting for issues'); ?></b></td>
<td style="padding: 2px; width: 50px;">
<select name="votes" style="width: 100%;">
<option value=1<?php print ($theProject->isVotesEnabled()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
<option value=0<?php print (!$theProject->isVotesEnabled()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
</select>
</td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Time measuring'); ?></b>&nbsp;(<?php echo __('time spent on issues, etc'); ?>)</td>
<td style="padding: 2px;">
<select name="time_unit" style="width: 100%;">
<option value=0<?php print ($theProject->getTimeUnit() == 0) ? " selected" : ""; ?>><?php echo __('Hours only'); ?></option>
<option value=1<?php print ($theProject->getTimeUnit() == 1) ? " selected" : ""; ?>><?php echo __('Hours and days'); ?></option>
<option value=2<?php print ($theProject->getTimeUnit() == 2) ? " selected" : ""; ?>><?php echo __('Hours, days and weeks'); ?></option>
<option value=3<?php print ($theProject->getTimeUnit() == 3) ? " selected" : ""; ?>><?php echo __('Days only'); ?></option>
<option value=4<?php print ($theProject->getTimeUnit() == 4) ? " selected" : ""; ?>><?php echo __('Days and weeks'); ?></option>
<option value=5<?php print ($theProject->getTimeUnit() == 5) ? " selected" : ""; ?>><?php echo __('Weeks only'); ?></option>
</select>
</td>
<td style="padding: 2px; padding-left: 10px;"><b><?php echo __('Hours per day'); ?></b></td>
<td style="padding: 2px; width: 50px;">
<input type="text" name="hrs_pr_day" style="width: 100%;" value="<?php print $theProject->getHoursPerDay(); ?>">
</td>
</tr>
<tr>
<td style="padding: 2px;">
<select name="planned_release" id="planned_release" style="width: 100%;" onchange="bB = document.getElementById('planned_release'); cB = document.getElementById('release_day'); dB = document.getElementById('release_month'); eB = document.getElementById('release_year'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }"><option value=0<?php print ($theProject->getReleaseDate() != 0) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option><option value=1<?php print ($theProject->getReleaseDate() == 0) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option></select>
</td>
<td style="padding: 2px; text-align: right;">
<select style="width: 85px;" name="release_month" id="release_month" <?php print ($theProject->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 12;$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php (($theProject->getReleaseDateMonth() == $cc) ? " selected" : "") ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
		<?php
	}

?>
</select>
<select style="width: 40px;" name="release_day" id="release_day" <?php print ($theProject->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 31;$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php (($theProject->getReleaseDateDay() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
<select style="width: 55px;" name="release_year" id="release_year" <?php print ($theProject->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 2000;$cc <= (date("Y") + 5);$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php (($theProject->getReleaseDateYear() == $cc) ? " selected" : "") ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
</td>
<td style="padding: 2px; padding-left: 10px;"><b><?php echo __('Released:'); ?></b></td>
<td style="padding: 2px; text-align: right;">
<select style="width: 50px;" name="released">
<option value=0<?php print (!$theProject->isReleased()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
<option value=1<?php print ($theProject->isReleased()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
</select>
</td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Allow issues to be reported'); ?></b></td>
<td style="padding: 2px; text-align: right;">
<select name="locked" style="width: 50px;">
<option value=1<?php print ($theProject->isLocked()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
<option value=0<?php print (!$theProject->isLocked()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
</select>
</td>
<td style="padding: 2px; padding-left: 10px;"><b><?php echo __('Use builds'); ?></b></td>
<td style="padding: 2px; width: 50px;">
<select name="disable_builds" style="width: 100%;">
<option value=0<?php print ($theProject->isBuildsEnabled()) ? " selected" : ""; ?>><?php echo __('Yes'); ?></option>
<option value=1<?php print (!$theProject->isBuildsEnableD()) ? " selected" : ""; ?>><?php echo __('No'); ?></option>
</select>
</td>
</tr>
<tr>
<td style="padding: 2px;"><b><?php echo __('Default status for new issues'); ?></b></td>
<td style="padding: 2px; text-align: right;">
<select name="defaultstatus" style="width: 200px;">
<?php 

$statusTypes = BUGSdatatype::getAll(BUGSdatatype::STATUS);
foreach ($statusTypes as $aListType)
{
	$aStatus = BUGSfactory::BUGSstatusLab($aListType);
	?><option style="color: <?php echo $aStatus->getItemdata(); ?>" value=<?php echo $aStatus->getID(); ?><?php echo ($theProject->getDefaultStatus()->getID() == $aStatus->getID()) ? " selected" : ""; ?>><?php echo $aStatus->getName(); ?></option><?php
}
?>
</select>
</td>
<td colspan=2>&nbsp;</td>
</tr>
</table>
<?php

	if ($access_level == "full")
	{
		?>
		<div style="padding: 10px; padding-right: 2px; text-align: right;">
		<button onclick="submitProjectSettings();"><?php echo __('Save'); ?></button>
		</div>
		</form>
		<?php
	}

?>
</div>
</div>
</td>
</tr>
</table>