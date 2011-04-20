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

/**
 * Show phpXplorer error message.
 */
class pxGlobal_openError extends pxGlobal___htmlDoc
{
	var $_aInfos = array();

	function pxGlobal_openError() {
		parent::pxGlobal___htmlDoc(false);
	}

	/**
	 * 
	 */
	function run($oObject, $aParameters)
	{
		global $pxp;

		$bJson = true;
		if (function_exists('getallheaders')) {
			$aHeaders = getallheaders();
			if (!isset($aHeaders['X-Requested-With']) || $aHeaders['X-Requested-With'] != 'XMLHttpRequest') {
				$bJson = false;
			}
		}

		$this->_aInfos = array(
			'bError' => true,
			'sId' => $aParameters['sId']
		);

		if ($pxp->aConfig['bDebug']) {
			$this->_aInfos['sVersion'] = $pxp->aConfig['sVersion'];
			$this->_aInfos['sFileIn'] = $aParameters['sFileIn'];
			$this->_aInfos['sLine'] = $aParameters['sLine'];
			$this->_aInfos['aValues'] = $aParameters['aValues'];
		}

		if ($bJson) {
			$this->sendJson($this->_aInfos);
			die;
		} else {
			$pxp->loadTranslation();
			parent::run();
			die;
		}
	}

	function buildBody()
	{
		global $pxp;

		$sMessage = $pxp->aTranslation['error.' . $this->_aInfos['sId']];
		
		if (is_array($this->_aInfos['aValues'])) {
			foreach ($this->_aInfos['aValues'] as $sValue) {
				$sMessage = pxUtil::str_replace_once('%s', $sValue, $sMessage);
			}
		}
		$sMessage = trim(str_replace('%s', '', $sMessage));

		$sLocation = $pxp->aTranslation['error'];
		if ($pxp->aConfig['bDebug']) {
			$sLocation .= ' in <b>' . $this->_aInfos['sFileIn'] . '</b> on line <b>' . $this->_aInfos['sLine'] . '</b>';
		}
		
		$sFoot = 'phpXplorer';
		if ($pxp->aConfig['bDebug']) {
			$sFoot .= ' ' . $pxp->aConfig['sVersion'];
		}
		$sFoot .= ' at ' . $_SERVER['HTTP_HOST'];

		$sHtml =
			'<h1>' . $sMessage . '</h1>' .
			'<p>' . $sLocation . '</p>' .
			'<hr/>' .
			'<address>' . $sFoot . '</address>' 
		;

		$this->sBody .= $sHtml;
		
	}
	
}

?>