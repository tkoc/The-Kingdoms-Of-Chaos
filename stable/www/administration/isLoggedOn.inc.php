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
/* isLoggedOn.php
 * 
 *  Creates database object, checks if user is logged on, creates user object.
 *  If user is not logged in, a standard error message will be displayed. (defined in all.inc.php)
 *  Author: Anders Elton
 * 
 */
require_once ("admin_all.inc.php");

$database = $GLOBALS['database'];


$user = new User ("",&$database);  // no cookie

if ($user->isLoggedOn()==false) {
//	require_once ("Div.class.inc.php");
//	$index = new Index($database);
//	TemplateDisplay("Gjøvik @ Efnet",false,"Du ble automatisk logget av systemet. Logg inn på nytt<br>".$index->html);

//	die();
	$user = false;
}
?>