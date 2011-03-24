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

require_once dirname(__FILE__) . '/pxGlobal___htmlDoc.pxAction.php';

class pxPhp_openSource extends pxGlobal___htmlDoc
{
	/**
	 * 
	 */
	function pxPhp_openSource()
	{
		global $pxp;

		parent::pxGlobal___htmlDoc(false);

		$this->addStyleCode('body{white-space:nowrap}');

		$this->sBody .= $this->xhtml_highlight($pxp->oObject->getContents());		
	}

	/**
	 * 
	 */
	function xhtml_highlight($sPHP)
	{
		$sPHP = highlight_string($sPHP, true);
		$sPHP = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $sPHP);
		$sPXP = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $sPHP);
		
		return $sPXP;
	}
}

?>