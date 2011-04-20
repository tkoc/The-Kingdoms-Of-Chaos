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

/* Copyright notice */

/**
 * Super class of all type classes
 *
 * @defaultActions pxObject_editProperties
 * @abstract
 */
class pxObject{
	/**
	 * Id of share where object is or should get stored
	 */
	var $sShare;

	/**
	 * @var int
	 */
	var $iDatabaseRowId;

	/**
	 * sName without extension
	 * 
	 * @var string
	 */
	var $sId;

	/**
	 * @var string
	 * @view Input
	 */
	var $sName;

	/**
	 * Directory (relative to shares sBaseDir)
	 *
	 * @var string
	 * @edit SelectManualOptions
	 */
	var $sRelDir;
	
	/**
	 * phpXplorer type
	 *
	 * @var string
	 * @view InputTranslated(namespace=type)
	 */
	var $sType;

	/**
	 * Array of subordinated filesystem files/objects
	 * 
	 * @var array
	 */
	var $aObjects = array();

	/**
	 * Stop triggering of further events
	 */
	var $_bCancelBubble;

	/**
	 * Is there a meta data record for this object
	 */
	var $_bMetaDataExists = false;
	

	function pxObject() {
		if (method_exists($this, '_init')) {
			$this->_init();
		}
	}

	/**
	 * Remove no longer defined class members
	 * 
	 * @access protected
	 */
	function _clean() {
		$aClassVars = array_keys(get_class_vars(get_class($this)));
		foreach (get_object_vars($this) as $sObjVar => $sObjValue) {
			if (!in_array($sObjVar, $aClassVars)) {
				eval('unset($this->' . $sObjVar . ');');
			}
		}
	}

	/**
	 * Clean object and prevent the serialisation of unneeded values
	 * 
	 * @internal Prevent serialisation of private members ? Possible ? Performance ?
	 */
	function __sleep()
	{
		global $pxp;

		if ($pxp->aConfig['bCleanObjects']) {
			$this->_clean();
		}

		$aMembers = (array)$this;

		// Prevents serialisation of null values and private members

		foreach ($aMembers as $mKey => $mVal) {
			if (
				is_null($mVal) or
				is_object($mVal) or
				is_resource($mVal) or
				substr($mKey, 0, 1) == '_' // private
			) {
				unset($aMembers[$mKey]);
			}
		}

		/**
		 * Prevent serialisation of subordinated filesystem objects and meta data
		 */

		unset($aMembers['iDatabaseRowId']);
		unset($aMembers['sName']);
		unset($aMembers['sRelDir']);
		unset($aMembers['sShare']);
		unset($aMembers['sType']);
		unset($aMembers['sExtension']);
		#unset($aMembers['bDirectory']); currently not possible
		unset($aMembers['iBytes']);
		unset($aMembers['dModified']);
		#unset($aMembers['dCreated']);

		unset($aMembers['aObjects']);
		

		return array_keys($aMembers);
	}

	/**
	 * Returns options of selection member types
	 *
	 * @param string $sMember Selection member ID
	 * 
	 * @return array Associative array with options
	 */
	function _getOptions($sMember, $bManualOptions = false)
	{
		global $pxp;

		switch($sMember)
		{
			case 'sRelDir':

				if ($bManualOptions) {

					$aDirectories = array();
					#$aDirectories[$this->sRelDir] = $this->sRelDir;

					$aStack = array();
					$aStack[] = $this->sRelDir;

					$oQuery = new pxQuery();

					while ($sCurrentDir = array_pop($aStack)) {
						$oQuery->sDirectory = $sCurrentDir;
						$oQuery->bOnlyDirectories = true;

						$aResult = $pxp->aShares[$this->sShare]->oVfs->ls($oQuery);

						foreach ($aResult as $oObject) {
							if ($oObject->sName == '.phpXplorer') continue; 
							$sDir = pxUtil::buildPath($oObject->sRelDir, $oObject->sName);
							$aDirectories[$sDir] = $sDir;
							array_unshift($aStack, $sDir);
						}
					}
					return $aDirectories;

				} else {
					return array($this->sRelDir => $this->sRelDir);
				}

				break;
			case 'aTags':
				return array();
				break;
			default:
				return null;
				break;
		}
	}

	/**
	 *
	 */
	function &call($sActionIn, $aParameters = null)
	{
		global $pxp;

		$sSearchType = $this->sType;
		$pxp->sFullAction = $sSearchType . '_' . $sActionIn;

		while (
			(
				!isset($pxp->aActions[$pxp->sFullAction]) &&
				isset($pxp->aTypes[$sSearchType]->sSupertype)
			)
			||
			(
				isset($pxp->aActions[$pxp->sFullAction]) &&
				!$pxp->aActions[$pxp->sFullAction][1]
			)
		) {
			$sSearchType = $pxp->aTypes[$sSearchType]->sSupertype;
			$pxp->sFullAction = $sSearchType . '_' . $sActionIn;
		}

		if (isset($pxp->aActions[$pxp->sFullAction])) {

			if (!isset($pxp->aActionObjects[$pxp->sFullAction])) {

				if (!$pxp->oShare->checkActionPermission(
					$this->sRelDir,
					$this->sType,
					$pxp->sFullAction,
					isset($this->sOwner) ? $this->sOwner : 'root'
				)) {
					$pxp->raiseError('notAllowedToRunAction', __FILE__, __LINE__, array($pxp->sFullAction, $pxp->sFullPathIn, $sSearchType));
				}

				require_once
					$pxp->sModuleDir . '/' .
					$pxp->aActions[$pxp->sFullAction][0] .
					'.pxm/actions/' .
					$pxp->sFullAction .
					'.pxAction.php';

				$pxp->aActionObjects[$pxp->sFullAction] =& new $pxp->sFullAction;
			}

			$mResult = $pxp->aActionObjects[$pxp->sFullAction]->run($this, $aParameters);

			/**
			 * Trigger events
			 */

			if (empty($this->_bCancelBubble)) { 

				$bTrigger = false;
				$oAction =& $pxp->aActionObjects[$pxp->sFullAction];

				if (!$oAction->bImplementsEventHandling) {
					$bTrigger = true;
				}

				if ($bTrigger) {
					#$pxp->log('object->triggerEvents: ' . $pxp->sFullAction);
					$this->triggerEvents($pxp->sFullAction);
				}
			}

			return $mResult;

		} else {
			$pxp->raiseError('invalidActionParam', __FILE__, __LINE__, array($sActionIn));
		}
	}

	/**
	 * Trigger events
	 */
	function triggerEvents($sFullAction)
	{
		global $pxp;

		$oSettings =& $pxp->oShare->getSettings($this->sRelDir, isset($this->sOwner) ? $this->sOwner : 'root');

		$sAction = substr($sFullAction, strpos($sFullAction, '_') + 1);
		$sBaseAction = $pxp->aActions[$sFullAction][2];

		$sEval = '';

		if (isset($oSettings->aCompiledEvents[$this->sType])) {
			$sEval .= $oSettings->aCompiledEvents[$this->sType];
		}

		if (isset($oSettings->aCompiledEvents[$this->sType . '.' . $sBaseAction])) {
			$sEval .= $oSettings->aCompiledEvents[$this->sType . '.' . $sBaseAction];
		}

		if (isset($oSettings->aCompiledEvents[$this->sType . '.' . $sBaseAction . '.' . $sAction])) {
			$sEval .= $oSettings->aCompiledEvents[$this->sType . '.' . $sBaseAction . '.' . $sAction];
		}

		$oType =& $pxp->aTypes[$this->sType];

		for ($s = 0, $m1 = count($oType->aSupertypes); $s < $m1; $s++) {

			$sSupertype = $oType->aSupertypes[$s];

			if (isset($oSettings->aCompiledEvents[$sSupertype])) {
				$sEval .= $oSettings->aCompiledEvents[$sSupertype];
			}

			if (isset($oSettings->aCompiledEvents[$sSupertype . '.' . $sBaseAction])) {
				$sEval .= $oSettings->aCompiledEvents[$sSupertype . '.' . $sBaseAction];
			}

			if (isset($oSettings->aCompiledEvents[$sSupertype . '.' . $sBaseAction . '.' . $sAction])) {
				$sEval .= $oSettings->aCompiledEvents[$sSupertype . '.' . $sBaseAction . '.' . $sAction];
			}
		}

		eval($sEval);

		#$iHandle = fopen($pxp->sDir . '/test.log', 'a');
		#fwrite(
		#	$iHandle,
		#	$pxp->aShares[$this->sShare]->sId . ':' .
		#	pxUtil::buildPath($this->sRelDir, $this->sName) . ' -> ' . $sFullAction . chr(13) . chr(10)
		#);
		#fclose($iHandle);
	}

	/**
	 * Set member value by name
	 */
	function setValue($sProperty, $mValue) {
		$this->{$sProperty} = $mValue;
	}
	
	/**
	 * 
	 */
	function store($sPathIn = null)
	{
		global $pxp;

		if (isset($sPathIn))
		{
			$mPos = strpos($sPathIn, '://');

			if ($mPos == false) {
				$this->sShare = 'phpXplorer';
				$sPath = $pxp->aShares['phpXplorer']->getRelativePath($sPathIn);
				$this->sRelDir = pxUtil::dirname($sPath);
				$this->sName = basename($sPath);
			} else {
				$this->sShare = substr($sPathIn, 0, $mPos);
				$sPath = substr($sPathIn, $mPos + 2);
				$this->sRelDir = pxUtil::dirname($sPath);
				$this->sName = basename($sPath);
			}
			
			$pxp->loadShare($this->sShare);
		}
		else
		{
			if (!isset($this->sShare)) {
				die('Object->sShare has to be set');
			}

			if (!isset($this->sRelDir)) {
				die('Object->sRelDir has to be set');
			}

			if (!isset($this->sName)) {
				die('Object->sName has to be set');
			}
		}

		$bResult = $pxp->aShares[$this->sShare]->oVfs->store_object($this);

		$this->isNew(false);

		return $bResult;
	}

	/**
	 * 
	 */
	function isNew($bNew = null)
	{
		global $pxp;

		if (isset($bNew)) {
			if ($bNew) {
				$this->_bNew = true;
			} else {
				unset($this->_bNew);
			}
		} else {
			if (isset($this->_bNew) && $this->_bNew == true) {
				return true;
			} else {
				if ($this->_bMetaDataExists) {
					return false;
				} else {
					return !$pxp->aShares[$this->sShare]->oVfs->file_exists($this->getFullPath());
				}
			}
		}
	}
	
	/**
	 * 
	 */
	function serializeToXml()
	{
		$sXml = '<pxObject>';

		$aVars = (array)$this;

		foreach ($aVars as $mKey => $mVal) {
			
			// Serialize private members?
			#if (strpos($mKey, '_') === 0) {
			#	continue;
			#}

			if (is_array($mVal)) {
				
			} else {
				$sXml .=
					'<' . $mKey . '>' .
					(string)$mVal .
					'</' . $mKey . '>';
			}
		}

		$sXml .= '</pxObject>';

		return $sXml;
	}
	
	/**
	 * 
	 */
	function serializeToPhp($aPropertySelection = null)
	{
		global $pxp;

		$aMembers = (array)$this;

		$sPhp = '$o =& new ' . $this->sType . '();';

		foreach ($aMembers as $sMember => $mValue) {

			if (empty($mValue)) {
				continue;
			}

			if (isset($aPropertySelection)) {
				if (!in_array($sMember, $aPropertySelection)) {
					continue;
				}
			}

			switch (gettype($mValue)) {
				case 'string':
					$sPhp .= '$o->' . $sMember . '=\'' . addslashes($mValue) . '\';';
					break;
				case 'integer':
					$sPhp .= '$o->' . $sMember . '=' . $mValue . ';';
					break;
				case 'double':
					$sPhp .= '$o->' . $sMember . '=' . $mValue . ';';
					break;
				case 'array':
					$sPhp .= '$o->' . $sMember . '=' . pxUtil::getArrayString($mValue) . ';';
					break;
				case 'object':
					break;
				case 'boolean':
				case 'bool':
					$sPhp .= '$o->' . $sMember . '=' . (int)$mValue . ';';
					break;
				default:
					break;
			}

			#$sPhp .= "\r\n";
		}
		return $sPhp;
	}
	
	/**
	 * @return string The full path of this objects file
	 */
	function getFullPath() {
		global $pxp;
		$sPath = pxUtil::buildPath($pxp->aShares[$this->sShare]->sBaseDir, $this->sRelDir);
		return pxUtil::buildPath($sPath, $this->sName);
	}
}

?>