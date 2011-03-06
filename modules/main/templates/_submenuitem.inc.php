<li class="<?php echo (array_key_exists('class', $item)) ? $item['class'] : 'breadcrumb'; ?>">
	<?php if (array_key_exists('subitems', $item) && is_array($item['subitems'])): ?>
		<div>
			<?php foreach ($item['subitems'] as $link => $subitem): ?>
				<a href="<?php echo $link; ?>"<?php if ($subitem == $item['title']): ?> class="selected"<?php endif; ?>>Menu item 1</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ($item['url']): ?>
		<?php echo link_tag($item['url'], $item['title']); ?>
	<?php else: ?>
		<?php echo $item['title']; ?>
	<?php endif; ?>
	<?php if ($show_popout): ?>
		<?php echo javascript_link_tag(image_tag('tabmenu_dropdown_popout.png', array('class' => 'dropdown_activator')), array('onclick' => "$(this).up().next().toggleClassName('popped_out');")); ?>
	<?php endif; ?>
</li>