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

/*
 *
 *
 * Changelog: 17.07.03
 *  Anders Elton: Gjorde kompatibelt med php 4.1.x
 */


require_once ("globals.inc.php");
$GLOBALS['game_debug'] = true;
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Thievery.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");

//templateDisplay($province,"This page is beeeing updated, please be patient. (it will come online again before the game officially starts)");
//die();

$html = "";

if (!isset($_POST['kingdom']))
	$kingdom = $province->getKiId();

$thievery = new Thievery (&$database,$province->getpID());
if (isset($_POST['step']))
	switch ( $_POST['step']) {
		case 'selectKingdom':
			if (isset($_POST['selectKingdom']))
				$kingdom = $_POST['selectKingdom'];
		break;
		case 'sendOut':
			$html .= $thievery->thieveryEffect($_POST['selectOperation'],$_POST['selectProvince']) . "<br>&nbsp";
		break;
	}

// need space *air* on output here..
$html .= '<table border=0 width="100%" style="background:url(../img/thback.jpg) black;background-repeat:no-repeat; background-position: 
right top">
	<tr><td ALIGN="RIGHT">
<table width=80%><tr><td><CENTER>
'.$province->getAdvisorName().', 
Although brute force is neccesary at times, i present to you some other 
means to .. harm people you do not like. You have an influence over several bandit chiefs located around the world that gives you, 
'.$province->getShortTitle() .' , the opportunity to send out minions to do your jobs.  Each time you do an operation your influence 
will drop, and for each day you rest your influence will rise again.  Remember that your success both depend on the number of thieves you have and your influence level.
<br>&nbsp</center>
</td></tr></table>
';

$html .= '<center>
</td><td width="400" valign=TOP><img src="../img/space.gif" width="400" height="150">
</td><tr>
<td align="right" valign="top">
<form action="'.$_SERVER['PHP_SELF'].'" method=POST>
<input type=hidden name="step" value="selectKingdom" class="form">
<input type=hidden name="kingdom" value="'. $kingdom .'" class="form">

<table border=0>
<tr>
<td><img src="../img/space.gif" width="200" height="40"></td>
<td align="right"><img src="../img/space.gif" width="70" height="40"> </td>
<td align="rigth"><img src="../img/space.gif" width="100" height="40"></td>
</tr>
<tr>
<td align="right">our influence:</td><td align="right">'.$province->influence.'%</td>
<td><img src="../img/space.gif" width="100" height="40"></td>
</tr>
<tr>
<td align="right">Input Kingdom:</td>
<td align="right"><input type=text size=4 name=selectKingdom class="form" title="Enter a kingdom number"></td>
<td>&nbsp </td>
</tr>
<tr>
<td align="right">&nbsp </td>
<td align="right"><input type=submit name="change" value="Change" class="form" title="Change to selected kingdom"></form></td>
<td>&nbsp </td>
</tr>
<tr>
<td align="right"><FORM ACTION="'.$_SERVER['PHP_SELF'].'" method=POST>Select Province:</td>
<td align="left" valign="middle" colspan=2><select name=selectProvince class="form">
';
$database->query("SELECT provinceName, pID from Province where  kiID='$kingdom'");
while (($item=$database->fetchArray())){
	$html .= "<option";
	if (isset($_POST['selectProvince']) && ($_POST['selectProvince']==$item['pID'])) $html .= " SELECTED";
	$html .=" value=\"$item[pID]\">$item[provinceName]</option>";
}
$html .='	</select>
</td>
</tr>
<tr>
<td align="right">Select operation:</td><td colspan=2>
';
$html .= $thievery->getSelectBox();
$html .= '
	</td>

   </tr>
  <tr><td><img src="../img/space.gif" height="50" width="10"> </td>
<td colspan="2" align="center">
	  <input type=HIDDEN name=step value="sendOut">
          <input type=submit value="Send out!" class="form" title="Send out you thieves">
          <input type=hidden name="kingdom" value='.$kingdom.'>

</FORM>
</td></tr>
<tr>
<td colspan=3><img src="../img/space.gif" height="200" width="50"></td>
</tr>
</table>
</td><td>&nbsp</td></tr></table>';


templateDisplay($province,$html);
?>