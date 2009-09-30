<a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $theProject->getID(); ?>&amp;edit_editions=true"><b>&lt;&lt; <?php echo __('Back to list of editions'); ?></b></a>
<br>
<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px; margin-top: 10px;"><b><?php echo __('EDITION DETAILS'); ?></b></div>
<?php

if ($access_level == "full")
{
	?>
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="edition_details" onsubmit="return false">
	<input type="hidden" name="module" value="core">
	<input type="hidden" name="section" value="10">
	<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
	<input type="hidden" name="e_id" value="<?php print $theEdition->getID(); ?>">
	<input type="hidden" name="edit_editions" value="true">
	<input type="hidden" name="edit_details" value="true">
	<table cellpadding=0 cellspacing=0 style="width: 100%; margin-top: 5px;">
	<tr>
	<td style="width: 95px; padding: 2px;"><b><?php echo __('Name:') ?></b></td>
	<td style="width: auto; padding: 2px;"><input type="text" style="width: 100%;" name="e_name" value="<?php print $theEdition->getName(); ?>"></td>
	</tr>
	<tr>
	<td style="padding: 2px;"><b><?php echo __('Description:') ?></b></td>
	<td style="padding: 2px;"><input type="text" style="width: 100%;" name="description" value="<?php print $theEdition->getDescription(); ?>"></td>
	</tr>
	<tr>
	<td style="padding: 2px;"><b><?php echo __('Documentation:') ?></b></td>
	<td style="padding: 2px;"><input type="text" style="width: 100%;" name="doc_url" value="<?php print $theEdition->getDocumentationURL(); ?>"></td>
	</tr>
	</table>
	<div style="padding: 10px; padding-right: 2px; text-align: right;">
	<button onclick="submitEditionDetails();"><?php echo __('Save'); ?></button>
	</div>
	</form>
	<?php
}

if ($theProject->isBuildsEnabled())
{
	?>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px; margin-top: 10px;"><b><?php echo __('ADD NEW BUILD'); ?></b></div>
	<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="post" id="add_build_form" onsubmit="return false;">
	<input type="hidden" name="module" value="core">
	<input type="hidden" name="section" value="10">
	<input type="hidden" name="p_id" value="<?php print $theProject->getID(); ?>">
	<input type="hidden" name="e_id" value="<?php print $theEdition->getID(); ?>">
	<input type="hidden" name="b_id" value="0">
	<input type="hidden" name="edit_editions" value="true">
	<input type="hidden" name="action" value="add">
	<table cellpadding=0 cellspacing=0 style="width: 100%;">
	<tr>
	<td style="width: 40px; text-align: left;"><b><?php echo __('Name:'); ?></b></td>
	<td style="width: auto;"><input type="text" name="build_name" style="width: 100%;" value=""></td>
	<td style="width: 30px; text-align: right;"><b><?php echo __('Ver: %version_number%', array('%version_number%' => '')); ?></b></td>
	<td style="width: 100px; text-align: right;"><input type="text" name="ver_mj" style="width: 25px; text-align: center;" value="">&nbsp;.&nbsp;<input type="text" name="ver_mn" style="width: 25px; text-align: center;" value="">&nbsp;.&nbsp;<input type="text" name="ver_rev" style="width: 25px; text-align: center;" value=""></td>
	<td style="width: 40px; text-align: right;"><button onclick="addBuild();"><?php echo __('Add'); ?></button></td>
	</tr>
	</table>
	</form>
	<div style="width: auto; padding: 3px; background-color: #F2F2F2; border-bottom: 1px solid #DDD; margin-bottom: 5px; margin-top: 10px;"><b><?php echo __('EXISTING BUILDS'); ?></b></div>
	<span id="build_table">
	<?php

	$include_table = true;
	foreach ($theEdition->getBuilds() as $aBuild)
	{
		require BUGScontext::getIncludePath() . 'include/config/projects_buildbox.inc.php';
	}

	?>
	</span>
	<?php

}

?>