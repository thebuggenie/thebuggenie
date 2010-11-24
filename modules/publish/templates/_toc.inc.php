<div class="rounded_box iceblue borderless toc">
	<div class="header"><a href="javascript:void(0);" onclick="$('publish_toc').toggle();"><?php echo __('Table of contents'); ?></a></div>
	<div class="faded_out"><?php echo __('Move your mouse here to toggle the table of contents'); ?></div>
	<div class="content rounded_box iceblue borderless" id="publish_toc">
		<?php foreach ($toc as $entry): ?>
			<div class="publish_toc_<?php echo $entry['level']; ?>"><a href="#<?php echo $entry['id']; ?>"><?php echo $entry['content']; ?></a></div>
		<?php endforeach; ?>
	</div>
</div>