<?php

	$bugs_response->setTitle(__('Configure issue types'));
	$bugs_response->addJavascript('config/issuetypes.js');

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php include_component('configleftmenu', array('selected_section' => 6)); ?>
<td valign="top">
	<div style="width: 750px;" id="config_issuetypes">
		<div class="configheader"><?php echo __('Configure issue types'); ?></div>
		<div class="content"><?php echo __('Edit issue types and their settings here.'); ?></div>
		<div class="header_div" style="margin-top: 15px;"><?php echo __('Issue types'); ?></div>
		<div id="issuetypes_list">
			<?php foreach ($issue_types as $type): ?>
				<?php include_component('issuetype', array('type' => $type)); ?>
			<?php endforeach; ?>
		</div>
		<div class="header_div" style="margin-top: 20px;"><?php echo __('Add a new issue type'); ?></div>
		<div class="rounded_box lightyellow_borderless" style="margin: 5px 0 0 0;">
			<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
			<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
				<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuetypes_add'); ?>" onsubmit="addIssuetype('<?php echo make_url('configure_issuetypes_add'); ?>');return false;" id="add_issuetype_form">
					<label for="new_issuetype_name"><?php echo __('Issue type name'); ?></label>
					<input type="text" name="name" id="new_issuetype_name" style="width: 200px;">
					<label for="new_issuetype_icon"><?php echo __('Type'); ?></label>
					<select name="icon" id="new_issuetype_icon">
						<?php foreach ($icons as $icon => $description): ?>
							<option value="<?php echo $icon; ?>"<?php if ($icon == 'bug_report'): ?> selected<?php endif; ?>><?php echo $description; ?></option>
						<?php endforeach; ?>
					</select>
					<input type="submit" value="<?php echo __('Add'); ?>" style="font-weight: bold;" id="add_issuetype_button">
					<?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_issuetype_indicator')); ?>
				</form>
			</div>
			<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
		</div>
	</div>
</td>
</tr>
</table>