<td style="width: 255px;" valign="top">
<div class="configheader" style="padding-left: 5px; width: auto;"><?php echo __('Configuration sections'); ?></div>
<ul class="config_buttons">
<?php foreach ($config_sections as $section => $config_info): ?>
	<?php if (array_key_exists('icon', $config_info)) $config_info = array($config_info); ?>
	<?php foreach ($config_info as $info): ?>
		<?php if ($info['module'] != 'core' && !TBGContext::getModule($info['module'])->hasConfigSettings()) continue; ?>
		<li<?php if (($selected_section == 15 && $selected_subsection == $info['module'] && $section == 15) || ($selected_section != 15 && $selected_section == $section)): ?> class="config_selected"<?php endif; ?>>
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