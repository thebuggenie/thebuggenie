Hi, <?php echo $user->getBuddyname(); ?>!
Someone registered the username '<?php echo $user->getUsername(); ?>' with The Bug Genie, here: <?php echo $module->generateURL('home'); ?>.

Before you can use the new account, you need to confirm it, by visiting the following link:
<?php echo $link_to_activate; ?>

* Your password is: <?php echo $password; ?>
and you can log in with this password from the link specified above.

(This email has been sent upon request to an email address specified by someone. If you did not register this username, or think you've received this email in error, please delete it. We are sorry for the inconvenience.)