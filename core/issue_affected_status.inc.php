<td style="width: 200px; padding: 4px; border-bottom: 1px solid #EEE;">
<table style="table-layout: fixed; width: 100%;" cellpadding=0 cellspacing=0>
<tr id="affected_status_inline_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>">
<td style="width: 20px;"><div style="border: 1px solid #AAA; background-color: <?php echo $anAffected['status']->getItemdata(); ?>; font-size: 1px; width: 13px; height: 13px;">&nbsp;</div></td>
<td><?php echo $anAffected['status']->getName(); ?></td>
<?php

if ($theIssue->canEditFields())
{
	?>
	<td style="width: 30px; text-align: right;"><a href="javascript:void(0);" onclick="Effect.Appear('affected_status_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>', { duration: 0.5 });getAffectedStatusList(<?php echo $anAffected['a_id']; ?>, '<?php echo $anAffected['a_type'] ?>');" style="font-size: 9px;" class="image"><?php echo image_tag('icon_switchassignee.png'); ?></a></td>
	<?php
}

?>
</tr>
</table>
<?php

if ($theIssue->canEditFields())
{
	?>
	<div id="affected_status_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>" style="position: absolute; right: 10px; padding: 5px; border: 1px solid #DDD; width: 250px; display: none; background-color: #FFF;">
	<div style="text-align: left; padding-bottom: 5px;"><b><?php echo __('Change status'); ?></b><br><?php echo __('Select the status of this component / build, from the list below.'); ?></div>
	<span id="affected_status_list_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>"><?php echo __('Please wait, loading list'); ?> ...</span>
	<div style="width: auto; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Effect.Fade('affected_status_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>', { duration: 0.5 });"><?php echo __('Close menu'); ?></a></div>
	</div>
	<?php
}

?>
</td>
<td style="width: 60px; padding: 4px; border-bottom: 1px solid #EEE; text-align: center;" id="affected_confirmed_<?php echo $anAffected['a_id'] . '_' . $anAffected['a_type']; ?>">
<?php

if ($theIssue->canEditFields())
{
	if ($anAffected['confirmed'])
	{
		echo '<a href="javascript:void(0);" onclick="setAffectedConfirmed(0, ' . $anAffected['a_id'] . ', \'' . $anAffected['a_type'] . '\')" class="image">' . image_tag('action_ok_small.png') . '</a>';
	}
	else
	{
		echo '<a href="javascript:void(0);" onclick="setAffectedConfirmed(1, ' . $anAffected['a_id'] . ', \'' . $anAffected['a_type'] . '\')" class="image">' . image_tag('action_cancel_small.png') . '</a>';
	}
}
else
{
	echo ($anAffected['confirmed']) ? image_tag('action_ok_small.png') : image_tag('action_cancel_small.png');
}

?>
</td>