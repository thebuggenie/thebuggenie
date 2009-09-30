<?php

	if (!$milestone instanceof BUGSmilestone)
	{
		exit();
	}

	$sch_month = date("n", $milestone->getScheduledDate());
	$sch_day = date("j", $milestone->getScheduledDate());
	$sch_year = date("Y", $milestone->getScheduledDate());
	
?>

<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="edit_milestone_<?php echo $milestone->getID(); ?>" style="display: none;" onsubmit="return false;">
<input type="hidden" name="module" value="core">
<input type="hidden" name="section" value=9>
<input type="hidden" name="p_id" value=<?php echo $theProject->getID(); ?>>
<input type="hidden" name="m_id" value=<?php echo $milestone->getID(); ?>>
<input type="hidden" name="edit_milestone" value="true">
<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 60px; padding: 3px;"><b><?php echo __('Name:'); ?></b></td>
<td style="width: auto;"><input type="text" style="width: 100%;" value="<?php echo $milestone->getName(); ?>" name="m_name"></td>
</tr>
<tr>
<td style="width: 60px; padding: 3px;"><b><?php echo __('Description:'); ?></b></td>
<td style="width: auto;"><input type="text" style="width: 100%;" value="<?php echo $milestone->getDescription(); ?>" name="description"></td>
</tr>
<tr>
<td style="width: 120px;">
	<select name="sch_date_<?php echo $milestone->getID(); ?>" id="sch_date_<?php echo $milestone->getID(); ?>" style="width: 100%;" onchange="if ($('sch_date_<?php echo $milestone->getID(); ?>').getValue() == '1') { $('sch_month_<?php echo $milestone->getID(); ?>').enable(); $('sch_day_<?php echo $milestone->getID(); ?>').enable(); $('sch_year_<?php echo $milestone->getID(); ?>').enable(); } else { $('sch_month_<?php echo $milestone->getID(); ?>').disable(); $('sch_day_<?php echo $milestone->getID(); ?>').disable(); $('sch_year_<?php echo $milestone->getID(); ?>').disable(); } ">
		<option value=0<?php print ($milestone->isScheduled()) ? "" : " selected"; ?>><?php echo __('No planned release'); ?></option>
		<option value=1<?php print (!$milestone->isScheduled()) ? "" : " selected"; ?>><?php echo __('Planned release'); ?></option>
	</select>
</td>
<td style="width: auto;">
<select style="width: 85px;" name="sch_month_<?php echo $milestone->getID(); ?>" id="sch_month_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 12;$cc++)
	{
		?><option value=<?php echo $cc; ?><?php echo (($sch_month == $cc) ? " selected" : ""); ?>><?php echo bugs_formatTime(mktime(0, 0, 0, $cc, 1), 15); ?></option><?php
	}

?>
</select>
<select style="width: 40px;" name="sch_day_<?php echo $milestone->getID(); ?>" id="sch_day_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
<?php

	for($cc = 1;$cc <= 31;$cc++)
	{
		?>
		<option value=<?php echo $cc; ?><?php echo (($sch_day == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
<select style="width: 55px;" name="sch_year_<?php echo $milestone->getID(); ?>" id="sch_year_<?php echo $milestone->getID(); ?>" <?php print (!$milestone->isScheduled()) ? "disabled" : ""; ?>>
<?php

	for($cc = 2000;$cc <= (date("Y") + 5);$cc++)
	{
		?>
		<option value=<?php echo $cc; ?><?php echo (($sch_year == $cc) ? " selected" : ""); ?>><?php echo $cc; ?></option>
		<?php
	}

?>
</select>
</td>
</tr>
<tr>
<td style="padding-bottom: 15px; text-align: right;" colspan=2>
<?php echo __('%update% or %cancel%', array('%update%' => '<button onclick="updateMilestone(' . $theProject->getID() . ', ' . $milestone->getID() . ');">' . __('Update') . '</button>', '%cancel%' => '<a href="javascript:void(0);" onclick="Element.hide(\'edit_milestone_' . $milestone->getID() . '\');Element.show(\'show_milestone_' . $milestone->getID() . '\');" style="font-size: 10px;">' . __('Cancel') . '</a>')); ?></td>
</tr>
</table>
</form>
<table style="width: 100%;" cellpadding=0 cellspacing=0 id="show_milestone_<?php echo $milestone->getID(); ?>">
<tr>
<td style="padding: 3px; padding-bottom: 0px; padding-top: 10px; width: auto;"><b><?php echo $milestone->getName(); ?></b></td>
<td style="width: 20px; padding-top: 10px;"><a class="image" href="javascript:void(0);" onclick="Element.show('edit_milestone_<?php echo $milestone->getID(); ?>');Element.hide('show_milestone_<?php echo $milestone->getID(); ?>');"><?php echo image_tag('icon_edit.png'); ?></a></td>
<td style="width: 20px; padding-top: 10px;"><a class="image" href="javascript:void(0);" onclick="Effect.Appear('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><?php echo image_tag('icon_delete.png'); ?></a><br>
<div style="display: none; width: 300px; position: absolute; left: 600px; padding: 5px; border: 1px solid #DDD; background-color: #FFF; text-align: center;" id="delete_milestone_<?php echo $milestone->getID(); ?>"><b><?php echo __('Do you really want to delete this milestone?'); ?></b><br>
<a href="javascript:void(0);" onclick="deleteMilestone(<?php echo $theProject->getID(); ?>, <?php echo $milestone->getID();?>);"><?php echo __('Yes'); ?></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('delete_milestone_<?php echo $milestone->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a>
</div></td>
</tr>
<tr>
<td colspan=4 style="padding: 3px; padding-top: 0px; padding-bottom: 0px; color: #AAA;"><?php echo (!$milestone->isScheduled()) ? __('This milestone has no planned schedule') : __('This milestone is scheduled for %scheduled_date% and is %visible_or_hidden%', array('%scheduled_date%' => bugs_formatTime($milestone->getScheduledDate(), 5), '%visible_or_hidden%' => ((!$milestone->isVisible()) ? __('hidden') : __('visible')))); ?>&nbsp;&nbsp;<a style="font-size: 9px;" href="javascript:void(0);" onclick="setMilestoneVisibility(<?php echo $theProject->getID(); ?>, <?php echo $milestone->getID();?>, <?php echo (!$milestone->isVisible()) ? 1 : 0; ?>);"><?php echo (!$milestone->isVisible()) ? __('Show') : __('Hide'); ?></a></td>
</tr>
<tr>
<td colspan=4 style="padding: 3px; padding-top: 0px; padding-bottom: 10px; border-bottom: 1px solid #DDD;"><?php echo bugs_BBDecode($milestone->getDescription()); ?></td>
</tr>
</table>
