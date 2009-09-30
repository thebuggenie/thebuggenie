<?php

$components = ($selectedEdition instanceof BUGSedition) ? $selectedEdition->getComponents() : array();

if ($theComponent instanceof BUGScomponent)
{
	?>
	<?php echo bugs_helpBrowserHelper('reportissue_component', image_tag('help.png', array('style' => "float: right;"))); ?>
	<div style="width: auto; border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Affected component'); ?></b></div>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?>
	<td style="width: auto; padding: 2px;"><?php print $theComponent->getName(); ?></td>
	<td style="width: 50px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="javascript:showHide('rni_setcomponent_dropdown');"><?php echo __('Change'); ?></a></td>
	</tr>
	</table>
	<div style="position: absolute; width: 150px; background-color: #FFF; padding: 2px; border: 1px solid #DDD; display: none;" id="rni_setcomponent_dropdown">
	<b><?php echo __('Change component'); ?></b><br>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<?php

	foreach ($components as $aComponent)
	{
		?>
		<tr>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?>
		<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setComponent(<?php print $aComponent->getID(); ?>)"><?php print $aComponent->getName(); ?></a></td>
		</tr>
		<?php
	}
	if (count($components) == 0)
	{
		?>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><div style="color: #AAA; padding: 2px;"><?php echo __('There are no available components'); ?></div></td>
		</tr>
		<?php
	}

	?>
	<tr>
	<td style="font-size: 10px; padding: 2px; text-align: right;" colspan=2>
	<a href="javascript:void(0);" onclick="javascript:showHide('rni_setcomponent_dropdown');"><?php echo __('Cancel'); ?></a>
	</td>
	</tr>
	</table>
	</div>
	<?php
}
else
{
	?>
	<?php echo bugs_helpBrowserHelper('reportissue_component', image_tag('help.png', array('style' => "float: right;"))); ?>
	<div style="width: auto; border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Available components'); ?></b></div>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<?php

	foreach ($components as $aComponent)
	{
		?>
		<tr>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?>
		<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setComponent(<?php print $aComponent->getID(); ?>)"><?php print $aComponent->getName(); ?></a></td>
		</tr>
		<?php
	}
	if (count($components) == 0)
	{
		?>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><div style="color: #AAA; padding: 2px;"><?php echo __('There are no available components'); ?></div></td>
		</tr>
		<?php
	}

	?>
	</table>
	<?php
}
?>