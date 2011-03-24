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
/* Copyright notice */

/**
 * Class data type
 * @access private
 */
class pxClassParserClass
{
	/**
	 * Name of the class
	 * 
	 * @var string
	 */
	var $sName;

	/**
	 * String value after extends keyword
	 * 
	 * @internal Get filled by parser class 
	 * @var string
	 */
	var $sSuperClass;

	/**
	 * Array of pxClassParserVariable objects
	 *  
	 * @var array
	 */
	var $aVariables = array();
	
	/**
	 * Array of pxClassParserFunction objects
	 *  
	 * @var array
	 */	
	var $aFunctions = array();

	/**
	 * Assoc array with tag names as keys and arrays with all occured key values as value
	 * 
	 * @var array 
	 */
	var $aCommentTags = array();
	
	/**
	 * @param string $sName Class name
	 * @param string $sComment Text of previous PHP comment block or null
	 */
	function pxClassParserClass($sName, $sComment)
	{
		$this->sName = $sName;

		$this->aCommentTags = pxClassParser::_splitComment($sComment);
	}

	/**
	 * Add new variable object to variable array.
	 * 
	 * @param integer $iVisibility Valid values T_VAR, T_PUBLIC, T_PROTECTED or T_PRIVATE
	 * @param string $sName Class member name
	 * @param string $sValue Initial value of the class member
	 * @param string $sComment Text of previous PHP comment block or null
	 */
	function addVariable($iVisibility, $sName, $sValue, $sComment)
	{
		$this->aVariables[$sName] =& new pxClassParserVariable($iVisibility, $sName, $sValue, $sComment);
	}

	/**
	 * Add new function/method object to variable array.
	 * 
	 * @param integer $iVisibility Valid values T_VAR, T_PUBLIC, T_PROTECTED or T_PRIVATE
	 * @param string $sName Class member name
	 * @param string $sComment Text of previous PHP comment block
	 */
	function addFunction($iVisibility, $sName, $sComment)
	{
		$this->aFunctions[$sName] =& new pxClassParserFunction($iVisibility, $sName, $sComment);
	}
	
	/**
	 * Returns command tag value
	 * 
	 * @param string $sTag Tag ID
	 * @return mixed Tag value
	 */
	function getTagValue($sTag)
	{
		if (isset($this->aCommentTags[$sTag])) {		
			return $this->aCommentTags[$sTag];
		} else {
			return null;
		}
	}
	
	/**
	 * Returns true or false depending on the existance of $sTag
	 * 
	 * @param string $sTag Tag ID
	 * @return boolean
	 */
	function tagExists($sTag)
	{
		return isset($this->aCommentTags[$sTag]);
	}
}

?>