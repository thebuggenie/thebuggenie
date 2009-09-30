<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<?php

if ($selectedEdition instanceof BUGSedition)
{
	?>
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?><br>
	<div id="rni_step1_setedition_dropdown" style="position: absolute; display: none; padding: 2px; background-color: #FFF; margin-top: 2px; width: 250px; border: 1px solid #DDD;"><b><?php echo __('Select a different edition'); ?></b><br>
	<table style="width: 100%;" cellpadding=0 cellspacing=0><?php

		$editions = $selectedProject->getEditions();
		$eCount = false;
		foreach ($editions as $anEdition)
		{
			if ($anEdition->getID() != $selectedEdition->getID() && $anEdition->isLocked() == false)
			{
				?>
				<tr>
				<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?>
				<td style="width: auto; padding: 2px; padding-right: 15px;"><a href="javascript:void(0);" onclick="setEdition(<?php print $anEdition->getID(); ?>);"><?php print $anEdition->getName(); ?></a></td>
				</tr>
				<?php
				$eCount = true;
			}
		}
		if ($eCount == false)
		{
			?>
			<tr><td style="padding: 2px; color: #AAA;"><?php echo __('There are no other available editions'); ?></td></tr>
			<?php
		}

	?>
	<tr>
		<td style="padding: 2px; font-size: 10px; text-align: right;" colspan=2><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setedition_dropdown');"><?php echo __('Cancel'); ?></a></td>
	</tr>
	</table></div>
	</td>
	<td style="width: auto; padding: 2px; padding-right: 15px;"><?php print $selectedEdition->getName(); ?></td><?php
}
else
{
	?><td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_edition.png'); ?>
	<td style="width: auto; padding: 2px; color: #AAA;"><?php echo __('There are no available editions'); ?></td><?php
}
?>
</tr>
<tr>
<td colspan=2 style="text-align: center;"><?php

	if ($eCount)
	{
		?><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setedition_dropdown');"><?php echo __('Change'); ?></a><?php
	}
	/*elseif(!$step1_set)
	{
		?><div style="color: #AAA;"><?php echo __('Change'); ?></div><?php
	}*/

?></td>
</tr>
</table>