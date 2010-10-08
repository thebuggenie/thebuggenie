<div class="rounded_box iceblue borderless" style="position: absolute; right: 5px; top: 35px; margin: 0; width: 300px;">
	<b class="xtop"><b class="xb1"></b><b class="xb2"></b><b class="xb3"></b><b class="xb4"></b></b>
	<div class="xboxcontent toc" style="padding: 5px; font-size: 13px;">
		<div class="header"><a href="javascript:void(0);" onclick="$('publish_toc').toggle();"><?php echo __('Table of contents'); ?></a></div>
		<div class="faded_out"><?php echo __('Move your mouse here to toggle the table of contents'); ?></div>
		<div class="content" id="publish_toc">
			<?php foreach ($toc as $entry): ?>
				<div class="publish_toc_<?php echo $entry['level']; ?>"><a href="#<?php echo $entry['id']; ?>"><?php echo $entry['content']; ?></a></div>
			<?php endforeach; ?>
		</div>
	</div>
	<b class="xbottom"><b class="xb4"></b><b class="xb3"></b><b class="xb2"></b><b class="xb1"></b></b>
</div>