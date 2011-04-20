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
 * Show phpXplorer info/about screen.
 */
class pxGlobal_sendMail extends pxAction
{
	/**
	 * 
	 */
	var $_aParameters = array();

	/**
	 * 
	 */
	function run(&$oObject, $aParameters)
	{
		global $pxp;

		die;

		$this->_aParameters =& $aParameters;

		if (!isset($aParameters)) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}

		$sFrom = implode(', ', $this->_getParameterArray('from'));
		$sTo = implode(', ', $this->_getParameterArray('to'));
		$sCc = implode(', ', $this->_getParameterArray('cc'));
		$sBcc = implode(', ', $this->_getParameterArray('bcc'));
		$sSubject = isset($aParameters['subject']) ? $aParameters['subject'] : '';
		$sText = isset($aParameters['text']) ? $aParameters['text'] : '';
		$sHtml = isset($aParameters['html']) ? $aParameters['html'] : '';
		$aAttachments = $this->_getParameterArray('attach');

		if (empty($sTo)) {
			$pxp->raiseError('noReceiver', __FILE__, __LINE__);
		}
		
		$aHeaders = array();

		$aHeaders[] = 'MIME-Version: 1.0' . $sSubject;
		$aHeaders[] = 'Subject: ' . $sSubject;
		$aHeaders[] = 'From: ' . $sFrom;
		$aHeaders[] = 'Return-Path: ' . $sFrom;
		$aHeaders[] = 'To: ' . $sTo;
		if (!empty($sCc)) {
			$aHeaders[] = 'Cc: ' . $sCc;
		}
		if (!empty($sBcc)) {
			$aHeaders[] = 'Bcc: ' . $sBcc;
		}
		$aHeaders[] = 'Content-type: text/plain; charset=' . $pxp->aConfig['sEncoding'];
		$aHeaders[] = 'Content-Transfer-Encoding: 7bit';

		mail(
			$sTo,
			$sSubject,
			$sText,
			implode("\r\n", $aHeaders)
		);
	}
	
	/**
	 * 
	 */
	function _getParameterArray($sKey)
	{
		$aResult = array();

		if (isset($this->_aParameters[$sKey])) {
			$sParameter = $this->_aParameters[$sKey];
			$sParameter = str_replace("\r", null, $sParameter);
			$aLines = explode("\n", $sParameter);

			foreach ($aLines as $iIndex => $sLine) {

				$sAddress = trim($sLine);

				if (!empty($sAddress)) {

					if (strpos($sAddress, '(') !== false) {
						$sAddress = str_replace('(', '<', $sAddress);
						$sAddress = str_replace(')', '>', $sAddress);
					}

					$aResult[] = $sAddress;
				}
			}
		}
		return $aResult;
	}
}

?>