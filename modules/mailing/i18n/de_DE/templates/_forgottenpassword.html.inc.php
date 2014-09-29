<h3>Passwort vergessen?</h3>
<br>
<h4>Hi, <?php echo $user->getBuddyname(); ?></h4>
<p>
    Eine Anfrage zum zur&uuml;cksetzen Ihres Passworts f&uuml;r Ihren Account bei <?php echo link_tag($module->generateUrl('home')); ?>  wurde gestellt.<br>
    Um Ihr Passwort zu &auml;ndern, klicken Sie auf folgenden Link: <?php echo link_tag($module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))); ?><br>
</p>
<br>
<div style="color: #888;">
    Sie erhalten diese Benachrichtigungs-E-Mail weil jemand das zur&uuml;cksetzen des Passworts f&uuml;r Ihren Account beantragt hat. Falls das nicht von Ihnen ausging, ignorieren Sie diese E-Mail.<br>
    Um einzustellen wann und wie oft diese E-Mails versandt werden sollen, aktualisieren Sie Ihre Account-Einstellungen: <?php echo link_tag($module->generateURL('account'), $module->generateURL('account')); ?>
</div>
