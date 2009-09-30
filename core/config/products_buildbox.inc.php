<?php 

if (isset($include_table))
{
	if ($access_level == 'full')
	{ 
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="edit_build_<?php print $aBuild->getID(); ?>_form" onsubmit="return false;">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value="10">
		<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
		<input type="hidden" name="e_id" value="<?php print $theEdition->getID(); ?>">
		<input type="hidden" name="b_id" value="<?php print $aBuild->getID(); ?>">
		<input type="hidden" name="edit_editions" value="true">
		<input type="hidden" name="action" value="edit">
		<?php
	}
	
	?>
	<table cellpadding=0 cellspacing=0 style="width: 100%;" id="build_table_<?php echo $aBuild->getID(); ?>">
	<?php
}
?>
<tr id="show_build_<?php print $aBuild->getID(); ?>" style="">
<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_' . (($aBuild->isReleased()) ? 'release' : 'build') . '.png'); ?></td>
<td style="width: auto; padding: 2px;"><b><?php print $aBuild; ?></b><br>
<?php

if ($aBuild->isReleased())
{
	echo __('Released %release_date%', array('%release_date%' => bugs_formatTime($aBuild->getReleaseDate(), 5)));
}
elseif ($aBuild->getReleaseDate() != 0)
{
	echo __('Planned release %release_date%', array('%release_date%' => bugs_formatTime($aBuild->getReleaseDate(), 5)));
}
else
{
	echo __('No release date set');
}

?>
</td>
</tr>
<tr id="edit_build_<?php print $aBuild->getID(); ?>" style="display: none;">
<td style="width: 20px; padding: 2px; padding-top: 10px;" valign="top"><?php echo image_tag('icon_edit_build.png'); ?></td>
<td style="width: auto; padding: 2px;">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr>
<td style="width: auto;"><input type="text" name="build_name" style="width: 170px;" value="<?php print $aBuild->getName(); ?>"></td>
<td style="width: 30px; text-align: right;"><b><?php echo __('Ver: %version_number%', array('%version_number%' => '')); ?></b></td>
<td style="width: 100px; text-align: right;"><input type="text" name="ver_mj" style="width: 25px; text-align: center;" value="<?php print $aBuild->getMajor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_mn" style="width: 25px; text-align: center;" value="<?php print $aBuild->getMinor(); ?>">&nbsp;.&nbsp;<input type="text" name="ver_rev" style="width: 25px; text-align: center;" value="<?php print $aBuild->getRevision(); ?>"></td>
</tr>
</table>
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr>
<td style="width: 120px;"><select name="planned_release_<?php print $aBuild->getID(); ?>" id="planned_release_<?php print $aBuild->getID(); ?>" style="width: 100%;" onchange="bB = document.getElementById('planned_release_<?php print $aBuild->getID(); ?>'); cB = document.getElementById('build_release_day_<?php print $aBuild->getID(); ?>'); dB = document.getElementById('build_release_month_<?php print $aBuild->getID(); ?>'); eB = document.getElementById('build_release_year_<?php print $aBuild->getID(); ?>'); if (bB.value == '0') { cB.disabled = true; dB.disabled = true; eB.disabled = true; } else { cB.disabled = false; dB.disabled = false; eB.disabled = false; }"><option value=0<?php print ($aBuild->getReleaseDate() != 0) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option><option value=1<?php print ($aBuild->getReleaseDate() == 0) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option></select></td>
<td style="width: auto; text-align: right;">
<select style="width: 85px;" name="build_release_month_<?php print $aBuild->getID(); ?>" id="build_release_month_<?php print $aBuild->getID(); ?>" <?php print ($aBuild->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 12;$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php echo ($aBuild->getReleaseDateMonth() == $cc) ? " selected" : "" ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option>
		<?php
	}

?>
</select>
<select style="width: 40px;" name="build_release_day_<?php print $aBuild->getID(); ?>" id="build_release_day_<?php print $aBuild->getID(); ?>" <?php print ($aBuild->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 31;$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php echo ($aBuild->getReleaseDateDay() == $cc) ? " selected" : "" ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
<select style="width: 55px;" name="build_release_year_<?php print $aBuild->getID(); ?>" id="build_release_year_<?php print $aBuild->getID(); ?>" <?php print ($aBuild->getReleaseDate() == 0) ? "disabled" : ""; ?>>
<?php

	for($cc = 2000;$cc <= (date("Y") + 5);$cc++)
	{
		?>
		<option value=<?php print $cc; ?><?php echo ($aBuild->getReleaseDateYear() == $cc) ? " selected" : "" ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
</td>
</tr>
<tr>
<td colspan=2 style="padding: 10px; text-align: right;"><button onclick="updateBuild(<?php echo $aBuild->getID(); ?>);"><?php echo __('Save'); ?></button>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Element.hide('edit_build_<?php print $aBuild->getID(); ?>');Element.show('show_build_<?php print $aBuild->getID(); ?>');" style="font-size: 9px;"><?php echo __('Cancel'); ?></a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan=2 style="border-bottom: 1px solid #DDD; padding-bottom: 2px;">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr id="icons_build_<?php print $aBuild->getID(); ?>">
<td style="width: auto; background-color: #FFF;">
<div id="icon_text_blank_<?php print $aBuild->getID(); ?>">&nbsp;</div>
<div id="icon_release_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Release this build'); ?></div>
<div id="icon_retract_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Retract (unrelease) this build'); ?></div>
<div id="icon_default_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Set as default for new issue reports'); ?></div>
<div id="icon_lock_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Lock this build for new issue reports'); ?></div>
<div id="icon_unlock_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Unlock this build for new issue reports'); ?></div>
<div id="icon_edit_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Edit the information about this build'); ?></div>
<div id="icon_delete_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Delete this build permanently'); ?></div>
<div id="icon_addtoopen_text_<?php print $aBuild->getID(); ?>" style="display: none; color: #AAA; padding-left: 2px;"><?php echo __('Add build to open issues'); ?></div>
</td>
<?php

	if (!$aBuild->isDefault())
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="setBuildAsDefault(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo image_tag('icon_build_default.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_default_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
		<?php
	}
	else
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;">&nbsp;</td>
		<?php
	}

?>
<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="Effect.Appear('addtoopen_build_<?php print $aBuild->getID(); ?>', { duration: 0.5 });" class="image"><?php echo image_tag('icon_build_addtoopen.png' , 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_addtoopen_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
<?php

	if (!$aBuild->isReleased())
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="releaseBuild(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo image_tag('icon_release.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_release_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
		<?php
	}
	else
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="retractBuild(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo image_tag('icon_retract.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_retract_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
		<?php
	}

	if ($aBuild->isLocked())
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="unlockBuild(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo image_tag('icon_locked.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_unlock_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
		<?php
	}
	else
	{
		?>
		<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="lockBuild(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo image_tag('icon_unlocked.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_lock_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
		<?php
	}

?>
<td style="width: 18px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="Element.show('edit_build_<?php print $aBuild->getID(); ?>');Element.hide('show_build_<?php print $aBuild->getID(); ?>');" class="image"><?php echo image_tag('icon_edit.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_edit_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"'); ?></a></td>
<td style="width: 13px; background-color: #FFF; padding: 1px;"><a href="javascript:void(0);" onclick="Effect.Appear('del_build_<?php print $aBuild->getID(); ?>', { duration: 0.5 });" class="image"><?php echo image_tag('action_cancel_small.png', 'onmouseover="javascript:buildContext(' . $aBuild->getID() . ', \'icon_delete_text_' . $aBuild->getID() . '\')" onmouseout="javascript:buildContext(' . $aBuild->getID() . ', \'icon_text_blank_' . $aBuild->getID() . '\')"', '', '', 0, 11, 11); ?></a></td>
</tr>
<?php

	if ($access_level == "full")
	{
		?>
		<tr id="del_build_<?php print $aBuild->getID(); ?>" style="display: none;">
		<td colspan=2 style="border-top: 1px solid #DDD; background-color: #F1F1F1; padding: 2px;"><?php echo __('Do you really want to delete this build?'); ?></td>
		<td colspan=5 style="text-align: right; border-top: 1px solid #DDD; background-color: #F1F1F1; padding: 2px;"><a href="javascript:void(0);" onclick="deleteBuild(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);" class="image"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:Effect.Fade('del_build_<?php print $aBuild->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></td>
		</tr>
		<?php
	}

?>
<tr id="addtoopen_build_<?php print $aBuild->getID(); ?>" style="display: none;">
<td colspan=2 style="border-top: 1px solid #DDD; background-color: #F1F1F1; padding: 2px;"><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Add this build to all open issue reports for this project?'); ?></td>
<td colspan=5 style="text-align: right; border-top: 1px solid #DDD; background-color: #F1F1F1; padding: 2px;"><a href="javascript:void(0);" onclick="addBuildToOpenIssues(<?php print $theProject->getID(); ?>, <?php print $theEdition->getID(); ?>, <?php print $aBuild->getID(); ?>);Effect.Fade('addtoopen_build_<?php print $aBuild->getID(); ?>', { duration: 0.5 });" class="image"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('addtoopen_build_<?php print $aBuild->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a></td>
</tr>
</table>
</td>
</tr>
<?php

if (isset($include_table))
{
	?>
	</table>
	<?php 
	
	if ($access_level == 'full')
	{ 
		?>
		</form>
		<?php
	}
}

?>