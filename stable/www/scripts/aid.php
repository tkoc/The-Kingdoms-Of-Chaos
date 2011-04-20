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
/*
 *
 *
 *  Anders Elton: Gjorde kompatibelt med php 4.1.x
 * �ystein patcha muligheten for � stjele ressurser innad i kinged�mmet ved � sende minusressurser!!!
 */


require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once($GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php");
$actionLogger = new ActionLogger($GLOBALS['database']);
$aidMsg = "";
$province = $GLOBALS['province'];
$BANDITS_STEAL = rand(10,20);
// effect class!
$effectObj = new Effect( $database );
$BANDITS_STEAL *= $effectObj->getEffect($GLOBALS['effectConstants']->ADD_RESOURCE_LOSS,$province->pID);
$ROBBEDAID = 1.0 - ($BANDITS_STEAL/100.0);

//echo "$ROBBEDAID";
// need space *air* on output here..
$html = '<table border=0 width="100%">
<tr><td>&nbsp</td></tr>
	<tr><td ALIGN="CENTER">
<table width=80%><tr><td><CENTER>
'.$province->getAdvisorName().', We can trade our excess resources to other provinces in our kingdom and perhaps get something in return for them. It is not perfectly safe to send a trade caravan as bandits in the countryside would like to get their hands on a share of the goods.
Keep in mind that sending away large portions of resources without getting anything in return may anger our peasants. 
</center>
</td></tr></table></td></tr>';


// actual aid is sendt here.
if (isset($_POST['step'])) {
	switch ($_POST['step']) {
		case 'sendAid':
			//break;
			$targetProvince = new Province($_POST['selectProvince'],$database);
			$targetProvince->getProvinceData();
			if ($targetProvince->isProtected()==true) {
				$aidMsg .=$province->getAdvisorName().", There isn't sufficient military waiting to protect the caravan, it will never survive the mountains";
				break;
			}
			if ((!is_numeric($_POST['aidFood'])) || ($_POST['aidFood'] < 0 )) $_POST['aidFood']="0";
			if ((!is_numeric($_POST['aidGold'])) || ($_POST['aidGold'] < 0 )) $_POST['aidGold']="0";
			if ((!is_numeric($_POST['aidMetal'])) || ($_POST['aidMetal'] < 0 )) $_POST['aidMetal']="0";
			if ((!is_numeric($_POST['aidPeasants'])) || ($_POST['aidPeasants'] < 0)) $_POST['aidPeasants']="0";
	
			$sendFood = (int) ($_POST['aidFood']* $ROBBEDAID);
			$sendGold = (int) ($_POST['aidGold']* $ROBBEDAID);
			$sendMetal = (int) ($_POST['aidMetal']* $ROBBEDAID);
			$sendPeasants = (int) ($_POST['aidPeasants']* $ROBBEDAID);
			// check if the one sending has the resources.
			
			if ($_POST['aidPeasants']>$province->peasants || $_POST['aidGold']>$province->gold ||
				$_POST['aidMetal']>$province->metal || $_POST['aidFood']>$province->food){
				$aidMsg .= "You can only send resources you have!";
				break;
			}
			$database->query("UPDATE Province set gold=gold-$_POST[aidGold], food=food-$_POST[aidFood], 
			metal=metal-$_POST[aidMetal], peasants=peasants-$_POST[aidPeasants] where pID=$province->pID");
	
			// add resources to target.
			$database->query("UPDATE Province set gold=gold+$sendGold, food=food+$sendFood, 
			metal=metal+$sendMetal, peasants=peasants+$sendPeasants where pID=$_POST[selectProvince]");
			
			// post news
			$targetProvince->postNews("A trade caravan from $province->provinceName has arrived with $sendGold gc, $sendFood 
			food, $sendMetal metal and $sendPeasants peasants.");
	
			$aidMsg .= "A trade caravan was sent to $targetProvince->provinceName! Unfortunately it was robbed, and $sendGold gc, $sendFood 
			food, $sendMetal metal and $sendPeasants peasants arrived.";
			
			$actionLogger->log($actionLogger->TRADE, $province->getpID(), $targetProvince->getpID(),$actionLogger->NOVALUE,true);
			
		break;
	}
}


$html .= '
<tr>
<td align="CENTER" valign="top">'. $aidMsg .'
</td>
</tr>
<tr>
<td align="CENTER" valign="top">
<table><tr><td>
<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post']; 
$html .= '<input type=hidden name="step" value="sendAid" class="form">
<table>
<tr><td colspan=2><select name=selectProvince class="form">';
if ($database->query("SELECT provinceName, pID from Province where  kiID='".$province->kiId."'")  && $database->numRows()) {
	while (($item=$database->fetchArray())){
		if ($province->pID != $item['pID']) {
        	$html .= "<option";
//       	 	if ($selectProvince==$item['pID']) $html .= " SELECTED";
        	$html .=" value=\"$item[pID]\">$item[provinceName]</option>";
		}
	}
}
$html .='</select>
</td></tr>
<tr><td>&nbsp</td></tr>
<tr><td>Food:</td><td><input type=text name=aidFood class="form" SIZE=6></td></tr>
<tr><td>Metal:</td><td><input type=text name=aidMetal class="form" SIZE=6></td></tr>
<tr><td>Gold:</td><td><input type=text name=aidGold class="form" SIZE=6></td></tr>
<tr><td>Peasants:</td><td><input type=text name=aidPeasants class="form" SIZE=6></td></tr>
<td><td colspan=2>
<input type=submit name="send" value="send" class="form">
</td></tr>
</table>
</form>
</td></tr>
</table>
</td></tr>
</table>
';
templateDisplay($province,$html,"../img/Cornerpictures/Trade_picture.jpg","");
?>