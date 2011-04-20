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
//* file: login.php
//*
//* Handles login for a user.
//* 
//* Author: Anders Elton
//*
//* History:
//*		01.08.04: Anders Elton.  Rewrote login system
//*		04.04.03: Anders Elton.  If you're logge in, use old session.
//**********************************************************************
require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "isLoggedOn.inc.php");
$user = $GLOBALS['user'];
$province = $GLOBALS['province'];
if ( ($GLOBALS['config']['status']=='Ended') && ( ($user!=false) && (!(intval($user->access) & $GLOBALS['constants']->USER_ADMIN) ))) {
  die ('
  <html>
  	<head>
   		<title>The Kingdoms of Chaos</title>	
		<link rel=stylesheet href="'.$GLOBALS['path_domain_root'].'" type="text/css">
	</head>
	<body bgcolor="#000000">
	<table width=100%>
	<tr>
	<td>
	<CENTER>
  The next age is beeing updated!  please be patient.  Check out the forum for more information.<br><br>
  <a href="'.$GLOBALS['path_domain_root'].'"><img src="'.$GLOBALS['path_domain_root'].'/img/logo/chaos_logo_main.jpg" border="0"></a>
  </CENTER>
  </td>
  </tr>
  </table>
  </body>
  </html>
  ');
}


if ($user == false) {

?>
<html>
<head>
<meta http-equiv="refresh"
content="1;url=<?php echo $GLOBALS['path_domain_root'];?>login.htm">
</head>
</html>
<?php
} else {
	if( $province == False ) {
                //cookie make test
                setcookie("TestCookie", "1234567890");
                //end cookie make test

	?>
<html>
<head>
<meta http-equiv="refresh"
content="1;url=<?php echo $GLOBALS['path_domain_root'];?>regProvince.php">
</head>
</html>
	<?php
	} else { // user pID != -1
		if (!isset($_POST['oldmenu'])) {
			setcookie("showmenu","false",time()+36000,"/",$GLOBALS['domain_name']);
			$_COOKIE['cId'] = $user->cId;
			$_COOKIE['showmenu'] = 'false';
			$GLOBALS['login']=true;
			$_SERVER['PHP_SELF'] = $GLOBALS['path_domain_script'] . "showProvince.php";
			if (isset($_REQUEST['nocookie']))
			{
				$_REQUEST['fcid'] = $user->cId;
				$GLOBALS['fcid'] = "?fcid=" . $_REQUEST['fcid'] . "&nocookie=true";
			}
			require( "showProvince.php");
			die();
		} else  {
		setcookie("showmenu","true",time()+36000,"/",$GLOBALS['domain_name']);
   ?>
<HTML><HEAD><TITLE><?php echo $GLOBALS['site_header']; ?></TITLE></HEAD>
<FRAMESET COLS=120,*>
<FRAME NAME=menu SRC="<?php echo $GLOBALS['path_domain_root'] . "menu.html";?>" MARGINWIDTH=0 MARGINHEIGHT=0>
<FRAME NAME=main SRC="<?php echo "showProvince.php?login=true" ?>" NORESIZE MARGINWIDTH=0 MARGINHEIGHT=0>
</FRAMESET>
<NOFRAMES>

</NOFRAMES>
</FRAMESET>
</BODY></HTML>   
   
   
<?php
		}
   } // end if user pID != -1
$database->shutdown();
}
?>