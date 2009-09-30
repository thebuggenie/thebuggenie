<table style="width: 100%; <?php if ($step3_title !== null && $step3_description === null) echo 'display: none;'; ?>" cellpadding=0 cellspacing=0>
<tr id="rni_step3_description_view">
	<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_description.png'); ?></td>
	<td style="width: 120px; padding: 2px;" valign="top"><b><?php echo __('Issue description'); ?></b></td>
	<td style="width: auto; padding: 2px;"><?php print ($step3_title === null) ? __('Please fill out the summary first') : bugs_BBDecode($step3_description); ?></td>
	<?php if ($step3_title !== null): ?>
		<td style="width: 70px; padding: 2px; text-align: right; font-size: 10px;" valign="top"><a href="javascript:void(0);" onclick="Element.hide('rni_step3_description_view');Element.show('rni_step3_description_edit');Element.show('rni_step3_description_cancel');"><?php echo __('Change'); ?></a></td>
	<?php endif; ?>
</tr>
</table>
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_step3_description_form" id="rni_step3_description_form">
	<table style="width: 100%; <?php if ($step3_description !== null || $step3_title === null) echo 'display: none;'; ?>" cellpadding=0 cellspacing=0 id="rni_step3_description_edit">
	<tr>
		<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_description.png') ?></td>
		<td style="width: 120px; padding: 2px;" valign="top"><b><?php echo __('Issue description'); ?></b></td>
		<td style="width: auto; padding: 2px;"><?php echo bugs_newTextArea('rni_step3_description', '200px', '100%', BUGScontext::getRequest()->sanitize_input($step3_description)); ?></td>
		<td style="width: 70px; padding: 2px;" valign="top"><input type="submit" value="<?php echo __('Set'); ?>" style="width: 100%;">
		<div style="padding: 5px; display: none; text-align: right; font-size: 10px;" id="rni_step3_description_cancel"><a href="javascript:void(0);" onclick="Element.show('rni_step3_description_view');Element.hide('rni_step3_description_edit');"><?php echo __('Cancel'); ?></a></div>
		</td>
	</tr>
	</table>
</form>