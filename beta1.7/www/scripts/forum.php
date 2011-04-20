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
		include_once ("../includes/functions.php");
		include_once ("../includes/session.php");
		$session = new Session;


		if ( $session->checkLogin() )
		{
			$username = $_SESSION['username'];
			$password = $_SESSION['password'];


			include_once("./header.php");
			include_once("./navigation.php");
			
			
			// BEGIN: Epikefalida
			echo "<div>";
			echo 	"<p id=\"overview\">Forum</p>";
			echo "</div>";
			// END
			
			
			// BEGIN: Perigrafi tis selidas
			echo "<div>";
			echo 	"<p id=\"plaintext\">";
			echo 		"Sir, on this page you can contact your teamplayers and organize your way to victory!";
			echo 	"</p>";
			echo "</div>";
			// END
			
			if( isset($_GET['categ']) ) {
				$categ = $_GET['categ'];
				
				if( strcmp($categ,"post") == 0 ) 
					include("./forum/post.php");
				else if( strcmp($categ,"read") == 0 )
					include("./forum/read.php");
			}
			else
				require_once("./forum/index.php");	
			
			include_once("./footer.php");
        }
        else
 		{
			/* NOT LOGGED IN -> REDIRECT TO THE GAME */
			header("Location: ../login.php");
		}

?>