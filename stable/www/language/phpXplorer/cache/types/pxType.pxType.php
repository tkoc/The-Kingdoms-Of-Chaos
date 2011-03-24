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

require_once dirname(__FILE__) . '/pxPhpClass.pxType.php';

/**
 * phpXplorer type/class class
 *
 * Implements functionality to resolve type inheritance and load property objects if needed 
 *
 * @extensions
 *   pxType.php => application/x-httpd-php
 * @belongsTo pxm/types
 * @edit
 */
class pxType extends pxPhpClass{
	/**
	 * ID of module which contains this type
	 *
	 * @var string
	 * @internal Gets filled while building type/action object cache file
	 */
	var $sModule;

	/**
	 * Specifies if type can contain other types
	 *
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bDirectory = false;

	/**
	 * Stores if it is possible to create instances through phpXplorer
	 * 
	 * This value gets set while building cache file in pxSystem class
	 *
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bCreate = false;

	/**
	 * Abstract type represented by @abstract tag in the PHP class definition.
	 *
	 * @var boolean
	 * @edit Checkbox
	 *
	*/
	var $bAbstract = false;

	/**
	 * @var array
	 * @edit Array
	 */
	var $aExtensions = array();

	/**
	 * @var string
	 * @edit Array
	 */
	var $aMimeTypes = '';

	/**
	 * A list of type IDs that could be contained by this type.
	 * Is represented by @contains tag in the PHP class definition.
	 * 
	 * 
	 * @var array
	 * @edit Array
	 */
	var $aContenttypes = array();

	/**
	 * This type could only be created in directories of listed types.
	 * Is represented by @belongsTo tag in the PHP class definition.
	 *
	 * @var array
	 * @edit Array
	 */
	var $aContainertypes = array();

	/**
	 * Super type/class
	 * Receives class name after extends keyword as value
	 *
	 * @var string
	 * @edit Input
	 */
	var $sSupertype = '';

	/**
	 * Default action gets represented by @defaultActions tag in PHP class definition
	 *
	 * This property gets bequeathed along the type hierarchy and is only stored
	 * in the JavaScript cache file
	 *
	 * @var string
	 * @edit Array
	 */
	var $aDefaultActions;
	
	/**
	 * @var array
	 * @edit Array
	 */
	var $aExpandSubtypes = array();
	

	/* internal */

	/**
	 * List of all super types
	 * 
	 * @var array
	 */
	var $aSupertypes = array();

	/**
	 * List of all actions that could be executed on this type
	 * 
	 * Filled by fillPossibleActions in pxSystem class 
	 *
	 * @var array
	 */
	var $aActions = array();

	var $_aAllProperties;

	var $aSubtypes = array();

	var $aAllSubtypes = array();

	var $_bAllSubtypesFilled = false;

	/**
	 * 
	 */

	function pxType($sId = null)
	{
		//parent::pxPhpClass();
		$this->sId = $sId;
		$this->sType = 'pxType';
	}

	/**
	 * 
	 */
	function __sleep()
	{
		$aVars = array_flip(parent::__sleep());

		#unset($aVars['aAllSubtypes']);
		#unset($aVars['aDefaultActions']);

		return array_keys($aVars);
	}

	/**
	 * Returns all properties of this class and all its super classes
	 *
	 * @return array
	 */
	function &getAllProperties()
	{
		global $pxp;

		if (isset($this->_aAllProperties)) {
			return $this->_aAllProperties;
		}

		if (!isset($this->aSupertypes)) {
			$this->fillSupertypes();
		}

		$this->_aAllProperties = array();

		// Combine this and super type array
		$aTypes = $this->aSupertypes;
		$aTypes[] = $this->sId;

		foreach ($aTypes as $sType) {
			$this->_aAllProperties = array_merge($this->_aAllProperties, $pxp->aTypes[$sType]->getProperties());
		}

		return $this->_aAllProperties;
	}

	/**
	 * Fills $this->aSupertypes with names of all super types/classes
	 */
	function fillSupertypes()
	{
		global $pxp;

		$sCurrentType = $pxp->aTypes[$this->sId]->sSupertype;
		while (!empty($sCurrentType)) {
			$this->aSupertypes[] = $sCurrentType;
			$sCurrentType = $pxp->aTypes[$sCurrentType]->sSupertype;
		}
		$this->aSupertypes = array_reverse($this->aSupertypes);
	}

	/**
	 * Fills $this->aActions array with IDs of all actions that can be performed on this type
	 * 
	 * Gets called while building cache file in pxSystem class
	 *
	 * @return array
	 */
	function fillPossibleActions()
	{
		global $pxp;

		// Combine this and super type array
		$aTypes = $this->aSupertypes;
		array_unshift($aTypes, $this->sId);

		
		// Use exact action - javascript action class must exist 
		#foreach (array_reverse($aTypes) as $sTypeKey) {
	
		foreach ($aTypes as $sTypeKey) {
			foreach ($pxp->aActions as $sActionKey => $aInfo) {
				$iPos = strpos($sActionKey, '_');
				$sType = substr($sActionKey, 0, $iPos);
				$sAction = substr($sActionKey, $iPos + 1);
				if ($sType == $sTypeKey) {
					$this->aActions[$sAction] = $sActionKey;
				}
			}
		}
		$this->aActions = array_unique(array_values($this->aActions));
	}

	/**
	 * Returns true if a super type is in parameter aArray
	 * 
	 * @param array $aArray Type list to check
	 * @return boolean
	 */
	function isSupertypeInArray($aArray)
	{
		global $pxp;

		if (in_array($this->sId, $aArray)) {
			return true;
		}

		for ($s = 0, $m = count($this->aSupertypes); $s < $m; $s++) {
			if (in_array($this->aSupertypes[$s], $aArray)) {
				return true;
			}
		}

		return false;
	}

	/**
	 *  
	 * 
	 */
	function fillAllSubtypes() {
		global $pxp;
		
		if ($this->_bAllSubtypesFilled) {
			return;
		}

		$aCheckTypes = array($this->sId);

		while ($sCurrentType = array_pop($aCheckTypes)) {
			foreach ($pxp->aTypes[$sCurrentType]->aSubtypes as $sSubtype) {
				$this->aAllSubtypes[] = $sSubtype;
				if (!empty($pxp->aTypes[$sSubtype]->aSubtypes)) {
					$aCheckTypes[] = $sSubtype;
				}
			}
		}
		$this->_bAllSubtypesFilled = true;
	}
	var $sType = 'pxType';}

?>