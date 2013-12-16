* Forgot your password? *
Hi, <?php echo $user->getBuddyname()."\n"; ?>

A request was made to reset your password for your user account at <?php echo $module->generateUrl('home')."\n"; ?>
To change your password, click the following link: <?php echo "\n".$module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))."\n"; ?>

---
You were sent this notification email because someone requested a password reset for your account. If you did not authorize this, ignore this email.
To change when and how often we send these emails, update your account settings: <?php echo $module->generateURL('account'), $module->generateURL('account'); ?>