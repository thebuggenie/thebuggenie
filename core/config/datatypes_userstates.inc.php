<table style="width: 100%;" class="configstrip" cellpadding=0 cellspacing=0>
<tr>
<td valign="middle" class="cleft" style="width: 20px;"><?php echo image_tag('icon_user.png'); ?></td>
<td valign="middle" class="cright" style="width: auto;"><b><?php echo __('User states'); ?></b></td>
</tr>
</table>
<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: auto; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD;"><b><?php echo __('Name / description'); ?></b></td>
<td style="width: 40px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: center;"><b><?php echo __('Online'); ?></b></td>
<td style="width: 60px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: center;"><b><?php echo __('Unavailable'); ?></b></td>
<td style="width: 30px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: center;"><b><?php echo __('Busy'); ?></b></td>
<td style="width: 60px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: center;"><b><?php echo __('In a meeting'); ?></b></td>
<td style="width: 40px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: center;"><b><?php echo __('Absent'); ?></b></td>
<td style="width: 25px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD; font-size: 9px; text-align: left;"><b><?php echo __('Color'); ?></b></td>
<td style="width: 15px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD;">&nbsp;</td>
<td style="width: 15px; padding: 2px; background-color: #F8F8F8; border-bottom: 1px solid #DDD;">&nbsp;</td>
</tr>
<?php

	$allStates = BUGScontext::getStates();
	
	foreach ($allStates as $aState)
	{
		#print_r($aState);
		$aState = BUGSfactory::userstateLab($aState);
		if ($access_level == "full")
		{
			?><tr id="edit_state_<?php print $aState->getID(); ?>" style="display: none;">
			<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post">
			<input type="hidden" name="module" value="core">
			<input type="hidden" name="section" value=4>
			<input type="hidden" name="subsection" value=8>
			<input type="hidden" name="edit_userstate" value="true">
			<input type="hidden" name="s_id" value="<?php print $aState->getID(); ?>">
			<td style="padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><input type="text" name="state_name" value="<?php print $aState->getName(); ?>" style="width: 100%;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><input type="checkbox" name="check_online" <?php echo ($aState->isOnline()) ? 'checked' : ''; ?> style="width: 13px; height: 13px;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><input type="checkbox" name="check_unavailable" <?php echo ($aState->isUnavailable()) ? 'checked' : ''; ?> style="width: 13px; height: 13px;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><input type="checkbox" name="check_busy" <?php echo ($aState->isBusy()) ? 'checked' : ''; ?> style="width: 13px; height: 13px;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><input type="checkbox" name="check_meeting" <?php echo ($aState->isInMeeting()) ? 'checked' : ''; ?> style="width: 13px; height: 13px;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><input type="checkbox" name="check_absent" <?php echo ($aState->isAbsent()) ? 'checked' : ''; ?> style="width: 13px; height: 13px;"></td>
			<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center; width: 60px;"><select name="state_color" style="width: 100%;">
			<?php foreach ($GLOBALS['BUGS_COLORS'] as $aColor): ?>
				<?php echo bugs_printColorOptions($aColor, $aState->getItemdata()); ?>
			<?php endforeach; ?>
			</select></td>
			<td valign="middle" colspan=2 style="text-align: center;">
			<?php echo __('%save% or %cancel%', array('%save%' => '<input type="submit" value="' . __('Save') . '" style="width: 100%;"><br>', '%cancel%' => '<br><a href="javascript:void(0);" onclick="$(\'edit_state_' . $aState->getID() . '\').toggle();$(\'show_state_' . $aState->getID() . '\').toggle();" style="font-size: 9px;">' . __('Cancel') . '</a>')); ?>
			</td>
			</form>
			</tr>
			<?php
		}
		?>
		<tr id="show_state_<?php print $aState->getID(); ?>">
		<td style="padding: 2px; border-bottom: 1px solid #DDD; width: auto;"><b><?php print $aState->getName(); ?></b></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><?php echo ($aState->isOnline()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><?php echo ($aState->isUnavailable()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><?php echo ($aState->isBusy()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><?php echo ($aState->isInMeeting()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center;"><?php echo ($aState->isAbsent()) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png'); ?></td>
		<td style="padding: 2px; border-bottom: 1px solid #DDD; text-align: center; width: auto;" align="middle">
		<div style="width: 15px; height: 15px; font-size: 2px; background-color: <?php print $aState->getItemdata(); ?>; border: 1px solid #AAA;" title="<?php print $aState->getItemdata(); ?>" align="right">&nbsp;</div>
		</td>
		<?php
			if ($access_level == "full")
			{
				?>
				<td valign="middle" style="border-bottom: 1px solid #DDD; width: 15px; text-align: right;"><a href="javascript:void(0);" onclick="$('edit_state_<?php print $aState->getID(); ?>').toggle();$('show_state_<?php print $aState->getID(); ?>').toggle();" class="image"><?php echo image_tag('icon_edit.png'); ?></a></td>
				<td valign="middle" align="right" style="border-bottom: 1px solid #DDD; width: 20px;"><a href="javascript:void(0);" onclick="$('delete_state_<?php print $aState->getID(); ?>').toggle();" class="image"><?php echo image_tag('icon_delete.png'); ?></a></td>
				</tr>
				<tr style="display: none;" id="delete_state_<?php print $aState->getID(); ?>">
				<td style="background-color: #FFF; border: 1px solid #DDD; border-right: 0px; padding: 5px;" colspan=6><b><?php echo __('Please confirm'); ?></b><br><?php echo __('Are you sure you want to delete this item?'); ?></td>
				<td style="background-color: #FFF; border: 1px solid #DDD; border-left: 0px; padding: 5px; text-align: right;" colspan=3><a href="config.php?module=core&amp;section=4&amp;subsection=8&amp;delete_userstate=true&amp;s_id=<?php print $aState->getID(); ?>"><?php echo __('Yes'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0);" onclick="$('delete_state_<?php print $aState->getID(); ?>').toggle();"><b><?php echo __('No'); ?></b></a></td>
				<?php
			}
		?>
		</tr>
		<?php
	}

	if ($access_level == "full")
	{
		?>
		<tr>
		<td style="width: auto;" colspan=9>&nbsp;</td>
		</tr>
		<tr>
		<td style="width: auto; padding: 3px; font-size: 13px; border-bottom: 1px solid #DDD;" colspan=9><b><?php echo __('Add user state'); ?></b></td>
		</tr>
		<tr>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value=4>
		<input type="hidden" name="subsection" value=8>
		<input type="hidden" name="add_userstate" value="true">
		<td style="padding: 2px;"><input type="text" name="state_name" value="" style="width: 100%;"></td>
		<td style="padding: 2px; text-align: center;"><input type="checkbox" name="check_online" style="width: 13px; height: 13px;"></td>
		<td style="padding: 2px; text-align: center;"><input type="checkbox" name="check_unavailable" style="width: 13px; height: 13px;"></td>
		<td style="padding: 2px; text-align: center;"><input type="checkbox" name="check_busy" style="width: 13px; height: 13px;"></td>
		<td style="padding: 2px; text-align: center;"><input type="checkbox" name="check_meeting" style="width: 13px; height: 13px;"></td>
		<td style="padding: 2px; text-align: center;"><input type="checkbox" name="check_absent" style="width: 13px; height: 13px;"></td>
		<td style="padding: 2px; text-align: center; width: 60px;"><select name="state_color" style="width: 100%;">
		<?php

			foreach ($GLOBALS['BUGS_COLORS'] as $aColor)
			{
				echo bugs_printColorOptions($aColor, '');
			}
		
		?>
		</select></td>
		<td colspan=2 style="text-align: right;"><input type="submit" value="<?php echo __('Add'); ?>" style="width: 100%;" align="right"></td>
		</form>
		</tr>
		<?php
	}
?>
</table>