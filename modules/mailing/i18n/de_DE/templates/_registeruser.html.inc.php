Hi, <?php echo $user->getBuddyname(); ?>!
Der Benutzername '<?php echo $user->getUsername(); ?>' wurde mit The Bug Genie auf <?php echo $module->generateURL('home'); ?> registriert.

Bevor Sie Ihren neuen Account einsetzen koennen, muessen Sie die E-Mail-Adresse mit Hilfe des folgenden Links bestaetigen:
<?php echo $link_to_activate; ?>

* Ihr Passwort lautet: <?php echo $password; ?>

und Sie koennen nach der Aktivierung auf der untenstehenen Seite mit diesem Passwort einloggen.

(Diese E-Mail wurde aufgrund einer Anfrage versandt, in welcher diese E-Mail-Adresse angegeben wurde. Falls Sie diesen Benutzernamen nicht registriert haben, oder denken dass Sie diese E-Mail faelschlicherweise erhalten haben, bitte loeschen Sie sie. Wir entschuldigen uns fuer die Unannehmlichkeiten.)
