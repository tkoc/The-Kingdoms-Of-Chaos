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
/*************************************************************************
 * RaceBase is the base class for all races. It extends EffectBase to 
 * give each race their own specialities.
 *
 * Made by Øystein Fladby 31.05.2003
 *
 * Worklog:
 * Anders Elton: added functionallity for science.
 * Version: test
 *
 *************************************************************************/
if( !class_exists("RaceBase") ) {
require_once( $GLOBALS['path_www_scripts'] . "effect/EffectBase.class.inc.php" );
class RaceBase extends EffectBase {
	var $name;
	var $rID;
	var $description;
	var $picturePath = "race/img/";			// the path to the race pictures
	var $pictureFile = "dummy.gif";			// default picture
	var $maxScience = array('military' =>255, "infrastructure" => 255, "magic" => 255, "thievery" => 255);
	
	function RaceBase( $rID, $inName, $inDescription,$inMaxScience=false, $inPictureFile=false ) {
		$this->name = $inName;
		$this->rID = $rID;
		$this->pictureFile = $inPictureFile ? $inPictureFile : $this->pictureFile;
		$this->description = $inDescription;
		$this->maxScience = $inMaxScience ? $inMaxScience : $this->maxScience;
		
	}
		
	////////////////////////////////////////////
    // RaceBase::isRace
    ////////////////////////////////////////////
    // Function to check if this is a given race or
	// if the function recieves an array of races, if
	// this race is among the races in the array.
    // Returns:
    //    	true if this is the same as given race name
	//		false if not	
    ////////////////////////////////////////////
	function isRace( $checkRace ) {
		if( !$checkRace ) {						// 0 / false means all races
			return true;
		} else if( is_array( $checkRace ) ) {	// ie: 1 => "Orc", 2 => "human"
			foreach( $checkRace as $race ) {
				if( !strcasecmp( $this->name, $race ) ) {
					return true;			
				}
			}
		} else {								// ie: "orc"
			if( !strcasecmp( $this->name, $checkRace ) ) {
				return true;		
			}
		}
		return false;
	}
	
	////////////////////////////////////////////
    // RaceBase::getPictureFile
    ////////////////////////////////////////////
    // Function to get the path and name of the 
	// picture of this race
    // Returns:
    //    string with path and picture name
    ////////////////////////////////////////////
	function getPictureFile() {		
		$file = $this->picturePath.$this->pictureFile;
		if( file_exists( $file ) ) {
			return $file;
		} else {
			return "no picture found";
		}
	}	

	////////////////////////////////////////////
	// RaceBase::getStartScience
	////////////////////////////////////////////
	// If a race should start with a science, this function
	// should return the science level
	////////////////////////////////////////////
	function getStartScience() {
		return array(	'military' 	=> 0,
				'infrastructure'=> 0,
				'magic'		=> 0,
				'thievery' 	=> 0 );
	}
	
	////////////////////////////////////////////
	// RaceBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function getID() {
		return $this->rID;
	}
	function getName() {
		return $this->name;
	}
	function getDescription() {
		return $this->description;
	}
} // end class
} // end if class exists


?>
