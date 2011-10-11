<div class="backdrop_box huge" id="reportissue_container">
	<div class="backdrop_detail_header"><?php echo __('Report an issue'); ?></div>
	<div id="backdrop_detail_content">
		<?php include_component('main/reportissue', compact('selected_project', 'issue', 'issuetypes', 'selected_issuetype', 'errors', 'permission_errors')); ?>
	</div>
	<div class="backdrop_detail_footer">
		<a href="javascript:void(0);" onclick="TBG.Main.Helpers.Backdrop.reset();"><?php echo __('Close this popup'); ?></a>
	</div>
</div>