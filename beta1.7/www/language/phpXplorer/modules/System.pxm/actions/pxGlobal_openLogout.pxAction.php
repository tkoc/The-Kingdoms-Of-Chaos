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

/**
 * Class for HTML login form 
 */
class pxGlobal_openLogout extends pxAction
{
	/**
	 * 
	 */
	function pxGlobal_openLogout()
	{
		global $pxp;

		if (!empty($pxp->sUser) && $pxp->sUser != 'everyone') {
			$oProfile = $pxp->getObject('phpXplorer://profiles/' . $pxp->sUser . '.pxProfile', false);
			$oProfile->iLastLogout = time();
			$oProfile->store();
		}

		if ($pxp->_SESSION['pxp_bAuth']) {
			unset($pxp->_SESSION['pxp_bLogin']);
		}

		if (isset($pxp->_SESSION['pxp_sUser'])) {
			unset($pxp->_SESSION['pxp_sUser']);
		}

		session_write_close();

		if (!empty($pxp->aConfig['sAuthentication'])) {
			$pxp->oAuthentication = $pxp->getObject($pxp->aConfig['sAuthentication'], false);
			if ($pxp->oAuthentication->iLogin == 1 && empty($pxp->_SESSION['pxp_bLogin'])) {
				header('WWW-Authenticate: Basic realm="phpXplorer@' . $pxp->_SERVER['HTTP_HOST'] . '"');
				header('HTTP/1.0 401 Unauthorized');
				exit;
			}
		}


		$sLocation = $pxp->sUrl;
		if (strpos($sLocation, '?') === false) {
			$sLocation .= '?';	
		}
		$sLocation .= '&bForceLogin=true';

		header('Location: ' . $sLocation);

		exit;
	}
}

?>