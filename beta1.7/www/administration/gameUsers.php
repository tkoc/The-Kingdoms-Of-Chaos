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
require_once ("../scripts/globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_administration'] . "admin_all.inc.php");
require_once ($GLOBALS['path_www_administration'] . "Div.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "isLoggedOn.inc.php");
require_once ("requireLogon.inc.php");

if (!(intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_GAME_ADMIN) ) 
   die("tkoc.net");
if (isset($_POST['step']) && $_POST['step']=='updateprovince')
{
	$_GET['userID'] = $_POST['user_userID'];
	$newProvincename = $_POST['province_pname'];
	$newRulername	 = $_POST['province_rname'];
	$newGender = $_POST['province_gender'];
	$newStatus = $_POST['province_status'];
	$newKiID = $_POST['province_kiid'];
	$newPID = $_POST['province_pID'];
	
	$GLOBALS['database']->query(
	"UPDATE Province SET
		provinceName='$newProvincename', rulerName='$newRulername',
		gender='$newGender', status='$newStatus', kiID='$newKiID'
			WHERE pID='$newPID'");
	
}
$extra = "";
if (isset($_POST['step']) && $_POST['step']=='godlygift')
{
	$_GET['userID'] = $_POST['user_userID'];
	$giftText = $_POST['gift_text'];
	$giftGold = intval($_POST['gift_gold']);
	$giftFood = intval($_POST['gift_food']);
	$giftMetal= intval($_POST['gift_metal']);
	$giftPeasants = intval($_POST['gift_peasants']);
	$giftAcres = intval($_POST['gift_acres']);
	$giftReputation = intval($_POST['gift_reputation']);
	$giftInfluence = intval($_POST['gift_influence']);
	$giftMana = intval($_POST['gift_mana']);
	$giftMorale = intval($_POST['gift_morale']);
	$giftProtection = intval($_POST['gift_protection']);
	if (strlen($giftText) < 5)
		die ("Error: invalid input");
	
	$newsText = $giftText ." The gift was:<br>";
	$newsText .= ($giftGold==0) ? "":"$giftGold gc,";
	$newsText .= ($giftFood==0) ? "":"$giftFood food,";
	$newsText .= ($giftMetal==0) ? "":"$giftMetal metal,";
	$newsText .= ($giftPeasants==0) ? "":"$giftPeasants peasants,";
	$newsText .= ($giftAcres==0) ? "":"$giftAcres acres,";
	$newsText .= ($giftReputation==0) ? "":"modified your thievery rank,";
	$newsText .= ($giftInfluence==0) ? "":"$giftInfluence influence,";
	$newsText .= ($giftMana==0) ? "":"$giftMana mana,";
	$newsText .= ($giftMorale==0) ? "":"$giftMorale morale,";
	$newsText .= ($giftProtection==0) ? "":"$giftProtection hour(s) of protection.";
	$extra = "The news added was: <b>".$newsText . "</b><br>";
	require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
	$updateProvince = new Province($_POST['province_pID'], $GLOBALS['database']);
	$updateProvince->postNews($newsText);
	$GLOBALS['database']->query("
UPDATE Province SET
	gold=gold+$giftGold,food=food+$giftFood,metal=metal+$giftMetal,	peasants=peasants+$giftPeasants,
	 acres=acres+$giftAcres, reputation=reputation+$giftReputation,	influence=influence+$giftInfluence,
	 mana=mana+$giftMana, morale=morale+$giftMorale, protection=protection+$giftProtection
	 	WHERE pID='$_POST[province_pID]'");
	
}
if (isset($_POST['step']) && $_POST['step']=='updateuser')
{
	$newUserID = $_POST['user_userID'];
	$newName = $_POST['user_name'];
	$newEmail = $_POST['user_email'];
	$newCountry = $_POST['user_country'];
	$newDOB	= $_POST['user_dob'];
	$newNick = $_POST['user_nick'];
	$newPID = $_POST['user_pid'];
	$newAccess= intval ($_POST['user_access']);

	$GLOBALS['database']->query(
	"UPDATE User SET
		name='$newName', email='$newEmail', country='$newCountry',
		dob='$newDOB', nick='$newNick',pID='$newPID',access='$newAccess',
		allowUserUpdateNick='$_POST[user_allowupdatenick]',allowUserUpdateImage='$_POST[user_allowupdateimage]',
		allowUserUpdateSignature='$_POST[user_allowupdatesignature]'
			WHERE userID='$newUserID'");
	$_GET['userID'] = $_POST['user_userID'];
}


$search = '
<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<input type=hidden name=step value="searchprovince">
	<table cellpadding=5 cellspacing=0 border=0>
		<tr class="subtitle"><td colspan="2" class="TLRB">Search for Province: </td></tr>
		<tr><td class="L">Province:</td><td class="R"><input type="text" size="30" name="provinceName"></td></tr>
		<tr><td class="L">Ruler name:</td><td class="R"><input type="text" size="30" name="rulerName"></td></tr>
		<tr><td class="L">PID:</td><td class="R"><input type="text" size="5" name="pID"></td></tr>
		<tr><td colspan="2" class="BLR"><input type="submit" value="Search" name="submitSearchProvince"></td></tr>
	</table>
</form>

<form action="'.$_SERVER['PHP_SELF'].'" method="POST">
<input type=hidden name=step value="searchuser">
	<table cellpadding=5 cellspacing=0 border=0>
		<tr class="subtitle"><td colspan="2" class="TLRB">Search for User: </td></tr>
		<tr><td class="L">Username:</td><td class="R"><input type="text" size="30" name="SearchUsername"></td></tr>
		<tr><td class="L">Name:</td><td class="R"><input type="text" size="30" name="SearchName"></td></tr>
		<tr><td class="L">userID:</td><td class="R"><input type="text" size="5" name="userID"></td></tr>
		<tr><td class="L">email:</td><td class="R"><input type="text" size="30" name="searchemail"></td></tr>
		<tr><td colspan="2" class="BLR"><input type="submit" value="Search" name="submitSearchProvince"></td></tr>
	</table>
</form>

';

$list = '
<table cellpadding=5 cellspacing=0 border=0 width="90%">
	<tr class="subtitle"><td class="TLRB">User: </td><td class="TRB">Province:</td><td class="TRB">Action:</td></tr>';

if (isset($_POST['step']) && ($_POST['step']=='searchuser'))
{
	// cowboy code.. deluze..
	$sql = "SELECT userID,username, name, User.status, User.pID, access,provinceName,rulerName,kiID FROM User LEFT JOIN Province on User.pID=Province.pID";
	$cond1 = "";
	$cond2 = "";
	$cond3 = "";
	$cond4 = "";
	$where = false;
	if (isset($_POST['SearchName']) && (strlen($_POST['SearchName'])>0))
		$cond1 = "name LIKE '" . $_POST['SearchName'] . "'";
	if (isset($_POST['SearchUsername']) && (strlen($_POST['SearchUsername'])>0))
		$cond2 = "username LIKE '" . $_POST['SearchUsername'] . "'";
	if (isset($_POST['userID']) && (is_numeric($_POST['userID'])))
		$cond3 = "userID='".$_POST['userID']."'";
	if (isset($_POST['searchemail']) && (strlen($_POST['searchemail'])>0))
		$cond4 = "email LIKE '".$_POST['searchemail']."%'";

	if (strlen($cond1)>0)
		if ($where)
			$sql .= " OR " . $cond1;
		else
			{$sql.=" WHERE " . $cond1;$where=true;}
	if (strlen($cond2)>0)
		if ($where)
			$sql .= " OR " . $cond2;
		else
			{$sql.=" WHERE " . $cond2;$where=true;}
	if (strlen($cond3)>0)
		if ($where)
			$sql .= " OR " . $cond3;
		else
			{$sql.=" WHERE " . $cond3;$where=true;}
	if (strlen($cond4)>0)
		if ($where)
			$sql .= " OR " . $cond4;
		else
			{$sql.=" WHERE " . $cond4;$where=true;}
	$sql .= " ORDER BY name limit 30";
//	echo "sql $sql";
	$GLOBALS['database']->query($sql);
	if ($GLOBALS['database']->numRows())
		while ($a = $GLOBALS['database']->fetchArray())
		{
			if (intval($a['pID']) < 0)
				$p = "NONE";
			else
				$p = $a['rulerName']." in ".$a['provinceName']."(#".$a['kiID'].")";
			$list .= '<TR>
						<td class="L">'.$a['name']." (".$a['username'].')</td>
						<td>'.$p.'</td>
						<td class="R"><a href='.$_SERVER['PHP_SELF'].'?userID='.$a['userID'].'>Details</a></td>
					  </TR>';
		}
}
if (isset($_POST['step']) && ($_POST['step']=='searchprovince'))
{
	// cowboy code.. deluze..
	$sql = "SELECT userID,username, name, User.status, User.pID, access,provinceName,rulerName,kiID FROM Province LEFT JOIN User on User.pID=Province.pID";
	$cond1 = "";
	$cond2 = "";
	$cond3 = "";
	$where = false;
	if (isset($_POST['provinceName']) && (strlen($_POST['provinceName'])>0))
		$cond1 = "provinceName LIKE '" . $_POST['provinceName'] . "'";
	if (isset($_POST['rulerName']) && (strlen($_POST['rulerName'])>0))
		$cond2 = "rulerName LIKE '" . $_POST['rulerName'] . "'";
	if (isset($_POST['pID']) && (is_numeric($_POST['pID'])))
		$cond3 = "User.pID='".$_POST['pID']."'";

	if (strlen($cond1)>0)
		if ($where)
			$sql .= " OR " . $cond1;
		else
			{$sql.=" WHERE " . $cond1;$where=true;}
	if (strlen($cond2)>0)
		if ($where)
			$sql .= " OR " . $cond2;
		else
			{$sql.=" WHERE " . $cond2;$where=true;}
	if (strlen($cond3)>0)
		if ($where)
			$sql .= " OR " . $cond3;
		else
			{$sql.=" WHERE " . $cond3;$where=true;}
	$sql .= " ORDER BY provinceName limit 30";
	$GLOBALS['database']->query($sql);
	if ($GLOBALS['database']->numRows())
		while ($a = $GLOBALS['database']->fetchArray())
		{
			if (!(intval($a['userID'])>0))
				$p = "NONE - ZOMBIE";
			else
				$p = $a['name']." (".$a['username'] . ")";
			$list .= '<TR>
						<td class="L">'.$p.'</td>
						<td>'.$a['rulerName']." in ".$a['provinceName']."(#".$a['kiID'].")".'</td>
						<td class="R"><a href='.$_SERVER['PHP_SELF'].'?userID='.$a['userID'].'>Details</a></td>
					  </TR>';
		}
		
}


$list .= "</table>";
$html = '<table width="100%"><tr><td valign=TOP width="40%">' . $search .'</td><td valign=TOP width="60%">' . $list . "</td></tr></table>";

if (isset($_GET['userID']))
{
	$provinceData = "";
	$GLOBALS['database']->query("SELECT * FROM User where userID='$_GET[userID]'");
	$detailUser = new User("",$GLOBALS['database']);
	$detailUser->setUserData(($udata = $GLOBALS['database']->fetchArray()));
	if (intval($udata['recruitedBy'])<= 0)
		$recruit = "NONE";
	else
	{
		$GLOBALS['database']->query("SELECT username,userID FROM User where userID='$udata[recruitedBy]'");
		$tmp = $database->fetcharray();
		$recruit = '<a href="'.$_SERVER['PHP_SELF'].'?userID='.$udata['recruitedBy'].'">' . $tmp['username']. '</a>';
	}
	$allowsig = '<select name=user_allowupdatesignature class="form">';
	if ($udata['allowUserUpdateSignature']=='true')
	{
		$allowsig .= '<option value=true SELECTED>Yes</option>';
		$allowsig .= '<option value=false>No</option>';
	}
	else
	{
		$allowsig .= '<option value=true>Yes</option>';
		$allowsig .= '<option value=false SELECTED>No</option>';
	}
	$allowimage .= "</select>";

	$allowimage = '<select name=user_allowupdateimage class="form">';
	if ($udata['allowUserUpdateImage']=='true')
	{
		$allowimage .= '<option value=true SELECTED>Yes</option>';
		$allowimage .= '<option value=false>No</option>';
	}
	else
	{
		$allowimage .= '<option value=true>Yes</option>';
		$allowimage .= '<option value=false SELECTED>No</option>';
	}
	$allowimage .= "</select>";
	$allownick = '<select name=user_allowupdatenick class="form">';
	if ($udata['allowUserUpdateNick']=='true')
	{
		$allownick .= '<option value=true SELECTED>Yes</option>';
		$allownick .= '<option value=false>No</option>';
	}
	else
	{
		$allownick .= '<option value=true>Yes</option>';
		$allownick .= '<option value=false SELECTED>No</option>';
	}
	$allownick .= "</select>";

	// User data:
	$userData = '
<table cellpadding=5 cellspacing=0 border=0 width="90%">
	<tr class="subtitle">
		<FORM ACTION='.$_SERVER['PHP_SELF'].' METHOD=POST>
		<td class="TLRB" width=50%>UserInfo: </td>
		<td class="TRB" width=50%>Other info:</td>
	</tr>
	<TR>
		<TD class="BLR">
			<table>
				<tr><td>UserID</td><td>'.$detailUser->userID.'</td></tr>
				<tr><td>Username</td><td>'.$detailUser->username.'</td></tr>
				<tr><td>Name</td><td><input type=text name=user_name value="'.$detailUser->name.'"></td></tr>
				<tr><td>Email</td><td><input type=text size=30 name=user_email value="'.$detailUser->email.'"></td></tr>
				<tr><td>Country</td><td><input type=text name=user_country value="'.$detailUser->country.'"></td></tr>
				<tr><td>DOB</td><td><input type=text name=user_dob value="'.$detailUser->dob.'"></td></tr>
				<tr><td>ForumNick</td><td><input type=text size=30 name=user_nick value="'.$detailUser->nick.'"></td></tr>
				<tr><td>pID</td><td><input type=text name=user_pid value="'.$udata['pID'].'"></td></tr>
			</table>
		</td>
		<TD class="BR">
			<table>
				<tr><td>Created</td><td>'.$detailUser->created.'</td></tr>
				<tr><td>Status</td><td>'.$detailUser->status.'</td></tr>
				<tr><td>Last played age</td><td>'.$udata['lastPlayedAge'].'</td></tr>
				<tr><td>access</td><td><input type=text name=user_access value="'.$detailUser->access.'"></td></tr>
				<tr><td>Recruited by</td><td>'.$recruit.'</td></tr>
				<tr><td>Allow Signaturechange</td><td>'.$allowsig.'</td></tr>
				<tr><td>Allow Avatarchange</td><td>'.$allowimage.'</td></tr>
				<tr><td>Allow Nickchange</td><td>'.$allownick.'</td></tr>
			</table>
		</td>
	</TR>
	<tr>
		<td colspan=2>
		<input type=hidden name=user_userID value='.$detailUser->userID.'>
		<input type=hidden name=step value=updateuser>
		<input type=submit name=Update value=Update>
		</FORM>
		</td>
	</tr>
</table>
	';
	$provinceData = "No Province.";
	if ($udata['pID']>0)
	{
		require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
		$detailProvince = new Province($udata['pID'], $GLOBALS['database']);
		$detailProvince->getProvinceData();
		$provinceData = '
<table cellpadding=5 cellspacing=0 border=0 width="90%">
	<tr class="subtitle">
		<FORM ACTION='.$_SERVER['PHP_SELF'].' METHOD=POST>
		<td class="TLRB" width=33%>Province: </td>
		<td class="TRB" width=20%>Resource:</td>
		<td class="TRB" width=47>Godly Gift:</td>
	</tr>
	<TR>
		<TD class="BLR" VALIGN=TOP>
			<table>
				<tr><td>pID</td><td>'.$detailProvince->pID.'</td></tr>
				<tr><td>Race</td><td>'.$detailProvince->race.'</td></tr>
				<tr><td>Province Name</td><td><input type=text size=30 name=province_pname value="'.$detailProvince->provinceName.'"></td></tr>
				<tr><td>Ruler Name</td><td><input type=text size=30 name=province_rname value="'.$detailProvince->rulerName.'"></td></tr>
				<tr><td>Gender</td><td><input type=text name=province_gender value="'.$detailProvince->gender.'"></td></tr>
				<tr><td>status</td><td><input type=text name=province_status value="'.$detailProvince->status.'"></td></tr>
				<tr><td>Kingdom #</td><td><input type=text name=province_kiid value="'.$detailProvince->kiId.'"></td></tr>
				<tr><td colspan=2>
				<input type=hidden name=user_userID value='.$detailUser->userID.'>
				<input type=hidden name=province_pID value='.$detailProvince->pID.'>
				<input type=hidden name=step value=updateprovince>
				<input type=submit name=Update value=Update>
				</FORM>
				</td></tr>
				
			</table>
		</td>
		<TD class="BR" VALIGN=TOP>
			<table>
				<tr><td>Gold</td><td align=RIGHT>'.writeChaosNumber($detailProvince->gold).'</td></tr>
				<tr><td>Food</td><td align=RIGHT>'.writeChaosNumber($detailProvince->food).'</td></tr>
				<tr><td>Metal</td><td align=RIGHT>'.writeChaosNumber($detailProvince->metal).'</td></tr>
				<tr><td>Peasants</td><td align=RIGHT>'.writeChaosNumber($detailProvince->peasants).'</td></tr>
				<tr><td>morale</td><td align=RIGHT>'.writeChaosNumber($detailProvince->morale).'</td></tr>
				<tr><td>mana</td><td align=RIGHT>'.writeChaosNumber($detailProvince->mana).'</td></tr>
				<tr><td>Influence</td><td align=RIGHT>'.writeChaosNumber($detailProvince->influence).'</td></tr>
				<tr><td>Reputation</td><td align=RIGHT>'.writeChaosNumber($detailProvince->reputation).'</td></tr>
				<tr><td>Acres</td><td align=RIGHT>'.writeChaosNumber($detailProvince->acres).'</td></tr>
				<tr><td>Protection</td><td align=RIGHT>'.$detailProvince->protectionTime.'</td></tr>
				<tr><td>Networth</td><td align=RIGHT>'.writeChaosNumber($detailProvince->networth).'</td></tr>
			</table>
		</td>
		<TD class="BR" VALIGN=TOP>
			<FORM ACTION='.$_SERVER['PHP_SELF'].' METHOD=POST>

			<table>
				<tr><td colspan=4><b>Text that will appear in target news:</b></td></tr>
				<tr><td colspan=4><TEXTAREA COLS=60 ROWS=3 name=gift_text>The Gods have given you a gift for faithful service.</TEXTAREA></td></tr>
				<tr>
					<td>Gold</td><td align=RIGHT><input type=text name=gift_gold value="0"></td>
					<td>Metal</td><td align=RIGHT><input type=text name=gift_metal value="0"></td>
				</tr>
				<tr>
					<td>Food</td><td align=RIGHT><input type=text name=gift_food value="0"></td>
					<td>Peasants</td><td align=RIGHT><input type=text name=gift_peasants value="0"></td>
				</tr>
				<tr>
					<td>Acres</td><td align=RIGHT><input type=text name=gift_acres value="0"></td>
					<td>Reputation</td><td align=RIGHT><input type=text name=gift_reputation value="0"></td>
				</tr>
				<tr>
					<td>Influence</td><td align=RIGHT><input type=text name=gift_influence value="0"></td>
					<td>Mana</td><td align=RIGHT><input type=text name=gift_mana value="0"></td>
				</tr>
				<tr>
					<td>Morale</td><td align=RIGHT><input type=text name=gift_morale value="0"></td>
					<td>Protection (hours)</td><td align=RIGHT><input type=text name=gift_protection value="0"></td>
				</tr>
				<tr>
				<td colspan=4>
				<input type=hidden name=step value=godlygift>
				<input type=hidden name=user_userID value='.$detailUser->userID.'>
				<input type=hidden name=province_pID value='.$detailProvince->pID.'>
				<input type=submit name=Give value=Give>
				</td>
				</tr>
			</table>
			</FORM>
		</td>
	</TR>
	<tr>
		<td colspan=3>
		&nbsp;
		</td>
	</tr>
</table>
	';

	$html = $provinceData;
	}
	$html = $userData . $provinceData;
}

AdminTemplateDisplay("Useradmin @ tkoc.net",$user,$extra . $html);

?>
