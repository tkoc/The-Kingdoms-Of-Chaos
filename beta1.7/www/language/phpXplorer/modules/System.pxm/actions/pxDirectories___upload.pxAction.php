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

require_once dirname(__FILE__) . '/pxGlobal___htmlDoc.pxAction.php';

class pxDirectories___upload extends pxGlobal___htmlDoc
{
	/**
	 *
	 */
	var $aFiles = array();

	var $_sMessages = '';
	var $_bOverwrite = false;

	/**
	 *
	 */
	function pxDirectories___upload()
	{
		global $pxp;
		
		$pxp->loadTranslation();

		if (strToLower(ini_get('file_uploads')) == 'off') {
			$pxp->raiseError('noUploads', __FILE__, __LINE__);
		}

		parent::pxGlobal___htmlDoc();

		$this->sBodyId = 'upload';
		$this->sTitle = $pxp->aTranslation['action.' . $pxp->sFullAction];
		$this->setForm($pxp->sUrl . '/action.php?' . $pxp->_SERVER['QUERY_STRING'], 'post');

		$this->_bOverwrite = $pxp->getRequestVar('bOverwrite') == 'true';

		$this->aFiles = isset($pxp->_FILES['aFiles']) ? $pxp->_FILES['aFiles'] : array();

		$this->addScript('frontend/px/action/pxDirectories_uploadHtml.js');
		$this->addScriptCode('pxp.bStop = true;');
		
		$this->addStyle('ui.css.php');
	}

	/**
	 * 
	 */
	function convertFilesArray() {}

	/**
	 * 
	 */
	function run($oObject, $bNoOutput = false)
	{
		global $pxp;

		/**
		 * [aFiles] => Array
		 *   [name] => Array
		 *   [type] => Array
		 *   [tmp_name] => Array
		 *   [error] => Array
		 *   [size] => Array
		 */
		 
		$bOneOk = false;
		$this->_sMessages = '';

		if (count($this->aFiles) > 0) {
			
			$this->convertFilesArray();

    	foreach ($this->aFiles['name'] as $iIndex => $sName)
    	{
    		if ($this->aFiles['error'][$iIndex] == 0 or !isset($this->aFiles['error'])) {
    			
    			if (!pxUtil::checkFilename($sName)) {
						$this->_sMessages .= $pxp->aTranslation['error.invalidFilename'] . ': ' . $sName . '<br/>';
    				continue;
    			}

    			$sNewPath = $pxp->sFullPathIn . '/' . $sName;

    			$sType = null;
    			$sExtension = null;
    			$pxp->getTypeKeyByExtension(
    				$sName,
    				false,
    				$sType,
    				$sExtension
    			);

    			if ($pxp->oShare->oVfs->file_exists($sNewPath)) {
    				if (empty($this->_bOverwrite) ) {
   						$this->_sMessages .= $pxp->aTranslation['error.objectExists'] . ': ' . $sName . '<br/>';
    					continue;
    				} else {
    					if (!$pxp->oShare->checkActionPermission(
								$pxp->sRelDir,
								$sType,
								'pxObject_editDelete',
								$pxp->sUser
							)) {
    						$pxp->raiseError('notAllowedToDeleteType', __FILE__, __LINE__, array($sType));
    					}
    				}
    			}

    			if (!$pxp->oShare->checkCreatePermission($pxp->sRelDir, $sType)) {
    				$pxp->raiseError('notAllowedToCreateType', __FILE__, __LINE__, array($sType));
    			}
					
    			if (!$pxp->oShare->oVfs->file_put($sNewPath, $this->aFiles['tmp_name'][$iIndex])) {
	    			$sTmpName = str_replace(chr(92), '/', tempnam($pxp->sDir . '/cache', $pxp->sCallId));
    				move_uploaded_file($this->aFiles['tmp_name'][$iIndex], $sTmpName);
						$pxp->oShare->oVfs->file_put($sNewPath, $sTmpName);
						unlink($sTmpName);
						#$oObject->triggerEvents('pxObject__editCreate');
	    		}

					$iNewId = $pxp->oShare->oVfs->iLastInsertId;
					$bOneOk = true;
    		}
    	}

			if (!$bNoOutput && $bOneOk) {
				$this->addScriptCode(
					'opener.pxp.refreshView("' .
						$pxp->sRelPathIn .
					'");window.setTimeout(\'window.close()\', 100);'
				);
			}
		}

		parent::run();
	}

	/**
	 * 
	 */
	function buildBody()
	{
		global $pxp;

		$sHtml = '';

		$sHtml .=
			'<div class="pxBar mainBar">' .
				'<span class="pxTb">' . $pxp->aTranslation['upload.uploadMethod'] . '</span>' .
				'<select size="1" name="upload_method" onchange="px.action.pxDirectories_uploadHtml.changeMethod(this)">';

		foreach ($pxp->aActions as $sFullAction => $aInfo) {
			$sBaseAction = $aInfo[2];
			if ($sBaseAction == 'upload' && strpos($sFullAction, '__') === false) {
				$sHtml .=
					'<option value="sShare=' . $pxp->sShare . '&amp;sPath=' . $pxp->_GET['sPath'] . '&amp;sAction=' . substr($sFullAction, strpos($sFullAction, '_') + 1) . '"' . ($sFullAction == $pxp->sFullAction ? ' selected="selected"' : '') . '>' .
						$pxp->aTranslation['action.' . $sFullAction] .
					'</option>';
			}
		}

		$sHtml .= '</select></div>';
		
		if (!empty($this->_sMessages)) {
			$sHtml .= '<p class="error">' . $this->_sMessages . '</p>';
		}
			
		$this->sBody = $sHtml;
	}
}
?>