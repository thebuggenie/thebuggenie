<?php

	if (($access_level != "full" && $access_level != "read") || BUGScontext::getRequest()->getParameter('access_level'))
	{
		bugs_msgbox(false, "", __('You do not have access to this section'));
	}
	else
	{
		if ($access_level == 'full')
		{
			?><script type="text/javascript" src="<?php echo BUGScontext::getTBGPath(); ?>js/config/modules_ajax.js"></script><?php
		}
		require_once BUGScontext::getIncludePath() . 'include/config/modules_logic.inc.php';
		
		?>
		<table style="width: 100%" cellpadding=0 cellspacing=0>
			<tr>
			<td style="padding-right: 10px;">
				<table class="configstrip" cellpadding=0 cellspacing=0>
					<tr>
						<td class="cleft"><b><?php echo __('Configure modules'); ?></b></td>
						<td class="cright">&nbsp;</td>
					</tr>
					<tr>
						<td colspan=2 class="cdesc">
						<?php echo __('From here you can manage BUGS 2 modules.'); ?>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<div id="message_box"> </div>
		<?php 
		
		if (isset($module_installed) && $module_installed)
		{
			echo bugs_successStrip(__('Module installed'), __('The module was installed successfully. You can configure the new module from the configuration menu to the left.'));
		}
		if (isset($module_removed) && $module_removed)
		{
			echo bugs_successStrip(__('Module removed'), __('The module was removed successfully.'));
		}
		
		?>
		<table style="width: 740px;" cellpadding=0 cellspacing=0>
		<tr>
		<?php if (BUGScontext::getRequest()->getParameter('subsection') == 2): ?>
			<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_modules.png', '', __('Manage modules'), __('Manage modules')); ?></td>
			<td style="border-bottom: 1px solid #DDD; padding: 4px; width: 160px;"><a href="config.php?module=core&amp;section=15"><?php echo __('Manage modules'); ?></a></td>
			<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; width: 20px; padding: 4px; text-align: center;"><?php echo image_tag('cfg_icon_modules.png', '', __('Manage module sections'), __('Manage module sections')); ?></td>
			<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; width: 160px; padding: 4px;"><b><?php echo __('Manage module sections'); ?></b></td>
		<?php else: ?>
			<td style="border-left: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 20px; text-align: center;"><?php echo image_tag('cfg_icon_modules.png', '', __('Manage modules'), __('Manage modules')); ?></td>
			<td style="border-right: 1px solid #DDD; border-top: 1px solid #DDD; padding: 4px; width: 160px;"><b><?php echo __('Manage modules'); ?></b></td>
			<td style="border-bottom: 1px solid #DDD; width: 20px; padding: 4px; text-align: center;"><?php echo image_tag('cfg_icon_modules.png', '', __('Manage module sections'), __('Manage module sections')); ?></td>
			<td style="border-bottom: 1px solid #DDD; width: 160px; padding: 4px;"><a href="config.php?module=core&amp;section=15&amp;subsection=2"><?php echo __('Manage module sections'); ?></a></td>
		<?php endif; ?>
		<td style="border-bottom: 1px solid #DDD; width: auto;">&nbsp;</td>
		</tr>
		</table>
		<?php

		if (!BUGScontext::getRequest()->getParameter('subsection'))
		{
			?>
			<div style="width: 732px; padding: 4px; margin-top: 10px; background-color: #F1F1F1;"><b><?php echo __('Installed modules'); ?></b></div>
			<?php
	
			foreach (BUGScontext::getModules() as $module)
			{
				?>
				<div id="modulestrip_<?php echo $module->getName(); ?>"><?php require BUGScontext::getIncludePath() . 'include/config/modulestrip.inc.php'; ?></div>
				<?php
			}
			
			?>
			<div style="width: 732px; padding: 4px; margin-top: 10px; background-color: #F1F1F1;"><b><?php echo __('Add a new module'); ?></b></div>
			<div style="width: 732px; padding: 4px;"><p><?php echo __('You can extend BUGS by adding modules. Modules can add new functionality, or improve functionality that already exists in BUGS.'); ?>
			<?php echo __('To add a new module to BUGS, unpack the module archive into the modules/ directory, then select the module to install in the drop-down menu below.'); ?><br>
			<br>
			<?php echo __('If you are looking for modules to add, you can find many new modules at %link_to_modules%. Here you can also find how to add your own modules for others to download.', array('%link_to_modules%' => '<a href="http://modules.thebuggenie.net"><b>modules.thebuggenie.net</b></a>')); ?><br>
			<br>
			<i>(<?php echo __('If you are installing a module developed by you, make sure the module follows the documentation available at %link_to_documentation%', array('%link_to_documentation%' => '<a href="http://doc.thebuggenie.net"><b>doc.thebuggenie.net</b></a>')); ?>)</i></p>
			<?php
			
			$uninstalled_modules = array();
			$cp_handle = opendir(BUGScontext::getIncludePath() . 'modules');
			while ($classfile = readdir($cp_handle))
			{
				if (strstr($classfile, '.') == '' && !BUGScontext::isModuleLoaded($classfile)) 
				{ 
					$uninstalled_modules[$classfile] = file_get_contents(BUGScontext::getIncludePath() . 'modules/' . $classfile . '/module');
				}
			}
			
			if (count($uninstalled_modules) > 0)
			{
				?>
				<form accept-charset="<?php echo BUGScontext::getI18n()->getCharset(); ?>" action="config.php" method="POST">
				<input type="hidden" name="module" value="core">
				<input type="hidden" name="section" value="15"> 
				<table style="width: 740px;" cellpadding=0 cellspacing=0>
					<tr>
						<td style="width: 125px; padding: 5px;"><b><?php echo __('Install module'); ?></b></td>
						<td style="width: auto;">
						<select name="install_module" style="width: 100%;">
						<?php 
						
						foreach ($uninstalled_modules as $module_name => $module_desc)
						{
							echo '<option value="' . $module_name . '">' . $module_desc . '</option>';
						}
						
						?>
						</select>
						</td>
						<td style="width: 50px; padding: 5px;"><input type="submit" value="<?php echo __('Install'); ?>" style="width: 100%;"></td>
					</tr>
				</table>
				</form>
				<?php
			}
			else
			{
				echo '<br><p style="color: #AAA;">' . __('There are no new modules to install') . '</p>';
			}
			
			?></div><?php
		}
		elseif (BUGScontext::getRequest()->getParameter('subsection') == 2)
		{
	
			foreach (BUGScontext::getModules() as $module)
			{
				?>
				<div id="modulesections_<?php echo $module->getName(); ?>"><?php require BUGScontext::getIncludePath() . 'include/config/modulesections.inc.php'; ?></div>
				<?php
			}
			
		}
	}
	
?>
