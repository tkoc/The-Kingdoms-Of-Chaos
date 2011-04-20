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
<?
session_start();
require("private/konf.nohack.php");
//error_reporting(E_ALL);
?>
<HTML>
<HEAD>
<SCRIPT language=JavaScript type=text/javascript>
	<!--

	function LmOver(elem, clr)
	{
		elem.className = clr;
		elem.style.cursor = 'hand';
	}

	function LmOut(elem, clr)
	{
		elem.className = clr;
	}

	function LmDown(elem, clr)
	{
		elem.className = clr;
	}

	function LmUp(path)
	{
		location.href = path;
	}

	//-->
</SCRIPT>
<script language="javascript" type="text/javascript">
<!--
function copy_clip(navn) {
var meintext = document.getElementById(navn).innerHTML;   
 if (window.clipboardData) 
	{
	window.clipboardData.setData("Text", meintext);
	
	}
	else if (window.netscape) 
	{ 
	
	netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
	
	var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
	if (!clip) return;
	
	var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
	if (!trans) return;
	
	trans.addDataFlavor('text/unicode');
	
	var str = new Object();
	var len = new Object();
	
	var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
	
	var copytext=meintext;
	
	str.data=copytext;
	
	trans.setTransferData("text/unicode",str,copytext.length*2);
	
	var clipid=Components.interfaces.nsIClipboard;
	
	if (!clip) return false;
	
	clip.setData(trans,null,clipid.kGlobalClipboard);
	
	}
}
//-->
</script> 
<TITLE>The Kingdom of Chaos statistics</TITLE>
<LINK href="style.css" type=text/css rel=stylesheet>
</HEAD>
<BODY>

<?
include_once('topstat.php.inc');
?>

<BR><BR>

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TR>
	 <TD vAlign=top>

<?
include_once('meny.php.inc');
?>
	 </TD>
	 <TD vAlign=top width="100%">
		<DIV align=center>
		<TABLE width="85%">
		  <TR>
			 <TD>
				<TABLE style="MARGIN-BOTTOM: 5px" cellSpacing=0 cellPadding=0 width="100%" border=0>
				  <TR>
					 <TD width="50%">
<?


$military = array (
	"Dwarf"  => array("Warriors","Axe Men","Dwarven Defender","Iron Breakers","Bandits","Magicians"),
	"Human" => array("Recruits","Legions","Pikemen","Paladins","Thieves","Wizards"),
	"Elf"  => array("Recruits","Rangers","Archers","Pegasus Riders","Thieves","Magicians"),
	"Orc"  => array("Goblins","Wolf Riders","Black Orcs","Trolls","Bandits","Shamans"),
	"Undead"  => array("Slaves","Skeletons","Zombies","Vampires","Ghosts","Liches")
);

$milstrength = array (
	"Warriors"  => array("DP" => "2", "OP" => "1"),
	"Axe Men" => array("DP" => "2", "OP" => "3"),
	"Dwarven Defender"  => array("DP" => "4", "OP" => "1"),
	"Iron Breakers"  => array("DP" => "3", "OP" => "5"),
	"Slaves"  => array("DP" => "1", "OP" => "1"),
	"Skeletons" => array("DP" => "1", "OP" => "4"),
	"Zombies"  => array("DP" => "3", "OP" => "1"),
	"Vampires"  => array("DP" => "5", "OP" => "4"),
	"Recruits"  => array("DP" => "1", "OP" => "1"),
	"Legions" => array("DP" => "1", "OP" => "3"),
	"Pikemen"  => array("DP" => "3", "OP" => "1"),
	"Paladins"  => array("DP" => "5", "OP" => "5"),
	"Rangers"  => array("DP" => "1", "OP" => "4"),
	"Soldiers" => array("DP" => "1", "OP" => "1"),
	"Archers"  => array("DP" => "3", "OP" => "1"),
	"Pegasus Riders"  => array("DP" => "6", "OP" => "2"),
	"Goblins"  => array("DP" => "2", "OP" => "1"),
	"Wolf Riders" => array("DP" => "1", "OP" => "3"),
	"Black Orcs"  => array("DP" => "3", "OP" => "1"),
	"Trolls"  => array("DP" => "4", "OP" => "6"),
	"Bandits"  => array("DP" => "1", "OP" => "1"),
	"Shamans"  => array("DP" => "1", "OP" => "1"),
	"Magicians"  => array("DP" => "1", "OP" => "1"),
	"Ghosts"  => array("DP" => "1", "OP" => "1"),
	"Liches"  => array("DP" => "1", "OP" => "1"),
	"Thieves"  => array("DP" => "1", "OP" => "1"),
	"Wizards"  => array("DP" => "1", "OP" => "1")
);
$error;

if ($scan) {

	if(eregi("([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})",$scan,$parstick)) {
		$thetick = 0;
		$thetick += ($parstick[7]-1)*12*24*2;
		$age = $parstick[7];
		$thetick += ($parstick[5]-1)*12*24;
		$year = $parstick[5];
		$thetick += ($parstick[3]-1)*24;
		$month = $parstick[3];
		$thetick += ($parstick[1]-1);
		$day = $parstick[1];

		if (eregi("([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})(.*)(discovered :|era+)(.*)\(#([0-9]{1,3})\)(.*)Ruler:(.*)Race:(.*)Gender:(.*)Knowledge:(.*)%(.*)Gold:(.*)gc(.*)Metal:(.*)kg(.*)food:(.*)kg(.*)Acres:(.*)Peasants:(.*)\((.*)%\)(.*)Morale:(.[^%]*)%(.*)", $scan, $parsed)) {
			/* Variables */
			$race = trim($parsed[15]);
			$province = trim($parsed[11]);
			$ruler = trim($parsed[14]);
			$gender = trim($parsed[16]);
			$kingdom = trim($parsed[12]);
			$science = trim($parsed[17]);
			$gold = str_replace(",", "", trim($parsed[19]));
			$food = str_replace(",", "", trim($parsed[23]));
			$metal = str_replace(",", "", trim($parsed[21]));
			$acres = str_replace(",", "", trim($parsed[25]));
			$peasants = str_replace(",", "", trim($parsed[26]));
			$morale = trim($parsed[29]);
			$ppeasants = trim($parsed[27]);
	      $scan = preg_replace("/\s{2,}/"," ", $parsed[30]);	
	      $scan = preg_replace("/\t/","", $scan);	

      	/* Networth calculation */
	      $sciscore = (round($science/5.8) * 1000);
	      $goldscore = (str_replace(",", "", $gold) * 0.001);
	      $metscore = (str_replace(",", "", $metal) * 0.001);
	      $fooscore = (str_replace(",", "", $food) * 0.0005);
	      $acrscore = (str_replace(",", "", $acres) * 15);
	      $acrscore += (str_replace(",", "", $acres) * 15);
	      $peascore = (str_replace(",", "", $peasants) * 1);
			
	      /* Message for output */   
	      $output = "Tick: $thetick ($day/$month/$year/$age)\n";
	      $output .= "$ruler of $province ($gender $race) (Kingdom: $kingdom)\n";
	      $output .= "Knowledge: $science%\n";
	      $output .= "Acres: $acres\n";
	      $output .= "Gold: $gold gc\n";
	      $output .= "Metal: $metal kg\n";
	      $output .= "Food: $food kg\n";
	      $output .= "Peasants: $peasants ($ppeasants%)\n";
	      $output .= "Daily Basic Gold Income:". ($peasants*3)."\n"; 
	      $output .= "Daily Food Consume:". ($peasants*0.4)."\n"; 
	      $output .= "Daily Basic Peasant Growth: 2.5%\n"; 
	      $output .= "Morale: $morale% \n";
	
	      /* Military Calculation */
	      $offmilsum = 0;
	      $defmilsum = 0;
	      $mil1score = 0;
	      $milamount = 0;
	      $mil1=0;
	      $mil2=0;
	      $mil3=0;
	      $mil4=0;	
	      //echo "<pre>$scan</pre>";
      	if(eregi($military[$race][0] . ":(\s| *)([-0-9,]*)",$scan,$parsmil)) {
      		$mil1 = str_replace(",", "", $parsmil[2]);
	      	$mil1score = str_replace(",", "", $parsmil[2]) * 2;
	      	$milamount += $mil1;
		      $output .= "{$military[$race][0]}: $parsmil[2]\n";
      		if ($offmilsum) {
		      	$offmilsum += ($milstrength[$military[$race][0]]['OP'] * $mil1);
		      	$defmilsum += ($milstrength[$military[$race][0]]['DP'] * $mil1);
		      }
		      else {
			      $offmilsum = ($milstrength[$military[$race][0]]['OP'] * $mil1);
			      $defmilsum = ($milstrength[$military[$race][0]]['DP'] * $mil1);
		      }
	      }
	$mil2score = 0;
	if(eregi($military[$race][1] . ":(\s| *)([-0-9,]*)",$scan,$parsmil)) {
		$mil2 = str_replace(",", "", $parsmil[2]);
		$mil2score = str_replace(",", "", $parsmil[2]) * 5;
     	$milamount += $mil2;
		$output .= "{$military[$race][1]}: $parsmil[2]\n";
		if ($offmilsum) {
			$offmilsum += ($milstrength[$military[$race][1]]['OP'] * $mil2);
			$defmilsum += ($milstrength[$military[$race][1]]['DP'] * $mil2);
		}
		else {
			$offmilsum = ($milstrength[$military[$race][1]]['OP'] * $mil2);
			$defmilsum = ($milstrength[$military[$race][1]]['DP'] * $mil2);
		}
	}
	$mil3score = 0;
	if(eregi($military[$race][2] . ":(\s| *)([-0-9,]*)",$scan,$parsmil)) {
		$mil3 = str_replace(",", "", $parsmil[2]);
		$mil3score = str_replace(",", "", $parsmil[2]) * 5;
     	$milamount += $mil3;
		$output .= "{$military[$race][2]}: $parsmil[2]\n";
		if ($offmilsum) {
			$offmilsum += ($milstrength[$military[$race][2]]['OP'] * $mil3);
			$defmilsum += ($milstrength[$military[$race][2]]['DP'] * $mil3);
		}
		else {
			$offmilsum = ($milstrength[$military[$race][2]]['OP'] * $mil3);
			$defmilsum = ($milstrength[$military[$race][2]]['DP'] * $mil3);
		}
	}
	$mil4score = 0;
	if(eregi($military[$race][3] . ":(\s| *)([-0-9,]*)",$scan,$parsmil)) {
		$mil4 = str_replace(",", "", $parsmil[2]);
		$mil4score = str_replace(",", "", $parsmil[2]) * 12;
     	$milamount += $mil4;
		$output .= "{$military[$race][3]}: $parsmil[2]\n";
		if ($offmilsum) {
			$offmilsum += ($milstrength[$military[$race][3]]['OP'] * $mil4);
			$defmilsum += ($milstrength[$military[$race][3]]['DP'] * $mil4);
		}
		else {
			$offmilsum = ($milstrength[$military[$race][3]]['OP'] * $mil4);
			$defmilsum = ($milstrength[$military[$race][3]]['DP'] * $mil4);
		}
	}
	
	/* Buildings Scan Parsing */
	if($scanb) {
		if (eregi("([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})(.*)this Buildings:(.*)(We are also getting more famous|Lady|Sir)", $scanb, $parsed)) {
			$thetickb = 0;
			$thetickb += ($parsed[7]-1)*12*24*2;
			$thetickb += ($parsed[5]-1)*12*24;
			$thetickb += ($parsed[3]-1)*24;
			$thetickb += ($parsed[1]-1);
	      $scanb = preg_replace("/\s{2,}/"," ", $parsed[10]);	
	      $scanb = preg_replace("/\t/","", $scanb);	

			if ($thetick != $thetickb) {
				$error .= "The tick on province scan and buildings scan is not the same.<br>";
			}
			else {
            while(ereg("([ a-zA-Z]+) Built: ([\s0-9]{1,5}) In progress: ([\s0-9]{1,5})", $scanb, $parsed)) {   
               $scanb = substr($scanb, strpos($scanb,$parsed[3])+strlen($parsed[3]));
               /* Variables */
               $building = trim($parsed[1]);
               $building_built = trim($parsed[2]);
               $building_ip = trim($parsed[3]);
               $built[$building] = $building_built;
               $inprogress[$building] = $building_ip;
            }
            $building_population = 0;
            $building_population_ip = 0;
            $built_acres=0;
				/* Buildings Calculation */  
				foreach ($built as $building => $amount) {
               switch ($building) {
                  case "Farm":
                     // 65 units of food, houses 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $calc_food = $amount * 65;
                     $calc_food_ip = $calc_food + 65 * $inprogress[$building];
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Wall":
                     // +1% defence if built on 1% land, max 10% of land, houses 10 people.
                     $building_population += 10 * $amount; 
                     $building_population_ip += (10 * $inprogress[$building]);
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>10) $percentage = 10;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>10) $percentage_ip = 10;
                     $calc_def = $percentage * 1;
                     $calc_def_ip = $percentage_ip * 1;
                     $output_def = "$calc_def% extra defence.\n";
                     $output_def_ip = "$calc_def_ip% extra defence.\n";                     
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Home":
                     // +1% peasants growth, houses 40 people.
                     $building_population += 40 * $amount; 
                     $building_population_ip += (40 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     $calc_pgrowth = $percentage * 1;
                     $calc_pgrowth_ip = $percentage_ip * 1;
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Marketplace":
                     // +2% gold, metal and food, and -2% trade loss if built on 1% land, max 50% of land, houses 20 people. 
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>50) $percentage = 50;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>50) $percentage_ip = 50;
                     $calc_pgold = $percentage * 2;
                     $calc_pgold_ip = $percentage_ip * 2;
                     $calc_pmetal = $percentage * 2;
                     $calc_pmetal_ip = $percentage_ip * 2;
                     $calc_pfood = $percentage * 2;
                     $calc_pfood_ip = $percentage_ip * 2;
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Inn":
                     // +1% thievery defence and on thievery ops, houses 30 thieves and 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     $calc_ptdef = $percentage * 1;
                     $calc_ptdef_ip = $percentage_ip * 1;
                     $calc_ptops = $percentage * 1;
                     $calc_ptops_ip = $percentage_ip * 1;
                     $calc_maxthiefs = $amount * 30;
                     $calc_maxthiefs_ip = ($calc_maxthiefs+$inprogress[$building] * 30);
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Wizard Tower":
                     // +1% magic defence and on magic ops, houses 30 wizards and 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     $calc_pwdef = $percentage * 1;
                     $calc_pwdef_ip = $percentage_ip * 1;
                     $calc_pwops = $percentage * 1;
                     $calc_pwops_ip = $percentage_ip * 1;
                     $calc_maxwizards = $amount * 30;
                     $calc_maxwizards_ip = ($calc_maxwizards+$inprogress[$building] * 30);
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Barrack":
                     // -2% military costs, max 25% of land, houses 20 people
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>25) $percentage = 25;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>25) $percentage_ip = 25;
                     $calc_milcost = $percentage * 2;
                     $calc_milcost_ip = $percentage_ip * 2;
                     $output_milcost = "-$calc_milcost% military costs.\n";
                     $output_milcost_ip = "-$calc_milcost_ip% military costs.\n";                     
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Temple":
                     // +5% peasants growth and adds 100 pr to gold income, max 20% of land, houses 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>20) $percentage = 20;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>20) $percentage_ip = 20;
                     $calc_pgrowth += $percentage * 5;
                     $calc_pgrowth_ip += $percentage_ip * 5;
                     $calc_gold = $amount * 100;
                     $calc_gold_ip = $inprogress[$building]*100;
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Mine":      
                     // Adds 10 gold and 100 metal pr to income, houses 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $calc_gold += $amount * 10;
                     $calc_gold_ip += $inprogress[$building] * 10;
                     $calc_metal = $amount * 100;
                     $calc_metal_ip = $calc_metal + $inprogress[$building] * 100;
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Blacksmith":      
                     // +2% attack power, max 20% of land, houses 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>20) $percentage = 20;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>20) $percentage_ip = 20;
                     $calc_off = $percentage * 2;
                     $calc_off_ip = $percentage_ip * 2;
                     $output_off = "$calc_off% extra offence.\n";
                     $output_off_ip = "$calc_off_ip% extra offence.\n";                     
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Crypt":      
                     // +5% to peasants growth, adds 100 to gold income, max 20% of land, houses 20 people.
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>20) $percentage = 20;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>20) $percentage_ip = 20;
                     $calc_pgrowth += $percentage * 5;
                     $calc_pgrowth_ip += $percentage_ip * 5;
                     $calc_gold += $amount * 100;
                     $calc_gold_ip += $inprogress[$building]*100;
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Stable":      
                     // -2% attack time, max 15% of land, houses 20 people.                        
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>15) $percentage = 15;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>15) $percentage_ip = 15;
                     $calc_atime = $percentage * 2;
                     $calc_atime_ip = $percentage_ip * 2;
                     $output_atime = "-$calc_atime% attack time.\n";
                     $output_atime_ip = "-$calc_atime_ip% attack time.\n";                     
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  case "Beast den":      
                     // -2% attack time, max 15% of land, houses 20 people.                        
                     $building_population += 20 * $amount; 
                     $building_population_ip += (20 * $inprogress[$building]); 
                     $percentage = round(($amount/$acres)*100);
                     if ($percentage>15) $percentage = 15;
                     $percentage_ip = round((($amount+$inprogress[$building])/$acres)*100);                                           
                     if ($percentage_ip>15) $percentage_ip = 15;
                     $calc_atime = $percentage * 2;
                     $calc_atime_ip = $percentage_ip * 2;
                     $output_atime = "-$calc_atime% attack time.\n";
                     $output_atime_ip = "-$calc_atime_ip% attack time.\n";                     
                     $built_acres += ($amount+$inprogress[$building]);
                     break;
                  default: 
                     break;   
               }
            }
            $unbuilt_acres = $acres - $built_acres;
            $building_population += $unbuilt_acres*15;
            $output_buildings = "\nBuildings report:\n";
            $output_buildings .= "Max population of $building_population.\n";
            $output_buildings .= "Peasant bonus growth of $calc_pgrowth%.\n";
            $calc = ($calc_food*$calc_pfood)/100;
            $output_buildings .= "$calc units of food produced each tick.\n";
            $calc = ($calc_metal*$calc_pmetal)/100;
            $output_buildings .= "$calc units of metal produced each tick.\n";
            $calc = ($calc_gold*$calc_pgold)/100;
            $output_buildings .= "$calc units of gold produced each tick.\n";
            $output_buildings .= "$calc_ptdef% bonus on thievery defence and $calc_ptops% bonus of thievery ops.\n";
            $output_buildings .= "$calc_pwdef% bonus on wizard defence and $calc_pwops% bonus on wizard ops.\n";
            $output_buildings .= "Maximum of $calc_maxwizards wizards and $calc_maxthiefs thieves.\n";
            $output_buildings .= $output_milcost;
            $output_buildings .= $output_atime;
            $output_buildings .= $output_off;
            $output_buildings .= $output_def;
            $output_buildings .= "\nWhen all buildings are finished:\n";
            $output_buildings .= "Max population of ".($building_population_ip+$building_population).".\n";
            $output_buildings .= "Peasant bonus growth of $calc_pgrowth_ip%.\n";
            $output_buildings .= (($calc_food_ip*$calc_pfood_ip)/100)." units of food produced each tick.\n";
            $output_buildings .= (($calc_metal_ip*$calc_pmetal_ip)/100)." units of metal produced each tick.\n";
            $output_buildings .= ($calc_gold+($calc_gold_ip*$calc_pgold_ip)/100)." units of gold produced each tick.\n";
            $output_buildings .= "$calc_ptdef_ip% bonus on thievery defence and $calc_ptops_ip% bonus of thievery ops.\n";
            $output_buildings .= "$calc_pwdef_ip% bonus on wizard defence and $calc_pwops_ip% bonus on wizard ops.\n";
            $output_buildings .= "Maximum of $calc_maxwizards_ip wizards and $calc_maxthiefs_ip thieves.\n";
            $output_buildings .= $output_milcost_ip;
            $output_buildings .= $output_atime_ip;
            $output_buildings .= $output_off_ip;
            $output_buildings .= $output_def_ip;
			}
		}
		else {
		   $error .= "Invalid scan format. You need to copy the whole 'Spy on Buildings' page.<br>";
		}
	}
	
	/* Military Scan Parsing */
	if ($scanm) {
		if (eregi("([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})(.*)military out!(.*)(We are also getting more famous|Lady|Sir)", $scanm, $parsed)) {
			$thetickm = 0;
			$thetickm += ($parsed[7]-1)*12*24*2;
			$thetickm += ($parsed[5]-1)*12*24;
			$thetickm += ($parsed[3]-1)*24;
			$thetickm += ($parsed[1]-1);
	      $scanm = preg_replace("/\s{2,}/"," ", $parsed[10]);	
	      $scanm = preg_replace("/\t/","", $scanm);	

			if ($thetick != $thetickm) {
				$error .= "The tick on province scan and military scan is not the same.<br>";
			}
			else {
			   $output_milaway = "\nMilitary away from home:\n";
			   $calc_def_away = 0;
			   $calc_off_away = 0;
				/* Military Away Calculation */  
            while(eregi("about ([,0-9]+) ([ A-Za-z]+) are out in war",$scanm,$parsed)) {               
               $scanm = substr($scanm, strpos($scanm,$parsed[2])+strlen($parsed[2]));
               /* Variables */
               $amount = trim($parsed[1]);
               $amount = preg_replace("/,/","", $amount);
               $milawaytype = trim($parsed[2]);
               $calc_def_away += $milstrength[$milawaytype]['DP'] * str_replace(",", "", $amount);
               $calc_off_away += $milstrength[$milawaytype]['OP'] * str_replace(",", "", $amount);
               $output_milaway .= "$amount $milawaytype\n";
            }
            $calc_def_home = $defmilsum - $calc_def_away;
            $calc_off_home = $offmilsum - $calc_off_away;
			}
		}   
		else {
		   $error .= "Invalid scan format. You need to copy the whole 'Spy on Military' page.<br>";
		}
	}
	
	/* Science Scan Parsing */
	if ($scans) {
		if (eregi("([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})/([0-9]+)([a-z]{0,2})(.*)this knowledge:(.*)(We are also getting more famous|Lady|Sir)", $scans, $parsed)) {
			$theticks = 0;
			$theticks += ($parsed[7]-1)*12*24*2;
			$theticks += ($parsed[5]-1)*12*24;
			$theticks += ($parsed[3]-1)*24;
			$theticks += ($parsed[1]-1);
	      $scans = preg_replace("/\s{2,}/"," ", $parsed[10]);	
	      $scans = preg_replace("/\t/","", $scans);	

			if ($thetick != $theticks) {
				$error .= "The tick on province scan and science scan is not the same.<br>";
			}
			else {
				/* Science Calculation */  
				$output_science = "\nKnowledge effects:\n";
            $sciences = split(",", $scans);
				foreach ($sciences as $key => $science) {
				   $science = trim($science);
               switch ($science) {
                  case "Basic Attacking":
                     break;
                  case "Mining":
                     break;
                  case "Leather Armour":
                     // +5% military
                     $offmilsum = $offmilsum * 1.05;
                     $defmilsum = $defmilsum * 1.05;
         		      $calc_off_away = $calc_off_away * 1.05;
		               $calc_off_home = $calc_off_home * 1.05;
         		      $calc_def_home = $calc_def_home * 1.05;
		               $calc_def_away = $calc_def_away * 1.05;
                     $output_science .= "Fighting strength of units increased by 5%.\n";                     
                     break;
                  case "Metal Weapons":
                     $offmilsum = $offmilsum * 1.1;
                     $defmilsum = $defmilsum * 1.1;
         		      $calc_off_away = $calc_off_away * 1.1;
		               $calc_off_home = $calc_off_home * 1.1;
         		      $calc_def_home = $calc_def_home * 1.1;
		               $calc_def_away = $calc_def_away * 1.1;
                     $output_science .= "Fighting strength of units increased by 10%.\n";                     
                     break;
                  case "Agriculture":
                     $output_science .= "Food production increased by 30%.\n";                     
                     break;
                  case "Military Training":
                  // Gives Elites
                     break;
                  case "Strategic Warfare":
                     break;
                  case "Metal Working":
                     break;
                  case "Masonry":
                     break;
                  case "Architecture":
                     break;
                  case "Apocalypse":
                     break;
                  case "Bug Infestation":
                     break;
                  case "Earthquake":
                     break;
                  case "Attack Magic":
                     break;
                  case "Covert Operations":
                     break;
                  case "Mage Circle":
                     $output_science .= "Reduced wizard requirements by 25%.\n";                     
                     break;
                  case "Espionage":
                     $output_science .= "Offensive thievery strength increased by 25%.\n";                     
                     break;
                  default:
                     break;
               }
            }
			}
		}      
		else {
		   $error .= "Invalid scan format. You need to copy the whole 'Spy on Science' page.<br>";
		}
	}   
	
	      /* NW Calculation */
	      $acrescore = $acrescore - ($unbuilt_Acres*15);
	      $sumscore = round($sciscore + $metscore + $fooscore + $goldscore + $peascore + $acrscore + $mil1score + $mil2score + $mil3score + $mil4score);
         //echo "$sumscore = round($sciscore + $metscore + $fooscore + $goldscore + $peascore + $acrscore + $mil1score + $mil2score + $mil3score + $mil4score)";
	
	      $output .= "\nCalculated Networth: $sumscore\n";

	      /* Thiefs and Wizards Calculation and output */
	      if ($realnw) {
		      $output .= "Real Networth: $realnw\n";
	         $newnw = $realnw - $sumscore;
	         if ($newnw >= 0) {
		         $antall_wiz = $newnw/6; 
		         $output .= "Calculated Wizards/Thiefs: $antall_wiz\n";	
	         }  	
	      }
	
	      /* Add Thiefs and Wizards to defence */
	      if ($antall_wiz) {
		      $defmilsum = $defmilsum + $antall_wiz;
		      $calc_def_home = $calc_def_home + $antall_wiz;
	      }   	

	      /* Add Orc Military Bonus */
	      if ($race == "Orc") {
		      $offmilsum = $offmilsum * 1.09;
		      $calc_off_away = $calc_off_away * 1.09;
		      $calc_off_home = $calc_off_home * 1.09;
		      $defmilsum = $defmilsum * 1.06;
		      $calc_def_home = $calc_def_home * 1.06;
		      $calc_def_away = $calc_def_away * 1.06;
	      }

	      /* Add Wall & Blacksmith Military Bonus */
	      if ($calc_off) {
	         $offmilsum = ($offmilsum*(1+($calc_off/100)));
	         $calc_off_away = ($calc_off_away*(1+($calc_off/100)));
	         $calc_off_home = ($calc_off_home*(1+($calc_off/100)));
	      } 
	      if ($calc_def) {
	         $defmilsum = ($defmilsum*(1+($calc_def/100)));
	         $calc_def_away = ($calc_def_away*(1+($calc_def/100)));
	         $calc_def_home = ($calc_def_home*(1+($calc_def/100)));
	      } 

         if($output_milaway) {
            $output_milaway .= "\nCalculated Away Defense Points: $calc_def_away\n";                     
            $output_milaway .= "Calculated Away Offence Points: $calc_off_away\n";
            $output_milaway .= "\nCalculated Home Defense Points: $calc_def_home\n";                     
            $output_milaway .= "Calculated Home Offence Points: $calc_off_home\n";
         }

	
	      /* End Output */
	      $output .= "\nCalculated Total Offensive Points: ".round($offmilsum)."\n";
	      $output .= "Calculated Total Defensive Points: ".round($defmilsum)."\n";

         if ($output_milaway) $output .= $output_milaway;
         if ($output_buildings) $output .= $output_buildings;
         if ($output_science) $output .= $output_science;
         

         // Input to mysqldb
	      $result = mysql_query("SELECT scan_id FROM parsed_scans");
         $i=0;
         while($arrayresult = mysql_fetch_array($result)) {
            $arraykeys[$i] = $arrayresult;
            $i++;
         }
         $key_id = mk_unique_key("50",&$arraykeys);
	      $result = mysql_query("INSERT INTO parsed_scans (scan_id,tick,name,data) VALUES('".$key_id."','".$thetick."','".$province."','".$output."')");
         // if (mysql_errno()) echo "<br><br>".mysql_error()."\n";

         // Output to screen

         echo "<a href=\"http://www.badstaile.com/chaos/parse.php?report=$key_id\"><div id=dajm2>http://www.badstaile.com/chaos/parse.php?report=$key_id</div></a> [<a href=\"javascript:copy_clip('dajm2')\">Copy URL to Clipboard</a>]";        
         echo "<pre id=dajm>";        
         echo $output;
	      echo "</pre><a href=\"javascript:copy_clip('dajm');\">Copy to clipboard</a><br>";
	      echo "<br><font color=red>Real nw score you see in TKOC might not be up to date.</font>";

      } 
      else {
	      $error .= "Invalid scan format. You need to copy the whole 'Spy on Province' page.<br>";
      }   
   } 
   else {
	   $error .= "Invalid scan format. You need to copy the whole 'Spy on Province' page.<br>";
   }
   if ($error) {
      echo "<br><br><font color=red>Following error(s) occured:<br>";
      echo $error;
      echo "</font>";
   }
}
else if ($report) {
   $result = mysql_query("SELECT data FROM parsed_scans WHERE scan_id='".$report."'");
   if ($data = mysql_fetch_row($result)) {
      echo "<a href=\"http://www.badstaile.com/chaos/parse.php?report=$report\"><div id=dajm2>http://www.badstaile.com/chaos/parse.php?report=$report</div></a> [<a href=\"javascript:copy_clip('dajm2')\">Copy URL to Clipboard</a>]";        
      echo "<pre id=dajm>";        
      echo $data[0];
	   echo "</pre><a href=\"javascript:copy_clip('dajm');\">Copy to clipboard</a><br>";
      echo "<br><font color=red>Real nw score you see in TKOC might not be up to date.</font>";
   }
}
?>

			<form action="parse.php" method="post">
				<table width="600" cellspacing="1" cellpadding="4" border="0" align="center">
					<tr>
						<td class="menuheader" align="center">Scan parser</td>
					</tr>
					<tr>
						<td class="body" align="center">
							<br><br>
							Spy On Province:<br>
							<textarea name="scan" rows="6" cols="50"></textarea>
							<br>
							Spy On Science (Optional):<br>
							<textarea name="scans" rows="6" cols="50"></textarea>
							<br>
							Spy On Buildings (Optional):<br>
							<textarea name="scanb" rows="6" cols="50"></textarea>
							<br>
							Spy On Military (Optional):<br>
							<textarea name="scanm" rows="6" cols="50"></textarea>
							<br>
							Province NW (Optional): <input type="text" name="realnw" value="" class="button">
							<br>
							<input type="submit" value="Parse Scan." class="button">
						</td>
					</tr>
					<tr>
					<td align="center">
						You need to copy the whole page for the parser to accept it
					</td>
					</tr>
				</table>
			</form>



				
				
			 </TD>
		  </TR>
		  <TR>
			 <TD>
				&nbsp;<BR><BR>
			 </TD>
		  </TR>
		  <TR>
			 <TD colSpan=2> 

			 </TD>
		  </TR>
		</TABLE>
		</DIV>
	 <BR><BR><BR>
	 </TD>
  </TR>
</TABLE>

</BODY>
</HTML>

<?

function mk_unique_key($size,&$array) {
   $keys = array_keys($array);
   $key = substr(md5(microtime()),0,$size);
   if (in_array($key,$keys)) {
      $key = mk_unique_key($size,&$array);
   }
   return $key;
}

?>