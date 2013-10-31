<td style="width: 255px; padding-top: 10px;" valign="top" class="project_information_sidebar">
	<div class="sidebar_links">
	<?php foreach ($config_sections as $config_info): ?>
		<?php foreach ($config_info as $section => $info): ?>
			<?php //if ($info['module'] != 'core' && !TBGContext::getModule($info['module'])->hasConfigSettings()) continue; ?>
			<?php $is_selected = (bool) (($selected_section == TBGSettings::CONFIGURATION_SECTION_MODULES && $section == TBGSettings::CONFIGURATION_SECTION_MODULES && $selected_subsection == $info['module']) || ($selected_section != TBGSettings::CONFIGURATION_SECTION_MODULES && $selected_section == $section)); ?>
			<?php if (is_array($info['route'])): ?>
				<?php $url = make_url($info['route'][0], $info['route'][1]); ?>
			<?php else: ?>
				<?php $url = make_url($info['route']); ?>
			<?php endif;?>
			<?php if ($is_selected) $tbg_response->addBreadcrumb($info['description'], $url, $breadcrumblinks); ?>
			<a href="<?php echo $url; ?>"<?php if ($is_selected): ?> class="selected"<?php endif; ?>>
				<?php if (isset($info['module']) && $info['module'] != 'core'): ?>
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