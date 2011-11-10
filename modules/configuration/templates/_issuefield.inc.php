<tr class="hover_highlight" id="item_<?php echo $type; ?>_<?php echo $item->getID(); ?>">
	<?php if ($type == 'status'): ?>
		<td style="width: 30px;"><div style="border: 0; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 25px; height: 8px; margin-right: 2px;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_itemdata">&nbsp;</div></td>
	<?php endif; ?>
	<?php if (!$item->isBuiltin()): ?>
		<td style="width: 50px;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_value"><?php echo $item->getValue(); ?></td>
	<?php endif; ?>
	<td style="padding: 2px; font-size: 12px;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_name"><?php echo $item->getName(); ?></td>
	<td style="width: 60px; padding: 2px; text-align: right;">
		<a href="javascript:void(0);" onclick="$('item_<?php echo $type; ?>_<?php echo $item->getID(); ?>').hide();$('edit_item_<?php echo $item->getID(); ?>').show();$('<?php echo $type; ?>_<?php echo $item->getID(); ?>_name_input').focus();" class="image" title="<?php echo __('Edit this item'); ?>"><?php echo image_tag('icon_edit.png'); ?></a>
		<a href="javascript:void(0);" onclick="$('item_<?php echo $item->getID(); ?>_permissions').toggle();" class="image" title="<?php echo __('Set permissions for this item'); ?>" style="margin-right: 5px;"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
		<?php if ($item->canBeDeleted()): ?>
			<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $item->getID(); ?>').toggle();" class="image" id="delete_<?php echo $item->getID(); ?>_link"><?php echo image_tag('icon_delete.png'); ?></a>
		<?php else: ?>
			<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Message.error('<?php echo __('This item cannot be deleted'); ?>', '<?php echo __('Other items - such as workflow steps - may depend on this item to exist. Remove the dependant item or unlink it from this item to continue.'); ?>');" class="image" id="delete_<?php echo $item->getID(); ?>_link"><?php echo image_tag('icon_delete_disabled.png'); ?></a>
		<?php endif; ?>
		<?php echo image_tag('spinning_16.gif', array('id' => 'delete_' . $type . '_' . $item->getID() . '_indicator', 'style' => 'display: none;')); ?>
	</td>
</tr>
<tr id="edit_item_<?php echo $item->getID(); ?>" style="display: none;">
	<td colspan="3">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $item->getID())); ?>" onsubmit="TBG.Config.Issuefields.Options.update('<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $item->getID())); ?>', '<?php echo $type; ?>', <?php echo $item->getID(); ?>);return false;" id="edit_<?php echo $type; ?>_<?php echo $item->getID(); ?>_form">
			<table style="width: 100%;" cellpadding="0" cellspacing="0">
				<tr>
					<?php if ($type == 'status'): ?>
						<td style="font-size: 14px; width: 70px;">
							<input type="text" name="itemdata" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_itemdata_input" style="width: 45px;" value="<?php echo $item->getColor(); ?>" onclick="picker.show(this);">
						</td>
					<?php endif; ?>
					<?php if (!array_key_exists($type, TBGDatatype::getTypes())): ?>
						<td style="font-size: 14px; width: 70px;">
							<input type="text" name="value" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_value_input" style="width: 45px;" value="<?php echo $item->getValue(); ?>">
						</td>
					<?php endif; ?>
					<td>
						<input type="text" name="name" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_name_input" style="width: 400px;" value="<?php echo $item->getName(); ?>">
					</td>
					<td style="text-align: right; width: 150px;">
						<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'edit_' . $type . '_' . $item->getID() . '_indicator')); ?>
						<input type="submit" value="<?php echo __('Update'); ?>" style="margin-right: 5px; font-weight: bold;">
						<?php echo __('%update% or %cancel%', array('%update%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'item_'.$type.'_'.$item->getID().'\').show();$(\'edit_item_'.$item->getID().'\').hide();"><b>' . __('cancel') . '</b></a>')); ?>
					</td>
				</tr>
			</table>
		</form>
	</td>
</tr>
<tr id="delete_item_<?php echo $item->getID(); ?>" style="display: none;">
	<td colspan="3">
		<div class="rounded_box white shadowed" style="margin: 5px 0 10px 0; font-size: 12px;">
				<div class="header"><?php echo __('Really delete "%itemname%"?', array('%itemname%' => $item->getName())); ?></div>
				<div class="content">
					<?php echo __('Are you really sure you want to delete this item?'); ?>
					<div style="text-align: right; font-size: 13px;">
						<a href="javascript:void(0);" onclick="TBG.Config.Issuefields.Options.remove('<?php echo make_url('configure_issuefields_delete', array('type' => $type, 'id' => $item->getID())); ?>', '<?php echo $type; ?>', <?php echo $item->getID(); ?>);"><?php echo __('Yes'); ?></a> ::
						<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $item->getID(); ?>').toggle();"><b><?php echo __('No'); ?></b></a>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>
<tr id="item_<?php echo $item->getID(); ?>_permissions" style="display: none;">
	<td colspan="3">
		<div class="rounded_box white" style="margin: 5px 0 10px 0; padding: 3px; font-size: 12px;">
			<div class="header"><?php echo __('Permission details for "%itemname%"', array('%itemname%' => $item->getName())); ?></div>
			<div class="content">
				<?php echo __('Specify who can set this value for issues.'); ?>
				<?php include_component('configuration/permissionsinfo', array('key' => $item->getPermissionsKey(), 'mode' => 'datatype', 'target_id' => $item->getID(), 'module' => 'core', 'access_level' => $access_level)); ?>
			</div>
		</div>
	</td>
</tr>