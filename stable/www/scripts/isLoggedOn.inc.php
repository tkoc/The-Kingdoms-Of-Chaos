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
//************************************************
//* file: isLoggedOn.inc.php
//*
//* Tries to load user and province object.  If the user is not logged in
//* user will be set to false.
//*	
//* 
//* Author: Anders Elton
//************************************************

require_once ("all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");

$database = $GLOBALS['database'];

if ($GLOBALS['context']['user']['is_logged'])
{
	$user = new User ($_COOKIE["PHPSESSID"],$database);
	$user->isLoggedOn();
	
	if ($user->pId > 0)
	{
		$province = new Province($user->getpID(),$database);
		$province->getProvinceData();
		//$province->setNetworth();
	} 
	else  // no province on this user...
	{
		$province = false;
	}
	//die("here");
}
else {
	if (isset($GLOBALS['game_debug_data']))
		$GLOBALS['game_debug_data'] .= '<br>User still not logged in, giving up.';
	$province = false;
	$user = false;
}

// set the globals so we can access the varables elsewhere :)
$GLOBALS['province'] = $province;
$GLOBALS['user'] = $user;
?>
