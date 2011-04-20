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

require_once dirname(__FILE__) . '/pxPhpClass.pxType.php';

/**
 * Base class for all actions
 *
 * @extensions pxAction.php => application/x-httpd-php
 * @belongsTo pxm/actions
 * @edit
 */
class pxAction extends pxPhpClass
{
	/**
	 * MIME type of action output
	 *
	 * @access protected
	 * @var string
	 */
	var $sMimeType = 'text/html';

	/**
	 * Used charset
	 *
	 * @access protected
	 * @var string
	 */
	var $sEncoding;

	/**
	 * Specifies if the output should be cached
	 *
	 * @var boolean
	 */
	var $bCache = false;

	var $bImplementsEventHandling = false;

	#var $aAllProperties;

	/**
	 * Constructor
	 */
	function pxAction($sId = null)
	{
		global $pxp;

		parent::pxPhpClass();

		$this->sId = $sId;
		$this->sLanguage = $pxp->sLanguage;
		$this->sEncoding = $pxp->aConfig['sEncoding'];

		if (isset($sId)) {
			if (isset($pxp->aActions[$sId])) {
				$this->sModule = $pxp->aActions[$sId][0];
			}
		}
	}

	/**
	 * 
	 */
	function __sleep() {
		$aVars = array_flip(parent::__sleep());
		unset($aVars['sMimeType']);
		unset($aVars['sEncoding']);
		unset($aVars['bCache']);
		unset($aVars['bImplementsEventHandling']);
		return array_keys($aVars);
	}

	/**
	 * Start execution
	 *
	 * Handles most common headers and HTTP caching
	 */
	function sendHeaders()
	{
		global $pxp;

		header('X-Powered-By: phpXplorer');
		header('Content-Type: ' . $this->sMimeType . '; charset=' . $pxp->aConfig['sEncoding']);
		header('Content-Language: ' . $this->sLanguage);

		if ($this->bCache) {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $pxp->oObject->dModified) . ' GMT');
			header('Cache-Control: private');
			header('max-age: 100000');
		}	else {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: no-store, no-cache, must-revalidate');  // HTTP/1.1
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
		}

		/*
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		// damit es mit ie 5.5 nach session start läuft
		header('Cache-Control: public');

		// nur request ausführen aber seite beibehalten
		header('HTTP/1.0 204 No Response');
		
		// Privacy policy needed ?
		header('P3P: CP="CAO DSP AND SO ON"');
		*/
	}
	
	function sendJson($mData)
	{
		global $pxp;

		$this->sMimeType = 'text/javascript';
		$this->sendHeaders();

		if ($pxp->aConfig['sEncoding'] == 'utf-8' && function_exists('json_encode')) {
			echo json_encode($mData);
		} else {
			require_once $pxp->sModuleDir . '/System.pxm/pxJson.php';
			$oJson = new pxJson();
			echo $oJson->encode($mData);
		}
	}

	function sendText($sData) {
		$this->sMimeType = 'text/plain';
		$this->sendHeaders();
		echo $sData;
	}
	
	function sendHtml($sData) {
		$this->sMimeType = 'text/html';
		$this->sendHeaders();
		echo $sData;
	}
	
	function sendXml($sData) {
		$this->sMimeType = 'application/xml';
		$this->sendHeaders();
		echo $sData;
	}

	/**
	 * 
	 */
	function getAllProperties() {}

	function sendRequestQueryResultAsJson($bFile = false)
	{
		global $pxp;

		$oQuery = $this->getRequestQuery(false, $bFile);
		$aObjects = $pxp->oShare->oVfs->ls($oQuery);

		if (method_exists($this, '_handleQueryResult')) {
			$this->_handleQueryResult($aObjects);
		}

/*
		$pxp->oShare->checkTypePermission(
			$pxp->sRelDir,
			$pxp->oObject->sType,
			false,
			$pxp->oObject->sOwner
		);
*/
		$oSettings =& $pxp->oShare->getSettings($pxp->sRelDir, isset($pxp->oObject->sOwner) ? $pxp->oObject->sOwner : 'root');

		if ($pxp->getRequestVar('bReloadFileMetaData') == 'true') {
			foreach ($aObjects as $oObject) {
				if (method_exists ($oObject, 'loadFileMetaData')) {
					$oObject->loadFileMetaData();
				}
			}
		}

		$aTypeOptions = array();
		if ($pxp->getRequestVar('bFillOptions') == 'true') {
			for ($i = 0, $m = count($aObjects); $i < $m; $i++) {
				if (isset($aTypeOptions[$i]->sType)) {
					continue;
				}
				$aTypeOptions[$aObjects[$i]->sType] = array();
				$aProperties = $pxp->aTypes[$aObjects[$i]->sType]->getAllProperties();
				foreach ($aProperties as $oProperty) {
					$aOptions = $aObjects[$i]->_getOptions($oProperty->sId);
					if (isset($aOptions)) {
						$aTypeOptions[$aObjects[$i]->sType][$oProperty->sId] = $aOptions;
					}
				}
			}
		}

		$bCreate = false;
		$bUpload = false;
		$bShare = false;

		if (!$bFile)
		{
			$bCreate = $pxp->oShare->checkActionPermission(
				$pxp->sRelDir,
				'pxGlobal',
				'pxGlobal_openCreate',
				$pxp->sUser
			);
	
			$bUpload = $pxp->oShare->checkActionPermission(
				$pxp->sRelDir,
				$pxp->oObject->sType,
				'pxDirectories_uploadHtml',
				$pxp->sUser
			);

			$bShare = in_array('pxAuthenticated', $oSettings->aRoles);
			
			/*
			$bShare = $pxp->oShare->checkActionPermission(
				$pxp->sRelDir,
				$pxp->oObject->sType,
				'pxUser__editCreate',
				$pxp->sUser
			);
			*/
		}

		$this->sendJson(
			array(
				'sDirectory' => $pxp->sShare . ':' . $pxp->sRelPathIn,
				'sSearchQuery' => $oQuery->sSearchQuery,
				'mResult' => $aObjects,
				'oSettings' => array(
					'sDirectoryType' => $pxp->oObject->sType,
					'aAllowedActions' => $oSettings->aAllowedActions,
					'oOptions' => $aTypeOptions,
					'bCreatePermission' => $bCreate && $pxp->oShare->oVfs->aPossibleActions['edit'],
					'bUploadPermission' => $bUpload && $pxp->oShare->oVfs->aPossibleActions['upload'],
					'bSharePermission' => $bShare && $pxp->oShare->oVfs->aPossibleActions['share']
					#'bDeletePermission' => $bDelete
				),
				'iPhpRuntime' => pxUtil::getMicrotime() - $pxp->iStartTime
			)
		);
	}

	/**
	 *
	 */
	function &getRequestQuery($bSubTree = false, $bFile = false)
	{
		global $pxp;

		$oQuery =& new pxQuery;
		$oQuery->bRecursive = $bSubTree;

		if ($bFile) {
			$oQuery->sDirectory = $pxp->sFullPathInDir;
			$oQuery->aNames = array($pxp->oObject->sName);			
		} else {
			if ($pxp->oObject->sType == 'pxVirtualDirectory') {
				$oVirtualDirectory = $pxp->oShare->oVfs->get_object($pxp->sFullPathIn, false);
				$oQuery->sSearchQuery = $oVirtualDirectory->sQuery;
				$oQuery->bSearchMatchCase = $oVirtualDirectory->bSearchMatchCase;
				$oQuery->sDirectory = pxUtil::dirname($pxp->sFullPathIn);
			} else {
				$oQuery->sSearchQuery = $pxp->getRequestVar('sSearchQuery');
				$oQuery->bSearchMatchCase = $pxp->getRequestVar('bSearchMatchCase') == 'true';
				$oQuery->sDirectory = $pxp->sFullPathIn;
			}
		}

		$sNames = $pxp->getRequestVar('aNames');
		if (!empty($sNames)) {
			$oQuery->aNames = explode('|', $sNames);
		}

		$sTypes = $pxp->getRequestVar('aTypes');
		if (!empty($sTypes)) {
			$oQuery->aTypes = explode('|', $sTypes);
		}

		$oQuery->bOnlyDirectories = $pxp->getRequestVar('bOnlyDirectories') == 'true';
		$oQuery->bOsPermissions = $pxp->getRequestVar('bOsPermissions') == 'true';
		$oQuery->bFilesize = $pxp->getRequestVar('bFilesize') != 'false';
		$oQuery->bFull = $pxp->getRequestVar('bFull') == 'true';

		// Handle sOrderBy parameter
		$sOrderBy = $pxp->getRequestVar('sOrderBy');
		if (!empty($sOrderBy)) {
			$oQuery->sOrderBy = $sOrderBy;
		}

		// Handle sOrderDirection parameter
		$oQuery->sOrderDirection = $pxp->getRequestVar('sOrderDirection');
		if (empty($oQuery->sOrderDirection)) {
			$oQuery->sOrderDirection = 'asc';
		}

		if ($pxp->getRequestVar('bHierarchical') == 'true') {
			$oQuery->iLimit = 0;
		} else {
			if (empty($oQuery->sSearchQuery)) {
				$oQuery->iLimit = (int)$pxp->oShare->iObjectsPerPage;
			} else {
				$oQuery->iLimit = (int)$pxp->oShare->iSearchResultsPerPage;
			}
		}
		
		$oQuery->iOffset = (int)$pxp->getRequestVar('iOffset');

		$oQuery->bRecursive = $pxp->getRequestVar('bRecursive') == 'true';
		$oQuery->bRecursiveFlat = $pxp->getRequestVar('bRecursiveFlat') == 'true';

		return $oQuery;
	}


	
	function run() {}



	function getFileSelection(&$aNames, $sParameterId = 'aNames', $bRaiseError = true)
	{
		global $pxp;

		$sSelection = $pxp->getRequestVar($sParameterId);

		if (!empty($sSelection)) {
			$aSelection = explode('|', $sSelection);
			foreach ($aSelection as $sItem) {
				if (!empty($sItem) && pxUtil::checkFilename($sItem)) {
					$aNames[] = $sItem;
				}
			}
		}

		if (empty($aNames) && $bRaiseError) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}
	}
	
	
	
	function deleteCheck(&$aObjects, &$aBookmarks)
	{
		global $pxp;

		$aStack = array();
		foreach ($aObjects as $oObject) {
			$aStack[] = $oObject;
		}

		while ($oObject = array_pop($aStack)) {

			if (!
				$pxp->aShares[$oObject->sShare]->checkActionPermission(
					$oObject->sRelDir,
					$oObject->sType,
					'pxObject_editDelete',
					isset($oObject->sOwner) ? $oObject->sOwner : 'root'
				)
			) {
				$pxp->raiseError('notAllowedToDeleteEveryFile', __FILE__, __LINE__);
				return false;
			}

			// Check for pxUser objects to clean bookmarks and profiles
			if ($oObject->sType == 'pxUser') {
				$sBookmarkDir = pxUtil::dirname($oObject->sRelDir);
				$aBookmarks[$oObject->getFullPath()] =
					array(
						'sUser' => $oObject->sId,
						'sBookmark' => 'px' . md5($pxp->aConfig['sId'] . $oObject->sShare . $sBookmarkDir),
						'sNewRelDir' => isset($oObject->_sNewRelDir) ? $oObject->_sNewRelDir : null
					);
			}

			if ($oObject->bDirectory) {
				foreach ($oObject->aObjects as $oSubObject) {
					$aStack[] = $oSubObject;
				}
			}
		}
	}
	
	
	function storeObject($aErrors)
	{
		global $pxp;
	
		if (count($aErrors) == 0) {
			if (!$pxp->oObject->store()) {
				$aErrors[] = 'couldNotWrite';
				$this->sendJson($aErrors);
			}
		} else {
			$this->sendJson($aErrors);
		}
	}
}

?>