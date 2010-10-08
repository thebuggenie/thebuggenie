<div class="header"><?php echo __('Friends'); ?></div>
<?php if (count($friends) > 0): ?>
	<?php foreach ($friends as $friend): ?>
		<div><?php echo include_component('main/userdropdown', array('user' => $friend)); ?></div>
	<?php endforeach; ?>
<?php else: ?>
	<div class="faded_out" style="padding: 0 0 0 5px;"><?php echo __('You haven\'t marked anyone as a friend'); ?></div>
<?php endif; ?>