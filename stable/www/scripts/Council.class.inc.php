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

/* Council class
 *
 * This class handles the sciences for the different provinces.
 *
 * Author: Anders Elton 01.04.2003
 *

 */

$GLOBALS['PATH_TO_COUNCIL'] = "council/";

if( !class_exists("Council") ) {
$GLOBALS['council_static_data_set'] = false;
$GLOBALS['council_static_data'] = false;

class Council {
	var $database;
	var $provinceObj;
	var $council;

	function Council ($db, $provinceObj=false) {
		$this->database = $db;
		$this->loadCouncil();
		if ($provinceObj) {
		   $this->provinceObj = $provinceObj;
        }
	}

	function doTick() {
		
	}
	function hasCouncil ()
	{
		return ($this->provinceObj->council>0) ? true:false;
	}
	
	function GetAdvisor()
	{
		if ($this->hasCouncil())
			return $this->council[$this->provinceObj->council];
		else return NULL;
	}
	
	function handlePost () {
//		echo "step:".$_POST['step'];
		if (isset($_POST['step']))
		{
			switch ($_POST['step']) {
				case 'hire': 
					reset($_POST);
					while (list ($key, $val) = each ($_POST)) {
			    	if ($val=='Hire') $toHire = $key;
					}
				return $this->hireCouncil($toHire);
				case 'drop':
					return $this->dropCouncil();
				case 'thieveryrank';
					$this->database->query("Update Province set showThieveryRank='true' where pID='".$this->provinceObj->getpID()."'");
				return "Thievery rank is now public!";
	//			default: return "Invalid input.";
			}
		}
	}
	function dropCouncil()
	{
		$this->database->query("UPDATE Province set council=0 where pID=".$this->provinceObj->pID."");
		$this->provinceObj->council=0;
		return "Your councilor has been dropped.";
	}
	
	function assasinateCouncil($province)
	{
		if ($this->council[$this->provinceObj->council]->OnAssasination($province) == false)
		{
			$this->database->query("UPDATE Province set council=0 where pID=".$this->provinceObj->pID."");
			$this->provinceObj->council=0;
		}
	}
	
	function hireCouncil($coname) {
		//$toHire = $this->council[$coID];
		foreach ($this->council as $advisor) {
			$councilorname = str_replace (" ", "", $advisor->councilName);
			if (strcmp($councilorname, $coname) == 0) {
				$toHire = $advisor;
				$coID  = $advisor->coID;
				break;
			}
		}
		if ($toHire->raceOk($this->provinceObj->race) == false)
			return $toHire->councilName . " refuses to enter your service";
		if (!$toHire->wantsToBeHired($txt))
			return $txt;
		if ($this->provinceObj->useResource($toHire->costToHire['gold'],
											$toHire->costToHire['metal'],
											$toHire->costToHire['food'],
											$toHire->costToHire['peasants'])) {
			$this->database->query("UPDATE Province set council=$coID where pID=".$this->provinceObj->pID."");
			$this->provinceObj->council=$coID;
			return "<center>".$toHire->councilName . " has been hired!<br></center>";
			}
		return "Not enough resources";
	}
	function showCouncil() {
		$html = "<br>&nbsp;<center>".$this->provinceObj->getAdvisorName() . ", here is a list of available councilors.<br>";
		if (is_array($this->council)) {
			$html .= '<form action="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
			$html .= $GLOBALS['fcid_post'];
			$html .= '<input type=hidden name=step value=hire>';
			$html .= "<table cellpadding=5 cellspacing=0>";
			$html .= '<tr class="subtitle">
						<td class="TLRB" width=100>Name</td>
						<td class="TRB" width=450>Description</td>
						<td class="TRB" width=100>Upkeep</td>
						<td class="TRB" width=100>Action</td>
					</tr>';
			reset($this->council);
			foreach ($this->council as $advisor) {
				$councilorname = str_replace (" ", "", $advisor->councilName);
				if ($this->provinceObj==false || ($advisor->raceOk($this->provinceObj->race) == true) )
				$html .= '<tr>
							<td class="BL" valign=TOP>'.$advisor->councilName.'<BR>&nbsp;<BR><I>Cost to Hire:</I><BR>'.$advisor->showCost().'</td>
							<td class="BL">'.$advisor->councilHistory.'</td>
							<td class="BL">'.$advisor->showUpkeep().'</td>
							<td class="BLR"><input type="submit" name="'.$councilorname.'" value="Hire"></td>
						</tr>';
			}
			$html .= "</table>";
		} else $html .= "None.";
		$html .="</form>";
		$html .="</center>";
		return $html;	
	}
	function dropCouncilBox()
	{
		if ($this->provinceObj->council>0){
			// drop council box
			$html = '<form action="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
			$html .= $GLOBALS['fcid_post'];
			$html .= '<input type=hidden name=step value=drop>';
			$html .= '<input type=submit name="Drop councilor" value="Drop councilor">';
			$html .= '</form>';
		}
		else{
			$html = '<html><head><body> </body></head></html>';
		}
		return $html;	
	}
	function getCouncil() {
		$html = "";
		if (is_array($this->council)) {
			if ($this->provinceObj->council>0) {
				$this->council[$this->provinceObj->council]->setProvince($this->provinceObj);
				$html .= '<table width="100%" height="600" style="background:url(../img/Sitepictures/Council_background.jpg) black;background-repeat:no-repeat; background-position: right bottom"><tr><td width="400" height="20" valign="TOP" align="LEFT">';
				$html .= "<center>". $this->provinceObj->getAdvisorName() . ", here is a compiled list of your province.<br></center>";
				$html .= $this->council[$this->provinceObj->council]->showMood();
				$html .= '</td><td valign="TOP">&nbsp;&nbsp;&nbsp;<img src="../img/Topicbars/Council_text.gif"></td></tr><tr><td colspan=2 valign="TOP">';
				$html .= $this->council[$this->provinceObj->council]->getCouncil();
				$html .= "</td></tr></table>";
				return $html;
			}
			return "You have not hired any councilors!";
		}
		return "There are no councilors in the market.";
	}
	function getLeftImage ()
	{
		if ($this->provinceObj->council>0){
			$html = "<img src=". $this->council[$this->provinceObj->council]->getImage() . "><br>";
		}
		else{
			$html = '<html><head><body></body></head></html>';
		}
		return $html;
	}
	function loadCouncil () {
		if ($GLOBALS['council_static_data_set'] == false)
		{		
			unset($this->council);
			$this->council = array();
			if ($this->database->query("SELECT * FROM Council") && $this->database->numRows() ) {
				$GLOBALS['council_static_data_set'] = true;
				if (! class_exists("CouncilBase")) {				
					require_once($GLOBALS['PATH_TO_COUNCIL']."CouncilBase.class.inc.php");
				}
				while (($className = $this->database->fetchArray())) {
					if (!class_exists($className['className'])) {
						require_once($GLOBALS['PATH_TO_COUNCIL'].$className['className'] .".class.inc.php");
					}
					$ID = $className['coID'];
					//echo $className['className'];
					$GLOBALS['council_static_data'][$ID] = new $className['className'] ($ID);
				}
			} else {
				return false;
			}
		}
		$this->council = $GLOBALS['council_static_data'];
		return true;
	}
	function percentToFloat ($number) {
		return (float) 1 + ((float) $number/100.0);
	}
	function getCouncilEffect($FUNCTION_FROM_EFFECT_CONSTANTS, $pID) 
	{
		$this->database->query("SELECT council from Province where pID='$pID'");
		$res = $this->database->fetchArray();
		$modifier = 1.00;
		if ($res['council']>0) $modifier *= $this->percentToFloat(  $this->council[$res['council']]->$FUNCTION_FROM_EFFECT_CONSTANTS() );
//		echo "$res[council] $modifier" . $this->council[$res['council']]->councilName;
		return $modifier;
	}

} // end of class


}
?>