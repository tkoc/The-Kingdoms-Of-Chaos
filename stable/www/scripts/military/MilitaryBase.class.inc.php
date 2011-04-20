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
	require_once("MilitaryConstants.class.inc.php");
	class MilitaryBase {
		var $mID = 1;					// the Military ID
		var $name = "NN";				// the Military name
		var $description = "";			// the description of this building
		var $type = 0;					// the type of this military
		var $ticks = 24;				// number of ticks to train this Military
		var $cost = NULL;				// array ( "gold" => amount, "metal" => amount, "food" => amount)
		var $strength = NULL;			// array ( "attack" => amount, "defense" => amount)
		var $targeting = NULL;			// array ( MilitaryConst-> => true/false, "T" => secondTarget, "T2" => thirdTarget)
		var $picturePath = "img/";		// the path to the Military pictures
		var $lessDead = NULL;

		function MilitaryBase( $inMid, $inName, $inDescription, $inType, $inTicks, $inCost, $inStrength) {
			$this->mID = $inMid;
			$this->name = $inName;
			$this->description = $inDescription;
			$this->type = $inType;
			$this->ticks = $inTicks;
			$this->cost = $inCost;
			$this->strength = $inStrength;
		}

		function scienceRequirements() {
			return array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);
		}

		function raceRequirements() {
			return "";
		}

		function housingRequirements() {
			return false;
		}

		function trainFrom() {
			return "Peasants";
		}

		function getPictureFile() {
			$file = $this->picturePath.$this->pictureFile();
			if( file_exists( $file ) ) {
				return $file;
			} else {
				return "no picture found";
			}
		}

		function pictureFile() {
			return "military.gif";
		}

		function getID() {
			return $this->mID;
		}
		function getName() {
			return $this->name;
		}
		function getCostGold() {
			return $this->cost['gold'];
		}
		function getCostMetal() {
			return $this->cost['metal'];
		}
		function getCostFood()  {
			return $this->cost['food'];
		}
		function getTicks() {
			return $this->ticks;
		}
		function getDescription() {
			return $this->description;
		}
		function getMilType() {
			return $this->type;
		}
	        function getAttack() {
		    return $this->strength['attack'];
		}
	        
	        function getDefense() {
		    return $this->strength['defense'];
		}

		function sciReqTxt() {
			$txt = "<ul><b>Knowledge req:</b><br><br><li>Infrastructure: 0<li>Military: 0<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
			return $txt;
		}
	    
	}
?>
