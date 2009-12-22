<?php

	$bugs_response->setTitle(__('Configure data types'));
	$bugs_response->addJavascript('config/issuefields.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 4)); ?>
<td valign="top">
	<div style="width: 750px;" id="config_issuefields">
		<div class="configheader"><?php echo __('Configure issue fields'); ?></div>
		<div class="content"><?php echo __('Edit built-in and custom issue fields and values here'); ?></div>
		<div class="header_div" style="margin-top: 15px;"><?php echo __('Built-in issue fields'); ?></div>
		<?php foreach ($builtin_types as $type_key => $type): ?>
			<div class="rounded_box borderless" style="margin: 5px 0 0 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
					<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
					<div class="header"><a href="javascript:void(0);" onclick="showIssuefieldOptions('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type['description']; ?></a>&nbsp;<span class="faded_dark" style="font-weight: normal; font-size: 12px;"><?php echo $type['key']; ?></span></div>
					<div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endforeach; ?>
		<div class="header_div" style="margin-top: 20px;"><?php echo __('Custom issue fields'); ?></div>
		<div class="rounded_box lightyellow_borderless" style="margin: 5px 0 0 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
				<div class="header"><?php echo __('Add new custom field'); ?></div>
				<div class="content" style="padding: 2px; margin-bottom: 15px;">
					<?php echo __('Enter a name for the field (only used here, same as ex. "%reproducability_grades%" above), then click %add%', array('%reproducability_grades%' => __('Reproducability grades'), '%add%' => '<b>' . __('Add') . '</b>')); ?>
				</div>
				<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add_customtype'); ?>" onsubmit="addIssuefieldCustom('<?php echo make_url('configure_issuefields_add_customtype'); ?>');return false;" id="add_custom_type_form">
					<label for="new_custom_field_name"><?php echo __('Field name'); ?></label>
					<input type="text" name="name" id="new_custom_field_name" style="width: 200px;">
					<select id="new_custom_field_type" name="field_type" style="width: 375px;">
						<?php foreach (BUGScustomdatatype::getFieldTypes() as $type => $description): ?>
							<option value="<?php echo $type; ?>"<?php if ($type == BUGScustomdatatype::DROPDOWN_CHOICE_TEXT): ?> selected<?php endif; ?>><?php echo $description; ?></option>
						<?php endforeach; ?>
					</select>
					<input type="submit" value="<?php echo __('Add'); ?>" style="font-weight: bold;" id="add_custom_type_button">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_custom_type_indicator')); ?>
				</form>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
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