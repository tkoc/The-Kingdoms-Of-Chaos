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

//**********************************************************************
//* requireLogon.inc.php
//*
//*	This should be included in every script that the user needs to be logged
//* on to view
//*
//* Author: Anders Elton
//*
//* History:
//*	
//**********************************************************************

require_once ("isLoggedOn.inc.php");		// we need some user/province objects to play with
$var = '<CENTER><br>Kingdoms of Chaos is an online strategy game. You compete with several other players from all over the world to 
create the most powerful province and to build the most powerful kingdom.  You have thieves and Wizards, even great heroes, 
that will help you reach your goal.  But be careful, you also have to master your economy to not fall behind your enemies!</CENTER>';

if (!$GLOBALS['context']['user']['is_logged']) 
{
	$province = new Province(-1,$GLOBALS['database']);
	templateDisplay($province,
	'<CENTER>Error: You need to be logged in to view this page.<br><br><span style="text-decoration:underline;"><a href="../login.php">Click Here to Login Again</a></span><br><br></CENTER>' . $var
	,"../img/space.gif","", false);
	exit;
}

if ($GLOBALS['province']==false && $GLOBALS["create_prov"] != 1) 
{
	$province = new Province(-1,$GLOBALS['database']);
	header ("Location: ../regProvince.php");
	exit();
}
/*
if (isset($GLOBALS['database_queries_count'])) 		$GLOBALS['game_queries_required'] = $GLOBALS['database_queries_count'];
else die("?????");
if (isset($GLOBALS['database_queries_fetch_count']))$GLOBALS['game_fetches_required'] = $GLOBALS['database_queries_fetch_count'];

if ($GLOBALS['province']->isAlive() == false && $GLOBALS["create_prov"] != 1)
{
	templateDisplay($GLOBALS['province'], '<CENTER>You have been killed!</CENTER>');
	die();
}*/

?>