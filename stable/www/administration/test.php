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
require_once ("../scripts/globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_administration'] . "admin_all.inc.php");
require_once ($GLOBALS['path_www_administration'] . "Div.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "isLoggedOn.inc.php");
require_once ("requireLogon.inc.php");
//die("TEST");


if ((intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_MODERATOR) == false )
{
	die("tkoc.net");
}

//
// ---- MAIN PROGRAM START ----

$database = $GLOBALS['database'];

$where = "";
if (isset($_GET['Age']))
{
	$where = " WHERE Age='".$_GET['Age']."'";
}

$database->query("SELECT ID, Age, Provinces, Logins, Tick, ServerStatus, Heroes from Log" . $where);

while (($res = $database->fetchArray()))
{
	$DataArray[$res['Age']][$res['ID']] = $res;
}

// Get last age.

if (isset($_GET['Age']))
{
	$database->query("SELECT ID, Age, Provinces, Logins, Tick, ServerStatus, Heroes from Log  WHERE Age='".($_GET['Age'] -1)."'");

	while (($res = $database->fetchArray()))
	{
		$DataArray2[$res['Age']][$res['ID']] = $res;
	}

}

$database->query("SELECT MAX(Logins) as maxlogins FROM Log");

$res = $database->fetchArray();
$maxLogins = $res['maxlogins'];

$database->query("SELECT MAX(Provinces) as maxProvince FROM Log");

$res = $database->fetchArray();
$maxProvince = $res['maxProvince'] + 50;



// specify diagram parameters (these are global)
$diagramHeight = 400;    
$ticksInPixel = 2;
$daysToShow = (($GLOBALS['config']['AgeLength'] / 24) + 10) / $ticksInPixel;
$diagramWidth = $daysToShow*24;

// create image
$image = imageCreate($diagramWidth, $diagramHeight);



// allocate all required colors
$colorBackgr       = imageColorAllocate($image, 192, 192, 192);
$colorForegr       = imageColorAllocate($image, 255, 255, 255);
$colorGrid         = imageColorAllocate($image, 0, 0, 0);
$colorCross        = imageColorAllocate($image, 0, 0, 0);
$colorPhysical     = imageColorAllocate($image, 0, 0, 255);
$colorIntellectual = imageColorAllocate($image, 0, 255, 0);
$colorAge = imageColorAllocate($image, 0, 0, 255);
$colorProvinces = imageColorAllocate($image, 0, 255, 0);
$colorAgeStart    = imageColorAllocate($image, 255, 0, 0);
$colorAgeEnd    = imageColorAllocate($image, 255, 0, 0);
// clear the image with the background color
imageFilledRectangle($image, 0, 0, $width - 1, $height - 1, $colorBackgr);


for ($i = 1; $i < $daysToShow; $i++)
{
    $xCoord = ($diagramWidth / $daysToShow) * $i;

    // draw day mark and day number
    imageLine($image, $xCoord, $diagramHeight - 25, $xCoord,
              $diagramHeight - 20, $colorGrid);
    imageString($image, 3, $xCoord - 5, $diagramHeight - 16,
                $i*$ticksInPixel, $colorGrid);

}

$max = $maxLogins + ($maxLogins/90) + 10;
for ($i=1; $i<=10; $i++)
{
	$yCoord = ($diagramHeight / 10) * $i;
        imageLine($image, 1, $yCoord, 5,
              $yCoord, $colorGrid);

    imageString($image, 3,3, $yCoord, (10 - $i) * ($diagramHeight / 10), $colorProvinces);

}



foreach ($DataArray as $Age)
{
	$started = false;
	$currentx = 0;
	$lasty = 0;
	$lastProvince = 0;
	$counter=0;
	foreach ($Age as $TickEntry)
	{
		if ($started == false)
		{
			if ($TickEntry['ServerStatus'] == "Running")
			{
		imageLine($image, $currentx, $diagramHeight , $currentx, 0, $colorAgeStart);
				$started = true;
			}
		}
		else
		{
			if ($TickEntry['ServerStatus'] == "Ended")
			{
		imageLine($image, $currentx, $diagramHeight , $currentx, 0, $colorAgeEnd);
				$started = false;
			}
		}
		
		$percent += ($TickEntry['Logins']*100)/$max;
		$percentProvince += ($TickEntry['Provinces']*100)/$maxProvince;

		if (($counter % $ticksInPixel) == 0)
		{
			$y = $diagramHeight*($percent/$ticksInPixel)/100;
			$yprovince = $diagramHeight*($percentProvince/$ticksInPixel)/100;
			imageLine($image, $currentx, $diagramHeight - ($lasty+20), $currentx, $diagramHeight - ($y+20), $colorAge);
			imageLine($image, $currentx, $diagramHeight - ($lastProvince+20), $currentx, $diagramHeight - ($yprovince+20), $colorProvinces);
			$percent = 0;
			$percentProvince=0;
			$currentx++;
		}	
		$counter++;
		$lasty = $y;
		$lastProvince = $yprovince;
//		echo "$y ... " . $TickEntry['Logins'] . "percent: $percent<br>";
	}
	break;
}

// last age
if (isset($_GET['Age']))
{
foreach ($DataArray2 as $Age)
{
	$started = false;
	$currentx = 0;
	$lasty = 0;
	$lastProvince = 0;
	$counter=0;
	foreach ($Age as $TickEntry)
	{
		if ($started == false)
		{
			if ($TickEntry['ServerStatus'] == "Running")
			{
//		imageLine($image, $currentx, $diagramHeight , $currentx, 0, $colorAgeStart);
				$started = true;
			}
		}
		else
		{
			if ($TickEntry['ServerStatus'] == "Ended")
			{
//		imageLine($image, $currentx, $diagramHeight , $currentx, 0, $colorAgeEnd);
				$started = false;
			}
		}
		
		$percent += ($TickEntry['Logins']*100)/$max;
		$percentProvince += ($TickEntry['Provinces']*100)/$maxProvince;

		if (($counter % $ticksInPixel) == 0)
		{
			$y = $diagramHeight*($percent/$ticksInPixel)/100;
			$yprovince = $diagramHeight*($percentProvince/$ticksInPixel)/100;
		//	imageLine($image, $currentx, $diagramHeight - ($lasty+20), $currentx, $diagramHeight - ($y+20), $colorAge);
			imageLine($image, $currentx, $diagramHeight - ($lastProvince+20), $currentx, $diagramHeight - ($yprovince+20), $colorAgeStart);
			$percent = 0;
			$percentProvince=0;
			$currentx++;
		}	
		$counter++;
		$lasty = $y;
		$lastProvince = $yprovince;
//		echo "$y ... " . $TickEntry['Logins'] . "percent: $percent<br>";
	}
	break;
}

} //isset

//print("$diagramWidth, $diagramHeight, $colorGrid");
//die("dead");









// draw rectangle around diagram (marks its boundaries)
imageRectangle($image, 0, 0, $diagramWidth - 1, $diagramHeight - 20,
               $colorGrid);

// draw middle cross
imageLine($image, 0, ($diagramHeight - 20) / 2, $diagramWidth,
          ($diagramHeight - 20) / 2, $colorCross);
imageLine($image, $diagramWidth / 2, 0, $diagramWidth / 2, $diagramHeight - 20,
          $colorCross);

// set the content type
header("Content-type:  image/gif");
//header("Content-type:  image/png");

// create an interlaced image for better loading in the browser
imageInterlace($image, 1);

// mark background color as being transparent
imageColorTransparent($image, $colorBackgr);
//die("TEST2");
// now send the picture to the client (this outputs all image data directly)
imageGIF($image);
//imagePNG($image);

?>
