<div style="text-align: center;">
	<div class="rounded_box lightgrey borderless" style="width: 500px; margin: 10px auto 10px auto; padding: 3px 5px 3px 5px; font-size: 13px; text-align: center;">
		<?php if ($currentpage > 1): ?>
			<?php if ($currentpage > 2): ?>
				<a href="javascript:void(0);" class="image" onclick="searchPage('<?php echo $route; ?>', 0);"><?php echo image_tag('search_go_first.png'); ?></a>
			<?php endif; ?>
			<a href="javascript:void(0);" class="image" onclick="searchPage('<?php echo $route; ?>', <?php echo ($currentpage - 2) * $ipp; ?>);"><?php echo image_tag('search_go_prev.png'); ?></a>
		<?php endif; ?>
		<?php for ($cc = 1; $cc <= $pagecount; $cc++): ?>
			<?php if ($cc == $currentpage): ?>
				<b><?php echo $cc; ?></b>
			<?php else: ?>
				<a href="javascript:void(0);" onclick="searchPage('<?php echo $route; ?>', <?php echo $ipp * ($cc - 1); ?>);"><?php echo $cc; ?></a>
			<?php endif; ?>
		<?php endfor; ?>
		<?php if ($currentpage < $pagecount): ?>
			<a href="javascript:void(0);" class="image" onclick="searchPage('<?php echo $route; ?>', <?php echo $currentpage * $ipp; ?>);"><?php echo image_tag('search_go_next.png'); ?></a>
			<?php if ($currentpage < $pagecount - 1): ?>
				<a href="javascript:void(0);" class="image" onclick="searchPage('<?php echo $route; ?>', <?php echo ($pagecount - 1) * $ipp; ?>);"><?php echo image_tag('search_go_last.png'); ?></a>
			<?php endif; ?>
		<?php endif; ?>
		<?php echo image_tag('spinning_20.gif', array('id' => 'paging_spinning', 'style' => 'display: none;')); ?>
	</div>
</div>