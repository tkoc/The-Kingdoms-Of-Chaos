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

class pxObject__editIndex extends pxAction 
{
	var $_oShare;

	/**
	 * 
	 */
	function run(&$oObject, $aParameters = null)
	{
		global $pxp;

		$this->bImplementsEventHandling = true;

		// Prevent recursive index calls
		$oObject->_bCancelBubble = true;

		$this->_oShare =& $pxp->aShares[$oObject->sShare];
		$oVfs =& $this->_oShare->oVfs;

		if (!$oVfs->bIndexed) {
			return false;
		}

		$bRebuild = isset($aParameters['bRebuild']) && $aParameters['bRebuild'];

		$sAction = $aParameters['sTriggeringAction'];

		#$pxp->log('editIndex ' . $aParameters['sTriggeringAction']);

		$sBaseAction = pxUtil::getBaseAction($sAction);
		if ($sBaseAction == 'open') {
			return false;
		}

		if ($sAction == 'editDelete') {
			if (!$bRebuild) {
				$oVfs->clearKeywords($oObject->iDatabaseRowId);
			}
		}
		else if ($sAction == '_editCreate') {
			$this->indexObject($oObject);
		}
		else {
			if (!$bRebuild) {
				$oVfs->clearKeywords($oObject->iDatabaseRowId);
			}
			$this->indexObject($oObject);
		}

		if ($sAction != 'editIndex') {
			$oVfs->flushSqlCache();
		}
	}

	function indexObject(&$oObject)
	{
		global $pxp;

		#print_r($oObject);

		$oVfs =& $this->_oShare->oVfs;

		if (!$oObject->_bMetaDataExists) {
			$sPath = pxUtil::buildPath(
				pxUtil::buildPath(
					$this->_oShare->sBaseDir,
					$oObject->sRelDir
				),
				$oObject->sName
			);
			$oFullObject = $oVfs->get_object($sPath, true, false);			
		} else {
			$oFullObject =& $oObject;
		}
		
		if (!isset($oFullObject)) {
			return false;
		}

		if (isset($oObject->_iNewDatabaseRowId)) {
			$oFullObject->iDatabaseRowId = $oObject->_iNewDatabaseRowId;
			$oFullObject->sShare = $oObject->_sNewShare;
			$oFullObject->sRelDir = $oObject->_sNewRelDir;
			$oFullObject->sName = $oObject->_sNewName;
		}

		$aMembers = (array)$oFullObject;

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

		unset($aMembers['bDirectory']);
		unset($aMembers['iDatabaseRowId']);
		unset($aMembers['sId']);
		unset($aMembers['sExtension']);
		unset($aMembers['fPosition']);
		unset($aMembers['aObjects']);
		unset($aMembers['bActive']);
		unset($aMembers['bVisible']);

		foreach ($aMembers as $mKey => $mValue)
		{
			if (is_array($mValue))
			{
				foreach ($mValue as $mKey2 => $mItem)
				{
					// Skip associative arrays
					if (!is_numeric($mKey2)) {
						break;
					}

					$oVfs->insert_keyword(
						$oFullObject->iDatabaseRowId,
						$mKey,
						$mItem
					);
				}
			}
			else
			{
				$iCamelCheckPos = ord(substr($mKey, 1, 1));
				if ($iCamelCheckPos > 64 and $iCamelCheckPos < 91)
				{
					switch (substr($mKey, 0, 1)) {
						case 'd': // date

							if (is_integer($mValue))
							{
								$oVfs->insert_keyword(
									$oFullObject->iDatabaseRowId,
									$mKey,
									date('Y-m-d H:i:s', $mValue)
								);
							}
							else
							{
								$oVfs->insert_keyword(
									$oFullObject->iDatabaseRowId,
									$mKey,
									$mValue
								);
							}
							break;
						default:
							$oVfs->insert_keyword(
								$oFullObject->iDatabaseRowId,
								$mKey,
								$mValue
							);
						break;
					}
				}
				else
				{
					$oVfs->insert_keyword(
						$oFullObject->iDatabaseRowId,
						$mKey,
						$mValue
					);							
				}
			}
		}
	}
}
?>