<div style="font-family: 'Trebuchet MS', 'Liberation Sans', 'Bitstream Vera Sans', 'Luxi Sans', Verdana, sans-serif; font-size: 11px; color: #646464;">
    Hi, <?php echo $user->getBuddyname(); ?>!<br>
    Someone registered the username <b><?php echo $user->getUsername(); ?></b> with The Bug Genie, here: <?php echo $module->generateURL('home'); ?>.<br>
    <br>
    Before you can use the new account, you need to confirm it, by visiting the following link:<br>
    <a href="<?php echo $link_to_activate; ?>"><?php echo $link_to_activate; ?></a><br>
    <br>
    Your password is:<br>
    <b><?php echo $password; ?></b><br>
    and you can log in with this password from the link specified above.<br>
    <br>
    (This email has been sent upon request to an email address specified by someone. If you did not register this username, or think you've received this email in error, please delete it. We are sorry for the inconvenience.)
</div>