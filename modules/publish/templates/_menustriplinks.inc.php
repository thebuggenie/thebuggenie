<li<?php if ($selected_tab == 'wiki'): ?> class="selected"<?php endif; ?>>
	<?php echo link_tag($url, image_tag('tab_publish.png', array(), false, 'publish') . TBGContext::getModule('publish')->getMenuTitle()); ?>
</li>