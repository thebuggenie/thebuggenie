<?php $tbg_response->setTitle(__('Configure modules')); ?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
	<tr>
		<?php include_component('leftmenu', array('selected_section' => TBGSettings::CONFIGURATION_SECTION_MODULES)); ?>
		<td valign="top">
			<div style="width: 750px;" id="config_modules">
				<div class="config_header"><?php echo __('Configure modules'); ?></div>
				<div class="header"><?php echo __('Installed modules'); ?></div>
				<div class="content"><?php echo __('This is a list of all modules that are installed on this system'); ?></div>
				<?php if ($module_error !== null): ?>
					<div class="rounded_box red borderless" style="margin: 5px 0px 5px 0px; color: #FFF; width: 750px;" id="module_error">
						<div class="header"><?php echo $module_error; ?></div>
					</div>
				<?php endif; ?>
				<?php if ($module_message !== null): ?>
					<div class="rounded_box green borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_message">
						<div class="header"><?php echo $module_message; ?></div>
					</div>
				<?php endif; ?>
				<?php if (count($outdated_modules) > 0): ?>
					<div class="rounded_box yellow borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_message">
						<div class="header"><?php echo __('You have %count% outdated modules. They have been disabled until you upgrade them, you can upgrade them on this page.', array('%count%' => count($outdated_modules))); ?></div>
					</div>
				<?php endif; ?>
				<?php foreach ($modules as $module_key => $module): ?>
					<?php if (!$module->isOutdated()): ?>
						<?php include_template('modulebox', array('module' => $module)); ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php if (TBGContext::getScope()->isDefault()): ?>
					<div class="header" style="margin-top: 15px;"><?php echo __('Uninstalled modules'); ?></div>
					<?php if (count($uninstalled_modules) == 0): ?>
						<div class="faded_out" style="margin-top: 5px;"><?php echo __('There are no uninstalled modules available'); ?></div>
					<?php else: ?>
						<form action="<?php echo make_url('configure_install_module'); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
							<div class="rounded_box mediumgrey borderless" style="margin: 5px 0px 5px 0px; text-align: right; width: 750px;">
								<div class="content">
									<?php echo __('This is a list of modules available, but not installed on this system.'); ?>
									<?php echo __('To install a module, select it from the dropdown list and press the %install%-button', array('%install%' => '<b>' . __('Install') . '</b>')); ?>
								</div>
								<select name="module_key" style="margin-top: 5px; width: 100%;">
								<?php foreach ($uninstalled_modules as $module_key => $description): ?>
									<option value="<?php echo $module_key; ?>"><?php echo $description; ?></option>
								<?php endforeach; ?>
								</select><br>
								<input type="submit" value="<?php echo __('Install'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
							</div>
						</form>
					<?php endif; ?>
					<div class="header" style="margin-top: 15px;"><?php echo __('Outdated modules'); ?></div>
					<?php if (count($outdated_modules) == 0): ?>
						<div class="faded_out" style="margin-top: 5px;"><?php echo __('There are no outdated modules to upgrade'); ?></div>
					<?php else: ?>
						<form action="<?php echo make_url('configure_update_module'); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>">
							<div class="rounded_box mediumgrey borderless" style="margin: 5px 0px 5px 0px; text-align: right; width: 750px;">
								<div class="content">
									<?php echo __('This is a list of modules available and installed, but have been disabled until you upgrade them.'); ?>
									<?php echo __('To upgrade a module, select it from the dropdown list and press the %upgrade%-button. This will likely involve changes to the database, so you may want to back up your database first', array('%upgrade%' => '<b>' . __('Upgrade') . '</b>')); ?>
								</div>
								<select name="module_key" style="margin-top: 5px; width: 100%;">
								<?php foreach ($outdated_modules as $module): ?>
									<option value="<?php echo $module->getName(); ?>"><?php echo $module->getLongName().__(' (version %ver%)', array('%ver%' => $module->getVersion())); ?></option>
								<?php endforeach; ?>
								</select><br>
								<input type="submit" value="<?php echo __('Upgrade'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
							</div>
						</form>
					<?php endif; ?>
					<div class="header" style="margin-top: 15px;"><?php echo __('Add new module'); ?></div>
						<form action="<?php echo make_url('configure_upload_module'); ?>" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" enctype="multipart/form-data">
							<div class="rounded_box mediumgrey borderless" style="margin: 5px 0px 5px 0px; text-align: right; width: 750px;">
								<div class="content">
									<?php echo __('To add a new module in The Bug Genie, download it then select it from the %browse%-button and press the %upload%-button', array('%upload%' => '<b>' . __('Upload') . '</b>', '%browse%' => '<b>' . __('Browse') . '</b>')); ?>
								</div>
								<input type="file" name="archive" size="100%" style="margin-top: 5px; width: 100%; background-color: #fff;"><br>
								<input type="submit" value="<?php echo __('Upload'); ?>" style="font-weight: bold; margin: 5px 0 10px 0;">
							</div>
						</form>
					</div>
				<?php endif; ?>
			</div>
		</td>
	</tr>
</table>