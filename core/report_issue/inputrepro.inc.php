<table style="width: 100%; <?php if ($step3_title !== null && $step3_description !== null && $step3_repro === null) echo 'display: none;'; ?>" cellpadding=0 cellspacing=0>
<tr id="rni_step3_repro_view">
	<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_description.png') ?></td>
	<td style="width: 120px; padding: 2px;" valign="top"><b><?php echo __('How to reproduce'); ?></b></td>
	<td style="width: auto; padding: 2px;">
	<?php 
	
	if ($step3_title === null || $step3_description === null)
	{
		echo __('Please fill out the summary and description first');
	}
	elseif ($step3_repro !== null && trim(bugs_BBDecode($step3_repro)) == '<p></p>')
	{
		echo __('No reproduction steps entered');
	}
	else
	{
		echo bugs_BBDecode($step3_repro);
	}
	
	?></td>
	<?php if ($step3_title !== null && $step3_description !== null): ?>
		<td style="width: 70px; padding: 2px; text-align: right; font-size: 10px;" valign="top"><a href="javascript:void(0);" onclick="Element.hide('rni_step3_repro_view');Element.show('rni_step3_repro_edit');Element.show('rni_step3_repro_cancel');"><?php echo __('Change'); ?></a></td>
	<?php endif; ?>
</tr>
</table>
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_step3_repro_form" id="rni_step3_repro_form">
	<table style="width: 100%; <?php if ($step3_repro !== null || $step3_description === null || $step3_title === null || $step3_set) echo 'display: none;'; ?>" cellpadding=0 cellspacing=0 id="rni_step3_repro_edit">
	<tr>
		<td style="width: 20px; padding: 2px;" valign="top"><?php echo image_tag('icon_description.png') ?></td>
		<td style="width: 120px; padding: 2px;" valign="top"><b><?php echo __('How to reproduce'); ?></b></td>
		<td style="width: auto; padding: 2px;"><?php echo bugs_newTextArea('rni_step3_repro', '200px', '100%', BUGScontext::getRequest()->sanitize_input($step3_repro)); ?></td>
		<td style="width: 70px; padding: 2px;" valign="top"><input type="submit" value="<?php echo __('Set'); ?>" style="width: 100%;">
		<div style="padding: 5px; display: none; text-align: right; font-size: 10px;" id="rni_step3_repro_cancel"><a href="javascript:void(0);" onclick="Element.show('rni_step3_repro_view');Element.hide('rni_step3_repro_edit');"><?php echo __('Cancel'); ?></a></div>
		</td>
	</tr>
	</table>
</form>