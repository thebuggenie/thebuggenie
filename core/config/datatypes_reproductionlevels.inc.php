<table style="width: 100%;" class="configstrip" cellpadding=0 cellspacing=0>
<tr>
<td valign="middle" class="cleft" style="width: 20px;"><?php echo image_tag('cfg_icon_repro.png'); ?></td>
<td valign="middle" class="cright" style="width: auto;"><b><?php echo __('Reproduction levels'); ?></b></td>
</tr>
</table>
<span id="datatypes_span">
<?php

	$include_table = true;
	foreach (BUGSdatatype::getAll(BUGSdatatype::REPRO) as $aListType)
	{
		$aDatatype = BUGSfactory::datatypeLab($aListType, BUGSdatatype::REPRO);
		require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
	}

?>
</span>
<?php
	if ($access_level == "full")
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_datatype_form" onsubmit="return false">
		<input type="hidden" name="subsection" value=<?php echo BUGScontext::getRequest()->getParameter('subsection'); ?>>
		<input type="hidden" name="add_datatype" value="true">
		<input type="hidden" name="datatype" value="<?php echo BUGSdatatype::REPRO; ?>">
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="width: auto;">&nbsp;</td>
		<td style="width: 15px;">&nbsp;</td>
		<td style="width: 20px;">&nbsp;</td>
		</tr>
		<tr>
		<td style="padding: 2px; width: auto;"><input type="text" name="datatype_name" value="" style="width: 100%;"></td>
		<td colspan=2 style="text-align: right;"><button onclick="addDatatype();"><?php echo __('Add'); ?></button></td>
		</tr>
		</table>
		</form>
		<?php
	}
?>