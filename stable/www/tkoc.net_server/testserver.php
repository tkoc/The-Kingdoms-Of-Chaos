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
//die();
//************************************************
//* server.php
//*
//* The game engine.  This should be run every tick
//* Author: Anders Elton
//*
//* History:
//*	- Rewrite 31.07.2004
//************************************************
// TODO:  writeLog function, writeErr function
//die("this is fucked up");


require_once("/home/chaos/www/scripts/globals.inc.php");
$GLOBALS['script_mode'] = 'server'; // override the web mode.

require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Kingdom.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "TrigEffect.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "seasons/SeasonFactory.class.inc.php");
require_once ($GLOBALS['path_server'] . "Server.class.inc.php");
require_once ($GLOBALS['path_server'] . "ServerStatistics.class.inc.php");

$database = $GLOBALS['database'];
$config = $GLOBALS['config'];
$start = $GLOBALS['game_start_clock'];

$server = new Server(&$database);
$server->removeResetProvince();
$server->fixBrokenUsers();
$server->fixBrokenProvinces();
$server->prepareTick();

echo "Kingdom\n";
$kingdom = new Kingdom($database);
$kingdom->doTick();

echo "Server\n";
// server class
$server->doTick();

?>
