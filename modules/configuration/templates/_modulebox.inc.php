<div class="rounded_box<?php if ($module->isEnabled()): ?> iceblue<?php else: ?> lightgrey<?php endif; ?> borderless" style="margin: 5px 0px 5px 0px; width: 750px; vertical-align: middle; text-align: right; min-height: 40px;" id="module_<?php echo $module->getID(); ?>">
	<div style="float: left;">
		<div class="header"><?php echo __($module->getLongName()); ?><span class="module_shortname faded_out"> <?php echo $module->getVersion(); ?> (<?php echo $module->getName(); ?>) <?php if ($module->getType() == TBGModule::MODULE_AUTH): echo ' - '.__('Authentication module'); endif; ?></span></div>
		<div class="content"><?php echo __($module->getDescription()); ?></div>
	</div>
	<div style="text-align: right; font-size: 13px; font-weight: normal; padding-top: 3px;">
		<div>
			<?php if (!$module->isCore()): ?>
				<?php if ($module->isEnabled()): ?>
					<?php echo image_tag('icon_enabled.png', array('style' => 'margin-right: 3px;')); ?>
					<span style="float: right; font-weight: bold;"><?php echo __('Enabled'); ?></span><br>
				<?php else: ?>
					<?php echo image_tag('icon_disabled.png', array('style' => 'margin-right: 3px;')); ?>
					<span style="float: right; font-weight: bold;"><?php echo __('Disabled'); ?></span><br>
				<?php endif; ?>
			<?php else: ?>
				<span style="float: right; font-weight: normal;" class="faded_out"><?php echo __('Core module'); ?></span><br>
			<?php endif; ?>
			<?php if ($module->hasConfigSettings()): ?>
				<?php echo link_tag(make_url('configure_module', array('config_module' => $module->getName())), image_tag('action_configure_module.png', array('style' => 'margin-right: 5px;', 'title' => __('Configure module'))), array('class' => 'image')); ?>
			<?php endif; ?>
			<a href="javascript:void(0);" class="image" onclick="$('permissions_module_<?php echo($module->getID()); ?>').toggle();$('uninstall_module_<?php echo $module->getID(); ?>').hide();$('<?php if($module->isEnabled()): ?>disable<?php else: ?>enable<?php endif; ?>_module_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('action_edit_permissions.png', array('style' => 'margin-right: 5px;', 'title' => __('Edit permissions'))); ?></a>
			<?php if (!$module->isCore()): ?>
				<?php if ($module->getType() !== TBGModule::MODULE_AUTH): ?>
					<?php if ($module->isEnabled()): ?>
						<a href="javascript:void(0);" class="image" onclick="$('disable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('action_enable_module.png', array('style' => 'margin-right: 5px;', 'title' => __('Disable module'))); ?></a>
					<?php else: ?>
						<a href="javascript:void(0);" class="image" onclick="$('enable_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('action_disable_module.png', array('style' => 'margin-right: 5px;', 'title' => __('Enable module'))) ?></a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (TBGContext::getScope()->isDefault()): ?>
					<a href="javascript:void(0);" class="image" onclick="$('uninstall_module_<?php echo $module->getID(); ?>').toggle();$('permissions_module_<?php echo($module->getID()); ?>').hide();$('<?php if($module->isEnabled()): ?>disable<?php else: ?>enable<?php endif; ?>_module_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('action_uninstall_module.png', array('title' => __('Uninstall module'))); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php if (!$module->isCore()): ?>
		<?php if ($module->isEnabled()): ?>
			<div id="disable_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
				<h4><?php echo __('Really disable "%module_name%"?', array('%module_name%' => $module->getLongname())); ?></h4>
				<span class="question_header"><?php echo __('Disabling this module will prevent users from accessing it or any associated data.'); ?></span><br>
				<div style="text-align: right;" id="disable_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_disable_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('disable_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
			</div>
		<?php else: ?>
			<div id="enable_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
				<h4><?php echo __('Really enable "%module_name%"?', array('%module_name%' => $module->getLongname())); ?></h4>
				<span class="question_header"><?php echo __('Enabling this module will give users access to it and all associated data.'); ?></span><br>
				<div style="text-align: right;" id="enable_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_enable_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('enable_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
			</div>
		<?php endif; ?>
		<div id="uninstall_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px; padding: 0 10px 5px 10px; text-align: left;" class="rounded_box white shadowed">
			<h4><?php echo __('Really uninstall "%module_name%"?', array('%module_name%' => $module->getLongname())); ?></h4>
			<span class="question_header"><?php echo __('Uninstalling this module will permanently prevent users from accessing it or any associated data. If you just want to prevent access to the module temporarily, disable the module instead.'); ?></span><br>
			<div style="text-align: right;" id="uninstall_module_controls_<?php echo $module->getID(); ?>"><?php echo link_tag(make_url('configure_uninstall_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'xboxlink')); ?> :: <a href="javascript:void(0)" class="xboxlink" onclick="$('uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a></div>
		</div>
	<?php endif; ?>
	<div id="permissions_module_<?php echo($module->getID()); ?>" style="display: none; margin-top: 10px;" class="rounded_box white shadowed">
		<div class="permission_list" style="padding: 0 10px 5px 10px; text-align: left;">
			<div class="header_div" style="margin-top: 0;"><?php echo __('Available permissions'); ?></div>
			<ul id="module_permission_details_<?php echo $module->getName(); ?>">
				<?php include_template('configuration/permissionsblock', array('base_id' => 'module_' . $module->getName() . '_permissions', 'permissions_list' => $module->getAvailablePermissions(), 'mode' => 'module_permissions', 'target_id' => 0, 'module' => $module->getName(), 'access_level' => (($tbg_user->canSaveConfiguration(TBGSettings::CONFIGURATION_SECTION_PERMISSIONS) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ)))); ?>
			</ul>
		</div>
	</div>
</div>