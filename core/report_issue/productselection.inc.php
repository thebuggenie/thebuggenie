<table style="width: 100%;" cellpadding=0 cellspacing=0>
<tr>
<?php
if ($selectedProject instanceof BUGSproject)
{
	?>
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_project.png'); ?><br>
	<div id="rni_step1_setproject_dropdown" style="position: absolute; display: none; padding: 2px; background-color: #FFF; margin-top: 2px; width: 250px; border: 1px solid #DDD;"><b><?php echo __('Select a different project'); ?></b><br>
	<table style="width: 100%;" cellpadding=0 cellspacing=0 id="prod_table">
	<?php

		$projects = BUGSproject::getAll($selectedProject->getID());
		if (count($projects) > 0)
		{
			foreach ($projects as $aProject)
			{
				$aProject = BUGSfactory::projectLab($aProject['id']);
				if (!$aProject->isLocked())
				{
					?>
					<tr>
					<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_project.png'); ?>
					<td style="width: auto; padding: 2px; padding-right: 15px;"><a href="javascript:void(0);" onclick="setProject(<?php print $aProject->getID(); ?>);"><?php print $aProject->getName(); ?></a></td>
					</tr>
					<?php
					$pCount++;
				}
			}
		}
		else
		{
			?>
			<tr><td style="padding: 2px; color: #AAA;"><?php echo __('There are no other available projects'); ?></td></tr>
			<?php
		}

	?>
	<tr>
		<td style="padding: 2px; font-size: 10px; text-align: right;" colspan=2><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setproject_dropdown');"><?php echo __('Close menu'); ?></a></td>
	</tr>
	</table></div>
	</td>
	<td style="width: auto; padding: 2px; padding-right: 15px;"><b><?php print $selectedProject->getName(); ?></b></td>
	<?php
}
else
{
	?>
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_project.png'); ?>
	<td style="width: auto; padding: 2px; color: #AAA;"><?php echo __('There are no available projects'); ?></td>
	<?php
}
?>
</tr>
<tr>
<td colspan=2 style="text-align: center;"><?php

	if ($pCount > 0)
	{
		?><a href="javascript:void(0);" onclick="javascript:showHide('rni_step1_setproject_dropdown');"><?php echo __('Change'); ?></a><?php
	}
	/*elseif(!$step1_set)
	{
		?><div style="color: #AAA;"><?php echo __('Change'); ?></div><?php
	}*/

?></td>
</tr>
</table>