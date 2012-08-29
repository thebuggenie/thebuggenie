<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
	<b>Your new password has been saved</b><br>
	Hi, <?php echo $user->getBuddyname(); ?>!<br>A request was made to reset your password for your user account at <a href="<?php echo $module->generateURL('home'); ?>"><?php echo $module->generateURL('home'); ?></a>.<br>
	The new password has been saved. Click this link: <a href="<?php echo $module->generateURL('login'); ?>"><?php echo $module->generateURL('login'); ?></a><br>
	and log in with the username <b><?php echo $user->getUsername(); ?></b> and the password <b><?php echo $password; ?></b>
</div>