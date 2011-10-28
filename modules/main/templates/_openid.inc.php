<div class="backdrop_box large" id="login_popup">
	<div class="backdrop_detail_header"><?php echo __('Add external login'); ?></div>
	<div id="backdrop_detail_content" class="rounded_top login_content">
		<?php include_template('main/openidbuttons'); ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close'); ?></a>
	</div>
</div>
