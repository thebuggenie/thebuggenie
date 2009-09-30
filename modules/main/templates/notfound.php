<div class="rounded_box red_borderless" id="viewissue_nonexisting">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent" style="vertical-align: middle; padding: 5px; color: #222; font-weight: bold; font-size: 13px; text-align: center;">
		<div class="viewissue_info_header"><?php echo __("404 - Not Found"); ?></div>
		<div class="viewissue_info_content">
			<?php if (isset($message) && $message): ?>
				<?php echo $message; ?>
			<?php else: ?>
				<?php echo __("This location doesn't exist, has been deleted or you don't have permission to see it"); ?>
			<?php endif; ?>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>