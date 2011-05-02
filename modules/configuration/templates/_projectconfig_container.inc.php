<div class="rounded_box white borderless shadowed backdrop_box large" id="project_config_popup_main_container">
	<div class="backdrop_detail_header">
		<?php echo __('Configure project'); ?>
	</div>
	<div id="backdrop_detail_content">
		<?php if (isset($edition)): ?>
			<?php include_component('configuration/projectedition', array('edition' => $edition, 'selected_section' => $selected_section)); ?>
		<?php else: ?>
			<?php include_component('configuration/projectconfig', array('project' => $project, 'section' => $section)); ?>
		<?php endif; ?>
	</div>
	<div class="backdrop_detail_footer">
		<?php echo image_tag('spinning_32.gif', array('id' => 'backdrop_detail_indicator', 'style' => 'display: none; float: right; margin-left: 5px;')); ?>
		<a href="javascript:void(0);" onclick="resetFadedBackdrop();"><?php echo __('Close'); ?></a>
	</div>
</div>