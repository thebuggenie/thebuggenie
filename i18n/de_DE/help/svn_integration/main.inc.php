Falls Sie noch nicht richtig die SVN Integration eingerichtet haben, schauen Sie sich bitte
<br>
<a href="help.php?topic=svn_integration/howto"><b>Einrichten der SVN Integration</b></a> an<br><br>
<b>Benutzen der SVN Integration</b><br>
Das Modul SVN Integration besteht aus verschiedenen Orten:
<ul>
	<li><b>SVN "Änderungen"</b><br>
	Wenn Sie Änderungen vornehmen schaut die SVN Integration das Kommentar der Änderung an und aktualisiert alle 
	entsprechende Sachverhalte.
	<br><br>
	Das Modul schaut nach folgenden Wörtern: <br>
	<i>fix, fixes, fixed, fixing, applies to, close, closes, references, ref, addresses, re, see, according to</i>, 
	gefolgt von einer <b>#</b> und der Nummer des Sachverhaltes.<br>
	(Sie können in dem Kommentar der Änderung auf soviel Sachverhalte verweisen wie Sie wollen.)<br><br>
	<b>Beispiel Änderungs-Kommentar: </b><i>Fixing #B2-12, #B2-11 and #B2-10. Also see #B2-14.</i><br>
	Dieses Kommentar wird alle vier Sachverhalte mit der Information, die bei der Änderung angegeben wurde, aktualisieren
	und vermerkt Kommentare bei diesen Sachverhalten.
	<br>
	<br>
	Das Modul SVN Integration <i>schließt nicht</i> Sachverhalte automatisch.
	<br><br>
	</li>
	<li><b>SVN Änderungen in Sachverhalten protokolliert</b><br>
	Wenn Sie Sachverhalte betrachten, finden Sie alle SVN Änderungen unterhalb der Zusammenfassung. Die SVN Änderungen 
	beinhalten einen Verweis zu den protokollierten Änderungen sowie direkt zur Datei (wenn ViewVC installiert ist).
	<br><br></li>
	<li><b>"Quelltext anzeigen"</b><br>
	Auf der Produkt-Übersicht - Seite wird einen Verweis "Quelltext anzeigen" rechts oben in der Ecke anzeigen.<br><br></li>
</ul>
Wenn Sie Tips für die weitere Benutzung des Modules SVN Integration haben, dann lassen Sie es uns bitte wissen!