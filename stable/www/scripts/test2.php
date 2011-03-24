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
// we will do our own error handling
error_reporting(0);

// user defined error handling function
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) 
{
   // timestamp for the error entry
   $dt = date("Y-m-d H:i:s (T)");

   // define an assoc array of error string
   // in reality the only entries we should
   // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
   // E_USER_WARNING and E_USER_NOTICE
   $errortype = array (
               E_ERROR          => "Error",
               E_WARNING        => "Warning",
               E_PARSE          => "Parsing Error",
               E_NOTICE          => "Notice",
               E_CORE_ERROR      => "Core Error",
               E_CORE_WARNING    => "Core Warning",
               E_COMPILE_ERROR  => "Compile Error",
               E_COMPILE_WARNING => "Compile Warning",
               E_USER_ERROR      => "User Error",
               E_USER_WARNING    => "User Warning",
               E_USER_NOTICE    => "User Notice",
               E_STRICT          => "Runtime Notice"
               );
   // set of errors for which a var trace will be saved
   $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
   
   $err = "<errorentry>\n";
   $err .= "\t<datetime>" . $dt . "</datetime>\n";
   $err .= "\t<errornum>" . $errno . "</errornum>\n";
   $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
   $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
   $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
   $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

   if (in_array($errno, $user_errors)) {
       $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
   }
   $err .= "</errorentry>\n\n";
   
   // for testing
   echo $err;

   // save to the error log, and e-mail me if there is a critical user error
   error_log($err, 3, "/usr/local/php4/error.log");
//   if ($errno == E_USER_ERROR) {
//       mail("phpdev@example.com", "Critical User Error", $err);
//   }
//	echo $err;
}


function distance($vect1, $vect2) 
{
   if (!is_array($vect1) || !is_array($vect2)) {
       trigger_error("Incorrect parameters, arrays expected", E_USER_ERROR);
       return NULL;
   }

   if (count($vect1) != count($vect2)) {
       trigger_error("Vectors need to be of the same size", E_USER_ERROR);
       return NULL;
   }

   for ($i=0; $i<count($vect1); $i++) {
       $c1 = $vect1[$i]; $c2 = $vect2[$i];
       $d = 0.0;
       if (!is_numeric($c1)) {
           trigger_error("Coordinate $i in vector 1 is not a number, using zero", 
                           E_USER_WARNING);
           $c1 = 0.0;
       }
       if (!is_numeric($c2)) {
           trigger_error("Coordinate $i in vector 2 is not a number, using zero", 
                           E_USER_WARNING);
           $c2 = 0.0;
       }
       $d += $c2*$c2 - $c1*$c1;
   }
   return sqrt($d);
}

$old_error_handler = set_error_handler("userErrorHandler");

// undefined constant, generates a warning
$t = I_AM_NOT_DEFINED;

// define some "vectors"
$a = array(2, 3, "foo");
$b = array(5.5, 4.3, -1.6);
$c = array(1, -3);

// generate a user error
$t1 = distance($c, $b) . "\n";

// generate another user error
$t2 = distance($b, "i am not an array") . "\n";

// generate a warning
$t3 = distance($a, $b) . "\n";

?>
