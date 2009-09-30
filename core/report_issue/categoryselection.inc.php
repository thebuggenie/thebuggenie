<?php

$categories = BUGSdatatype::getAll(BUGSdatatype::CATEGORY);

if ($theCategory instanceof BUGSdatatype)
{
	?>
	<?php echo bugs_helpBrowserHelper('reportissue_category', image_tag('help.png', array('style' => "float: right;"))); ?>
	<div style="width: auto; border-bottom: 1px solid #DDD; padding: 2px;"><b>Category</b></div>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<tr>
	<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_category.png'); ?>
	<td style="width: auto; padding: 2px;"><?php print $theCategory->getName(); ?></td>
	<td style="width: 50px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="javascript:showHide('rni_setcategory_dropdown');"><?php echo __('Change'); ?></a></td>
	</tr>
	</table>
	<div style="position: absolute; width: 150px; background-color: #FFF; padding: 2px; border: 1px solid #DDD; display: none;" id="rni_setcategory_dropdown">
	<b><?php echo __('Change category'); ?></b><br>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<?php

	foreach ($categories as $aCategory)
	{
		$aCategory = BUGSfactory::datatypeLab($aCategory, BUGSdatatype::CATEGORY);
		?>
		<tr>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_category.png'); ?>
		<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setCategory(<?php print $aCategory->getID(); ?>)"><?php print $aCategory->getName(); ?></a></td>
		</tr>
		<?php
	}
	if (count($categories) == 0)
	{
		?>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><div style="color: #AAA; padding: 2px;"><?php echo __('There are no available categories'); ?></div></td>
		</tr>
		<?php
	}

	?>
	<tr>
	<td style="font-size: 10px; padding: 2px; text-align: right;" colspan=2>
	<a href="javascript:void(0);" onclick="javascript:showHide('rni_setcategory_dropdown');"><?php echo __('Cancel'); ?></a>
	</td>
	</tr>
	</table>
	</div>
	<?php
}
else
{
	?>
	<?php echo bugs_helpBrowserHelper('reportissue_category', image_tag('help.png', array('style' => "float: right;"))); ?>
	<div style="width: auto; border-bottom: 1px solid #DDD; padding: 2px;"><b><?php echo __('Available categories'); ?></b></div>
	<table style="width: 100%;" cellpadding=0 cellspacing=0>
	<?php

	foreach ($categories as $aCategory)
	{
		$aCategory = BUGSfactory::datatypeLab($aCategory, BUGSdatatype::CATEGORY);
		?>
		<tr>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_category.png'); ?>
		<td style="width: auto; padding: 2px;"><a href="javascript:void(0);" onclick="setCategory(<?php print $aCategory->getID(); ?>)"><?php print $aCategory->getName(); ?></a></td>
		</tr>
		<?php
	}
	if (count($categories) == 0)
	{
		?>
		<tr>
		<td style="width: auto; padding: 2px;" colspan=2><div style="color: #AAA; padding: 2px;"><?php echo __('There are no available categories'); ?></div></td>
		</tr>
		<?php
	}

	?>
	</table>
	<?php
}
?>