<?php

	if (!$module instanceof BUGSmodule)
	{
		die();
	}

?>
<table style="width: 740px;" cellpadding=0 cellspacing=0>
<tr>
<td style="padding: 3px; width: 200px;"><b><a href="config.php?module=<?php echo $module->getName(); ?>"><?php echo $module->getLongname(); ?></a></b></td>
<td style="padding: 3px; width: auto;"><?php echo ($module->getDescription() != '') ? $module->getDescription() : __('No description available'); ?></td>
</tr>
</table>
<table style="margin-bottom: 5px; border-bottom: 1px solid #DDD; width: 740px; background-color: #F5F5F5;" cellpadding=0 cellspacing=0>
<tr>
<td style="width: 200px; padding: 3px;"><a href="javascript:void(0);" onclick="Effect.Appear('uninstall_<?php echo $module->getID(); ?>');" style="font-size: 9px;"><?php echo __('Uninstall'); ?></a><br>
<div id="uninstall_<?php echo $module->getID(); ?>" style="padding: 4px; width: 300px; display: none; position: absolute; border: 1px solid #DDD; background-color: #FFF;">
<b><?php echo __('Please confirm'); ?></b><br>
<?php echo __('Are you sure you want to remove this module?'); ?>
	<div style="text-align: right;">
	<a href="config.php?module=core&amp;section=15&amp;uninstall_module=<?php echo $module->getName(); ?>"><?php echo __('Yes'); ?></a>&nbsp;|
	&nbsp;<a href="javascript:void(0);" onclick="Effect.Fade('uninstall_<?php echo $module->getID(); ?>');"><b><?php echo __('No'); ?></b></a>
	</div>
</div>
</td>
<td style="width: auto; padding: 3px; text-align: left;"><?php echo ($module->isEnabled()) ? __('Enabled') : __('Disabled'); ?>
<?php if ($access_level == 'full'): ?>
	&nbsp;<a href="javascript:void(0);" onclick="set<?php echo ($module->isEnabled()) ? 'Disabled' : 'Enabled'; ?>('<?php echo $module->getName(); ?>');" style="font-size: 9px;"><?php echo (!$module->isEnabled()) ? __('Enable') : __('Disable'); ?></a>
<?php endif; ?>
</td>
<td style="width: 180px; <?php echo (!$module->isEnabled()) ? 'color: #AAA;' : ''; ?>text-align: left; padding: 3px;"><?php echo (!$module->isVisibleInMenu()) ? __('Not visible in the menu bar') : __('Visible in the menu bar'); ?>
<?php if ($access_level == 'full'): ?>
	&nbsp;<a href="javascript:void(0);" onclick="<?php echo ($module->isVisibleInMenu()) ? 'hideFrom' : 'showIn'; ?>Menu('<?php echo $module->getName(); ?>');" style="font-size: 9px;"><?php echo ($module->isVisibleInMenu() == false) ? __('Show') : __('Hide'); ?></a>
<?php endif; ?>
</td>
<td style="width: 180px; <?php echo (!$module->isEnabled()) ? 'color: #AAA;' : ''; ?>text-align: left; padding: 3px;"><?php echo (!$module->isVisibleInUsermenu()) ? __('Not visible in "My account"') : __('Visible in "My account"'); ?>
<?php if ($access_level == 'full'): ?>
	&nbsp;<a href="javascript:void(0);" onclick="<?php echo ($module->isVisibleInUserMenu()) ? 'hideFrom' : 'showIn'; ?>UserMenu('<?php echo $module->getName(); ?>');" style="font-size: 9px;"><?php echo ($module->isVisibleInUserMenu() == false) ? __('Show') : __('Hide'); ?></a>
<?php endif; ?>
</td>
</tr>
</table>