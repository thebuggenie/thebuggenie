<?php

	$tbg_response->setTitle(__('Configure data types'));
	$tbg_response->addJavascript('cpicker/cpicker.js');
	$tbg_response->addStylesheet('cpicker/cpicker.css');
?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('leftmenu', array('selected_section' => 4)); ?>
<td valign="top">
	<div style="width: 750px;" id="config_issuefields">
		<div class="config_header"><?php echo __('Configure issue fields'); ?></div>
		<div class="content"><?php echo __('Edit built-in and custom issue fields and values here'); ?></div>
		<div class="header_div" style="margin-top: 15px;"><?php echo __('Built-in issue fields'); ?></div>
		<?php foreach ($builtin_types as $type_key => $type): ?>
			<div class="rounded_box borderless mediumgrey" style="margin: 5px 0 0 0; font-size: 12px;">
				<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
				<div class="header"><a href="javascript:void(0);" onclick="TBG.Issues.Field.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type['description']; ?></a>&nbsp;<span class="faded_out dark" style="font-weight: normal; font-size: 12px;"><?php echo $type['key']; ?></span></div>
				<div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
			</div>
		<?php endforeach; ?>
		<div class="header_div" style="margin-top: 20px;"><?php echo __('Custom issue fields'); ?></div>
		<div class="rounded_box yellow borderless" style="margin: 5px 0 0 0; font-size: 12px;">
			<div class="header"><?php echo __('Add new custom field'); ?></div>
			<div class="content" style="padding: 2px; margin-bottom: 15px;">
				<?php echo __('Enter a name for the field (same as ex. "%resolution_types%" above), then click %add%', array('%resolution_types%' => __('Resolution types'), '%add%' => '<b>' . __('Add') . '</b>')); ?>
			</div>
			<form accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add_customtype'); ?>" onsubmit="TBG.Issues.Field.Custom.add('<?php echo make_url('configure_issuefields_add_customtype'); ?>');return false;" id="add_custom_type_form">
				<label for="new_custom_field_name" style="width: 100px; display: block; float: left;"><?php echo __('Custom type'); ?></label>
				<select id="new_custom_field_type" name="field_type" style="width: 375px;">
					<?php foreach (TBGCustomDatatype::getFieldTypes() as $type => $description): ?>
						<option value="<?php echo $type; ?>"<?php if ($type == TBGCustomDatatype::INPUT_TEXTAREA_MAIN || $type == TBGCustomDatatype::DROPDOWN_CHOICE_TEXT || $type == TBGCustomDatatype::INPUT_TEXT || $type == TBGCustomDatatype::RADIO_CHOICE || $type == TBGCustomDatatype::INPUT_TEXTAREA_SMALL || $type == TBGCustomDatatype::EDITIONS_CHOICE || $type == TBGCustomDatatype::COMPONENTS_CHOICE || $type == TBGCustomDatatype::RELEASES_CHOICE || $type == TBGCustomDatatype::STATUS_CHOICE): ?> selected<?php else: ?> disabled<?php endif; ?>><?php echo $description; ?></option>
					<?php endforeach; ?>
				</select>
				<br style="clear: both;">
				<label for="new_custom_field_name" style="width: 100px; display: block; float: left;"><?php echo __('Field name'); ?></label>
				<input type="text" name="name" id="new_custom_field_name" style="width: 200px;">
				<br style="clear: both;">
				<label for="new_custom_field_label" style="width: 100px; display: block; float: left;"><?php echo __('Field label'); ?></label>
				<input type="text" name="label" id="new_custom_field_label" style="width: 300px;">&nbsp;&nbsp;<span class="faded_out">(<?php echo __('The label is shown to the user in issue view'); ?>)</span>
				<br style="clear: both;">
				<div style="text-align: right; padding: 5px;">
					<input type="submit" value="<?php echo __('Add custom field'); ?>" style="font-weight: normal; font-size: 14px;" id="add_custom_type_button">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_custom_type_indicator')); ?>
				</div>
			</form>
		</div>
		<div id="custom_types_list">
			<?php foreach ($custom_types as $type_key => $type): ?>
				<?php include_component('issuefields_customtype', array('type_key' => $type_key, 'type' => $type)); ?>
			<?php endforeach; ?>
		</div>
	</div>
</td>
</tr>
</table>
<script>
	var picker = new ColorPicker({iconPath: '<?php echo TBGSettings::getURLhost() . TBGContext::getTBGPath() . 'themes/' . TBGSettings::getThemeName() . '/cpicker/'; ?>'});
	
	picker.setCallback(function (color) {
		picker.selector.style.backgroundColor = color;
		picker.selector.value = color;
		picker.hide();
	});
</script>