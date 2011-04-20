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

class pxGlobal_openContact extends pxGlobal___htmlDoc
{
	/**
	 *
	 */
	function pxGlobal_openContact($aUserInfo = null)
	{
		global $pxp;

		parent::pxGlobal___htmlDoc(false);

		$this->addStyle('modules/System.pxm/doc.css');

		#$this->addScript('modules/System.pxm/frontend/px/action/pxGlobal_openLoginForm.js');

		#$this->addScriptCode('pxp.bStop = true;');

		$this->setForm($pxp->_SERVER['PHP_SELF'] . '?' . $pxp->_SERVER['QUERY_STRING']);
		$this->sHtmlId = 'contact';

		$pxp->loadTranslation();

		$sHtml =
			'<div class="contentFrame">' .
			'<h2>' . $pxp->aConfig['aContact']['sOrganisation'] . '</h2>'
		;
		
		$aGroups = array();
		$sActiveGroup = '';

		foreach ($pxp->aConfig['aContact'] as $sKey => $sItem) {			
			if (!empty($sItem)) {
				if (isset($pxp->aTranslation['contact.' . $sKey])) {
					$sActiveGroup = $sKey;
				}
				$aGroups[$sActiveGroup][] = pxUtil::translateTitle($sItem);
			}
		}

		foreach ($aGroups as $sGroup => $aItems) {
			if (!empty($sGroup)) {
				$sHtml .= '<h3>' . $pxp->aTranslation['contact.' . $sGroup] . '</h3><p>';
			}
			foreach ($aItems as $sItem) {
				$sHtml .= $sItem . '<br/>';
			}
			$sHtml .= '</p>';
		}

		$sHtml .=
			'<br/>' .
			'<h2 class="sub">Nachricht</h2>' .
			'<p>' .
			'<textarea name="message" id="message" rows="8" cols="24"></textarea>' .
			'<input type="text" name="email" id="email" />' .
			'<button type="submit" id="submit">' .
			'<div>' . $pxp->aTranslation['send'] . '</div>' .
			'</button>' .
			'</p>' .
			'</div>'
		;		
		

		$this->sBody .= $sHtml;
	}
}


?>