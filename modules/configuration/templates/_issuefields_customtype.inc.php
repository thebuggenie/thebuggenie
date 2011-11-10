<div id="item_<?php echo $type_key; ?>_<?php echo $type->getID(); ?>" class="rounded_box borderless mediumgrey" style="margin: 5px 0 0 0; padding: 3px; font-size: 12px;">
	<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
	<div class="header"><a href="javascript:void(0);" onclick="TBG.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');" id="custom_type_<?php echo $type_key; ?>_name_link"><?php echo $type->getName(); ?></a>&nbsp;<span class="faded_out dark" style="font-weight: normal; font-size: 12px;"><?php echo $type_key; ?></span></div>
	<div class="content">
		<a title="<?php echo __('Edit this custom type'); ?>" href="javascript:void(0);" onclick="$('edit_custom_type_<?php echo $type_key; ?>_form').toggle();$('custom_type_<?php echo $type_key; ?>_info').toggle();" class="image" style="float: right; margin-right: 5px;"><?php echo image_tag('icon_edit.png'); ?></a>
		<?php
			switch ($type->getType())
			{
				case TBGCustomDatatype::INPUT_TEXT:
				case TBGCustomDatatype::INPUT_TEXTAREA_MAIN:
				case TBGCustomDatatype::INPUT_TEXTAREA_SMALL:
				case TBGCustomDatatype::EDITIONS_CHOICE:
				case TBGCustomDatatype::COMPONENTS_CHOICE:
				case TBGCustomDatatype::RELEASES_CHOICE:
				case TBGCustomDatatype::STATUS_CHOICE:
					break;
				default:
					?><a title="<?php echo __('Show and edit available choices'); ?>" href="javascript:void(0);" onclick="TBG.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');" class="image" style="float: right; margin-right: 5px;"><?php echo image_tag('action_dropdown_small.png'); ?></a><?php
					break;
			}
		?>
		<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $type->getID(); ?>').toggle();" class="image" style="float: right; margin-right: 5px;" id="delete_<?php echo $type->getID(); ?>_link"><?php echo image_tag('icon_delete.png'); ?></a>
		<b><?php echo __('Type'); ?>:</b>&nbsp;<?php echo $type->getTypeDescription(); ?>
	</div>
	<div id="delete_item_<?php echo $type->getID(); ?>" class="rounded_box white shadowed" style="margin: 5px 0 10px 0; font-size: 12px; display: none;">
		<div class="header"><?php echo __('Really delete "%itemname%"?', array('%itemname%' => $type->getName())); ?></div>
		<div class="content">
			<?php echo __('Are you really sure you want to delete this item?'); ?><br>
			<?php echo __('This will also remove the value of this custom field from all issues, along with any possible options this field can take.')?>
			<div style="text-align: right; font-size: 13px;">
				<a href="javascript:void(0);" onclick="TBG.Issues.Field.Custom.remove('<?php echo make_url('configure_issuefields_delete_customtype', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>', '<?php echo $type->getID(); ?>');return false;"><?php echo __('Yes'); ?></a> ::
				<a href="javascript:void(0);" onclick="$('delete_item_<?php echo $type->getID(); ?>').toggle();"><b><?php echo __('No'); ?></b></a>
			</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'delete_'.$type_key.'_'.$type->getID().'_indicator', 'style' => 'margin-left: 5px; display: none;')); ?>
		</div>
	</div>
	<div id="custom_type_<?php echo $type_key; ?>_info">
		<b><?php echo __('Label'); ?>:</b>&nbsp;<span id="custom_type_<?php echo $type_key; ?>_description_span"><?php echo $type->getDescription(); ?></span><br>
		<span id="custom_type_<?php echo $type_key; ?>_instructions_div"<?php if (!$type->hasInstructions()): ?> style="display: none;"<?php endif; ?>>
			<b><?php echo __('Instructions'); ?>:</b>&nbsp;<span id="custom_type_<?php echo $type_key; ?>_instructions_span"><?php echo $type->getInstructions(); ?></span>
		</span>
		<span id="custom_type_<?php echo $type_key; ?>_no_instructions_div" class="faded_out dark"<?php if ($type->hasInstructions()): ?> style="display: none;"<?php endif; ?>><?php echo __("This custom type doesn't have any instructions"); ?></span>
	</div>
	<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type_key)); ?>" onsubmit="TBG.Issues.Field.Custom.update('<?php echo make_url('configure_issuefields_update_customtype', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');return false;" id="edit_custom_type_<?php echo $type_key; ?>_form" style="display: none;">
		<div class="rounded_box white" style="margin: 5px 0 0 0; padding: 3px; font-size: 12px;">
			<label for="custom_type_<?php echo $type_key; ?>_name"><?php echo __('Name'); ?></label>
			<input type="text" name="name" id="custom_type_<?php echo $type_key; ?>_name" value="<?php echo $type->getName(); ?>" style="width: 250px;">
			<label for="custom_type_<?php echo $type_key; ?>_description"><?php echo __('Label'); ?></label>
			<input type="text" name="description" id="custom_type_<?php echo $type_key; ?>_description" value="<?php echo $type->getDescription(); ?>" style="width: 250px;"><br>
			<div class="faded_out" style="margin-bottom: 10px; padding: 2px;"><?php echo __('Users see the label, not the name'); ?></div>
			<label for="custom_type_<?php echo $type_key; ?>_instructions"><?php echo __('Instructions'); ?></label>&nbsp;<span class="faded_out"><?php echo __('Optional instruction that will be displayed to users'); ?></span><br>
			<textarea name="instructions" id="custom_type_<?php echo $type_key; ?>_instructions" style="width: 500px; height: 70px;" cols="70" rows="5"><?php echo $type->getInstructions(); ?></textarea><br>
			<input type="submit" value="<?php echo __('Update details'); ?>" style="font-weight: bold; font-size: 13px;">
			<?php echo __('%update_details% or %cancel%', array('%update_details%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'edit_custom_type_' . $type_key . '_form\').toggle();$(\'custom_type_' . $type_key . '_info\').toggle();"><b>' . __('cancel') . '</b></a>')); ?>
			<?php echo image_tag('spinning_20.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'edit_custom_type_' . $type_key . '_indicator')); ?>
		</div>
	</form>
	<div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
</div>