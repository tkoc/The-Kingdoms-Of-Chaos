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

require_once($GLOBALS['path_www_scripts'] . "all.inc.php");
	require_once($GLOBALS['path_www_scripts']."effect/EffectConstants.class.inc.php");
	if( !class_exists("MilitaryConstants")) {
		class MilitaryConstants extends EffectConstants {
			var $PATH_TO_MILITARY = "military/";
			
			
			// MILITARYTYPES
			var $PESANTS = -1;
			var $SOLDIERS = 0;
			var $DEF_SOLDIERS = 1;
			var $OFF_SOLDIERS = 2;
			var $ELITE_SOLDIERS = 3;
			var $WIZARDS = 4;
			var $THIEVES = 5;
			var $RAIDERS = 6;
			var $SPECIAL = 50;



			//Whos military?
			var $OWN_MILITARY = 100;
			var $OTHER_MILITARY = 101;
			var $MAGIC_MILITARY = 102;

			//MISC
			
			var $KINGDOM = 200;
			var $WORLD = 201;
			
			var $BAN_RES_MIN = 5;
			var $BAN_RES_MAX = 15;
			
			var $WIZ_TOWERS = 1;
			var $INNS = 2;
		    
		    var $MIL_LOSSES = NULL;
		    var $NETWORTH = NULL;
		    
			var $RESTICKS = 24;

		    function MilitaryConstants() {
			    
			    //Military Losses in %
			    $this->MIL_LOSSES[$this->PESANTS] = 0;
			    $this->MIL_LOSSES[$this->SOLDIERS] = 5;
			    $this->MIL_LOSSES[$this->DEF_SOLDIERS] = 20;
			    $this->MIL_LOSSES[$this->OFF_SOLDIERS] = 15;
			    $this->MIL_LOSSES[$this->ELITE_SOLDIERS] = 25;
			    $this->MIL_LOSSES[$this->WIZARDS] = 5;
			    $this->MIL_LOSSES[$this->THIEVES] = 5;
			    $this->MIL_LOSSES[$this->RAIDERS] = 25;
			    $this->MIL_LOSSES[$this->SPECIAL] = 0;
			    
			    //ArmyNetworth
			    
			    $this->NETWORTH[$this->PESANTS] = 1.0;
			    $this->NETWORTH[$this->SOLDIERS] = 2.0;
			    $this->NETWORTH[$this->DEF_SOLDIERS] = 5.0;
			    $this->NETWORTH[$this->OFF_SOLDIERS] = 5.0;
			    $this->NETWORTH[$this->ELITE_SOLDIERS] = 12.0;
			    $this->NETWORTH[$this->WIZARDS] = 6.0;
			    $this->NETWORTH[$this->THIEVES] = 6.0;
			    $this->NETWORTH[$this->RAIDERS] = 8.0;
			    $this->NETWORTH[$this->SPECIAL] = 8.0;
			}




		}

		$GLOBALS['MilitaryConst'] = new MilitaryConstants();
		//$MilitaryConst = new MilitaryConstants();
	}
?>