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

//

// Changelog:

// Anders Elton 13.08.04 - Added correct reply mail adress...

// Anders Elton 17.02.04 - om man lager nytt kingdom tar den et "tomt" kingdom som finnes først.  er det ikke tomme kingdoms lager den et.

//

if( !class_exists( "Register" ) ) {

require_once($GLOBALS['path_www_scripts'] . "globals.inc.php");

require_once($GLOBALS['path_www_scripts'] . "Div.func.inc.php" );

require_once($GLOBALS['path_www_scripts'] . "Race.class.inc.php" );

require_once($GLOBALS['path_www_scripts'] . "Buildings.class.inc.php" );

require_once($GLOBALS['path_www_scripts'] . "Science.class.inc.php" );
//include_once ("../language/english.php");

class Register {

	var $db;

	var $password = "";
	
	var $repassword = "";
	
	var $realpassword = ""; 

	var $today;

	var $userMessage = "";

	var $MAX_PROV = 6;

	

	// user vars

	var $lastName = "";

	var $firstName = "";

	var $userName = "";

	var $country = "";

	var $day = "";

	var $month = "";

	var $year = "";
	
	var $email = "";
	
	var $reemail = "";

	

	//Province vars

	var $provinceName = "";

	var $rulerName = "";

	var $gender = "M";

	var $race = 0;

	var $kingdomName = "";

	var $newKingdomName = "";

	var $races = NULL;

	var $buildingObj = NULL;

	var $scienceObj = NULL;



	function Register( $db ) {

		$dummy = NULL;

		$this->db = $db;

		$this->today = getdate();

		$this->day = $this->today['mday'];

		$this->month = $this->today['mon'];

		$this->year = $this->today['year'];

		$raceObj = new Race( $this->db, $dummy );

		$this->races = $raceObj->getAllRaces();

		$this->buildingObj = new Buildings( $this->db, $dummy );

		$this->scienceObj = new Science( $this->db, $dummy );
		

	}

	

	function showNewProvince( $userID ) {

		$html = "";

		if( isset( $_POST['registerProvince'] ) ) {

			if( $this->insertNewProvince( $userID ) ) {

				$html .= $this->okNewProvince();

				return $html;

			} else {

				$html .= "<table align='center'><tr><td>$this->userMessage</td></tr></table><br>";

			}

		}

		$html .= $this->newProvinceForm();

		return $html;

	}

	

	function showNewUser() {

		$html = "";

		if( isset( $_POST['register'] ) ) {

			if( $this->insertNewUser() ) {

				$html .= $this->okNewUser();

				return $html;

			} else {

				$html .= "<table align='center'><tr><td>$this->userMessage</td></tr></table><br>";

			}

		}

		$html .= $this->newUserForm();

		return $html;

	}

	

	function okNewUser() {

		$html = "";

		$html .= "<table align=\"center\" width=\"75%\">

							<tr><td colspan=\"2\" align=\"center\">Congratulations, you've been registered...

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							<br><b>welcome to The Kingdoms of Chaos</b><br>&nbsp;

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							We will automatically send you an email with your account information.							

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							We recommend that you read through the 

							<a href='guide.html' target='_blank'><i><b>guide</b></i></a> and the 

							<a href='/tutorial/tut1.htm' target='_blank'><i><b>tutorial</b></i></a> before you create your new province.

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							Go to the login page to set up your province and when you've done that, 

			 			  you might go to the preferences page to change your password. If you for some 

				 			reason can't log in, please post a thread in the world forum.

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							We wish you good luck!

							</td></tr><tr><td colspan=\"2\" align=\"center\">

							- Admins -

							</td></tr></table>";

		return $html;

	}

	

	function okNewProvince() {

		$html = "";

		$html .= "<br>Province: $this->provinceName";

		$html .= "<br>Ruler: $this->rulerName";

		$html .= "<br>Password: $this->password";

		$html .= "<br><br><b><a href='./scripts/showProvince.php'>Continue to Game</a></b>";

		return $html;

	}


	function insertNewProvince( $userID ) {

		$result = true;

		// Check all input for errors

		if( isset( $_POST['provinceName'] ) && strlen( $_POST['provinceName'] ) ) { 

			if( is_string( $_POST['provinceName'] ) ) {

				$this->provinceName = $_POST['provinceName'];

				if( !strcmp( $_POST['provinceName'] , "Administrator" ) || !strcmp( $_POST['provinceName'] , "God" ) ) {

					$this->userMessage .= "You have to name your province something else!<br>";

					$result = false;

				}

			} else {

				$this->userMessage .= "You have to enter a text as your province name<br>";

				$result = false;

			}

		} else {

			$this->userMessage .= "You have to enter a province name<br>";

			$result = false;

		}

		

		if( isset( $_POST['rulerName'] ) ) { 

			if( is_string( $_POST['rulerName'] ) && strlen( $_POST['rulerName'] ) ) {

				$this->rulerName = $_POST['rulerName'];

				if( !strcmp( $_POST['rulerName'] , "ADMIN" ) ) {

					$this->userMessage .= "You have to name your ruler something else!<br>";

					$result = false;

				}

			} else {

				$this->userMessage .= "You have to enter a text as your ruler name<br>";

				$result = false;

			}

		} else {

			$this->userMessage .= "You have to enter a ruler name<br>";

			$result = false;

		}

		

		if( isset( $_POST['gender'] ) ) { 

			$this->gender = $_POST['gender'];

		} else {

			$this->userMessage .= "You have to enter a gender<br>";

			$result = false;

		}

		

		if( isset( $_POST['race'] ) ) { 

			$this->race = $_POST['race'];

		} else {

			$this->userMessage .= "You have to enter a race<br>";

			$result = false;

		}

		

		if( isset( $_POST['kingdomName'] ) && strlen( $_POST['kingdomName'] ) ) { 

			$this->kingdomName = $_POST['kingdomName'];

			if( !strcmp( $this->kingdomName, "new" ) ) {





// DETTE ER LAGT INN SOM FEILMELDING FORDI MAN IKKE SKAL KUNNE REGISTRERE NYE KONGEDØMMER!!!

//	$this->userMessage .= "Sorry, but you have to choose an existing kingdom or a random kingdom because we want more people in each kingdom<br>";

//	$result = false;

//DETTE ER UTKOMMENTERT FORDI DET IKKE SKAL GÅ Å OPPRETTE NYE KONGEDØMMER

//START ENABLE REG   (utkommenter mellom start og end enable reg for å hindre reg)

				if( isset( $_POST['newKingdomName'] ) && strlen( $_POST['newKingdomName'] ) ) { 

					if( is_string( $_POST['newKingdomName'] ) ) {

						$this->newKingdomName = $_POST['newKingdomName'];

					} else {

						$this->userMessage .= "You have to enter a text as your new kingdom name<br>";

						$result = false;

					}

				} else {

					$this->userMessage .= "You have to enter a name for your new kingdom<br>";

					$result = false;

				}

//END ENBL REG

			}

		} else {

			$this->userMessage .= "You have to select a existing / new / random kingdom<br>";

			$result = false;

		}		

		

		if( isset( $_POST['password'] ) && strlen( $_POST['password'] ) ) { 

			if( is_string( $_POST['password'] ) ) {

				$this->password = $_POST['password'];

			} else {

				$this->userMessage .= "You have to enter a text as your password<br>";

				$result = false;

			}

		}

		

		// check against the DB for duplicates / required passwords

		if( $result ) {

			$selectSQL = "SELECT provinceName  

						FROM Province

						WHERE provinceName LIKE '$this->provinceName'";

			if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

				$this->userMessage .= "Province name already in use! Please enter a different province name<br>";

				$result = false;	

			}

			$selectSQL = "SELECT pID

										FROM User 

										WHERE userID ='".$userID."' 

											AND pID != '-1'";

			if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

				$this->userMessage .= "You have already registered a province for this age! Please 

															log in to the game, go to preferences and delete your province, 

															then wait one tick (one hour at most) for your province to be 

															deleted. Then you're welcome to create a new province.<br>";

				$result = false;

			}

			if( !strcmp( $this->kingdomName, "new" ) ) {

				$selectSQL = "SELECT name  

						FROM Kingdom

						WHERE name LIKE '$this->newKingdomName'";

				if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

					$this->userMessage .= "Kingdom name already in use! Please enter a different kingdom name<br>";

					$result = false;	

				}

			}

			

			if( strcmp( $this->kingdomName, "new") && strcmp( $this->kingdomName, "random" ) ) {

				$selectSQL = "SELECT password, numProvinces

							 FROM Kingdom

							 WHERE kiID LIKE '$this->kingdomName'";

				if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

					$row = $this->db->fetchArray();

					if( strlen( $row['password'] ) && ( strcmp( $row['password'], $this->password ) ) ) {

						$this->userMessage .= "You have to enter the correct password to join this kingdom<br>";

						$result = false;

					}

					if( $row['numProvinces'] >= $this->MAX_PROV ) {

						$this->userMessage .= "This kingdom is full. Try another kingdom.<br>";

						$result = false;

					}	

				}

			}

			

			if( !strcmp( $this->kingdomName, "random" ) ) {

				$selectSQL = "SELECT kiID, numProvinces

							 FROM Kingdom

							 WHERE numProvinces < ".$this->MAX_PROV." 

							 AND password LIKE '' 

							 ORDER BY numProvinces ASC";

				if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

					$row = $this->db->fetchArray();

					$this->kingdomName = $row['kiID'];

				} else {

					$this->userMessage .= "All kingdoms are full / password protected. Please create a new kingdom.<br>";

					$result = false;

				}

			}			

		}

		

		// make kingdom

		if( $result ) {

			if( !strcmp( $this->kingdomName, "new" ) ) {

				$this->db->query( "SELECT kiID FROM Kingdom where numProvinces=0 ORDER BY kiID ASC" );

				$newKingdomName = $this->newKingdomName;

				if ($this->db->numRows()>0) {

					// there is an empty unused kingdom, lets hijack it instead of creating a "real" new one.

					// added 18.02.04 by Anders.

					$hijackID = $this->db->fetchArray();

					$this->kingdomName = $hijackID['kiID'];

					$updateSQL = "UPDATE Kingdom set

									name='$newKingdomName', password='$this->password', numProvinces='0'

									WHERE kiID='$this->kingdomName'";

					$this->db->query( $updateSQL );

					$this->writeHistory( "Wanted to create new Kingdom.  Found empty kingdom $this->kingdomName - hijacking.", $userID );

				} else {

					$insertSQL = "INSERT INTO Kingdom 

							( name, password, numProvinces )

							 VALUES ( '$newKingdomName', '$this->password', '0' )";

					$this->db->query( $insertSQL );

					$this->kingdomName = $this->db->lastInsertId();

				}

				$selectSQL = "SELECT kiID FROM Kingdom WHERE name LIKE '$newKingdomName' AND kiID='$this->kingdomName'";

				if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

					$row = $this->db->fetchArray();

					$this->kingdomName = $row['kiID'];

					$created = $this->today['year']."-".$this->today['mon']."-".$this->today['mday'];

					$this->writeHistory( "Created the kingdom of $newKingdomName the $created.", $userID );
				} else {

					$this->writeHistory( "Error creating kingdom!  SELECT kiID FROM Kingdom WHERE name LIKE '$newKingdomName' AND kiID='$this->kingdomName'<br>", $userID );

					$this->userMessage .= "Error creating kingdom! Please tell the admins.";

					$result = false;

				}

			}

		}

		

		// make province

		if( $result ) {			

			$provinceName = $this->provinceName;

			$rulerName = $this->rulerName;

			$created = $this->today['year']."-".$this->today['mon']."-".$this->today['mday'];			

			$this->writeHistory( "Tried to create the province of $provinceName ( #".$this->kingdomName." ) the $created.", $userID);

			

			$insertSQL = "INSERT INTO Province 

					( provinceName, rulerName, gender, kiID, spID, created )

					 VALUES 

			 ( '$provinceName', '$rulerName', '$this->gender', '$this->kingdomName' ,'$this->race', NOW())";

//			echo $insertSQL;

			$this->db->query( $insertSQL );
			
			if($this->race == 6){
				$updateinfluenceSQL = "UPDATE Province SET influence = 110 WHERE provinceName LIKE '".$this->provinceName."'";
				$this->db->query($updateinfluenceSQL);
			}
			if($this->race == 7){
				$updatemanaSQL = "UPDATE Province SET mana = 110 WHERE provinceName LIKE '".$this->provinceName."'";
				$this->db->query($updatemanaSQL);
			}
			 
			
			
			//PETRO EDW THA BALW KWDIKA GIA TO BUG POU POSTARE O GIEL DLD OTAN KANEIS NEW KD BGAZEI PALIA NEA 
			//APLA ME TO POU KSEKINHSA ME PHRAN THL GIA KAFE :P
			//$selectSQL = "SELECT ";
			
			
			
			$updateSQL = 	"UPDATE Kingdom 

					SET numProvinces = numProvinces + 1 

					WHERE kiID LIKE '".$this->kingdomName."'";

			$this->db->query( $updateSQL );

			$selectSQL = "SELECT pID FROM Province WHERE provinceName LIKE '$provinceName'";

			if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

				$row = $this->db->fetchArray();

				$updateSQL = 	"UPDATE User 

								SET pID='".$row['pID']."' 

								WHERE userID LIKE '".$userID."'

									AND pID='-1'";

				$this->db->query( $updateSQL );

				$this->buildingObj->setStartBuildings( $row['pID'] );

				$this->writeHistory( "Created the province of $provinceName.<br>\n", $userID);

				$this->scienceObj->setStartScience( $row['pID'] );

				

				$province = new Province($row['pID'], $this->db);

				$province->getProvinceData();

				$province->getMilitaryData();  // create the military rows etc.

				$province->milObject->create( $GLOBALS['MilitaryConst']->SOLDIERS, 300);
				$province->getProvinceData();
				/*$updateSQL = "UPDATE Province set acres='300' where pID ='".$row['pID']."'";

				$this->db->query( $updateSQL );*/

				

			} else {

				$this->userMessage .= "Error creating province! Please tell the admins.<br>";

				$result = false;	

			}

		}		

		return $result;

	}


		

	function getJavaScript() {

		$html = '	<script language="JavaScript" type="text/JavaScript">

							<!--

								function checkNewProvince(){		

									var x=document.getElementById("kingdomName")

									var text = x.options[x.selectedIndex].value;		

									if( text == "new" ) {

										var userMessage="You have chosen to create a new Kingdom. We strongly recommend "+

																		"new players or players who does not know who they want in their "+

																		"kingdom to join anoter kingdom. Playing alone or with very few "+

																		"people in a kingdom is less fun and a lot tougher. If you have any "+

																		"questions or you want a full kingdom, please visit the Kingdoms thread "+

																		"in the world forum."+

																		"\nDo you want to continue creating a new kingdom?";

										return confirm( userMessage );

									}

									return true;

								}

							-->

							</script>';

		return $html;

	}	

		

	function newProvinceForm() {

		$html = "";

		$html .= $this->getJavaScript();

		$html .= "<form action='".$_SERVER['PHP_SELF']."' method='POST' name='NewProvince' onSubmit='return checkNewProvince();'>

				 <table class='form' align='center'>

				 <tr><td colspan='3'>

				 		We recommend that you read through the 

						<a href='guide.html' target='_blank'><i><b>guide</b></i></a> and the 

						<a href='/tutorial/tut1.htm' target='_blank'><i><b>tutorial</b></i></a> before you create your new province.

				 </td></tr>

				 ";

		$html .= "<tr><td>Province info:</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

		$html .= "<tr>

					<td>Name of your province:</td>

			<td><input type='text' name='provinceName' value='$this->provinceName' title='Write your province name here' maxlength='20' size='20'></td>

					<td rowspan='7'>";

		//foreach( $this->races as $race ) {

		//	$html .= $race->baseFunction( $GLOBALS['RaceConstants']->GET_DESCRIPTION )."<br><br>";

		//}

		$html .= "&nbsp</td>";

		$html .= "</tr>";

		$html .= "<tr>

					<td>Ruler name:</td>

					<td><input type='text' name='rulerName' value='$this->rulerName' title='Write your ingame name here' maxlength='20' size='20'></td>

				</tr>";

		$html .= "<tr>

					<td>Select gender:</td>

					<td>".

						( !strcmp( "M", $this->gender ) ? 

							"<input name='gender' type='radio' value='M' checked>Male<br>":

							"<input name='gender' type='radio' value='M'>Male<br>" ).

						( !strcmp( "F", $this->gender ) ? 

							"<input name='gender' type='radio' value='F' checked>Female<br>":

							"<input name='gender' type='radio' value='F'>Female<br>" )."						

					</td>

				</tr>";

		$html .= "<tr>

					<td>Select race:</td>

					<td><select name='race' title='Select your race here'>";

		foreach( $this->races as $race ) {

			$rID = $race->baseFunction( $GLOBALS['RaceConstants']->GET_ID );

			if( $this->race == $rID ) {

				$html .= "<option value='$rID' selected>

							".$race->baseFunction( $GLOBALS['RaceConstants']->GET_NAME )."	

						  </option>";

			} else {

				$html .= "<option value='$rID'>

							".$race->baseFunction( $GLOBALS['RaceConstants']->GET_NAME )."	

						  </option>";

			}

		}

		$html .= "</select></td>

				</tr>";

		$html .= "<tr>

					<td>Select kingdom:</td>

					<td>

						<select name='kingdomName' id='kingdomName' title='Select new / random / existing kingdom'>".

						( ( !strcmp( $this->kingdomName, "New kingdom" ) 

						|| ( !strlen( $this->kingdomName ) ) )?

						"<option value='random' selected>Random kingdom</option>":

						"<option value='random'>Random kingdom</option>" ).

						( !strcmp( $this->kingdomName, "New kingdom" ) ?

						"<option value='new' selected>New kingdom</option>" :

						"<option value='new'>New kingdom</option>" ).						

						"<option value=''>&nbsp;</option>>";

		$selectSQL = 	"SELECT name, kiID, password, numProvinces as number

							FROM Kingdom K

							ORDER BY K.name";

		if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {

			while( $row = $this->db->fetchArray() ) {
				$additional = "";
				if( strlen( $row['password'] ) ) {
					

					if( $row['number'] >= $this->MAX_PROV ) {

						$additional = "full";

					} else {

						$additional = $row['number'];

					}

					if( !strcmp( $this->kingdomName, $row['kiID'] ) ) {

						$html .= "<option value='".$row['kiID']."' selected>".$row['name']." ($additional) (password)</option>";

					} else {

						$html .= "<option value='".$row['kiID']."'>".$row['name']." ($additional) (password)</option>";

					}

				} else {

					if( !strcmp( $this->kingdomName, $row['kiID'] ) ) {

						$html .= "<option value='".$row['kiID']."' selected>".$row['name']." ($additional)</option>";

					} else {

						$html .= "<option value='".$row['kiID']."'>".$row['name']." ($additional)</option>";

					}

				}

			}

		}

		$html .= "	</td>

				</tr>";

		$html .= "<tr>

					<td>New kingdom name:</td>

					<td><input type='text' name='newKingdomName' value='$this->newKingdomName' title='Write the name of your new kingdom here' maxlength='20' size='20'></td>";

		$html .= "<tr>

					<td>Kingdom password:</td>

					<td><input type='password' name='password' value='$this->password' title='Write the password others have to enter to join your new kingdom here' maxlength='8' size='20'></td>

				</tr>";

		$html .= "<tr>

					<td>&nbsp;</td>

					<td>

						<input type='submit' name='registerProvince' value='Create' title='Click to register your new province'>

						&nbsp; &nbsp;<input type='reset' value='Reset' title='Click to reset the form'>

					</td>

					<td>&nbsp;</td>

				</tr>";

		$html .= "</table></form>";

		return $html;

	}

	

	function writeHistory( $text, $uID ) {

		$selectSQL = "SELECT history FROM User WHERE userID=$uID";

		$result = $this->db->query( $selectSQL );

		if( $result && $this->db->numRows() ) {

			$row = $this->db->fetchArray( $result );

		$history = $row['history'];

			$history .= "\n<br>".$text;

			$updateSQL = "UPDATE User SET history='$history' WHERE userID=$uID";

			$this->db->query( $updateSQL );

		} else {

			return false;

		}

	}

}

} // end class exists

?>

