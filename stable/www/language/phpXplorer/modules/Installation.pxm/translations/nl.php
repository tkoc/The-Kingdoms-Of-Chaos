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

$this->aLanguages['nl'] = array_merge($this->aLanguages['nl'], array(
  'adminPasswordText' => '', // Legen sie das Passwort für den Benutzer ~root~ fest. Nach der Installation können sie sich mit dem Benutzernamen ~root~ und dem Passwort als Administrator anmelden. 
  'adminPassword' => '', // Password
  'recheck' => '', // recheck
  'installationText' => '', // The following dialog leads you through the basic configuration of your phpXplorer installation and points you out to the needed preparations. These settings could be modified in ~&hellip;/phpXplorer/config.php~ after installation.
  'installation' => '', // Installation
  'chooseLanguageText' => '', // Diese Sprache wird nach der Installation standardmäßig neuen Benutzern sowie dem administrativen Benutzer (root) zugewiesen. Zudem werden fehlende Übersetzungen in dieser Sprache ergänzt.
  'chooseLanguage' => '', // Language
  'webserverPermissionsText' => '', // The following phpXplorer directories have to be writable. Please enshure that the webserver/PHP system user is allowed to write to these directories.<br/><a href="http://www.google.de/search?hl=de&q=ftp+file+permissions&btnG=Suche&meta=">Search for instructions</a>
  'webserverPermissions' => '', // Webserver permission
));

?>