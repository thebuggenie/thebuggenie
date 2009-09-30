<?php

	if (!$module instanceof BUGSmodule)
	{
		die();
	}

?>
<div style="margin-top: 10px; width: 740px; padding: 4px; border-bottom: 1px solid #DDD;"><b><?php echo $module->getLongname(); ?></b></div>
<table style="border-bottom: 1px solid #DDD; width: 740px; background-color: #F5F5F5;" cellpadding=0 cellspacing=0>
<?php

foreach ($module->getAvailableSections() as $section)
{
	$base_url = 'config.php?module=core&amp;section=15&amp;subsection=2&amp;module_name=' . $module->getName() . '&amp;section_module=' . $section['module'] . '&amp;identifier=' . $section['identifier'] . '&amp;';
	$section_enabled = $module->isSectionEnabled($section['module'], $section['identifier']);
	?>
	<tr>
		<td style="width: 60px; padding: 2px;"><a href="<?php echo $base_url . (($section_enabled) ? 'disable_section=true' : 'enable_section=true'); ?>"><?php echo ($section_enabled) ? __('Disable') : __('Enable'); ?></a></td>
		<td style="width: auto; padding: 2px;"><?php echo $section['description']; ?></td>
	</tr>
	<?php
}

?>
</table>