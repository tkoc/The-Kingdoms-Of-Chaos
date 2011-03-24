<?php
/*******************************************************************************
    The Kingdoms of Chaos - An online browser text game - <http://www.tkoc.net>
    Copyright (C) 2011 - Administrators of The Kingdoms of Chaos

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact Information:
	Petros Karipidis  - petros@rufunka.com - <http://www.rufunka.com/>
	Anastasios Nistas - tasosos@gmail.com  - <http://tasos.pavta.com/>
	
	Other Information
	=================
	The exact Author of each source file should be specified after this license
	notice. If not specified then the "Current Administrators" found at
	<http://www.tkoc.net/about.php> are considered the Authors of the source
	file.

	As stated at the License Section 5.d: "If the work has interactive user
	interfaces, each must display Appropriate Legal Notices; however, if the
	Program has interactive interfaces that do not display Appropriate Legal
	Notices, your work need not make them do so.", we require you give
	credits at the appropriate section of your interface.
********************************************************************************/
?>
<?php

$this->aLanguages['de'] = array_merge($this->aLanguages['de'], array(
  'webserverPermissions' => 'Webserver-Berechtigungen',
  'webserverPermissionsText' => 'Folgende Verzeichnisse benötigen Schreibrechte. Bitte stellen Sie sicher das der Webserver/PHP Systembenutzer in diese Verzeichnisse schreiben kann.<br/><a href="http://www.google.de/search?hl=de&q=ftp+file+permissions&btnG=Suche&meta=">Anleitungen suchen</a>',
  'chooseLanguage' => 'Sprache',
  'chooseLanguageText' => 'Diese Sprache wird nach der Installation standardmäßig neuen Benutzern sowie dem administrativen Benutzer (root) zugewiesen. Zudem werden fehlende Übersetzungen in dieser Sprache ergänzt.',
  'installation' => 'Installation',
  'installationText' => 'Der folgende Dialog führt sie durch die Grundeinstellungen ihrer phpXplorer Installation und weißt sie auf nötige Vorbereitungen hin. Die Einstellungen können nach der Installation in der Datei ~&hellip;/phpXplorer/config.php~ geändert werden.',
  'recheck' => 'erneut prüfen',
  'adminPassword' => 'Passwort',
  'adminPasswordText' => 'Legen sie das Passwort für den Benutzer ~root~ fest. Nach der Installation können sie sich mit dem Benutzernamen ~root~ und dem Passwort als Administrator anmelden. '
));

?>