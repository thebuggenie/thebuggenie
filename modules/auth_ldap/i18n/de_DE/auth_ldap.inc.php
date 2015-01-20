<?php
  
// ----------------------------------------------------------------------------
// modules/auth_ldap/classes/\thebuggenie\core\entities\LDAPAuthentication.class.php
  $strings['LDAP Authentication'] = 'LDAP Authentifizierung';
  $strings['Allows authentication against a LDAP or Active Directory server'] = 'Erlaubt Authentifizierung per LDAP oder Active Directory Server';
  $strings['Configure server connection settings'] = 'Serververbindungseinstellungen konfigurieren';

// ----------------------------------------------------------------------------
// modules/auth_ldap/templates/_settings.inc.php
  $strings['Use this page to set up the connection details for your LDAP or Active Directory server. It is highly recommended that you read the online help before use, as misconfiguration may prevent you from accessing configuration pages to rectify issues.'] = 'Verwenden Sie diese Seite, um die Verbindungsdetails für Ihre LDAP- oder Active Directory-Server einrichten. Es wird dringend empfohlen, dass Sie die Online-Hilfe vor dem Gebrauch lesen. Eine Fehlkonfiguration kann möglicherweise den Zugriff auf die Konfigurationsseiten, um Probleme zu beheben, verhindern.';
  $strings['View the online documentation'] = 'Onlinedokumentation ansehen';
  $strings['LDAP support is not installed'] = 'LDAP Unterst&uuml;tzung ist nicht installiert';
  $strings['The PHP LDAP extension is required to use this functionality. As this module is not installed, all functionality on this page has been disabled.'] = 'Die PHP LDAP Erweiterung wird ben&ouml;tigt, um das Modul zu ben&uuml;tzen. Weil dieses Erweiterung nicht installiert ist, wurden alle Einstellungen auf dieser Seite deaktiviert';
  $strings['Important information'] = 'Wichtige Information';
  $strings['When you enable LDAP as your authentication backend in Authentication configuration, you will lose access to all accounts which do not also exist in the LDAP database. This may mean you lose administrative access.'] = 'Beim Aktivieren von LDAP als Ihre Authentifizierung-Backend in Authentifizierungskonfiguration verlieren Sie den Zugriff auf alle Konten die nicht auch in der LDAP-Datenbank existieren. Dies kann bedeuten, dass Sie den Administratorzugriff verlieren.';
  $strings['To resolve this issue, either import all users using the tool on this page and make one an administrator using Users configuration, or create a user with the same username as one in LDAP and make that one an administrator.'] = 'Um dieses Problem zu l&ouml;sen, importieren Sie alle Benutzer, welche das Tool auf dieser Seite benutzen und setzen Sie einen Benutzer in der Benutzerkonfiguration als Administrator ein, oder erstellen Sie einen Benutzer mit demselben Benutzernamen wie in LDAP und ernennen Sie ihn zum Administrator.';
  $strings['Connection details'] = 'Verbindungdetails';
  $strings['Hostname'] = 'Hostname';
  $strings['Use URL syntax (ldap://hostname:port). If your server requires SSL, use ldaps://hostname/ in this field.'] = 'Setzen Sie URL Syntax (ldap://hostname:port) ein. Wenn Ihr Server SSL ben&ouml;tigt, ben&uuml;tzen Sie ldaps://hostname/ in diesem Feld.';
  $strings['Base DN'] = 'Basis DN';
  $strings['This should be the DN string for an OU where all user and group OUs can be found. For example, DC=ldap,DC=example,DC=com.'] = 'Dies sollte der DN String f&uuml;r eine OU sein, bei dem alle Benutzer und Gruppen OUs gefunden werden können. Zum Beispiel DC=ldap,DC=example,DC=com.';
  $strings['Object DN attribute'] = 'Objekt DN';
  $strings['Enter the name of the property containing the distinguished name of an object. On Linux systems this may be entrydn (which is the default value if this is left blank), on Active Directory it is distinguishedName.'] = 'Geben Sie den Namen der Eigenschaft an, welcher den kennzeichnenden Namen eines Objekts enthält. Auf Linux Systemen könnte das die entrydn sein (welche der Standardwert ist wenn hier leer gelassen wird), bei Active Directory ist es distinguishedName.';
  $strings['User class'] = 'Benutzerklasse';
  $strings['Enter the value to check for in objectClass for users. Leave blank to use the default of person'] = 'Wert eingeben um in objectClass auf Benutzer zu prüfen. Leer lassen um den Standardwert der Person zu verwenden.';
  $strings['Username attribute']  = 'Benutzername';
  $strings['This field should contain the name of the attribute where the username is stored, such as uid.']  = 'Das Feld sollte den Namen der Eigenschaft enthalten, in welchem der Benutzername gespeichert ist, so wie uid.';
  $strings['Full name attribute']  = 'Kompletter Name';
  $strings['Given name attribute'] = 'Vorname';
  $strings['Email address attribute']  = 'E-Mail Adresse';
  $strings['Group class'] = 'Gruppenklasse';
  $strings['Enter the value to check for in objectClass for groups. Leave blank to use the default of group'] = 'Geben Sie einen Wert ein, um in objectClass für Gruppen zu prüfen. Lassen Sie leer um die Standardgruppe zu verwenden';
  $strings['Group members attribute']  = 'Gruppenmitglieder';
  $strings['This field should contain the name of the attribute where the list of members of a group is stored, such as uniqueMember.']  = 'Dieses Feld sollte den Attributnamen enthalten, in welchem die Liste der Mitglieder einer Gruppe gespeichert werden, so wie uniqueMember. ';
  $strings['Allowed groups'] = 'Erlaubte Gruppen';
  $strings['You may wish to restrict access to users who belong to certain groups in LDAP. If so, write a comma separated list of group names here. Leave blank to disable this feature.'] = 'Sie k&ouml;nnen den Zugriff f&uuml;r Benutzer verschiedener Gruppen in LDAP einschr&auml;nken. Geben Sie dazu eine kommaseparierte Liste der Gruppennamen an. Leer lassen um diese Funktion zu deaktivieren.';
  $strings['Control username'] = 'Benutzernamen kontrollieren';
  $strings['Control user password'] = 'Benutzerpasswort kontrollieren';
  $strings['Please insert the authentication details for a user who can access all LDAP records. Only read only access is necessary, and for an anonyous bind leave this blank.'] = 'Bitte die Authentifizierungsdetails für Benutzer welche Zugriff auf alle LDAP Datensätze haben angeben. Nur Lesezugriff ist nötig.Lassen Sie die Details leer um eine anonyme Anbindung zu ermöglichen.';
  $strings['Use HTTP Integrated Authentication'] = 'Benütze HTTP integrierte Authentifizierung';
  $strings['Activate to enabled automatic user login using HTTP integrated authentication. This requires your web server to be authenticating the user (e.g. HTTP Basic Authentication, Kerberos etc).'] = 'Aktivieren um automatische Benutzeranmeldungen per HTTP integrierter Authentifizierung zu erlauben. Dies bedingt, dass Ihr Webserver eine Benutzerauthentifizierung unterstützt (z. B. HTTP Basic Authentication, Kerberos usw.)';
  $strings['HTTP header field'] = 'HTTP Header Feld';
  $strings['If using HTTP integrated authentication specify the HTTP header field that will contain the user name.'] = 'Wenn HTTP integrierte Authentifizierung eingesetzt wird, definieren Sie das HTTP Header Feld welches den Benutzernamen enthält.';
  $strings['Click "%save" to save the settings'] = 'Klicken Sie "%save" um Ihre Einstellungen zu speichern';
  $strings['Save'] = 'Sichern';
  $strings['Test connection'] = 'Verbindung testen';
  $strings['After configuring and saving your connection settings, you should test your connection to the LDAP server. This test does not check whether the DN can correctly find users, but it will give an indication if The Bug Genie can talk to your LDAP server.'] = 'Nachdem Sie konfiguriert und gespeichert haben, sollten Sie Ihre Verbindung zu LDAP Server testen. Dieser Test zeigt nicht auf ob der DN Benutzer korrekt findet, aber kann Hinweise darauf geben, ob The Bug Genie mit Ihren LDAP Server kommunizieren kann.';
  $strings['Import all users'] = 'Alle Benutzer importieren';
  $strings['You can import all users who can log in from LDAP into The Bug Genie with this tool. This will not let them log in without switching to LDAP Authentication. We recomemnd you do this before switching over, and make at least one of the new users an administrator. Already existing users with the same username will be updated.'] = 'Sie können alle Benutzer importieren, welche sich von LDAP in The Bug Genie mit diesem Werkzeug einloggen können. Dies wird nicht dazu führen, dass sich die Benutzer ohne LDAP Authentifizierung einloggen können. Wir empfehlen Ihnen, vor dem umstellen zu importieren und mindestens einem neuen Benutzer Administratorenrechte zu vergeben. Bereits existierende Benutzer mit demselben Benutzernamen werden aktualisiert.';
  $strings['Import users'] = 'Benutzer importieren';
  $strings['Prune users'] = 'Benutzer aufr&auml;umen';
  $strings['To remove the data from The Bug Genie of users who can no longer log in via LDAP, run this tool. These users would not be able to log in anyway, but it will keep your user list clean. The guest user is not affected, but it may affect your current user - if this is deleted you will be logged out.'] = 'Benützen Sie dieses Werkzeug, um die Daten von Benutzern von The Bug Genie zu entfernen, die sich nicht mehr via LDAP anmelden können. Diese Benutzer werden sowieso nicht mehr in der Lage sein, sich einzuloggen, aber es wird Ihre Benutzerliste sauber halten. Der Gast Benutzer ist nicht betroffen, aber es kann Ihren aktuellen Benutzer betreffen - wenn er gelöscht wird werden Sie ausgeloggt.';
  $strings['After configuring and saving your connection settings, you should test your connection to the LDAP server. This test does not check whether the DN and attributes can allow The Bug Genie to correctly find users, but it will give an indication if The Bug Genie can talk to your LDAP server, and if any groups you specify exist. If HTTP integrated authentication is enabled, this will also test that your web server is providing the REMOTE_USER header.'] = 'Nach der Konfiguration und dem Speichern Ihrer Verbindungseinstellungen, sollten Sie die Verbindung zum LDAP-Server testen. Dieser Test prüft nicht ob der DN und die Einstellungen The Bug Genie erlauben Benutzer zu finden, aber es wird Ihnen aufzeigen ob The Bug Genie mit Ihrem LDAP Server kommunizieren kann, und ob von Ihnen spezifizierte Gruppen existieren. Wenn HTTP integrierte Authentifizierung aktiviert ist, wird auch getestet ob Ihr Web Server den REMOTE_USER Header unterstützt.';

// modules/auth_ldap/classes/actions.class.php
// ----------------------------------------------------------------------------
  $strings['Failed to connect to server'] = 'Verbindung zum Server konnte nicht aufgebaut werden.';
  $strings['Failed to bind:'] = 'Verknüpfen fehlgeschlagen:';
  $strings['Search failed:'] = 'Suche fehlgeschlagen:';
  $strings['User does not exist in the directory'] = 'Benutzer existiert nicht im Verzeichnis';
  $strings['This user was found multiple times in the directory, please contact your admimistrator'] = 'Dieser Benutzer wurde mehrmals im Verzeichnis gefunden, bitte kontaktieren Sie Ihren Admimistrator.';
  $strings['Search failed'] = 'Suche fehlgeschlagen';
  $strings['You are not a member of a group allowed to log in'] = 'Sie sind keine Mitglied einer Gruppe, die sich anmelden kann';
  $strings['Your password was not accepted by the server'] = 'Ihr Passwort wurde vom Server nicht akzeptiert.';
  $strings['HTTP authentication internal error.'] = 'Interner Fehler bei HTTP Authentifizierung.';
  $strings['HTTP integrated authentication is enabled but the HTTP header has not been provided by the web server.'] = 'HTTP integrierte Authentifizierung ist aktiviert aber der HTTP Header wird vom Webserver nicht unterstützt.';
