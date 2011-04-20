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

require_once("ScienceBase.class.inc.php");

if( !class_exists( "ApocalypseScience" ) ) {

class ApocalypseScience extends ScienceBase {
        function ApocalypseScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID, "magic" ,"Apocalypse", 240, 10000000, 2000000,"
Our wise men have only heard rumours about this knowledge.  The effect is still unknown...
",
/* requires */
array('military' =>2, "infrastructure" => 4, "magic" => 2, "thievery" => 
2),
/* Gives */
array("military" => 256, "infrastructure" => 256, "magic" => 256, 
"thievery" => 256)

		);    
        }
	function doTick ($database) {
	   $database->query("SELECT * from Science where sccID='$this->scID' AND ticks=90");
           if ($database->numRows()>0) {
	      $database->query("SELECT pID from Province");

              while ($items[]=$database->fetchArray());
              reset ($items);
              foreach ($items as $item) {
                 $terrorNews = new Province($item['pID'],&$database);
	         $terrorNews->postNews("Our wizards felt a great disturbance in the power today.  They 
came to me with fear in their eyes, and demanded that I gave you this message.  They say we will all 
die...");
	      }

           }
// more terror news
	   $database->query("SELECT * from Science where sccID='$this->scID' AND ticks=35");
           if ($database->numRows()>0) {
	      $item = $database->fetchArray();
	      $province = new Province($item['pID'],&$database);
	      $province->getProvinceData();
	      $database->query("SELECT * from Kingdom where kiID='$province->kiId'");
	      $kingdom = $database->fetchArray();
	      $database->query("SELECT pID from Province");

              while ($items[]=$database->fetchArray());
              reset ($items);
              foreach ($items as $item) {
                 $terrorNews = new Province($item['pID'],&$database);
	         $terrorNews->postNews("Our wizards felt strong magic beeing used today near the borders in the 
kingdom of $kingdom[name](#$province->kiId) ... It is still unknown to us who did this, but rumours have it that 
the prophecy of the apocalypse is fullfilling...
");
	      }

           }
	   $database->query("SELECT * from Science where sccID='$this->scID' AND ticks=1");
           if ($database->numRows()>0) {
	      $item = $database->fetchArray();
	      $province = new Province($item['pID'],&$database);
	      $province->getProvinceData();
	      $database->query("SELECT * from Kingdom where kiID='$province->kiId'");
	      $kingdom = $database->fetchArray();
	      $database->query("SELECT pID from Province");

              while ($items[]=$database->fetchArray());
              reset ($items);
              foreach ($items as $item) {
                 $terrorNews = new Province($item['pID'],&$database);
	         $terrorNews->postNews("The mad province of $province->provinceName(#$province->kiId) has discovered 
Apocalypse!  We are all doomed...");
	      }

           }
	   
	   // post terror news to everyone
		// end game.
	}
}

} // end if( !class_exists() )
?>