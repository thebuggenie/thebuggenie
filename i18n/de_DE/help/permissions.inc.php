Im Gegensatz zu BUGS besitzt BUGS 2 ein sehr leistungsstarkes Zugriffsrechte-System. Einfach zu verstehen und zu bedienen, jedoch sehr flexibel.<br>
<br>
Die Zugriffsrechte in BUGS 2 basieren auf 4 Stufen (sortiert nach Wichtigkeit):
<ul>
        <li>Für einen bestimmten Benutzer</li>
        <li>Für Mitglieder eines Teams</li>
        <li>Für Mitglieder einer Gruppe</li>
        <li>Für jeden</li>
</ul>
Die Zugriffsrechte sind zuerst definiert für "Jeder", dann eine Gruppe, dann ein Team, und dann für einen bestimmten Benutzer. Normalerweise sind die ersten beiden genug. Wie auch immer, es ist wichtig zu wissen in welcher Reihenfolge die Zugriffsrechte angewendet werden:
<ul>
        <li>Benutzer-Rechte überscheiben alles, sofern es benutzerspezifisch ist</li>
        <li>Team-Rechte überschreiben Gruppen-Rechte</li>
        <li>Gruppen-Rechte überschreiben "Jeder"-Rechte</li>
        <li>Das "Jeder"-Recht ist das Standard-Recht und wird von allen überschrieben</li>
</ul>
Wenn Sie die Zugriffsrechte festlegen, erscheinen farbige Symbole, die Ihnen zeigen, welche Zugriffsrechte für das angegebene Objekt erteilt wurden:
<div style="padding: 10px; margin-left: 15px;">
        <p><img src="themes/default/led_green.png"> - Voller Zugriff auf Objekt. Für Objekte mit Lese- / Schreibzugriff bedeutet grün voller Lese- / Schreibzugriff.</p>
        <p><img src="themes/default/led_yellow.png"> - Begrenzter Zugriff auf angegebenes Objekt - bei Objekten mit Lese- / Schreibzugriff haben Sie nur Lesezugriff.</p>
        <p><img src="themes/default/led_red.png"> - Kein Zugriff auf angegebenes Objekt.</p>
        <p><img src="themes/default/led_lightblue.png"> - Keine Zugriffsrechte für Benutzer/Team/Gruppe erteilt. Wenn ein blaues Symbol erscheint, wurde der Zugriff über eine untere Stufe erteilt.</p>
</div>
Wenn Sie festlegen wollen, was "Jeder" machen darf, können Sie das unter <b>Einstellungen &ndash;&gt; Teams &amp; Gruppen verwalten</b>.
Die Zugriffsrechte, die in der Gruppe "Jeder" erteilt werden, gibt an, was ein Benutzer darf oder worauf er Zugriff hat. Nachdem Sie festgelegt haben was "Jeder" darf,
können Sie festlegen was Benutzer der verschiedenen Gruppen dürfen. Standardmäßig existieren eine "Administrator"-Gruppe, eine "Gast"-Gruppe und eine "Benutzer"-Gruppe.
Wählen Sie eine dieser Gruppen aus um die Zugriffsrechte für die Benutzer ein dieser Gruppe festzulegen.<br>
<br>
Individuelle Benutzer-Rechte werden über <b>Einstellungen &ndash;&gt; Benutzer verwalten</b> festgelegt.
Suche Sie einen Benutzer und klicken Sie auf "Berechtigungen setzen" bei diesem Benutzer.<br>
<br>
<b>Jetzt schauen wir uns das mal an einem Beispiel an</b><br>
Benutzer in der "Administrator"-Gruppe soll Zugriff zu den "Einstellungen" haben. Wählen Sie die "Administrator" Gruppe aus und klicken Sie
auf das blaue "Einstellungen" Symbol. Dies berechtigt allen Benutzern in der "Administrator"-Gruppe den Zugriff auf den "Einstellungen" Verweis
im oberen Menü, sowie auf die Einstellungen-Seite.<br>
<br>
Jetzt geben wir den verschiedenen Modulen unter den "Einstellungen", durch das Klicken auf "Einstellungen", die Zugriffsrechte.
Jetzt Klicken Sie auf die blauen Symbole für jedes Modul, dem Sie <i>Lesezugriff</i> geben wollen.
Durch nochmaliges Anklicken des (jetzt gelben) Symbols erteilen Sie diesem Modul ebenfalls <i>Schreibzugriff</i>.
Durch weiteres Anklicken des (jetzt grünen) Symbols verweigert diesem Benutzer Zugriff zu diesem Modul (das ist normalerweise nur nötig, wenn
der Zugriff bereits anhand einer unteren Stufe erteilt wurde). Erledigen Sie dies für die Module "Benutzer verwalten" sowie für "BUGS 2 Bereiche".
<br><br>
Nehmen wir an, dass Sie die Benutzer- und Bereichsverwaltung nur für "Mitarbeiter" zugänglich machen wollen. Stellen Sie sicher, dass Sie
den Zugriff auf "Einstellungen" -> "Benutzer verwalten" keiner Gruppe (inkl. der "Jeder" Gruppe) oder keinem Benutzer gewährt haben.
Jetzt erstellen Sie ein <i>Team</i> "Mitarbeiter" und wählen es durch Anklicken aus. Jetzt wählen Sie das Modul "Einstellungen"
durch Anklicken aus (alle Symbole sollten blau sein). Jetzt klicken Sie auf doppelt auf das "Benutzer verwalten" Zugriffs-Symbol, damit es grün wird.
Führen Sie die selbe Aktion für "BUGS 2 Bereiche" aus.<br>
<br>
<b>Glückwunsch!</b><br>
Jetzt haben alle Benutzer in der Administrator-Gruppe Zugriff auf alle Module unter Einstellungen, außer "Benutzer verwalten" und "BUGS 2 Bereiche",
welche nur für Benutzer, die Mitglieder des Team "Mitarbeiter" sind, zugänglich sind.<br>
<br>
Bedenken Sie, dass Benutzer nur einer Gruppe angehören können, jedoch verschiedenen Teams angehören können. Denken Sie ebenfalls daran, dass
die Einstellung Zugriff "verweigern" nur gültig ist, solange nicht Zugriff "erlauben" auf der selben Ebene oder einer Ebene höher gewählt wurde.
Mehr über die Stufen des Zugriffs sowie über das Überschreiben von Zugriffsrechten erfahren Sie am Anfang dieses Hilfethemas.
