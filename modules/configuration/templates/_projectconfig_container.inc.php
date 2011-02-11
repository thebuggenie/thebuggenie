<div class="rounded_box white borderless shadowed backdrop_box large" id="project_config_popup_main_container">
	<div class="backdrop_detail_header">
		<?php echo __('Configure project'); ?>
	</div>
	<div class="backdrop_detail_content" id="backdrop_detail_content">
		<?php if (isset($edition)): ?>
			<?php include_component('configuration/projectedition', array('edition' => $edition, 'selected_section' => $selected_section)); ?>
		<?php else: ?>
			<?php include_component('configuration/projectconfig', array('project' => $project, 'section' => $section)); ?>
		<?php endif; ?>
	</div>
	<div class="backdrop_detail_content" id="backdrop_detail_indicator" style="text-align: center; padding: 50px; display: none;">
		<?php echo image_tag('spinning_32.gif'); ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>