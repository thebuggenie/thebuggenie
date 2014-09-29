<h3>Forgot your password?</h3>
<br>
<h4>Hi, <?php echo $user->getBuddyname(); ?></h4>
<p>
    A request was made to reset your password for your user account at <?php echo link_tag($module->generateUrl('home')); ?><br>
    To change your password, click the following one-time password reset link: <?php echo link_tag($module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))); ?><br>
</p>
<br>
<div style="color: #888;">
    You were sent this notification email because someone requested a password reset for your account. If you did not authorize this, ignore this email.<br>
    To change when and how often we send these emails, update your account settings: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
</div>
