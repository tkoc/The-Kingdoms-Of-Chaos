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
//************************************************
//* server.php
//*
//* The game engine.  This should be run every tick
//* Author: Anders Elton
//*
//* History:
//*	- Rewrite 31.07.2004
//************************************************
// TODO:  writeLog function, writeErr function
require_once("/home/chaos/www/scripts/globals.inc.php");
$GLOBALS['script_mode'] = 'server'; // override the web mode.

require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Military.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Buildings.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Science.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Magic.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "News.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Thievery.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Explore.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Attack.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Race.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Kingdom.class.inc.php");
require_once ($GLOBALS['path_server'] . "Server.class.inc.php");

$database = $GLOBALS['database'];
$config = $GLOBALS['config'];
$start = $GLOBALS['game_start_clock'];
//* RUNNING => END => PAUSE => RUNNING => ...
//*	TIMES
//*	should be put in config.. erm
//* for now.
//* 24*35*2 => 24*7 => 24*4

if ($config['status']!='Running') {
	// PAUSE STATUS
	if ($config['status']=='Pause') {
		// start the game.
		if (intval($config['pause'])<1) 
		{
			$database->query("UPDATE Config set status='Running'");	
			$length = $config['AgeLength'] - $config['ApocalypseLength'];
			$database->query("UPDATE Config set pause='$length'");	 // 1000 ticks until Endgame starts (500 ticks) = 62,5 days.
			$database->query("UPDATE Config set ticks=1");	
			writeLog ($GLOBALS['FILE_LOG'],"\nGAME AUTOMATICALLY STARTED!!!!.");
			writeLog ($GLOBALS['FILE_LOG'],"\nTotal age length: " .  $GLOBALS['config']['AgeLength'] . " hours");
		}
	//  update protection	
	}
	// END-STATUS
    if ($config['status']=='Ended') 
	{
		if ($config['pause']<1) 
		{
			writeLog ($GLOBALS['FILE_LOG'], "\n\nGame is going from Ended TO Pause\n");
			$database->query("UPDATE User set status='Active' where status='Inactive'");
			$database->query("UPDATE Config set status='Pause'");	
			$database->query("UPDATE Config set pause=96");
			sendMassMail($database);
			resetGameData($database);
			writeLog ($GLOBALS['FILE_LOG'],"\n--------------------\nNEW AGE ONLINE ($config[age])\n--------------------\nGAME AUTOMATICALLY SET TO ALLOW SIGNUPS!!!!.");

			$stop = clock();
			$exe_time = $stop - $start;
			$database->query("SELECT count(pID) as totalUsers from Province");
			$totalUsers = $database->fetchArray();

			writeLog ($GLOBALS['FILE_LOG'],
'
tick: '. $config['ticks'] . '
   Script execution time: ' . (clock() - $GLOBALS['game_start_clock']) . '
   STATISTICS
       Number of queries   : ' . $GLOBALS['database_queries_count'] . '
       Number of fetches   : ' . $GLOBALS['database_queries_fetch_count'] . '
       Provinces           : ' . $totalUsers['totalUsers'] .'
   END');
			
		}
//* END OF STATUS-End		
    }
	$server = new Server(&$database);
	$server->removeResetProvince();
	$server->fixKingdoms();
//	$database->query("UPDATE Province set created=NOW()");
	writeLog ($GLOBALS['FILE_LOG'],"\nNot running.($config[status]:$config[pause])");
	$database->query("UPDATE Config set lastTickTime=NOW()");
	// $database->query("UPDATE Config set pause=pause-1 WHERE pause>-1"); // needed????
    $database->query("UPDATE Config set pause=pause-1 WHERE pause>-1");
	die();
}

if ($config['status']=='Running') {
	if ($config['pause']=='1')
	{
		writeLog ($GLOBALS['FILE_LOG'],"\n--------------------\nAPOCALYPSE HITS THE WORLD ($config[age])\n--------------------\nGAME ENDING IN " .$GLOBALS['config']['ApocalypseLength']." hours.");
		$database->query("UPDATE Config set pause='-1'");		// nothing happens until endgame science kills teh world
		$GLOBALS['config']['pause'] = $config['pause'] = -1;
		$endhours = $GLOBALS['config']['ApocalypseLength'] +1;
		$database->query("INSERT INTO Science (pID,sccID,ticks) values ('-1','18','$endhours')");
	}
}


// the ticker!
$database->query("UPDATE Config set pause=pause-1 WHERE pause>-1");
$config['pause']--;
$GLOBALS['config']['pause']--;


$server = new Server(&$database);
$server->prepareTick();


//Attack CLASS
//This HAS to go before Military!!!!!!!!!!!!!
$dummy = NULL;
$attack = new Attack($database);
$attack->doTick();

//Military CLASS
//echo "military";
$militaryStart = clock();
$user = NULL;
$military = new Military($database);
$military->doTick();
$militaryEnd = clock();
$militaryTot = $militaryEnd-$militaryStart;
//echo "ended.";

//Explore CLASS
$explore = new Explore($database, $dummy);
$explore->doTick();


// Building CLASS
// each class is timed.

$dummy = NULL;
$buildingStart= clock();
$buildings = new Buildings( &$database, $dummy );
$buildings->doTick();
$buildingStop =clock();
$buildingsTot = $buildingStop-$buildingStart;


// Science CLASS
$scienceStart = clock();
$myNULL = NULL;
$science = new Science (&$database,$myNULL);
$science->doTick();
$scienceEnd = clock();
$scienceTot = $scienceEnd-$scienceStart;
//echo "science done";

// Magic class
$dummy = NULL;
$magicStart = clock();
$magic = new Magic(&$database,$dummy);
$magic->doTick();
$magicEnd = clock();
$magicTot = $magicEnd - $magicStart;

// thievery CLASS
$thieveryStart = clock();
$th = new Thievery(&$database,false);
$th->doTick();
$thieveryEnd = clock();
$thieveryTot = $thieveryEnd - $thieveryStart;

// RACE class
$dummy = NULL;
$race = new Race(&$database,$dummy);
$race->doTick();

// News CLASS
$newsDel = new News($database);
$newsDel->doTick();

$kingdom = new Kingdom($database);
$kingdom->doTick();

// server class
$server->doTick();

// RecruitBonus class
require_once($GLOBALS['path_www_administration'] . "RecruitPlayers.class.inc.php");

$recruit = new RecruitPlayers($database);
$recruit->doTick();


//$database->query("UPDATE Config set ticks=(ticks+1), lastTickTime=NOW()");

$database->query("SELECT count(pID) as totalUsers from Province");
$totalUsers = $database->fetchArray();

$database->shutdown();



$stop = clock();
$exe_time = $stop - $start;

writeLog ($GLOBALS['FILE_LOG'],
'
tick: '. $config['ticks'] . '
   Script execution time: ' . (clock() - $GLOBALS['game_start_clock']) . '
   STATISTICS
       Number of queries   : ' . $GLOBALS['database_queries_count'] . '
       Number of fetches   : ' . $GLOBALS['database_queries_fetch_count'] . '
       Provinces           : ' . $totalUsers['totalUsers'] .'
   END');



//*
//*
//* functions below

function resetGameData ($database)
{
writeLog ($GLOBALS['FILE_LOG'],"\nResetting game data");

$database->query("DELETE FROM Army") or die($database->error());
$database->query("ALTER TABLE Army AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Attack") or die($database->error());
$database->query("ALTER TABLE Attack AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Buildings") or die($database->error());
$database->query("ALTER TABLE Buildings AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Explore") or die($database->error());
$database->query("ALTER TABLE Explore AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Kingdom") or die($database->error());
$database->query("ALTER TABLE Kingdom AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Login") or die($database->error());
$database->query("ALTER TABLE Login AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM MagicMilitary") or die($database->error());
$database->query("ALTER TABLE MagicMilitary AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Message") or die($database->error());
$database->query("ALTER TABLE Message AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Military") or die($database->error());
$database->query("ALTER TABLE Military AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM News") or die($database->error());
$database->query("ALTER TABLE News AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM NewsProvince") or die($database->error());
$database->query("ALTER TABLE NewsProvince AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM ProgressBuild") or die($database->error());
$database->query("ALTER TABLE ProgressBuild AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM ProgressExpl") or die($database->error());
$database->query("ALTER TABLE ProgressExpl AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM ProgressMil") or die($database->error());
$database->query("ALTER TABLE ProgressMil AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Province") or die($database->error());
$database->query("ALTER TABLE Province AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Science") or die($database->error());
$database->query("ALTER TABLE Science AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM Spells") or die($database->error());
$database->query("ALTER TABLE Spells AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM TmpInCommandMilitary") or die($database->error());
$database->query("ALTER TABLE TmpInCommandMilitary AUTO_INCREMENT = 1") or die($database->error());
$database->query("DELETE FROM adminLogin") or die($database->error());
$database->query("ALTER TABLE adminLogin AUTO_INCREMENT = 1") or die($database->error());

writeLog ($GLOBALS['FILE_LOG'],"\nResetting forum data");
// old forum
$database->query("DELETE FROM Forum where type != 0") or die($database->error());
$database->query("UPDATE Forum SET time=time, ticks=0") or die( $database->error());
$database->query("UPDATE Forum SET guestName='OldAgePlayer', pID='-1', time=time WHERE pID>0") or die( $database->error());
$database->query("ALTER TABLE Forum AUTO_INCREMENT = 1") or die($database->error());
// new forum

if ($database->query("SELECT * FROM ForumMain WHERE kiID>0") && ($database->numRows()>0))
{
	while ($e = $database->fetchArray())
			$f[] = $e;
	reset ($f);
	foreach ($f as $a)
	{
		$query = "DELETE FROM ForumPost WHERE PostForumID=$a[ForumID]";
		$database->query($query);
		$query = "DELETE FROM ForumThread WHERE ThreadForumID=$a[ForumID]";
		$database->query($query);
		$query = "DELETE FROM ForumMain WHERE ForumID=$a[ForumID]";
	}
}

writeLog ($GLOBALS['FILE_LOG'],"\nDONE");

}


function sendMassMail ($database)
{
	writeLog ($GLOBALS['FILE_LOG'],"\nSending Email to users...");
$msg = 'A New Age in The Kingdom of Chaos is Online!

Age '.$GLOBALS['config']['age'].' has just been placed online for registration.

To view the most recent changes please go to the forum and look in the announcement forum.
http://www.tkoc.net/scripts/forum.php?forumID=5

We hope to see you again for a new exciting age
Regards,

Chaos Admins.
';

// do not edit below!!!
$subject = "The Kingdoms of Chaos";
$mailheaders = "From: Chaos Admin <admin@tkoc.net> \n";
$mailheaders .= "Reply-To: admin@tkoc.net\n\n";

$num =0;
$database->query("SELECT * From User WHERE status='Active'");
while ($database->numRows() && ($usr=$database->fetchArray())) {
  $num++;
  $message =  "Dear $usr[name]\n\n" . $msg;
  $mail = $usr['email'];
  mail($mail, $subject, $message, $mailheaders);
}

writeLog ($GLOBALS['FILE_LOG'],"\nDONE ($num messages sent!)");

}
/////////////////////////////////////
// void writeLog(filename, txt)
/////////////////////////////////////
//
// parameters:
//    filename: the filename to write to
//    txt     : the string to write
//
// writes txt to file.
/////////////////////////////////////

function writeLog ($filename, $txt) {
   if (is_writable($filename)) {

      if (!$handle = fopen($filename, 'a')) {
         print "Cannot open file ($filename)";
         exit;
      }

      if (!fwrite($handle, $txt)) {
         print "Cannot write to file ($filename)";
         exit;
      }
      fclose($handle);
   } else {
      echo "Error writing to $filname, file not writeable";
   }
}
?>

