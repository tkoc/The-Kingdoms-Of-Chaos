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

if(!class_exists("TrigEffect")) {
  $GLOBALS['triggeredeffect_static_data'] = false;
  $GLOBALS['triggeredeffect_static_data2'] = false;
  class TrigEffect {
    var $database;
    var $pID;
    var $effects;
    var $effectTypes = NULL;
    var $effectTypes2;
    var $pathTo;
    var $pidEff = NULL;
    /*
    Constructor
    */
    function TrigEffect(&$db) {
      $this->database = $db;
      $this->pathTo = "trigeff/";
    }
    
    /*
    getEffect()
    */
    function getEffect($EFFECT_CLASS_FUNCTION, $pID) {
      $this->pID = $pID;
      $mod = 1;
      $this->getEffects();
      $i = 0;
      foreach($this->effects as $effObj) {
	$mod *= ((float)1 + (float)($effObj->$EFFECT_CLASS_FUNCTION()/100) );
      }
      if($mod > 2) {
        $mod = 2;
      }
      return $mod;
    }
    
    /*
    getEffects()
    */
    function getEffects($force=false) {
      $sql = "select teID, effID, pID, strength, duration from TrigEffect where pID={$this->pID} and duration>0";
      $this->getEffectTypes();
      if( ($force == true) || (!isset($this->pidEff["TrigEffect".$this->pID])) ) {
	$result = $this->database->query($sql);
	while($data = $this->database->fetchArray($result)) {
	  $effID = $data['effID'];
	  $this->effects[] = $this->effectTypes[$effID];
	}
	$this->pidEff["TrigEffect".$this->pID] = $this->effects;
      }
      else $this->effects = $this->pidEff["TrigEffect".$this->pID];
    }
    
    /*
    getEffectTypes
    */
    function getEffectTypes() {
      if(is_null($this->effectTypes)) {
	$sql = "select * from TrigEffectType";
	$tmpArray = NULL;
	$tmpArray2 = NULL;
	$result = $this->database->query($sql);
	while($data = $this->database->fetchArray($result)) {
	  if(!class_exists($data['className'])) {
	    require_once($this->pathTo.$data['className'].".class.inc.php");
	  }
	  $effID = $data['effID'];
	  $effObj = new $data['className']($effID);
	  $tmpArray[$effID] = $effObj;
	  $tmpArray2[$effObj->getTP()] = $effObj;
	}
	$this->effectTypes = $tmpArray;
	$this->effectTypes2 = $tmpArray2;
	//$GLOBALS['triggeredeffect_static_data'] = true;
	//$GLOBALS['triggeredeffect_static_data2'] = $tmpArray2;
      }
      //else {
        //$this->effectTypes = $GLOBALS['triggeredeffect_static_data'];
	//$this->effectTypes2 = $GLOBALS['triggeredeffect_static_data2'];
      //}
    }
    
    /*
    triggEffect()
    */
    function triggEffect($effType, $pID=0, $strength=1) {
      //$this->getEffectTypes();
      //$effObj = $this->effectTypes2[$effType];
      //echo "Trigger";
      //$effID = $effObj->getID();
      //echo $effID;      
      //echo "hmm";
      //$duration = $effObj->getDuration();
      
      $sql = "INSERT INTO TrigEffect (effID, kiID, pID, strength, duration) values(1, 0, $pID, 1, 5)";
      
      $this->database->query($sql);
//      echo $this->database->error();      
    }

    function doTick() {
	$sqlTick = "update TrigEffect set duration=duration-1 where duration>0";
	$this->database->query($sqlTick);
    }
  }
}
?>