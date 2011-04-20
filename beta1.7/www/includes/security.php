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

/* ALL THE FUNCTIONS NEEDED */
class Security {
	function sanitizeData ($variable, $type) {
		$variable = trim($variable);
		switch ($type) {
			case "string":
				$variable = filter_var ($variable, FILTER_SANITIZE_STRING);
				break;
			case "int":
				$variable = filter_var ($variable, FILTER_SANITIZE_NUMBER_INT);
				break; 
			case "email":
				$variable = filter_var ($variable, FILTER_SANITIZE_EMAIL);
				break; 
			default:
				$variable = filter_var ($variable, FILTER_SANITIZE_STRING);
				break;
		}
		
		return $variable;
	}
	
	function validData ($variable, $type) {
		switch ($type) {
			case "int":
				$check = filter_var ($variable, FILTER_VALIDATE_INT);
				break; 
			case "email":
				$check = filter_var ($variable, FILTER_VALIDATE_EMAIL);
				break;
			case "alnum":
				$check = ctype_alnum ($variable);
				break;
			case "alpha":
				$check = ctype_alpha ($variable);
				break;
			case "digit":
				$check = ctype_digit ($variable);
				break;
			default:
				$check = filter_var ($variable, FILTER_SANITIZE_STRING);
				break;
		}
		
		return $check;
	}
	
	function encryptData ($value) {
		return sha1(md5($value, TRUE));
	}
}
?>