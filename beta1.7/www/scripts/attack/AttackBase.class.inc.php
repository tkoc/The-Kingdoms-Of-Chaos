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
	if(!class_exists("AttackBase")) {
		//require_once("all.inc.php");

	        $path = $GLOBALS['path_www_scripts'];
		require_once($path."military/MilitaryConstants.class.inc.php");

		
		class AttackBase {
			var $aID = 0;
			var $name = "";
			var $description;
			var $type;
			var $ticks;
			var $cost;
		        var $reqMil;
		        var $canUseMil = NULL;

			function AttackBase($inID, $inName, $inDescription, $inTicks, $inCost, $inReqMil, $inCanUseMil) {
				$this->aID = $inID;
				$this->name = $inName;
				$this->description = $inDescription;
				$this->ticks = $inTicks;
				$this->cost = $inCost;
			        $this->reqMil = $inReqMil;
			        $this->canUseMil = $inCanUseMil;
			        //echo "<pre>";
			        //print_r($this->canUseMil);
			        //echo "</pre>";
			}

			function getID() {
				return $this->aID;
			}

			function getName() {
				return $this->name;
			}

			function getDescription() {
				return $this->description;
			}

			function getToTicks() {
				return $this->ticks['to'];
			}

			function getBackTicks() {
				return $this->ticks['back'];
			}

			function getAttackTicks() {
				return $this->ticks['attack'];
			}

			function getCostGold() {
				return $this->cost['gold'];
			}

			function getCostMetal() {
				return $this->cost['metal'];
			}

			function getCostFood() {
				return $this->cost['food'];
			}

			function explain() {
				$desc = "Description<br><br>A description on special features in this attack. A description on special features in this attack. A
				description on special features in this attack. A description on special features in this attack.<br>";
				return $desc;
			}

			function explainBasic() {
				$attackBasics = "<br>The outcome of an attack is based on the attackpoints and the defensepoints of the armies involved in that
				particular fight. By reading the <a href='guide_military.php'>military</a> guide you find both the attackpoints and the defensepoints of the 
				different military units in the game. The total attackpoints of an army is the sum of the attackpoints of every military unit you send out in 
				an attack. To find the total attackforce of an army you have to do this calculation: 'total attackpoints * (current morale/100)  * bonuses earned by
				knowledge or other means'. To calculate the total defenseforce of an army you have to do this calculation: 'total defensepoints * 
				max( (0.9 + (current morale/100)), 1) * bonuses earned by knowledge or other means'.
				<br><br>The intension of an attack is to steal/grab acres/resources and/or ruin for another province. When attacking
				another province you will always lose some morale, but the defending province will not. Your military casualties will be somewhat based on how
				many defensepoints there are in your own army, so sending some defense soldiers might make your casualties smaller. 
				<br>&nbsp";
				return $attackBasics;
			}

			function canUse($milType) {
				if(is_array($this->canUseMil)) {
				       // echo " DEBUG: ARRAY CANUSE: ";
					if(isset($this->canUseMil[$milType])) {
				         //       echo "ISSET CANUSE OK - ";
						return $this->canUseMil[$milType];
					}
					else {
					   // echo "ISSET CANUSE ERR -";
					    return false;
					}
				}
				else return true;
			}
		    
		        // overloaded ? 
		        function scienceRequirements() {
			    return array('military' => 0, 'infrastructure' => 0, 'magic' => 0, 'thievery' => 0);
			}
		    
		        function militaryRequirements() {
			    $this->reqMil;
		        }

			//

			function update() {
				return 0;
			}

			function attack( $provinceMilitaryObject, $enemyMilitaryObject ) {
				return 0;
			}

			function handleAttack($milObj, $tID, $army, $attID, &$dbRef, $effect) {
				//Wanna do something when inserting attack ?
			}
		    
			function doTick($attID, $milObjAttacker, $milObjDefender, $newsProvince, $newsKingdom, $buildings, $pID, $targetID, $effect) {
			    return true;
			}
			
			function sciReqTxt() {
				$txt = "<br><ul>Knowledge req:<br><br><li>Infrastructure: 0<li>Military: 0<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
				return $txt;
			}
		    
			// END FUNCTIONS
		}

	}
?>