<?php

if (!$aComponent instanceof BUGScomponent)
{
	exit();
}

?>
<tr id="show_component_<?php print $aComponent->getID(); ?>">
<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?>
<div style="display: none; border: 1px solid #DDD; padding: 5px; text-align: center; width: 300px; position: absolute; background-color: #FFF;" id="del_component_<?php print $aComponent->getID(); ?>">
<?php echo __('Are you sure you want to delete this component?'); ?><br>
<a href="javascript:void(0);" onclick="deleteComponent(<?php print $aComponent->getID(); ?>);"><?php echo __('Yes'); ?></a> | <a href="javascript:void(0);" onclick="Effect.Fade('del_component_<?php print $aComponent->getID(); ?>', { duration: 0.5 });"><b><?php echo __('No'); ?></b></a>
</div></td>
<td style="width: auto; padding: 2px;" id="component_<?php echo $aComponent->getID(); ?>_name"><?php print $aComponent; ?></td>
<td style="width: 40px; text-align: right;"><a href="javascript:void(0);" onclick="Element.hide('show_component_<?php print $aComponent->getID(); ?>');Element.show('edit_component_<?php print $aComponent->getID(); ?>');" style="font-size: 9px;"><?php echo __('Edit'); ?></a><br><a href="javascript:void(0);" onclick="Effect.Appear('del_component_<?php print $aComponent->getID(); ?>', { duration: 0.5 });" style="font-size: 9px;"><?php echo __('Delete'); ?></a></td>
</tr>
<tr id="edit_component_<?php print $aComponent->getID(); ?>" style="display: none;">
<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_components.png'); ?></td>
<td style="width: auto; padding: 0px;">
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="edit_component_<?php echo $aComponent->getID(); ?>_form" onsubmit="updateComponent(<?php echo $aComponent->getID(); ?>);return false;">
<input type="hidden" name="module" value="core">
<input type="hidden" name="section" value="10">
<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
<input type="hidden" name="c_id" value="<?php print $aComponent->getID(); ?>">
<input type="hidden" name="edit_component" value="true">
<input type="hidden" name="edit_editions" value="true">
<input type="text" name="c_name" value="<?php print $aComponent->getName(); ?>" style="width: 100%;">
</form></td>
<td style="width: 40px; text-align: right;"><button onclick="updateComponent(<?php echo $aComponent->getID(); ?>);"><?php echo __('Save'); ?></button></td>
</tr>