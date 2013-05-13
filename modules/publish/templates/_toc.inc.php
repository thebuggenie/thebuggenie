<div class="toc">
	<div class="header"><a href="javascript:void(0);" onclick="$('publish_toc').toggle();"><?php echo __('Table of contents'); ?></a></div>
	<div class="content" id="publish_toc">
		<?php foreach ($toc as $entry): ?>
			<div class="publish_toc_<?php echo $entry['level']; ?>"><a href="#<?php echo $entry['id']; ?>"><?php echo $entry['content']; ?></a></div>
		<?php endforeach; ?>
	</div>
</div>