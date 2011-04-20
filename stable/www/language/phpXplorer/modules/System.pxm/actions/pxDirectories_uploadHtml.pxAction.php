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

require_once dirname(__FILE__) . '/pxDirectories___upload.pxAction.php';

class pxDirectories_uploadHtml extends pxDirectories___upload
{
	/**
	 *
	 */
	function pxDirectories_uploadHtml() {
		global $pxp;
		parent::pxDirectories___upload();
	}

	/**
	 * 
	 */
	function buildBody() {
		global $pxp;

		$sHtml = '';

		parent::buildBody();

		$sHtml .=
			'<p>' .
			$pxp->aTranslation['upload.targetDirectory'] . ': &raquo;' . pxUtil::buildPath($pxp->sShare . ':/' , $pxp->decodeURI($pxp->_GET['sPath'])) . '&laquo;<br/><br/>' .
			'<span id="file_attach" style="padding:0">' .
				'<input size="30" type="file" class="upload" name="aFiles[]" onchange="px.action.pxDirectories_uploadHtml.attachFile(this)" id="file_input" />' .
			'</span>' .
			'<select name="aFileSelection" multiple="multiple" id="aFileSelection" size="8" style="width:92%;margin-top:0.3em"></select><br/>' .
			'<button style="margin-top:0.3em" onclick="px.action.pxDirectories_uploadHtml.removeSelectedFile()" class="action" name="remove">' .
				$pxp->aTranslation['upload.removeSelection'] .
			'</button>' .
			'</p>' .
			'<p>' .
			'<label for="overwrite">' . $pxp->aTranslation['upload.overwriteFiles'] . '</label> ' .
			'<input type="checkbox" name="bOverwrite" value="true" id="overwrite" />' .
			'<br/>' .
			'<button style="margin-top:0.3em" id="submitButton" onclick="if($(\'aFileSelection\').options.length > 0)document.forms[0].submit()" disabled="disabled">' . $pxp->aTranslation['upload.upload'] . '</button>' .
			'</p>'
			;
		$this->sBody .= $sHtml;
	}
}
?>