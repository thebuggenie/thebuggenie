<div class="backdrop_box huge" id="reportissue_container">
	<div class="backdrop_detail_header"><?php echo __('Report an issue'); ?></div>
	<div id="backdrop_detail_content">
		<?php include_component('main/reportissue', compact('selected_project', 'issuetypes', 'errors')); ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Cancel'); ?></a>
	</div>
</div>