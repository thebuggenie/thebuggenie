<?php

if (!$aProject instanceof BUGSproject)
{
	exit();
}

?>
<tr>
<td style="border: 1px solid #DDD; padding: 3px; margin-bottom: 5px;">
<div style="padding: 3px;"><b style="font-size: 13px;"><a href="<?php echo BUGScontext::getTBGPath(); ?>viewproject.php?project_id=<?php echo $aProject->getID(); ?>" target="_blank"><?php print $aProject; ?></a></b><br>
<?php print $aProject->getDescription(); ?></div>
<div style="border-top: 1px solid #DDD; background-color: #F5F5F5; padding-top: 3px; padding-bottom: 3px;">
<table cellpadding=0 cellspacing=0 style="width: 100%;">
<tr>
<td style="width: 130px; padding-left: 3px;"><b><?php echo __('Lead by: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
<td>
<?php

if ($aProject->getLeadBy() != null)
{
	?>
	<table cellpadding=0 cellspacing=0 width="100%">
	<?php
	
	print ($aProject->getLeadType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($aProject->getLeadBy()->getID()) : bugs_teamDropdown($aProject->getLeadBy()->getID());
	
	?>
	</table>
	<?php
}
else
{
	?><div style="color: #AAA; padding: 2px;"><?php echo __('None'); ?></div><?php
}

?>
</td>
<td style="width: auto;">&nbsp;</td>
<td style="width: 250px;">
<table cellpadding=0 cellspacing=0 width="100%">
<tr>
<td style="width: 20px;"><a class="image" href="config.php?module=core&amp;section=10&amp;p_id=<?php print $aProject->getID(); ?>&amp;edit_settings=true"><?php echo image_tag('cfg_icon_projectsettings.png', '', __('Edit settings'), __('Edit settings')); ?></a></td>
<td><a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $aProject->getID(); ?>&amp;edit_settings=true"><?php echo __('Information &amp; settings'); ?></a></td>
</tr>
</table>
</td>
</tr>
<tr>
<td style="width: 80px; padding-left: 3px;"><b><?php echo __('QA Manager: %user_or_team%', array('%user_or_team%' => '')); ?></b></td>
<td>
<?php

if ($aProject->getQA() != null)
{
	?>
	<table cellpadding=0 cellspacing=0 width="100%">
	<?php
	
	print ($aProject->getQAType() == BUGSidentifiableclass::TYPE_USER) ? bugs_userDropdown($aProject->getQA()->getID()) : bugs_teamDropdown($aProject->getQA()->getID());

	?>
	</table>
	<?php
}
else
{
	?><div style="color: #AAA; padding: 2px;"><?php echo __('None'); ?></div><?php
}

?>
</td>
<td style="width: auto;">&nbsp;</td>
<td>
<table cellpadding=0 cellspacing=0 width="100%">
<tr>
<td style="width: 20px;"><a class="image" href="config.php?module=core&amp;section=10&amp;p_id=<?php print $aProject->getID(); ?>&amp;edit_editions=true"><?php echo image_tag('cfg_icon_projecteditionsbuilds.png', '', __('Edit editions and builds'), __('Edit editions and builds')); ?></a></td>
<td><a href="config.php?module=core&amp;section=10&amp;p_id=<?php print $aProject->getID(); ?>&amp;edit_editions=true"><?php echo ($aProject->isBuildsEnabled()) ? __('Editions, builds and components') : __('Editions and components'); ?></a></td>
</tr>
</table>
</td>
</tr>
</table>
</div>
</td>
</tr>
<tr>
<td style="height: 5px; font-size: 1px;">&nbsp;</td>
</tr>