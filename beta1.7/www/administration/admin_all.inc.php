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
//require($GLOBALS['path_www']."worldforum/SSI.php");

function AdminTemplateDisplay($topic,$user,$info) {
	$menu = MakeMenu($user);
	$database = $GLOBALS['database'];
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Admin @ tkoc.net</TITLE>
<link rel="stylesheet" href="Pro.css" type="text/css">
</HEAD>
<BODY TOPMARGIN="5" LEFTMARGIN="5" bgcolor="#FFFFFF">
<table cellpadding="5" cellspacing="0" border="0" style="width:100%">
<TR>
	<TD colspan=2><table cellpadding="0" cellspacing="0" border="0">
			<TR height=50>
				<TD valign=top><IMG src="images/left.gif"></IMG></TD>
				<TD valign=top></TD>
				<TD align=right valign=top><img src="images/righthandheader.jpg"></img></TD>
			</TR>
		</table>
		<table cellpadding=5; cellspacing=0; border=0;>
			<TR bgcolor=#FFFFFF>
				<TD valign=top>Admin @ tkoc.net!</TD>
			</TR>
		</table></TD>
</TR>
<TR>
	<TD valign=top><!-- Side bar -->
		<table cellpadding=3; cellspacing=0; border=0;>
			<!-- Navigation -->
			<?php
					if ($user==false) {
					?>
			<tr align=center>
				<td width="125"><span style="width:125px"><B>Login</B></span></td>
			</tr>
			<tr align=center>
				<td><table>
						<form action="login.php" method=POST>
							<tr align=center>
								<td align="left">Username:</td>
								<td align="right"><INPUT TYPE=TEXT SIZE=8 NAME=username maxlength=8></TD>
							</tr>
							<tr align=center>
								<td align="left">Password:</td>
								<td align="right"><INPUT TYPE=password SIZE=8 NAME=password maxlength=10></TD>
							</tr>
							<tr align=center>
								<td colspan=2 align="center"><input type="submit" value="Login" name="loginbutton"></TD>
							</tr>
						</form>
					</table></td>
			</tr>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?
					}
					?>
			<tr align=center>
				<td width="125"><span style="width:125px"><B>Main Menu</B></span></td>
			</tr>
			<?php echo makeMainMenu($user);?>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?
					if ($user->access & $GLOBALS['constants']->USER_NORMAL) {
					?>
			<tr align=center>
				<td><span style="width:125px"><B>User Menu</B></span></td>
			</tr>
			<?php  echo $menu; ?>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?
					}
					?>
			<?
					if ( false ) {
					?>
			<tr align=center>
				<td><span style="width:125px"><B>Game</B></span></td>
			</tr>
			<?php  echo makeGameMenu($user); ?>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?
					}
					?>
			<?
					if ( (intval($user->access) & $GLOBALS['constants']->USER_POST_NEWS) || (intval($user->access) & $GLOBALS['constants']->USER_MODERATOR) ) {
					?>
			<tr align=center>
				<td><span style="width:125px"><B>Admin</B></span></td>
			</tr>
			<?php echo makeAdminMenu($user);?>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?
					}
					?>
			<?php
$t =false;
$onlineNow="";
$numOnlineUsers=0;
if ($database) {
	if ($database->query("select username,User.userID from Login LEFT JOIN User on Login.userID=User.userID where (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(timestamp))<(3600) AND loggedon='Y' group by Login.userID")
						&& $database->numRows()>0) {
		$numOnlineUsers = $database->numRows();
		while ($nick = $database->fetchArray()) {
			if ($t) $onlineNow .= ", ";
			$onlineNow .= '<a href="/administration/gameUsers.php?userID='.$nick['userID'].'">' . $nick['username'] ."</a>";
			$t=true;
		}
		
	} else $onlineNow = "No users online."; 
} else $onlineNow = "No users online.";
$onlineNowTable = '<tr align="center" class="row"><td class="TLR">' .$onlineNow.'</td></tr>';
?>
			<?php
					if ( (intval($user->access) & $GLOBALS['constants']->USER_GAME_ADMIN) ) {
					?>
			<tr align=center>
				<td><span style="width:125px"><B>Online Users(<?php echo $numOnlineUsers;?>)</B></span></td>
			</tr>
			<?php echo $onlineNowTable;?>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<?php 
					}
					?>
			<tr align=center>
				<td><span style="width:125px"><B>Useful links:</B></span></td>
			</tr>
			<?php //echo $menu; ?>
			<tr align=center>
				<td><A class="black" href="http://www.tkoc.net">tkoc.net</A></TD>
			</tr>
			<tr align=center>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<tr>
				<td><img src="images/1p.gif"></TD>
			</tr>
			<!-- Links -->
		</table></TD>
	<TD valign=top width=100%><table cellpadding=5; cellspacing=0; border=0;>
			<TR>
				<TD><B><?php echo $topic; ?></B></TD>
			</TR>
			<TR >
				<TD bgcolor=#CCCCCC><?php echo $info; ?> </TD>
			</TR>
			<TR>
				<TD><!-- Submit -->
					<img src="images/1p.gif"> </TD>
			</TR>
		</TABLE>
</body>
</HTML>
<?php
}

function makeGameMenu ($userObject)
{
	$html = "";
	if ((intval($userObject->access) & $GLOBALS['constants']->USER_GAME_ADMIN) ) {
		$html .='<tr align=center class="row">
				    <td class="LR"><A class="black" href="">Config</A></TD>
				 </tr>';
	}
	
	return $html;

}

function makeAdminMenu ($userObject) {
	$html = "";
//	print_r($GLOBALS);
	if (intval($userObject->access) & $GLOBALS['constants']->USER_GAME_ADMIN) {
		$html .='<tr align=center class="row">
				    <td class="LR"><A class="black" href="gameStatus.php">Game Status</A></TD>
				 </tr>';
	}
	if (intval($userObject->access) & $GLOBALS['constants']->USER_GAME_ADMIN) {
		$html .='<tr align=center class="row">
				    <td class="LR"><A class="black" href="gameUsers.php">Game Users</A></TD>
				 </tr>';
	}
	if (intval($userObject->access) & $GLOBALS['constants']->USER_GAME_ADMIN) {
		$html .= '<tr align=center class="row">
				<td class="LR"><A class="black" href="cheaters.php">Cheaters</A></td>
			</tr>';
	}
	
	if (intval($userObject->access) & $GLOBALS['constants']->USER_POST_NEWS) {
		$html .='<tr align=center class="row">
				    <td class="LR"><A class="black" href="news.php">Post Game News</A></TD>
				 </tr>';
	}
	return $html;
}


function MakeMenu ($userObject) {
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
	$html = "";
	if ($userObject == false) {
		$html .='<tr align=center class="row">
			    <td class="TLR"><A class="black" href="index.html">Main page</A></TD>
			 </tr>';
		$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="regler.php">Rules</A></TD>
			 </tr>';
	
		return $html;
	}
	$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="profile.php">User profile</A></TD>
			 </tr>';
	$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="recruit.php">Recruit Players</A></TD>
			 </tr>';

	$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="../worldforum">Forum</A></TD>
			 </tr>';
	
/*	$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="Webchat.php">Chat</A></TD>
			 </tr>';*/

	$html .='<tr align=center class="row">
			    <td class="LR"><A class="black" href="../login.php">Enter Game</A></TD>
			 </tr>';
	
	$html .='<tr align=center class="row">
			    <td class="LR"><span class="black">'.ssi_logout($GLOBALS['path_domain_root']).'</span></TD>
			 </tr>';


	return $html;
}

function makeMainMenu ($userObject) {
	$html = "";
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
	$html .='<tr align=center class="row">
		    <td class="TLR"><A class="black" href="index.html">Main page</A></TD>
		 </tr>';
	$html .='<tr align=center class="row">
		    <td class="LR"><A class="black" href="../guide.html">Guide</A></TD>
		 </tr>';
	if ($userObject == false) {
	$html .='<tr align=center class="row">
		    <td class="LR"><A class="black" href="../reg.html">Registration</A></TD>
		 </tr>';
	}
	return $html;
}
?>