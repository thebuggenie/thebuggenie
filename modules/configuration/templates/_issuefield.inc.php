<li class="hover_highlight issuefield_item_option" id="item_option_<?php echo $type; ?>_<?php echo $item->getID(); ?>" style="clear: both; height: 24px;">
	<div id="item_option_<?php echo $type; ?>_<?php echo $item->getID(); ?>_content">
		<?php if ($type == 'status'): ?>
			<div style="border: 0; background-color: <?php echo $item->getColor(); ?>; font-size: 1px; width: 16px; border: 1px solid rgba(0, 0, 0, 0.2); height: 16px; margin-right: 2px; float: left;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_itemdata">&nbsp;</div>
		<?php endif; ?>
		<?php if (!$item->isBuiltin()): ?>
			<div style="width: 50px; display: inline;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_value"><?php echo $item->getValue(); ?></div>
		<?php endif; ?>
		<div style="padding: 2px; font-size: 12px; display: inline;" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_name"><?php echo $item->getName(); ?></div>
		<div class="button-group" style="float: right;">
			<a href="javascript:void(0);" class="button button-icon button-silver" onclick="$('item_option_<?php echo $type; ?>_<?php echo $item->getID(); ?>_content').hide();$('edit_item_option_<?php echo $item->getID(); ?>').show();$('<?php echo $type; ?>_<?php echo $item->getID(); ?>_name_input').focus();" title="<?php echo __('Edit this item'); ?>"><?php echo image_tag('icon_edit.png'); ?></a>
			<a href="javascript:void(0);" class="button button-icon button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issuefield_permissions', 'item_id' => $item->getID(), 'item_key' => $item->getPermissionsKey(), 'access_level' => $access_level)); ?>');" title="<?php echo __('Set permissions for this item'); ?>"><?php echo image_tag('cfg_icon_permissions.png'); ?></a>
			<?php if ($item->canBeDeleted()): ?>
				<a href="javascript:void(0);" class="button button-icon button-silver" onclick="TBG.Main.Helpers.Dialog.show('<?php echo __('Really delete %itemname%?', array('%itemname%' => $item->getName())); ?>', '<?php echo __('Are you really sure you want to delete this item?'); ?>', {yes: {click: function() {TBG.Config.Issuefields.Options.remove('<?php echo make_url('configure_issuefields_delete', array('type' => $type, 'id' => $item->getID())); ?>', '<?php echo $type; ?>', <?php echo $item->getID(); ?>); TBG.Main.Helpers.Dialog.dismiss(); }}, no: {click: TBG.Main.Helpers.Dialog.dismiss}});" id="delete_<?php echo $item->getID(); ?>_link"><?php echo image_tag('icon_delete.png'); ?></a>
			<?php else: ?>
				<a href="javascript:void(0);" class="button button-icon button-silver disabled" onclick="TBG.Main.Helpers.Message.error('<?php echo __('This item cannot be deleted'); ?>', '<?php echo __('Other items - such as workflow steps - may depend on this item to exist. Remove the dependant item or unlink it from this item to continue.'); ?>');" id="delete_<?php echo $item->getID(); ?>_link"><?php echo image_tag('icon_delete_disabled.png'); ?></a>
			<?php endif; ?>
		</div>
		<?php echo image_tag('spinning_16.gif', array('id' => 'delete_' . $type . '_' . $item->getID() . '_indicator', 'style' => 'display: none; float: right; margin: 2px 5px 0 0;')); ?>
	</div>
	<div id="edit_item_option_<?php echo $item->getID(); ?>" style="display: none;">
		<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $item->getID())); ?>" onsubmit="TBG.Config.Issuefields.Options.update('<?php echo make_url('configure_issuefields_edit', array('type' => $type, 'id' => $item->getID())); ?>', '<?php echo $type; ?>', <?php echo $item->getID(); ?>);return false;" id="edit_<?php echo $type; ?>_<?php echo $item->getID(); ?>_form">
			<table style="width: 100%;" cellpadding="0" cellspacing="0">
				<tr>
					<?php if ($type == 'status'): ?>
						<td style="font-size: 14px; width: 70px;">
							<input type="text" name="itemdata" id="<?php echo $type; ?>_<?php echo $item->getID(); ?>_itemdata_input" style="width: 45px;" value="<?php echo $item->getColor(); ?>">
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
						<?php echo __('%update% or %cancel%', array('%update%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'item_option_'.$type.'_'.$item->getID().'_content\').show();$(\'edit_item_option_'.$item->getID().'\').hide();"><b>' . __('cancel') . '</b></a>')); ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
</li>