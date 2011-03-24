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
 * Variable data type
 * @access private
 */
class pxClassParserVariable
{
	/**
	 * Visibility level of class member.
	 * 
	 * Valid values are T_VAR, T_PUBLIC, T_PROTECTED and T_PRIVATE
	 * 
	 * @var integer
	 */
	var $iVisibility;

	/**
	 * Class member name
	 * 
	 * @var string
	 */
	var $sName;

	/**
	 * Initial value of the class member
	 * 
	 * @var mixed 
	 */
	var $mValue;

	/**
	 * Assoc array with tag names as keys and arrays with all occured key values as value
	 * 
	 * @var array
	 */
	var $aCommentTags = array();

	function pxClassParserVariable($iVisibility, $sName, $sValue, $sComment)
	{
		$this->aCommentTags = pxClassParser::_splitComment($sComment);

		/**
		 * Visibility marks are used in the following order.
		 * 
		 * 1. PHP5 visibility keywords like public, protected and private
		 * 2. @pxAccess tag
		 * 3. @access tag (phpDocumentor like)
		 * 4. A leading '_' char is a mark for private class members
		 * 
		 * Class members without any visibility mark a treated as public
		 */
		if ($iVisibility == T_VAR) {

			// Is there a @access tag?

			if (isset($this->aCommentTags['access'])) {

				// Translate string to token constant

				switch ($this->aCommentTags['access'][0]) {

					case 'public':
						$this->iVisibility = T_PUBLIC;
					break;

					case 'protected':
						$this->iVisibility = T_PROTECTED;
					break;

					case 'private':
						$this->iVisibility = T_PRIVATE;
					break;
				}

			} else {
				
				// No access tag found.
				// Is there a leading '_' char?
				
				if (strpos($sName, '$_') === 0) {
					$iVisibility = T_PRIVATE;
				} else {
					// Class members without any visibility mark are treated as public.
					$iVisibility = T_PUBLIC;
				}
			}
		} else {
			// Parameter has already got a value of T_PUBLIC, T_PROTECTED or T_PRIVATE
			$this->iVisibility = $iVisibility;
		}

		$this->sName = $sName;
		$this->sValue = $sValue;
	}
	
	/**
	 * Returns command tag value
	 * 
	 * @param string $sTag Tag ID
	 * 
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
	 * 
	 * @return boolean
	 */
	function tagExists($sTag)
	{
		return isset($this->aCommentTags[$sTag]);
	}
}

?>