<?php

	$tbg_response->setTitle(__('Configure authentication'));
	
?>
<center>
<h1><?php echo __('Settings saved'); ?></h1>
<?php echo __('You must log out to continue. You will be automatically logged out if you visit a different page'); ?>
<p><?php echo link_tag(make_url('logout'), __('Logout')); ?></p>
</center>