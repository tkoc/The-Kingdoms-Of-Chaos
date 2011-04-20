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


class myDatabase {
	var $errors = array ();
	var $dbarray= array ();
	var $link_id;
	
	
	function __construct ($dbhost,$dbusername,$dbpassword, $dbname) {
		$this->connectDB ($dbhost,$dbusername,$dbpassword, $dbname);
	}
	
	
	function connectDB ($dbhost,$dbusername,$dbpassword, $dbname) {
		/*@include_once ("./data/data.php");
		if (empty($dbhost)) {
			@include_once ("../data/data.php");
			if (empty($dbhost))
				@include_once ("../../data/data.php");
		}*/
		
		// Linking to the database
		$this->link_id = mysql_connect($dbhost,$dbusername,$dbpassword, true);
		if(!$this->link_id)
			die ('Could not connect this database:'. mysql_error());

		/*** Select the specific database ***/
		mysql_select_db($dbname,$this->link_id);
		/************************************/
	}
	
	function insertUser ($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10) {
		$data = array($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10);
		$data = $this->protectSQL ($data);
		/* Insert User */
		$query = "INSERT INTO User (name, country, email, dob, userName, password, created, pID, history, access) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
				
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertUserPrefs ($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10, $a11, $a12, $a13, $a14) {
		$data = array($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10, $a11, $a12, $a13, $a14);
		$data = $this->protectSQL ($data);
		/* Insert User */
		$query = "INSERT INTO USER_PREFS (UP_username, UP_password, UP_email, UP_firstname, UP_lastname, UP_phone, UP_country, UP_state, UP_city, UP_postalcode, UP_adress, UP_mobile, UP_mobilecode, UP_mobileverified) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
				
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	
	function insertPage ($formurl, $formtitle, $formhtitle, $formcategory, $text, $formlanguage, $formlevel, $formorder, $formbutton, $type, $formkeywords) {
		
		$data=array($formurl, $formtitle, $formhtitle, $formcategory, $text, $formlanguage, $formlevel, $formorder, $formbutton, $type, $formkeywords);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO DATA (D_page, D_title, D_htitle, D_category, D_text, D_lang, D_level, D_order, D_link, D_type, D_keywords) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	
	function insertNews ($title, $lang, $date, $text) {
		$data=array($title, $lang, $date, $text);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO NEWS (N_title, N_lang, N_date, N_text) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertPhoto ($text, $jstext, $photo, $thumb, $order, $category, $alt, $lang) {
		$date = time();
		
		$data=array($text, $jstext, $photo, $thumb, $order, $category, $alt, $lang, $date);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO GALLERY (G_text, G_jstext, G_photo, G_thumb, G_order, G_category, G_alt, G_lang, G_date) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		/*
		$query = "INSERT INTO GALLERY (G_text, G_jstext, G_photo, G_thumb, G_order, G_category, G_alt, G_lang, G_date) VALUES ('$text', '$jstext', '$photo', '$thumb', $order, '$category', '$alt', $lang, $date)";*/
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertProdCategory ($order, $title, $lang) {
		$data=array($order, $title, $lang);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO CATEGORIES (C_order, C_title, C_lang) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertProduct ($title, $lang, $price, $description, $availability, $photo, $order, $category, $alt, $weight) {
		$data=array($title, $lang, $price, $description, $availability, $photo, $order, $category, $alt, $weight);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO PRODUCTS (P_title, P_lang, P_price, P_description, P_availability, P_photo, P_order, P_category, P_alt, P_weight) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertOldProduct ($id, $title, $lang, $price, $description, $availability, $photo, $order, $category, $alt, $weight) {
		$data=array($id, $title, $lang, $price, $description, $availability, $photo, $order, $category, $alt, $weight);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO OLD_PRODUCTS (P_id, P_title, P_lang, P_price, P_description, P_availability, P_photo, P_order, P_category, P_alt, P_weight) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertBasket ($product, $quantity, $price, $username, $sid, $now, $weight) {
		$data=array($product, $quantity, $price, $username, $sid, $now, $weight);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO BASKET (B_pid, B_quantity, B_price, B_user, B_sid, B_date, B_weight) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	
	function insertOrder ($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10, $a11, $a12, $a13, $a14, $a15, $a16, $a17, $a18, $a19, $a20, $a21, $a22, $a23, $a24, $a25) {
		
		$data=array($a1, $a2, $a3, $a4, $a5, $a6, $a7, $a8, $a9, $a10, $a11, $a12, $a13, $a14, $a15, $a16, $a17, $a18, $a19, $a20, $a21, $a22, $a23, $a24, $a25);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO ORDERPR (O_orderid, O_status, O_sum, O_date, O_user, O_ShippingFirstName, O_ShippingLastName, O_ShippingPhone, O_ShippingCountry, O_ShippingState, O_ShippingCity, O_ShippingPostalCode, O_ShippingAddress1, O_ShippingEmail, O_PaymentFirstName, O_PaymentLastName, O_PaymentPhone, O_PaymentCountry, O_PaymentState, O_PaymentCity, O_PaymentPostalCode, O_PaymentAddress1, O_PaymentEmail, O_PaymentMethod, O_weight) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	

	function insertItems ($a1, $a2, $a3, $a4, $a5, $a6) {
		$data=array($a1, $a2, $a3, $a4, $a5, $a6);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO ORDER_ITEM (OI_orderid,OI_user,OI_pid,OI_ptitle,OI_pquantity,OI_cost) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertStatus ($a1, $a2, $a3, $a4, $a5) {
		$data=array($a1, $a2, $a3, $a4, $a5);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO STATUS (S_orderid,S_status,S_order,S_date,S_user) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertIPN ($a1, $a2, $a3, $a4, $a5) {
		$data=array($a1, $a2, $a3, $a4, $a5);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO IPN (IP_memo,IP_total,IP_invoice,IP_rinvoice,IP_radress) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	
	function insertPass ($a1, $a2) {
		$data=array($a1, $a2);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO CHPASS (CP_email, CP_pass) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertSMS ($a1, $a2, $a3, $a4) {
		$data=array($a1, $a2, $a3, $a4);
		
		$data = $this->protectSQL ($data);
		
		$query = "INSERT INTO SMS (S_status, S_message, S_enabled, S_language) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function updateField ($table, $field, $value, $wherefield, $wherevalue) {
		$data=array($value, $wherevalue);
		$data = $this->protectSQL ($data);
		
		$query = "UPDATE $table SET $field=$data[0] WHERE $wherefield=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function updateField2 ($table, $field, $value, $wherefield, $wherevalue, $wherefield2, $wherevalue2) {
		$data=array($value,$wherevalue,$wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "UPDATE $table SET $field=$data[0] WHERE $wherefield=$data[1] AND $wherefield2=$data[2]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function selectField ($field, $table, $wherefield, $wherevalue) {
		$data=array($wherevalue);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield=$data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}

	function selectField2 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField3 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2, $orderfield, $ordertype) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1] ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField4 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2, $wherefield3, $wherevalue3) {
		$data=array($wherevalue1, $wherevalue2, $wherevalue3);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1] AND $wherefield3=$data[2]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField5 ($field, $table, $wherefield1, $wherevalue1, $orderfield, $ordertype) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField6 ($field, $table, $orderfield, $ordertype) {
		
		$query = "SELECT $field FROM $table ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function deleteRow ($table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function deleteRow2 ($table, $wherefield1, $wherevalue1) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1=$data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function deleteRow3 ($table, $wherefield1, $wherevalue1) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1 < $data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function emptyTable ($table) {
		$query = "TRUNCATE TABLE $table";
		$result = mysql_query($query,$this->link_id);

		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function protectSQL ($data) {
		if(get_magic_quotes_gpc()) {
			foreach ($data as &$value)
				$value = stripslashes ($value);
		}
		foreach ($data as &$value) {
			$new_value = mysql_real_escape_string($value ,$this->link_id);
			if (empty($new_value) && !empty($value)) {
            	die("mysql_real_escape_string failed.");
        	}
        	$value = $new_value;
			if (!is_numeric($value)) {
				$value = "'".$value."'";
			}
		}
		
		return $data;
	}
	
	function verifyValue($result) {
		$rows = mysql_num_rows($result);
		if ( $rows == 0)
			return true;
		
		return false;
	}
	
	
	function equalValues ($value1, $value2) {
		if ( strcmp($value1,$value2) == 0)
			return true;
		
		return false;
	} 
	
	
}

?>
