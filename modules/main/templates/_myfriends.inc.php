<div class="left_menu_header"><?php echo __('Friends'); ?></div>
<?php if (count($friends) > 0): ?>
	<table cellpadding=0 cellspacing=0>
		<?php foreach ($friends as $friend): ?>
			<?php echo include_component('main/userdropdown', array('user' => $friend)); ?>
		<?php endforeach; ?>
	</table>
<?php else: ?>
	<div class="faded_medium"><?php echo __('You haven\'t marked anyone as a friend'); ?></div>
<?php endif; ?>