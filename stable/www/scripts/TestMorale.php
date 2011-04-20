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

die();
	require_once("all.inc.php");
	require_once("Database.class.inc.php");
	require_once("Effect.class.inc.php");
	require_once("effect/EffectConstants.class.inc.php");
	require_once("isLoggedOn.inc.php");
	$effConst = new EffectConstants();
		
	$thSelf = $_SERVER['PHP_SELF'];
	
	$kingdom = 0;
	$prov = 0;
	if(isset($_POST['kingdom'])) {
		$kingdom = $_POST['kingdom'];
	}
	$sql = "select kiID, name from Kingdom";
	$database->query($sql);
	$option = "";
	while($data = $database->fetchArray()) {
		$option .= "\n\t\t\t<option value='".$data['kiID']."'";
		if($data['kiID'] == $kingdom) $option .= " selected";
		$option .= ">".$data['name']."</option>";
	}
	
	
	
	$form1 = "<form name='form1' method='post' action='$thSelf'>
  		<select name='kingdom'>$option
   		</select>
  		<input type='submit' name='Submit' value='Submit'>
	</form>";
	
	if(isset($_POST['prov'])) {
		$prov = $_POST['prov'];
	}
	$sql = "select pID, provinceName from Province where kiID=$kingdom";
	$database->query($sql);
	$option = "";
	while($data = $database->fetchArray()) {
		$option .= "\n\t\t\t<option value='".$data['pID']."'";
		if($data['pID'] == $prov) $option .= " selected";
		$option .= ">".$data['provinceName']."</option>";
	}
	
	$form2 = "<form name='form2' method='post' action='$thSelf'>
  		<select name='prov' size='6'>$option
  		</select>
		<input type='hidden' value='$kingdom' name='kingdom'>
  		<input type='submit' name='Submit2' value='Submit'>
	</form>";

	$moralePr = 0;
	$spID = 0;
	$effy = 0;
	
	if($prov != 0) {
		$database->query("select morale, spID from Province where pID=$prov");
		$data = $database->fetchArray();
		$moralePr = $data['morale'];
		$spID = $data['spID'];
		$effect = new Effect($database);
		$effy = $effect->getEffect($effConst->ADD_MORALE, $prov);
	}
	

	
	$moral = "<p>&nbsp;</p>
	<p>Race: $spID</p>
	<p>Effect: $effy</p>
	<p>Moral: $moralePr%, ".($moralePr*$effy)."%</p>"

	
?>

<html>
<body>
<?php
	echo "$form1";
	echo "$form2";
	echo "$moral";
?>
</body>
</html>