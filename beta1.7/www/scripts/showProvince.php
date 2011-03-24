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
require_once ("all.inc.php");
//require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "News.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "seasons/SeasonFactory.class.inc.php");

$province = $GLOBALS['province'];
$database = $GLOBALS['database'];
$config   = $GLOBALS['config'];
$info = "";
/*
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
*/

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
if ($config['status']=='Pause') {
	$remainingTicks = $config['statusLength']-$config['ticks'];
	$info .= "<b>NOTICE! <i>The game is NOT running.  While you can still build, the effects 
will not take place until the game officially starts.  The game will start in about $remainingTicks hour".($remainingTicks == 1 ? "" : "s").".</i></b> ";
}


//**********************************************************************
//* Show statusbar with date/time and if he has new messages	
//**********************************************************************

require_once($GLOBALS['path_www_scripts'] . "ChaosTime.class.inc.php");
// Fix to not show wrong date when on pause status - Soptep: 19/02/2010
if ($config['status'] == 'Pause') 
	$tick = 1;
else
	$tick = $config['ticks'];
		
$thisTime = new ChaosTime($tick);
$era = $thisTime->getEra();
$year = $thisTime->getYear();
$month = $thisTime->getMonth();
$day = $thisTime->getDay();

//**********************************************************************
// Øystein: tried to include the message class... we'll see...
//*	Hack: Load messages.. (ugly)
//* Soptep Hack: Check if there are unread news - 07/01/2010
//**********************************************************************
require_once( $GLOBALS['path_www_scripts']."messages/Message.class.inc.php" );
$myMessages = new Message( $database, $province->getpID());
if( $myMessages->unreadMessages() ) {
	$msg = "Your councilor informs you that you have <a href='".$GLOBALS['path_domain_script']."message.php'><font 
color=WHITE><b>unread Messages</b></font></a>.";
} else {
	$msg="";
}
$ID = $province->getpID();
$sql = "select seen, info, timeS from NewsProvince where pID=$ID or pID=0 order by neID desc";
$database->query($sql);

$unread = 0;					
while($row = $database->fetchArray() ) {
	if( ($row['seen'] == 'N') )
		$unread++;
}

if ($unread != 0) {
	$msg .= "<br />You have <a href='".$GLOBALS['path_domain_script']."provinceNews.php'><font 
color=WHITE><b>".$unread." unread News</b></font></a>.";
}
//**********************************************************************
//*	And now we show the rest of the province
//**********************************************************************
$info .='<CENTER>
<table width="700px" border=0 style="background:url(../img/postback.jpg) black;background-repeat:no-repeat; background-position:left top">
<tr>
<td ALIGN=LEFT valign=TOP><img src="../img/space.gif" width="30" height="40"></td><td>
<b>'.$GLOBALS['CurrentSeason']->Name.' Report</b> of the  ';
$info .="
$day day of the $month Month of the $year year in the $era era
</td>
</tr>
</table>
</CENTER>
";
$info .= "<br />$msg<br />";

$info .= $province->displayProvince();

/*
PROTECTION
*/
$info .= "<br><br>".$province->displayProtection()."<br><br>";
/*
if ( ($province->vacationmode==true) && ($province->vacationTicks > 48))
{
	$info .= "You can cancel vacation mode by pressing the cancel button. Your province will then resume as normal.<br>";
	$info .= "<form action=".$_SERVER['PHP_SELF']." method=POST>";
	$info .= "<input type=submit name='Cancel' value='Cancel'>";
	$info .= "</form>";
}
*/
templateDisplay($province,$info,"../img/space.gif","");
?>
