<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<?php

	if ($selectedBuild instanceof BUGSbuild)
	{
		?>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_build.png'); ?><br>
		<div id="rni_step1_setbuild_dropdown" style="position: absolute; display: none; padding: 2px; background-color: #FFF; margin-top: 2px; width: 250px; border: 1px solid #DDD;"><b><?php echo __('Select a different build'); ?></b><br>
		<table style="width: 100%;" cellpadding=0 cellspacing=0><?php

			$builds = $selectedEdition->getBuilds();
			$bCount = false;
			foreach ($builds as $aBuild)
			{
				if ($aBuild->getID() != $selectedBuild->getID() && $aBuild->isLocked() == false)
				{
					?>
					<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_build.png'); ?>
					<td style="width: auto; padding: 2px; padding-right: 15px;"><a href="javascript:void(0);" onclick="setBuild(<?php print $aBuild->getID(); ?>);"><?php print $aBuild; ?></a></td>
					</tr>
					<?php
					$bCount = true;
				}
			}
			if ($bCount == false)
			{
				?>
				<tr><td style="padding: 2px; color: #AAA;"><?php echo __('There are no other available builds'); ?></td></tr>
				<?php
			}

		?>
		<tr>
			<td style="padding: 2px; font-size: 10px; text-align: right;" colspan=2><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setbuild_dropdown');"><?php echo __('Cancel'); ?></a></td>
		</tr>
		</table></div>
		</td>
		<td style="width: auto; padding: 2px;"><?php print $selectedBuild; ?></td>
		<?php
	} 
	elseif (!($selectedProject instanceof BUGSproject) || $selectedProject->isBuildsEnabled())
	{
		?>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_build.png'); ?>
		<td style="width: auto; padding: 2px; color: #AAA;"><?php echo __('There are no available builds'); ?></td>
		<?php
	}
?>
</tr>
<tr>
<td colspan=2 style="text-align: center;"><?php

	if (!($selectedProject instanceof BUGSproject) || $selectedProject->isBuildsEnabled())
	{
		if ($bCount)
		{
			?><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setbuild_dropdown');"><?php echo __('Change'); ?></a><?php
		}
		/*else
		{
			?><div style="color: #AAA;"><?php echo __('Change'); ?></div><?php
		}*/
	}

?></td>
</tr>
</table>