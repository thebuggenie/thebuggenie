<b>Einrichten der SVN Integration</b><br>
Um eine SVN Integration einzurichten, befolgen Sie bitte die 3 Schritte:
<ul>
	<li><b>Hinzufügen des "post-commit hook" zum SVN Repository</b><br>
	In dem Verzeichnis des Moduls ( /modules/svn_integration ) befindet sich ein Skript namens post-commit.sh, welches bei einem "SVN commit" ausgelöst wird. Falls Sie keine anderen "commit-hooks" benutzen, kann dieses Skript das standard "post-commit" Skript ersetzen. Falls Sie bereits ein funktionierendes post-commit.sh Skript haben, kopieren Sie den Inhalt dieser Datei in Ihr bereits existierendes post-commit.sh Skript.<br>
	<b>Denken Sie daran das Skript post-commit.sh aus dem Modul-Verzeichnis zu entfernen, oder unzugänglich vom Internet aus zu machen.</b><br>
	<br>
	Falls Sie keine Ahnung, wie Sie das einstellen, besuchen Sie bitte das SVN Handbuch.<br><br></li>
	<li><b>"post-commit" Skript bearbeiten</b><br>
	Stellen Sie beim Bearbeiten des Skriptes sicher, dass Sie den richtigen Pfad benutzen. Falls sie das Web-Update benutzen, vergewissern Sie sich, dass die Einstellungen in der Datei mit den Einstellungen auf der Konfigurations-Seite der SVN Integration übereinstimmen.<br><br></li>
	<li><b>ViewVC einrichten</b><br>
	ViewVC ist ein Web-Interface für das Betrachten der Repositories. Um es zu benutzen, richten Sie es bitte anhand der Instruktionen, die Sie auf der <a href="http://www.viewvc.org" target="_blank">ViewVC Homepage</a> finden, ein.<br>
	<br>
	Wenn Sie ViewVC eingerichtet haben, vergewissern Sie sich, dass Sie die URL's zu ViewVC auf der Konfigurations-Seite der SVN Integration eingestellt haben.
</ul>
<br>
Wenn Sie die oberen drei Schritte befolgt haben, sollten Sie in der Lage sein das Modul SVN Integration zu benutzen.<br>
<br>
Um mehr über die richte Benutzung der SVN Integration zu erfahren, schauen Sie bitte unter<br><a href="help.php?topic=svn_integration/main"><b>Benutzen der SVN Integration</b></a>.