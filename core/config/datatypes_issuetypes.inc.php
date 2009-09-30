<table style="width: 100%;" class="configstrip" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 50%;" valign="top">
<table style="margin-top: 0px; width: 100%;" class="configstrip" cellpadding=0 cellspacing=0>
<tr>
<td valign="middle" class="cleft" style="width: 20px;"><?php echo image_tag('cfg_icon_issuetypes.png', 'align="left"'); ?></td>
<td valign="middle" class="cright" style="width: auto;"><b><?php echo __('Issue types'); ?></b></td>
</tr>
</table>
<span id="datatypes_span">
<?php

	//$allIssueTypes = BUGSissuetype::getAll();
	$include_table = true;
	foreach (BUGSissuetype::getAll() as $anIssueType)
	{
		$aDatatype = $anIssueType;
		require BUGScontext::getIncludePath() . 'include/config/datatypes_datatypebox.inc.php';
	}

?>
</span>
<?php
	if ($access_level == "full")
	{
		?>
		<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_datatype_form" onsubmit="return false">
		<input type="hidden" name="module" value="core">
		<input type="hidden" name="section" value=4>
		<input type="hidden" name="subsection" value=1>
		<input type="hidden" name="add_issuetype" value="true">
		<table style="width: 100%;" cellpadding=0 cellspacing=0>
		<tr>
		<td style="width: auto;">&nbsp;</td>
		<td style="width: 15px;">&nbsp;</td>
		<td style="width: 20px;">&nbsp;</td>
		</tr>
		<tr>
		<td style="padding: 2px; width: auto;"><input type="text" name="issue_name" value="" style="width: 100%;"></td>
		<td colspan=2 style="text-align: right;"><button style="width: 100%;" onclick="addIssuetype();"><?php echo __('Add'); ?></button></td>
		</tr>
		</table>
		</form>
		<?php
	}
	
?>
</td>
<td style="width: 50%;" valign="top" id="edit_datatype_td"></td>
</tr>
</table>