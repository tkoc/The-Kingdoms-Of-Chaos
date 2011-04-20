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

    /*----------------------------------------------------------------------
        PHP JSON Class
        ==============
        The PHP JSON Class can be used to encode a php array or object into
        Java Script Object Notation, without the need for an additional PHP
        Extension.
        
        Normal usage is as follows:
        
            $json = new json;
            $encoded = $json->encode($var);
            echo $encoded;

        Version 0.5
        Copyright Jack Sleight - www.reallyshiny.com
        This script is licensed under the:
            Creative Commons Attribution-ShareAlike 2.5 License
    ----------------------------------------------------------------------*/

class pxJson
{
	function encode($mData) {
		return $this->_encode(NULL, $mData);
	}

	function _encode($key, $value, $parent = NULL)
	{
		$sType = $this->type($key, $value);

		switch ($sType) {
			case 'string':
				$value = '"' . $this->escape($value) . '"';
			break;
			case 'number':
				$value = $value;
			break;
			case 'boolean':
				$value = ($value) ? 'true' : 'false';
			break;
			case 'array':
				$value = '[' . $this->loop($value, $sType) . ']';
			break;
			case 'object':
				$value = '{' . $this->loop($value, $sType) . '}';
			break;
			case 'null':
				$value = 'null';
			break;
		}

		if(!is_null($key) && $parent != 'array') {
			$value = '"' . $key . '":' . $value;
		}

		return $value;
	}

	function type($key, $value)
	{
		if (is_object($value)) {
			$sType = 'object';
		}
		else if(is_string($value)) {
			$sType = 'string';
		}
		else if(is_int($value) || is_float($value)) {
			$sType = 'number';
		}
		else if(is_bool($value)) {
			$sType = 'boolean';
		}
		else if(is_array($value)) {
			$sType = $this->isAssoc($value) ? 'object' : 'array';
		}
		else if(is_null($value)) {
			$sType = 'null';
		}

		return $sType;
	}

	function loop($input, $sType)
	{
		$output = NULL;

		foreach($input as $key => $value) {
			$output .= $this->_encode($key, $value, $sType) . ',';
		}

		$output = trim($output, ',');

		return $output;
	}    

	function escape($sString) {
		return str_replace(
			array('\\',   '"',  '/',  "\b", "\f", "\n", "\r", "\t", "\u"),
			array('\\\\', '\"', '\/', '\b', '\f', '\n', '\r', '\t', '\u'),
			$sString
		);
	}

	function isAssoc($array) {
		krsort($array, SORT_STRING);
		return !is_numeric(key($array));
	}
}
?>