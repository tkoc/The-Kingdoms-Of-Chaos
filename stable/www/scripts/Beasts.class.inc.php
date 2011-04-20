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

/* Beast class
 *
 *
 * Author: Anders Elton 03.01.2007
 *

 */

$GLOBALS['PATH_TO_BEASTS'] = "beasts/";

if( !class_exists("Beasts") ) {
$GLOBALS['beasts_static_data_set'] = false;
$GLOBALS['beasts_static_data'] = false;

class Beasts {
	var $database;
	var $provinceObj;
	var $beasts;

	function Beasts (&$db, $provinceObj=false) {
		$this->database = &$db;
		$this->loadBeasts();
		if ($provinceObj) {
		   $this->provinceObj = $provinceObj;
        	}
	}

	function doTick() {
		// load ALL completed beasts.
		$this->database->query("select ID from Beast where (Beast.goldLeft=0 AND Beast.foodLeft=0 AND Beast.metalLeft=0)");
		while (($res = $this->database->fetchArray()))
		{
			$beastlist[] = $res['ID'];
		}

		if (empty($beastlist) == false)
		{
			foreach ($beastlist as $res)
			{
				$beast = $this->GetBeastFromID($res);
				$beast->doTick($this->database);
			}
		}

		$this->database->query("SELECT * from Beast where strength<=0");
		
		while (($res = $this->database->fetchArray()))
		{
			$ab = $this->GetBeastFromID($res['ID']);
			require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
			$news = new News($GLOBALS['database']);
			$news->postNews("The " . $ab->GetName() . " has been slayed!",$ab->kingdomID, $news->SYMBOL_BEAST);
		}
		$this->database->query("DELETE FROM Beast where strength<=0");
		
		$this->database->query("UPDATE Beast set RemainTick=(RemainTick-1) where (Beast.goldLeft=0 AND Beast.foodLeft=0 AND Beast.metalLeft=0)");


		// halve strength
		$this->database->query("Update Beast set strength=strength/2 where RemainTick=0");
		
		$this->database->query("SELECT * from Beast where RemainTick=0");
		while (($res = $this->database->fetchArray()))
		{
			$ab = $this->GetBeastFromID($res['ID']);
			require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
			$news = new News($GLOBALS['database']);
			// find new kingdom.
			$this->database->query("SELECT kiID from Kingdom where kiID!=".$res['kiID']." order by RAND() limit 1");
			$kd = $this->database->fetchArray();
			if ($kd)
			{
				$this->database->query("Update Beast set kiID=".$kd['kiID'].", senderID=".$res['kiID'].", RemainTick=100 where ID=". $res['ID']."");
				$news->postNews("The " . $ab->GetName() . " has left our kingdom!",$ab->kingdomID, $news->SYMBOL_BEAST);
				$news->postNews("A " . $ab->GetName() . " is attacking our kingdom!  its coming from #". $ab->kingdomID ."!",$kd['kiID'], $news->SYMBOL_BEAST);
			}
			else
			{
				$news->postNews("The " . $ab->GetName() . " has left our kingdom!",$ab->kingdomID, $news->SYMBOL_BEAST);
			}
		}
		$this->database->query("DELETE FROM Beast where RemainTick=0");

	}
	
	function handlePost () {
	}
	
	function getLeftImage ()
	{
		return '../img/Leftpictures/Council_leftpicture.jpg';
	}
	function loadBeasts () {
		if ($GLOBALS['beasts_static_data_set'] == false)
		{		
			unset($this->beasts);
			$this->beasts = array();
			if ($this->database->query("SELECT * FROM Beasts") && $this->database->numRows() ) {
				$GLOBALS['beasts_static_data_set'] = true;
				if (! class_exists("BeastBase")) {				
					require_once($GLOBALS['PATH_TO_BEASTS']."BeastBase.class.inc.php");
				}
				while (($className = $this->database->fetchArray())) {
					if (!class_exists($className['className'])) {
						require_once($GLOBALS['PATH_TO_BEASTS'].$className['className'] .".class.inc.php");
					}
					$ID = $className['bID'];
					//echo $className['className'];
					$GLOBALS['beasts_static_data'][$ID] = new $className['className'] ($ID);
				}
			} else {
				return false;
			}
		}
		$this->beasts = $GLOBALS['beasts_static_data'];
		return true;
	}

       function percentToFloat ($number) {
                return (float) 1 + ((float) $number/100.0);
        }

	function getBeastsEffect($FUNCTION_FROM_EFFECT_CONSTANTS, $pID) 
	{
		$this->database->query("select Beast.* from Beast LEFT JOIN Province on Beast.kiID=Province.kiID where pID='$pID' AND (Beast.goldLeft=0 AND Beast.foodLeft=0 AND Beast.metalLeft=0)");
		$modifier = 1.00;
		while (($res = $this->database->fetchArray()))
		{
			if ($res['bID']>0) $modifier *= $this->percentToFloat(  $this->beasts[$res['bID']]->$FUNCTION_FROM_EFFECT_CONSTANTS() );
//		echo "$res[council] $modifier" . $this->council[$res['council']]->councilName;
		}
		return $modifier;
	}

	function GetEnemyBeasts()
	{
		$ret = NULL;
		$kiID = $this->provinceObj->kiId;
		$this->database->query("select * from Beast where kiID='$kiID' AND (Beast.goldLeft=0 AND Beast.foodLeft=0 AND Beast.metalLeft=0)");
		while ($res = $this->database->fetchArray())
		{
			$beast = $this->beasts[$res['bID']];
			$beast->SetData($res);
			$ret[] = $beast;
		}
		return $ret;
	}

	function CancelProject($beastID)
	{
		$this->database->query("delete from Beast where ID='$beastID'");
	}
	function AttackBeast($beastID, $attackpower)
	{
		$damage = 0;
		if ($beastID==0)
		{
			print_r($_POST);
			die("BEAST ID IS 0");
		}
		$beast = $this->GetBeastFromID($beastID);

		$this->database->query("select SUM(Province.networth) as nw from Province where kiID='$beast->kingdomID' and status='Alive'");
		$res = $this->database->fetchArray();
		$nw = $res['nw'];


		if ($beast != NULL)
		{
			$hp = $nw * $beast->attacktodamage;
			echo "hp : $hp, nw: $nw";
			if ($attackpower > $hp)
			{
				// always do 1% damage at this point, but lets randomize stuff a bit.
				$damage = $attackpower / $hp;
				
				if (rand(1,10) > 7)
				{
					$flux = rand(1,$damage);
					if (rand(0,1) == 1) $flux*=-1;
					$damage += $flux;
				}
				$damage = round($damage);
				// hardcap at 10%..
				if ($damage > 10)
					$damage = 10;
				if ($damage < 1)
					$damage = 1;

				$this->database->query("UPDATE Beast set strength=GREATEST((strength-$damage),0) where ID=$beastID");
				//$this->database->showDebugData();
				if ($beast->strength - $damage <= 0)
				{
					require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
					$news = new News($GLOBALS['database']);
					$news->postNews("The " . $ab->GetName() . " has been slayed by ".$GLOBALS['province']->provinceName ."!",$ab->kingdomID, $news->SYMBOL_BEAST);
					$this->database->query("delete from Beast where ID=$beastID");
				}
			}
		}
		return $damage;
	}
	function StartBeastProject($target, $beastID)
	{
		if ($beastID==0)
		{
			print_r($_POST);
			die("BEAST ID IS 0");
		}
		$this->database->query("select count(*) as antall from Province where kiID='$target' and status='Alive'");
		$res = $this->database->fetchArray();
		$thecount = $res['antall'];
		if ($thecount== 0)
		{
			$target = $this->provinceObj->getKiId();
		}

		$this->database->query("select SUM(Province.networth) as nw from Province where kiID='$target' and status='Alive'");
		$res = $this->database->fetchArray();
		$nw = $res['nw'];

		$beast = $this->beasts[$beastID];

		$this->database->query("INSERT INTO Beast (kiID, bID, senderID, goldLeft, metalLeft, foodLeft) values 
					('$target',
					 '$beastID', 
					'" .$this->provinceObj->getKiId() ."',
					'". ($beast->goldCost * $nw) ."',
					'". ($beast->metalCost * $nw) ."',
					'". ($beast->foodCost * $nw) ."')");
		return $this->GetBeastFromID($this->database->lastInsertId());
	
	}
	
	function GetBeastFromID($beastID)
	{
		$this->database->query("select * from Beast where ID='$beastID'");
		
		$res = $this->database->fetchArray();
		$beast = $this->beasts[$res['bID']];
		if ($beast == NULL)
		{
			die("no beast with id => $beastID");
		}
		$beast->SetData($res);
		return $beast;
	}
	// gets both completed and uncompleted!
	function GetOurBeasts()
	{
		$ret = NULL;
		$kiID = $this->provinceObj->kiId;
		$this->database->query("select * from Beast where senderID='$kiID'");
		while ($res = $this->database->fetchArray())
		{
			$beast = $this->beasts[$res['bID']];
			$beast->SetData($res);
			$ret[] = $beast;
		}
		return $ret;
	}

} // end of class


}
?>