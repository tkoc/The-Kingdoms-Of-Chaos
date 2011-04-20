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

/* BuildingDescription.php
 * 
 * This file contains code to display a small information page about a building.
 * A bit slow, but the users won't be viewing this page often.
 *
 * Modified by Øystein		21.03.03	- Added some functionality to let the user see his/her benefits
 *										building percentage and so on
 * Modified by Øystein		11.03.03	- Added info from the functions added in BuildingBase.class.inc.php
 * Author: Øystein Fladby	04.03.2003
 * 
 * Changelog
 *
 * Version: test
 * 
 */
	require_once("../all.inc.php");
 	require_once("../isLoggedOn.inc.php");	// makes an User-object named $user and Database-object named $database
	require_once("../Buildings.class.inc.php");
	require_once("../Effect.class.inc.php");
	$dummy = NULL;
	$buildings = new Buildings( $database, $dummy );
	$effectObj = new Effect( $database );
	
	if( isset( $_GET['more'] ) ) {
		$more = true;
	} else {
		$more = false;
	}
	$bID = $_GET['bID'];
	$acres = $_GET['acres'];
	$num = $_GET['num'];
	$pID = $user->getpID();
		
	$acres = ( $acres > 0 ? $acres : 1 );		// making sure there's no division by 0 error
	
	$building = $effectObj->buildObj->getBuilding( $bID );
	$bonusAdded=0;		// if one or more addXXX bonus is given for the building, this value is not 0 anymore
	$count=0;
	$width="width='75'";

	$bonus[$count++] = "<tr><td colspan='3'><h1>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME )."</h1></td></tr>";
	$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>Original</td><td $width>You</td></tr>";
	$bonus[$count++] = "<tr><td>Cost in gold:</td><td $width>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST )."</td><td $width>".round( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_GOLD_COST, $pID) * $building->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) )."</td></tr>";
	$bonus[$count++] = "<tr><td>Cost in metal:</td><td $width>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST )."</td><td $width>".round( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_METAL_COST, $pID) * $building->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) )."</td></tr>";
	$bonus[$count++] = "<tr><td>Time to build:</td><td $width>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_TICKS )."</td><td $width>".round( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_TIME, $pID) * $building->baseFunction( $GLOBALS['BuildingConst']->GET_TICKS ) )."</td></tr>";
	$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td>&nbsp;</td $width></tr>
						<tr><td>You have:</td><td $width>".round( ( $num / $acres * 100 ),1 )."% *</td><td $width>&nbsp;</td></tr>";
	if( $maxB = $building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS ) ) {
		$bonus[$count++] = "<tr><td>Max to get benefit from:</td><td $width>$maxB% *</td><td $width>&nbsp;</td></tr>";
	}
	$bonus[$count++] = "<tr><td colspan='3'><font size='-3'> &nbsp; * % of your acres built over with this building</font></td></tr>";
	if( $more ) {
		$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
							<tr><td>Building bonuses</td><td $width>Original</td><td $width>You</td></tr>";	
		
		//INCOME
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->GOLD_INCOME ) )
			$bonus[$count++] = "<tr><td>Gold income:</td><td $width>$dummy</td><td $width>".round( $dummy * $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_GOLD_INCOME, $pID ) )."</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->METAL_INCOME ) )
			$bonus[$count++] = "<tr><td>Metal income:</td><td $width>$dummy</td><td $width>".round( $dummy * $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_METAL_INCOME, $pID ) )."</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->FOOD_INCOME ) )
			$bonus[$count++] = "<tr><td>Food income:</td><td $width>$dummy</td><td $width>".round( $dummy * $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_FOOD_INCOME, $pID ) )."</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_GOLD_INCOME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional gold income:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_GOLD_INCOME, $pID ) - 1) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_METAL_INCOME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional metal income:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_METAL_INCOME, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_FOOD_INCOME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional food income:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_FOOD_INCOME, $pID ) - 1 )* 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_PEASANT_GROWTH ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional peasant growth:</td><td $width>$dummy% *</td $width><td>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_PEASANT_GROWTH, $pID ) - 1 )* 100 )."%</td></tr>";
		
		//MILITARY
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_DEFENSE ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional defense:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_DEFENSE, $pID ) - 1 )* 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_ATTACK ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional attack:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_ATTACK, $pID ) - 1 )* 100 )."%</td></tr>";
		if( ( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MILITARY_GOLD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional military gold cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MILITARY_GOLD_COST, $pID ) - 1 )* 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MILITARY_METAL_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional military metal cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MILITARY_METAL_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MILITARY_FOOD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional military food cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MILITARY_FOOD_COST, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MILITARY_TRAIN_TIME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional military training time:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MILITARY_TRAIN_TIME, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_ATTACK_TIME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional military attack time:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_ATTACK_TIME, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MORALE ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional morale:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MORALE, $pID ) - 1 ) * 100  )."%</td></tr>";
		
		//EXPLORE
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_EXPLORE_TIME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional exploring time:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_EXPLORE_TIME, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_EXPLORE_SOLDIER_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional explore soldier cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_EXPLORE_SOLDIER_COST, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_EXPLORE_GOLD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional explore gold cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_EXPLORE_GOLD_COST, $pID ) - 1 ) * 100  )."%</td></tr>";
		
		//THIEVERY
		if( ($dummy = $building->baseFunction( "addThieveryOff" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Offensive thievery bonus:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addThieveryOff", $pID ) - 1 ) * 100  )."%</td></tr>";		
		if( ($dummy = $building->baseFunction( "addThieveryDef" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Defensive thievery bonus:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addThieveryDef", $pID ) - 1 ) * 100  )."%</td></tr>";		
		if( ($dummy = $building->baseFunction( "addSpyBonus" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spy bonus:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addSpyBonus", $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_INFLUENCE ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional influence:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_INFLUENCE, $pID ) - 1 ) * 100  )."%</td></tr>";
		
		//TRADING
		if( ($dummy = $building->baseFunction( "addResourceLoss" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional loss when trading:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addResourceLoss", $pID ) - 1 ) * 100  )."%</td></tr>";
		// KNOWLEDGE
		if( ($dummy = $building->baseFunction( "addResearchTime" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Modifies knowledge time:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addResearchTime", $pID ) - 1 ) * 100  )."%</td></tr>";
		
		//BUILDINGS
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_BUILDING_GOLD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional building gold cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_GOLD_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_BUILDING_METAL_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional building metal cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_METAL_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_BUILDING_TIME ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional building build time:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_TIME, $pID ) - 1 ) * 100 )."%</td></tr>";
		
		//HOUSING
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->WIZARD_HOUSING ) )
			$bonus[$count++] = "<tr><td>Allows for wizards:</td><td $width>$dummy</td><td $width>$dummy</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->THIEF_HOUSING ) )
			$bonus[$count++] = "<tr><td>Allows for thieves:</td><td $width>$dummy</td><td $width>$dummy</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->MILITARY_HOUSING ) )
			$bonus[$count++] = "<tr><td>Rooms for militants:</td><td $width>$dummy</td><td $width>$dummy</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->PEASANT_HOUSING ) )
			$bonus[$count++] = "<tr><td>Rooms for people:</td><td $width>$dummy</td><td $width>".round( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_PEASANT_HOUSING, $pID) * $building->baseFunction( $GLOBALS['BuildingConst']->PEASANT_HOUSING ) )."</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->EMPLOYES ) )
			$bonus[$count++] = "<tr><td>Employes peasants:</td><td $width>$dummy</td><td $width>$dummy</td></tr>";
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_PEASANT_HOUSING ) )
			$bonus[$count++] = "<tr><td>Additional room for people:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_PEASANT_HOUSING, $pID) -1 ) * 100 )."%</td></tr>";
		
		//MAGIC
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_WIZARD_USE ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional recommended wizards:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_WIZARD_USE, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MANA_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional mana cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MANA_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MAGIC_GOLD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spell gold cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MAGIC_GOLD_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MAGIC_METAL_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spell metal cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MAGIC_METAL_COST, $pID ) - 1 ) * 100 )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MAGIC_FOOD_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spell food cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MAGIC_FOOD_COST, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MAGIC_PEASANT_COST ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spell peasant cost:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MAGIC_PEASANT_COST, $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( "addMagicChance" ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional chance to cast spells:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( "addMagicChance", $pID ) - 1 ) * 100  )."%</td></tr>";
		if( ($dummy = $building->baseFunction( $GLOBALS['BuildingConst']->ADD_MAGIC_PROTECTION ) ) && ++$bonusAdded )
			$bonus[$count++] = "<tr><td>Additional spell protection:</td><td $width>$dummy% *</td><td $width>".round( ( $effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_MAGIC_PROTECTION, $pID ) - 1 ) * 100  )."%</td></tr>";
		
		
		//////////////////////////////////////
		if( $bonusAdded ) 
			$bonus[$count++] = "<tr><td colspan='3'><font size='-3'> &nbsp; * if this building is built on one percent of your land</font></td></tr>";
		
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->BUILDING_PREVENT ) ) {
			$dummyResult = "";
			foreach($dummy as $dummyname){
				if( strlen( $dummyResult ) ) {
					$dummyResult .= ", $dummyname";
				} else {
					$dummyResult .= $dummyname;
				}
			}
			$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
								<tr><td>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME )." prevented by:</td>
								<td colspan='2'>$dummyResult</td></tr>";
		}
		
		if( $dummy = $building->baseFunction( $GLOBALS['BuildingConst']->BUILDING_REQUIREMENTS ) ) {
			$dummyResult = "";
			foreach($dummy as $dummyname){
				if( strlen( $dummyResult ) ) {
					$dummyResult .= ", $dummyname";
				} else {
					$dummyResult .= $dummyname;
				}
			}
			$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
								<tr><td>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME )." requires:</td>
								<td colspan='2'>$dummyResult</td></tr>";
		}
		
		$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
							<tr><td>Description:</td><td $width colspan='2'>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_DESCRIPTION )."</td></tr>";
		$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td>&nbsp;</td></tr>
							<tr><td colspan='3'><a href='".$_SERVER['PHP_SELF']."?".ereg_replace("&more=true", "", $_SERVER['QUERY_STRING'] )."' target='_self'>- less -</a></td></tr>";
	} else {
		$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
							<tr><td>Description:</td><td $width colspan='2'>".$building->baseFunction( $GLOBALS['BuildingConst']->GET_DESCRIPTION )."</td></tr>";
		$bonus[$count++] = "<tr><td>&nbsp;</td><td $width>&nbsp;</td><td $width>&nbsp;</td></tr>
							<tr><td colspan='3'><a href='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."&more=true' target='_self'>- more -</a></td></tr>";
	}
?>

<html>
<title>
	<?php echo $building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ) ?> description
</title>
<link rel=stylesheet href="../../css/chaos.css" type="text/css">
<body bgcolor="#000000">
<br><br>
<table align="left" class="buildInfo" cellpadding="0" cellspacing="0" cols="3">
	<tr>
		<td valign='top' border="1"  bordercolor="#FFEECC">
			<br><img src="<?php echo $building->baseFunction( $GLOBALS['BuildingConst']->GET_PICTURE );  ?> ">
		</td>
		<td>
			&nbsp; &nbsp;
		</td>
		<td>
			<table align="center" cellpadding="1" cellspacing="0" cols="3">
				<?php
				foreach( $bonus as $info ) {
					echo $info;
				}
				?>
			</table>
		</td>
	</tr>
</table>
</body>
</html>