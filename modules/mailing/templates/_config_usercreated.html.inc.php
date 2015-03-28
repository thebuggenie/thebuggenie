<?php if ($user instanceof \thebuggenie\core\entities\User): ?>
    <h3>
        You have been registered by an administrator<br>
    </h3>
    A username and account has been registered with this email address for The Bug Genie at the following address:<br>
    <?php echo link_tag($module->generateURL('home')); ?><br>
    <br>
    You can log in with the username <strong><?php echo $user->getUsername(); ?></strong>, and the password <strong><?php echo $password; ?></strong><br>
    <br>
    This is a temporary password, you should change it immediately after logging in for the first time.
<?php endif; ?>