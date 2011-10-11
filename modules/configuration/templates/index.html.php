<?php

	$tbg_response->setTitle(__('Configuration center'));
	
?>
<div class="update_div rounded_box lightgrey">
	<div class="header"><?php echo __('Check for the latest updates'); ?></div>
	<?php echo __('Checking for updates and installing the latest releases ensures you have the latest improvements, fixes and features for The Bug Genie.'); ?>
	<br>
	<?php echo __('You currently have version %thebuggenie_version% of The Bug Genie.', array('%thebuggenie_version%' => TBGSettings::getVersion(false))); ?>
	<div id="update_button"><a href="javascript:void(0);" onClick="TBG.Config.updateCheck('<?php echo make_url('configure_update_check'); ?>');"><?php echo __('Check for updates now'); ?></a></div>
	<div id="update_spinner" style="display: none;"><?php echo image_tag('spinning_32.gif'); ?></div>
</div>
<?php if (count($outdated_modules) > 0): ?>
	<div class="update_div rounded_box yellow" style="margin-top: 20px;">
		<div class="header"><?php echo __('You have %count% outdated modules. They have been disabled until you upgrade them, you can upgrade them from Module settings.', array('%count%' => count($outdated_modules))); ?></div>
	</div>
<?php endif; ?>
<?php if (get_magic_quotes_gpc()): ?>
	<div class="update_div rounded_box red" style="margin-top: 20px;">
		<div class="header"><?php echo __('You appear to have Magic Quotes enabled. This will cause problems with The Bug Genie, and so it is highly recommended that you disable it in your PHP configuration. Please note that this feature has been deprecated by the PHP developers, and so leaving it enabled is not advised. %furtherdetails%', array('%furtherdetails%' => '<a href="http://www.php.net/manual/en/security.magicquotes.php">'.__('Further details').'</a>')); ?></div>
	</div>
<?php endif; ?>
<table style="table-layout: fixed; width: 1000px; margin: 10px 0 0 10px;" cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top">
			<div style="margin-left: 5px;">
				<div class="config_header" style="width: 972px;"><?php echo __('General configuration'); ?></div>
				<ul class="config_badges">
				<?php foreach ($general_config_sections as $section => $config_info): ?>
					<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
					<?php foreach ($config_info as $info): ?>
						<li class="rounded_box">
						<?php if (is_array($info['route'])): ?>
							<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
						<?php else: ?>
							<?php $url = make_url($info['route']); ?>
						<?php endif; ?>
							<a href="<?php echo $url; ?>">
								<b>
									<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
									<?php echo $info['description']; ?>
								</b>
								<span><?php echo $info['details']; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
				<?php foreach ($data_config_sections as $section => $config_info): ?>
					<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
					<?php foreach ($config_info as $info): ?>
						<li class="rounded_box">
						<?php if (is_array($info['route'])): ?>
							<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
						<?php else: ?>
							<?php $url = make_url($info['route']); ?>
						<?php endif; ?>
							<a href="<?php echo $url; ?>">
								<b>
									<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
									<?php echo $info['description']; ?>
								</b>
								<span><?php echo $info['details']; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</ul>
				<div class="config_header" style="width: 972px; clear: both;"><?php echo __('Modules / addons'); ?></div>
				<ul class="config_badges">
				<?php foreach ($module_config_sections as $section => $config_info): ?>
					<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
					<?php foreach ($config_info as $info): ?>
						<?php if ($info['module'] != 'core' && !TBGContext::getModule($info['module'])->hasConfigSettings()) continue; ?>
						<li class="rounded_box">
						<?php if (is_array($info['route'])): ?>
							<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
						<?php else: ?>
							<?php $url = make_url($info['route']); ?>
						<?php endif; ?>
							<a href="<?php echo $url; ?>">
								<b>
									<?php if ($info['module'] != 'core'): ?>
										<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;'), false, $info['module']); ?>
									<?php else: ?>
										<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
									<?php endif; ?>
									<?php echo $info['description']; ?>
								</b>
								<span><?php echo $info['details']; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</ul>
				<br style="clear: both;">
			</div>
		</td>
	</tr>
</table>