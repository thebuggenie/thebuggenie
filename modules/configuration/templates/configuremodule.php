<?php

	$bugs_response->setTitle(__('Configure modules'));

?>
<table style="table-layout: fixed; width: 100%" cellpadding=0 cellspacing=0>
<tr>
<?php

include_component('configleftmenu', array('selected_section' => 15));

?>
<td valign="top">
	<div style="width: 750px;" id="config_modules">
		<div class="configheader"><?php echo __('Configure module %module_name%', array('%module_name%' => $module->getName())); ?></div>
		<?php if ($module_error !== null): ?>
			<div class="rounded_box red_borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_error">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="vertical-align: middle; color: #FFF;">
					<div class="header"><?php echo $module_error; ?></div>
					<div class="content"><b><?php echo __('Error details:'); ?></b><br>
						<?php if ($module_error_details !== null): ?>
							<?php if (is_array($module_error_details)): ?>
								<?php foreach ($module_error_details as $detail): ?>
									<?php echo $detail; ?><br>
								<?php endforeach; ?>
							<?php else: ?>
								<?php echo $module_error_details; ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endif; ?>
		<?php if ($module_message !== null): ?>
			<div class="rounded_box green_borderless" style="margin: 5px 0px 5px 0px; width: 750px;" id="module_message">
				<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
				<div class="xboxcontent" style="vertical-align: middle;">
					<div class="header"><?php echo $module_message; ?></div>
				</div>
				<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
			</div>
		<?php endif; ?>
		<?php include_component($module->getName() . '/settings', array('access_level' => $access_level, 'module' => $module)); ?>
	</div>
</td>
</tr>
</table>