<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
	Hi, <?php echo $user->getBuddyname(); ?>!<br>
	Someone registered the username '<?php echo $user->getUname(); ?>' with The Bug Genie, here: %thebuggenie_url%.<br>
	<br>
	Before you can use the new account, you need to confirm it, by visiting the following link:<br>
	<?php echo link_tag(make_url('activate', array('user' => $user->getUsername(), 'key' => $user->getPasswordMD5()))); ?><br>
	<br>
	Your password is:<br>
	<b><?php echo $password; ?></b><br>
	and you can log in with this password from the link specified above.<br>
	<br>
	(This email has been sent upon request to an email address specified by someone. If you did not register this username, or think you've received this email in error, please delete it. We are sorry for the inconvenience.)
</div>