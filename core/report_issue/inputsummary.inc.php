<?php

	if ($step3_title !== null)
	{
		?>
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
		<tr id="rni_step3_summary_view">
			<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_title.png'); ?>
			<td style="width: 120px; padding: 2px;"><b><?php echo __('Issue summary'); ?></b></td>
			<td style="width: auto; padding: 2px;"><?php print $step3_title; ?></td>
			<td style="width: 70px; padding: 2px; text-align: right; font-size: 10px;" colspan=2><a href="javascript:void(0);" onclick="Element.hide('rni_step3_summary_view');Element.show('rni_step3_summary_edit');"><?php echo __('Change'); ?></a></td>
		</tr>
		</table>
		<?php
	}

?>
<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="reportissue.php" enctype="multipart/form-data" method="post" name="rni_step3_title_form" id="rni_step3_title_form">
	<table style="width: 100%; <?php if ($step3_title !== null) echo 'display: none;'; ?>" cellpadding=0 cellspacing=0 id="rni_step3_summary_edit">
	<tr>
		<td style="width: 20px; padding: 2px;"><?php echo image_tag('icon_title.png'); ?>
		<td style="width: 120px; padding: 2px;"><b><?php echo __('Issue summary'); ?></b></td>
		<td style="width: auto; padding: 2px;"><input type="text" name="rni_step3_title" id="rni_step3_title" value="<?php print (isset($step3_title)) ? $step3_title : ""; ?>" style="width: 100%;"></td>
		<td style="width: 70px; padding: 2px;"><input type="submit" value="<?php echo __('Set'); ?>" style="width: 100%;" onclick="setTitle();"></td>
		<?php
		if ($step3_title !== null)
		{
			?>
			<td style="width: 45px; padding: 2px; text-align: right; font-size: 10px;"><a href="javascript:void(0);" onclick="Element.show('rni_step3_summary_view');Element.hide('rni_step3_summary_edit');"><?php echo __('Cancel'); ?></a></td>
			<?php
		}
		?>
	</tr>
	</table>
	<script type="text/javascript">
		if ($('rni_step3_summary_edit').style.display == '')
		{
			$('rni_step3_title').focus();
		}
	</script>
</form>