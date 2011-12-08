<td style="width: 255px; padding-top: 10px;" valign="top" class="project_information_sidebar">
	<?php /*<style>
		.config_selected:after { content: url('<?php echo make_url('home'); ?>iconsets/<?php echo TBGSettings::getIconsetName(); ?>/selected_highlight_right.png'); position: absolute; right: -16px; margin-top: -20px; }
	</style> */ ?>
	<div class="sidebar_links">
	<?php foreach ($config_sections as $section => $config_info): ?>
		<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
		<?php foreach ($config_info as $info): ?>
			<?php if ($info['module'] != 'core' && !TBGContext::getModule($info['module'])->hasConfigSettings()) continue; ?>
			<?php $is_selected = (bool) (($selected_section == TBGSettings::CONFIGURATION_SECTION_MODULES && $selected_subsection == $info['module'] && $section == TBGSettings::CONFIGURATION_SECTION_MODULES) || ($selected_section != TBGSettings::CONFIGURATION_SECTION_MODULES && $selected_section == $section)); ?>
			<?php if (is_array($info['route'])): ?>
				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
			<?php else: ?>
				<?php $url = make_url($info['route']); ?>
			<?php endif;?>
			<?php if ($is_selected) $tbg_response->addBreadcrumb($info['description'], $url, $breadcrumblinks); ?>
			<a href="<?php echo $url; ?>"<?php if ($is_selected): ?> class="selected"<?php endif; ?>>
				<?php if ($info['module'] != 'core'): ?>
					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; position: relative; margin-right: 5px;'), false, $info['module']); ?>
				<?php else: ?>
					<?php echo image_tag('cfg_icon_'.$info['icon'].'.png', array('style' => 'float: left; position: relative; margin-right: 5px;')); ?>
				<?php endif; ?>
				<?php echo $info['description']; ?>
			</a>
		<?php endforeach;?>
	<?php endforeach;?>
	</div>
</td>