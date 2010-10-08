<?php

	$tbg_response->setTitle(__('Configuration center'));
	
?>
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