* Passwort vergessen? *
Hi, <?php echo $user->getBuddyname()."\n"; ?>

Eine Anfrage zum zuruecksetzen Ihres Passworts fuer Ihren Account bei <?php echo $module->generateUrl('home')."\n"; ?> wurde erstellt.
Um Ihr Passwort zu aendern, klicken Sie auf folgenden Link: <?php echo "\n".$module->generateUrl('reset_password', array('user' => $user->getUsername(), 'reset_hash' => $user->getActivationKey()))."\n"; ?>


Sie erhalten diese Benachrichtigungs-E-Mail weil jemand das zuruecksetzen des Passworts fuer Ihren Account beantragt hat. Falls das nicht von Ihnen ausging, ignorieren Sie diese E-Mail.
Um einzustellen wann und wie oft diese E-Mails versandt werden sollen, aktualisieren Sie Ihre Account-Einstellungen: <?php echo $module->generateURL('account'), $module->generateURL('account'); ?>
