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
		<?php foreach ($builtin_types as $type_key => $type_description): ?>
			<div class="rounded_box borderless" style="margin: 5px 0 0 0;">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
					<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
					<div class="header"><a href="javascript:void(0);" onclick="showIssuefieldOptions('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type_description; ?></a></div>
					<div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endforeach; ?>
		<div class="header_div" style="margin-top: 20px;"><?php echo __('Custom issue fields'); ?></div>
		<div class="faded_medium" style="padding: 3px; font-size: 13px;"><?php echo __('Not implemented yet'); ?></div>
	</div>
</td>
</tr>
</table>