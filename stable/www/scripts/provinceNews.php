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
//* showProvince.php
//*
//* Shows basic stuff about your province.  Also updates the
//* networth for your province.
//*	
//* 
//* Author: Anders Elton
//*
//* History:
//*		- 1.08.2004: Anders Elton.  Rewrote to match new coding style
//*	
//**********************************************************************

require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "News.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "seasons/SeasonFactory.class.inc.php");

$province = $GLOBALS['province'];
$database = $GLOBALS['database'];
$config   = $GLOBALS['config'];
$info = "";

if (isset($_POST['Cancel']))
{
	if ($province->vacationTicks > 48)
	{
		$database->query("UPDATE Province set vacationMode='false' where pID='". $province->pID ."'");
	}
	else
	{
		die("cheat.");
	}

	die("Please log in again");
}


//**********************************************************************
//*	Code starts here
//**********************************************************************

$province->setNetworth();
$province->getMilitaryData();

//**********************************************************************
//*	Ugly hack to load admin News.
//**********************************************************************
$news = "";
$database->query("SELECT news,header,DATE_FORMAT(timestamp,'%d.%m.%y %H:%i:%s') as time, nick from adminNews LEFT JOIN User on User.userID=adminNews.userID
					WHERE (TO_DAYS(curdate())-TO_DAYS(DATE_FORMAT(timestamp,'%Y-%m-%d')))<2 order by adminNewsID desc"); 
if ($database->numRows())
{
	$dataNews = $database->fetchArray();
	$news = "
				<table width=100% border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >
					<TR class=subtitle>
 					<TD class=\"TLR\" colspan=\"2\">
						<b>Game News: ".stripslashes($dataNews['header'])."</b>
						</TD>
					</TR>
					<TR bgcolor=#000000>
						<TD class=\"TLR\" colspan=\"2\">
					".stripslashes($dataNews['news'])."<br>&nbsp;<br>
						<i>- $dataNews[nick], $dataNews[time]</i>
						</TD>
					</TR>
				</table>";
}

$info ="<br>". $news;

//**********************************************************************
//* Show game status.	
//**********************************************************************

if ($config['status']=='Ended') {
	$info .= "<br><b>NOTICE! <i>The game is NOT running.  While you can still build, the effects 
will not take place until the game officially starts.  Read forum for more information.</i></b> ";
}

if ($config['status']=='Pause') {
	$info .= "<br><b>NOTICE! <i>The game is NOT running.  While you can still build, the effects 
will not take place until the game officially starts.  The game will start in about $config[pause] hours.</i></b> ";
}


/*
NEWS SECTION
*/
$news = new News($database, 1, $user->getpID());
$info .= "\n\n<center>".$news->getNews()."</center>";
templateDisplay($province,$info,"../img/space.gif","");

?>