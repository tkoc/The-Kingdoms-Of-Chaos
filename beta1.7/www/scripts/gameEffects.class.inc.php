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

if( !class_exists( "gameEffects" ) ) {
	require_once ("Province.class.inc.php");
	class gameEffects {
		var $database;
		var $config;
		
		function gameEffects($database, $config) {
			$this->database = $database;
			$this->config = $config;										   
		}
		
		
		function doTick () {
			// Increase overpopulated effect
			if ($this->config['ticks'] > $this->config['statusLength'] - 50)
				$overpopModifier = 2;
			else
				$overpopModifier = 1;
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 500) {
				$news = "Our council has just fled in fear of the Apocalypse!";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set council=0");
			}
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 440) {
				$news = "A strange Apocalyptic shockwave has decimated our population!  Maybe we should go underground..?";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set peasants=(peasants/2)");
			}
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 340) {
				$news = "The people are revolting!  They fear the apocalypse..  so should we!";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set goldChange=0, metalChange=0, foodChange=0");
			}
			
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 240) {
				$news = "In your throne room your master wizard come out from the shadow.  With a shaking voice he tells you that all the mana has been drained from this world... You bet it has something to do with the apocalypse!";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set mana=50");
			}
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 150) {
			
				$news = "Our generals report that the morale of our troops is gone since they all think we will die.. That damn apocalypse!";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set morale=50");
			}
			
			if ($this->config['ticks'] == $this->config['statusLength'] - 100) {
				$news = "Our thief guild reports that all of our thieves has left the lands! Seems like everyone is fleeing..";
				$this->postErrorNews ($news);
				$this->database->query("UPDATE Province set influence=50");
			}
			
			return $overpopModifier;
		}
		
		
		function postErrorNews ($news) {
			$this->database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$this->database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],$this->database);
				$terrorNews->postNews($news);
			}
		}  
		 
	}
} 
?>