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

// 

if( !class_exists( "SpyOnProvince" ) ) {
require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

class SpyOnProvince extends ThieveryBase {
	var $difficulity = 20;
	var $costInfluence = 3;
	var $optimalThieves = 2000;	
	var $randomNess = 5;

	function SpyOnProvince( $thieveryID ) 
	{
		$this->ThieveryBase( $thieveryID,"Spy on Province", 
		"This will let you see the loginscreen of another player.",
		/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0));    
    }

	function thieveryEffect ($province,$victimProvince) 
	{
		$txt = "The operation was a success!";
		$victimProvince->getMilitaryData();
		$show = $GLOBALS['ProvinceConst']->SHOW_ALL - $GLOBALS['ProvinceConst']->SHOW_MIL_THIEVES - $GLOBALS['ProvinceConst']->SHOW_MIL_WIZARDS;
		$txt .="<br>Our thieves have discovered :" . $this->generateReport($victimProvince,$show);
		if (parent::thieveryEffect($province,$victimProvince)>0) {
			$txt .= "<br>We are also getting more famous.";			
		}
		$html = "<center><br>$txt</center>";
		return $html;
	}

	//*
	//*
	//* Warning, cutn paste from Province.class.inc.php
	function generateReport ($province,$show=255)
	{

		require_once($GLOBALS['path_www_scripts'] . "Science.class.inc.php");
		$science = new Science($province->database,$province->pID);
		$scienceLevel =  $science->getScienceAge();
		if (!$province->isAlive()) {
			$info = "<center>This province is dead..</center>";
		} else {
			if ($province->buildingPeasantPopulation<1) {
				$province->buildingPeasantPopulation = $this->acres*15;
			}
			$max =round ( (($province->peasants+$province->militaryPopulation)/$province->buildingPeasantPopulation)*100 );

			$info = '
<table align="center">
	<tr>
		<td><br><br><center><font color="#BDB585" size=6><b>' .$province->provinceName .'(#' .$province->kiId.')</b></font></center>
		</td>
	</tr>
	<tr bgcolor="#000000">
		<td valign="top">
			<table cellspacing="0" cellpadding="0" align="center" width="700" border="0">
				<tr>
					<td colspan="3" width="700" align="left">
						<img src="../img/msg_top.gif" width="700" height="46" border="0" alt="">
					</td>
				</tr>
				<tr>
					<td align="left" background="../img/msg_left.gif" width="52">&nbsp; 
					</td>
					<td width="606" align="center">
						<table border="0">
							<tr>
								<td>
									<table border="0">
										<tr><th align=left>Ruler:</th><td>'. $province->rulerName .' </td></tr>
										<tr><th align=left>Race:</th><td>' .$province->race. '</td></tr>
										<tr><th align=left>Gender:</th><td>' . ($province->gender=='M' ? 'male':'female') .'</td></tr>
										<tr><th align=left>Knowledge:</th><td>'.$scienceLevel.'</td></tr>
										<tr><th align=left>Gold:</th><td>' .number_format($province->gold,0,' ',',') .'gc</td></tr>
										<tr><th align=left>Metal:</th><td>' . number_format($province->metal,0,' ',',').'kg</td></tr>
										<tr><th align=left>food:</th><td>' . number_format($province->food,0,' ',',').'kg</td></tr>
										<tr><th align=left>Acres:</th><td>' .$province->acres . '</td><td width=40></td></tr>
									</table>
								</td>
								<td width=40>
								&nbsp;
								</td>
								<td VALIGN=TOP>
									<table border="0">
									<tr><th align=left>Peasants:</th><td>' . number_format($province->peasants,0,' ',',') . '('.$max.'%)</td></tr>
									<tr><th align=left>Morale:</th><td>' .$province->morale .'%</td></tr>
		';
	$province->getMilitaryData();
	$milUnits = $province->milObject->getMilitaryNotTr();
	foreach ($milUnits as $unit) {
		if (($unit['object']->getMilType()==$GLOBALS['MilitaryConst']->WIZARDS && (!($show&$GLOBALS['ProvinceConst']->SHOW_MIL_WIZARDS))) ||
			($unit['object']->getMilType()==$GLOBALS['MilitaryConst']->THIEVES && (!($show&$GLOBALS['ProvinceConst']->SHOW_MIL_THIEVES)))
		) {
			$info .= "<tr><th align=left>Trained unit:</th><td>unknown</td></tr>";
		} else {
			$info .= "<tr><th align=left>" . $unit['object']->getName(). ":</th><td>" .number_format(intval($unit['num']*$this->randomPercent($this->randomNess)),0,' ',',') . "</td></tr>";
		}
	}

$info .=		'</table>
								</td>
							</tr>
						</table>
					</td>
						<td align="left" background="../img/msg_right.gif" width="42">&nbsp; 
					</td>
				</tr>
				<tr>
					<td colspan="3" width="700"><img src="../img/msg_bottom.gif" width="700" height="45" border="0" alt="">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
';

		}
	return $info;


	
	}
}
} // end if( !class_exists() )
?>