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
include_once 'facebook/facebook.php';
$fb=new Facebook();
$fb_user=$fb->get_loggedin_user();

/*echo $_SERVER["QUERY_STRING"];
if ($_SERVER["QUERY_STRING"] && !isset($_SESSION["facebook"]))
	$_SESSION["facebook"] = true;*/


require_once( "scripts/globals.inc.php" );
if ( isset( $GLOBALS['new_css'] ) ){
	$css = $GLOBALS['path_domain_root']."css/chaos2.css";
} else {
	$css = $GLOBALS['path_domain_root']."css/chaos.css";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>The Kingdoms of Chaos</title>
   <meta name="description" content="A massively played mutliplayer online strategy game.   You compete with several other players from all over the world to create the most powerful province and to build the most powerful kingdom.">
   <meta name="keywords" content="game, mutliplayer, friends, war, roleplay, strategy">	
   <link rel=stylesheet href="<?php echo $css;?>" type="text/css">
   <script src="<?php echo $GLOBALS['path_domain_root']; ?>javascript/rollover.js" type="text/javascript"></script>
   <?php if (isset($GLOBALS['extra_javascript'])) echo $GLOBALS['extra_javascript']; ?>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<div class="content">
	<table width="800" border="0" align="center">
		<tr height="200" bgcolor="#000000">
        	<td>
				<table cellspacing="0" cellpadding="0" align="center" width="750">
					<tr>
						<td colspan="3" width="750" align="right">
							<img src="<?php echo $GLOBALS['path_domain_root']; ?>img/msg_top.gif" width="750" height="46" border="0" alt=""></td>
					</tr>
						<td align="left" background="<?php echo $GLOBALS['path_domain_root']; ?>img/msg_left.gif" width="52"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/space.gif"" height="1" width="52"></td>
						<td width="656" align=center>
							<a href="http://www.tkoc.net"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/banner.jpg" width="650" border="0"></a>
						</td>
						<td align="right" background="<?php echo $GLOBALS['path_domain_root']; ?>img/msg_right.gif" width="42"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/space.gif"" height="1" width="42"></td>
					</tr>
					<tr>
						<td colspan="3" width="750">
							<img src="<?php echo $GLOBALS['path_domain_root']; ?>img/msg_bottom.gif" width="750" height="45" border="0" alt=""></td>
					</tr>
				</table>
			</td>   
		</tr>
	</table>
	<!-- Menu -->
		<div class="navbar">
			<div class="nav-button">
				<a href="<?php echo $GLOBALS['path_domain_root']; ?>news.php"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/Button_news.gif" name="news" alt="News" class="imgover"></a>
			</div>
			<div class="nav-button">
				<a href="<?php echo $GLOBALS['path_domain_root']; ?>guide.html"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/Button_guide.gif" name="guide" alt="Guide" class="imgover"></a>
			</div>
			<div class="nav-button">
				<a href="<?php echo $GLOBALS['path_domain_root']; ?>worldforum"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/Button_forum.gif" name="forum" alt="Forum" class="imgover"></a>
			</div>
			<div class="nav-button">
				<a href="<?php echo $GLOBALS['path_domain_root']; ?>worldforum/index.php?action=register"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/Button_register.gif" name="register" alt="Register" class="imgover"></a>
			</div>
			<div class="nav-button">
				<a href="<?php echo $GLOBALS['path_domain_root']; ?>login.php"><img src="<?php echo $GLOBALS['path_domain_root']; ?>img/Button_login.gif" name="login" alt="Login" class="imgover"></a>
			</div>  
		</div>
<img src="<?php echo $GLOBALS['path_domain_root']; ?>img/space.gif" height="15">
<?php /*
if (($GLOBALS['user'] != false) && ($GLOBALS['constants']->USER_DONATED_CASH  & intval($GLOBALS['user']->access)))
{
require_once("thankyou.php");
}
else
{
require_once("donate.php");
}*/
?>
