<div style="text-align: center;">
	<div class="rounded_box white borderless" style="width: 500px; margin: 10px auto 10px auto; padding: 3px 5px 3px 5px; font-size: 13px; text-align: center;">
		<?php if ($currentpage > 1): ?>
			<?php if ($currentpage > 2): ?>
			<button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', 0);">&larrb; <?php echo __('First page'); ?></button>
			<?php endif; ?>
			<button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($currentpage - 2) * $ipp; ?>);">&laquo; <?php echo __('Previous page'); ?></button>
		<?php endif; ?>
		<?php for ($cc = 1; $cc <= $pagecount; $cc++): ?>
			<?php if ($cc == $currentpage): ?>
				<button class="button button-silver disabled"><?php echo $cc; ?></button>
			<?php else: ?>
				<button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>);"><?php echo $cc; ?></button>
			<?php endif; ?>
		<?php endfor; ?>
		<?php if ($currentpage < $pagecount): ?>
			<button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $currentpage * $ipp; ?>);"><?php echo __('Next page'); ?> &raquo;</button>
			<?php if ($currentpage < $pagecount - 1): ?>
				<button class="button button-silver" onclick="TBG.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($pagecount - 1) * $ipp; ?>);"><?php echo __('Last page'); ?> &rarrb;</button>
			<?php endif; ?>
		<?php endif; ?>
		<?php echo image_tag('spinning_20.gif', array('id' => 'paging_spinning', 'style' => 'display: none; margin: 0 0 -6px 5px;')); ?>
	</div>
</div>