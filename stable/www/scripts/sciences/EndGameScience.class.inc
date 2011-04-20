<?php
// 

require_once("ScienceBase.class.inc.php");

if( !class_exists( "EndGameScience" ) ) {

class EndGameScience extends ScienceBase {
        function EndGameScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID, "magic" ,"Time Stop", 300, 0, 0,"
Ends the game.  This science should not appear in your science list!  If it appears, contact the admins IMMIDIATLY!
",
/* requires */
array('military' =>1024, "infrastructure" => 1024, "magic" => 1024, "thievery" =>1024),
/* Gives */
array("military" => 1024, "infrastructure" => 1024, "magic" => 1024, "thievery" => 1024)

		);    
        }
	function doTick ($database) {
		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=500");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("Our council has just fled in fear of the Apocalypse!");
			}
			$database->query("UPDATE Province set council=0");
		}


		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=440");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("A strange Apocalyptic shockwave has decimated our population!  Maybe we should go underground..?");
			}
			$database->query("UPDATE Province set peasants=(peasants*0.8)");
		}


		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=120");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("The people is revolting!  They fear the apocalypse..  so should we!");
			}
			$database->query("UPDATE Province set goldChange=0, metalChange=0, foodChange=0");
		}
/*
		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=120");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("In your throne room your master wizard come out from the shadow.  With a shaking voice he tells you that all the mana has been drained from this world... You bet it has something to do with the apocalypse!");
			}
			$database->query("UPDATE Province set mana=mana*0.75");
		}
// more terror news

		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=150");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("Our generals report that the morale of our troops is gone since they all think we will die.. That damn apocalypse!");
			}
			$database->query("UPDATE Province set morale=morale*0.75");
		}
	   
		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=100");
		if ($database->numRows()>0) {
			$database->query("SELECT pID from Province where status='Alive'");
			while ($items[]=$database->fetchArray());
			reset ($items);
			foreach ($items as $item) {
				$terrorNews = new Province($item['pID'],&$database);
				$terrorNews->postNews("Our thief guild reports that all of our thieves has left the lands! Seems like everyone is fleeing..");
			}
			$database->query("UPDATE Province set influence=infuence*0.75");
		}
*/
		$database->query("SELECT * from Science where sccID='$this->scID' AND ticks=1");
		
		// ALL end stuff should go here... 
		if ($database->numRows()>0) 
		{
			$age = $GLOBALS['config']['age'];
			$database->query("UPDATE Config set status='Ended', pause='72'");
			$database->query("UPDATE Login set loggedon='N'");
			$database->query("UPDATE User, Login SET User.lastPlayedAge='$age' WHERE User.userID=Login.userID");
			$this->getAgeEndScores($database);
		}

	   // post terror news to everyone
		// end game.
	} // doTick function

    function getAgeEndScores ($database)
    {
        require_once( $GLOBALS['path_www_scripts'] .  ResultsBuilder.class.inc.php );
        $rb = new ResultsBuilder( $database );
        $rb->saveResults();
/*			$items = array ();
                        $database->query("SELECT age FROM Config"); 
                        $age = $database->fetchArray();
 
                       $database->query("SELECT  		provinceName, networth, acres, reputation, Province.kiID, 
Kingdom.name,User.userID,username 
								FROM Province 
								LEFT JOIN Kingdom on Province.kiID=Kingdom.kiID 
								LEFT JOIN User on Province.pID=User.pID 
									WHERE Province.status='Alive' AND Province.kiID>0
						");
                        while ($items[]=$database->fetchArray());
                        reset ($items);
                        foreach ($items as $item) {
				if (isset($item['provinceName']))
                        	$database->query("INSERT INTO AgeEndScores
                        (age,provinceName,networth,acres,reputation,kingdomID,kingdomName,userID) VALUES
                        ('".$age['age']."','".$item['provinceName']."','".$item['networth']."','".$item['acres']."',
                        '".$item['reputation']."','".$item['kiID']."','".$item['name']."','".$item['userID']."')");
                        
                        }
                         
	}        
*/
}

} // end if( !class_exists() )
?>
