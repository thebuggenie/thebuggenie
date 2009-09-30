<?php 

	$config_sections = array();
	$config_sections[12] = array('route' => 'configure_settings', 'description' => __('Settings'), 'icon' => 'general', 'module' => 'core');
	if (BUGScontext::getUser()->getScope()->getID() == BUGSsettings::getDefaultScope()->getID())
	{
		$config_sections[14] = array('route' => 'configure_scopes', 'description' => __('Scopes'), 'icon' => 'scopes', 'module' => 'core');
		$config_sections[16] = array('route' => 'configure_import', 'description' => __('Import data'), 'icon' => 'import', 'module' => 'core');
	}
	$config_sections[3] = array('route' => 'configure_files', 'description' => __('Uploads &amp; attachments'), 'icon' => 'files', 'module' => 'core');
	
	$config_sections[10] = array('route' => 'configure_projects', 'description' => __('Projects'), 'icon' => 'projects', 'module' => 'core');
	#$config_sections[9] = array('route' => 'configure_milestones', 'description' => __('Milestones'), 'icon' => 'builds');
	$config_sections[4] = array('icon' => 'resolutiontypes', 'description' => __('Data types'), 'route' => 'configure_resolution_types', 'module' => 'core');
	/*$config_sections[4][] = array('icon' => 'issuetypes', 'description' => __('Issue types'), 'route' => 'configure_issue_types');
	$config_sections[4][] = array('icon' => 'resolutiontypes', 'description' => __('Resolution types'), 'route' => 'configure_resolution_types');
	$config_sections[4][] = array('icon' => 'priorities', 'description' => __('Priority levels'), 'route' => 'configure_priority_levels');
	$config_sections[4][] = array('icon' => 'categories', 'description' => __('Categories'), 'route' => 'configure_categories');
	$config_sections[4][] = array('icon' => 'repro', 'description' => __('Reproduction levels'), 'route' => 'configure_reproduction_levels');
	$config_sections[4][] = array('icon' => 'statustypes', 'description' => __('Status types'), 'route' => 'configure_status_types');
	$config_sections[4][] = array('icon' => 'severities', 'description' => __('Severity levels'), 'route' => 'configure_severity_levels');
	$config_sections[4][] = array('icon' => 'users', 'description' => __('User states'), 'route' => 'configure_user_states');*/
	$config_sections[2] = array('route' => 'configure_users', 'description' => __('Users, teams &amp; groups'), 'icon' => 'users', 'module' => 'core');
	#$config_sections[1] = array('route' => 'configure_teams_groups', 'description' => __('Teams &amp; groups'), 'icon' => 'projects');
	$config_sections[15][] = array('route' => 'configure_modules', 'description' => __('Modules'), 'icon' => 'modules', 'module' => 'core');
	foreach (BUGScontext::getModules() as $module)
	{
		if ($module->hasAccess() && $module->isVisibleInConfig())
		{
			$config_sections[15][] = array('route' => array('configure_module', array('config_module' => $module->getName())), 'description' => $module->getConfigTitle(), 'icon' => $module->getName(), 'module' => $module->getName());
		}
	}
	
?>
<td style="width: 255px;" valign="top">
<div class="configheader" style="padding-left: 5px; width: auto;"><?php echo __('Configuration sections'); ?></div>
<ul class="config_buttons">
<?php foreach ($config_sections as $section => $config_info): ?>
	<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
	<?php foreach ($config_info as $info): ?>
		<li<?php if ($selected_section == $section): ?> class="config_selected"<?php endif; ?>>
  			<?php if (is_array($info['route'])): ?>
  				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
  			<?php else: ?>
  				<?php $url = make_url($info['route']); ?>
  			<?php endif;?>
  			<a href="<?php echo $url; ?>">
  				<?php if ($info['module'] != 'core'): ?>
					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;'), false, $info['module']); ?>
				<?php else: ?>
					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; margin-right: 5px;')); ?>
				<?php endif; ?>
				<?php echo $info['description']; ?>
			</a>
		</li>
	<?php endforeach;?>
<?php endforeach;?>
</ul>