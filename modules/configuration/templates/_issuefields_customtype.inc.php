<div class="rounded_box borderless" style="margin: 5px 0 0 0;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="padding: 3px; font-size: 12px;">
		<?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
		<div class="header"><a href="javascript:void(0);" onclick="showIssuefieldOptions('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type->getName(); ?></a>&nbsp;<span class="faded_dark" style="font-weight: normal; font-size: 12px;"><?php echo $type_key; ?></span></div>
		<div class="content"><?php echo $type->getTypeDescription(); ?></div>
		<div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>