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

require_once dirname(__FILE__) . '/pxPhp.pxType.php';

/**
 * PHP class object implements functionality to access class members
 *
 * @abstract
 */
class pxPhpClass extends pxPhp
{
	/**
	 * List of pxProperty member objects
	 *
	 * @access protected
	 * @var array
	 */
	var $aProperties;

	/**
	 * Fills and returns aProperties with all ascertainable properties of this class
	 *
	 * @return array
	 */
	function &getProperties()
	{
		global $pxp;

		$pxp->loadType('pxProperty');

		if (isset($this->aProperties)) {
			return $this->aProperties;
		}
		
#		// serve cache
#		$sCacheFile = $pxp->sCacheDir . '/properties/' . $this->sId . '.serializedPhp';
#		if ($pxp->oVfs->file_exists($sCacheFile)) {
#			$this->aProperties = unserialize($pxp->oVfs->file_get_contents($sCacheFile));
#			return $this->aProperties;
#		}

		$this->aProperties = array();

   	require_once $pxp->sModuleDir . '/System.pxm/pxClassParser.php';

   	$oParser =& new pxClassParser;

   	switch (strtolower(get_class($this))) {
   		case 'pxaction':
	   		$oParser->parse($pxp->sModuleDir . '/' . $this->sModule . '.pxm/actions/' . $this->sId . '.pxAction.php');
   			break;
   		case 'pxtype':
	   		#$oParser->parse($pxp->sModuleDir . '/' . $this->sModule . '.pxm/types/' . $this->sId . '.pxType.php');
	   		$oParser->parse($pxp->sCacheDir . '/types/' . $this->sId . '.pxType.php');
   			break;
   	}

   	foreach($oParser->aClasses[$this->sId]->aVariables as $oVariable)
   	{
   		$sId = substr($oVariable->sName, 1);

   		if ($oVariable->tagExists('view')) {
   			$sTagValue = $oVariable->getTagValue('view');
   			$sMode = 'view';
   		} else {
   			if ($oVariable->tagExists('edit')) {
   				$sTagValue = $oVariable->getTagValue('edit');
   				$sMode = 'edit';
   			} else {
   				continue;
   			}
   		}

   		$this->aProperties[$sId] =& new pxProperty($sId);

   		$this->aProperties[$sId]->sMode = $sMode;
   		$this->aProperties[$sId]->sValue = $oVariable->sValue;
   		if ($oVariable->tagExists('var')) {
   			$this->aProperties[$sId]->sDataType = $oVariable->getTagValue('var');  
   		} else {
   			switch (substr($sId, 0, 1)) {
   				case 's': $sType = 'string'; break;
   				case 'i': $sType = 'integer'; break;
   				case 'a': $sType = 'array'; break;
   			}
   			if (!empty($sType)) {
   				$this->aProperties[$sId]->sDataType = $sType;
   			}
   		}
   		$aParts = pxClassParser::parseTagValue($sTagValue);
   		$this->aProperties[$sId]->sWidget = $aParts['sName'];
   		$this->aProperties[$sId]->aParameters = $aParts['aParameters'];

   		if ($oVariable->tagExists('validate')) {
   			$sValidationTagValue = $oVariable->getTagValue('validate');
   			$aParts = pxClassParser::parseTagValue($sValidationTagValue);
   			$this->aProperties[$sId]->sValidation = $aParts['sName'];
   			$this->aProperties[$sId]->aValidationParameters = $aParts['aParameters'];
   		}

   		if ($oVariable->tagExists('format')) {
   			$sFormatTagValue = $oVariable->getTagValue('format');
   			$aParts = pxClassParser::parseTagValue($sFormatTagValue);
   			$this->aProperties[$sId]->sFormat = $aParts['sName'];
   			$this->aProperties[$sId]->aFormatParameters = $aParts['aParameters'];
   		}

   		$this->aProperties[$sId]->sStore = $oVariable->getTagValue('store');
			$this->aProperties[$sId]->sPermission = $oVariable->getTagValue('permission');   		
   	}

		// build cache
#		$pxp->oVfs->file_put_contents($sCacheFile, serialize($this->aProperties));

		return $this->aProperties;
	}

	/**
	 * Fills and returns aProperties with all ascertainable properties of this class
	 *
	 * @return array
	 */
	function &getVariables($bObjects = false)
	{
		global $pxp;

   	require_once $pxp->sModuleDir . '/System.pxm/pxClassParser.php';

   	$oParser =& new pxClassParser;

   	switch (strtolower(get_class($this))) {
   	case 'pxaction':
   		$oParser->parse($pxp->sModuleDir . '/' . $this->sModule . '.pxm/actions/' . $this->sId . '.pxAction.php');
   		break;
   	case 'pxtype':
   		#$oParser->parse($pxp->sModuleDir . '/' . $this->sModule . '.pxm/types/' . $this->sId . '.pxType.php');
			$oParser->parse($pxp->sCacheDir . '/types/' . $this->sId . '.pxType.php');
   		break;
   	}
   	
   	$aVariables = array();

   	foreach($oParser->aClasses[$this->sId]->aVariables as $oVariable) {
			$sId = substr($oVariable->sName, 1);
			$sDataType = substr($sId, 0, 1);
			if ($bObjects == false) {
				if ($sDataType == 'o') {
					continue;
				}
			}
   		$aVariables[] = $sId;
   	}

   	return $aVariables;
	}
}

?>