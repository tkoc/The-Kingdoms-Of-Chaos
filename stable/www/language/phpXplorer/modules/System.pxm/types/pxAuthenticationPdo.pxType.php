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

require_once dirname(__FILE__) . '/pxAuthentication.pxType.php';

/**
 * Implements functionality to handle authentication against a RDBMS table per PDO
 *
 * @extensions pxAuthenticationPdo
 * @belongsTo phpXplorer:
 * @edit
 */
class pxAuthenticationPdo extends pxAuthentication
{
	/**
	 * Database user
	 *
	 * @var string
	 * @edit Input
	 */
	var $sUsername;
	
	/**
	 * Database password
	 *
	 * @var string
	 * @edit Password
	 */
	var $sPassword;

	/**
	 * Database table
	 *
	 * @var string
	 * @edit Input
	 */
	var $sTable = 'pxUsers';
	
	/**
	 * Name of username column
	 *
	 * @var string
	 * @edit Input
	 */
	var $sColumnUser = 'name';
	
	/**
	 * Name of password column
	 *
	 * @var string
	 * @edit Input
	 */
	var $sColumnPassword = 'password';
	
	/**
	 * PDO object
	 * 
	 * @access private
	 * @var object
	 */
	var $_oPDO;
	
	function connect()
	{
		global $pxp;
		
		if (!class_exists('pdo')) {
			require_once $pxp->sModuleDir . '/System.pxm/includes/PDO/PDO.class.php';
		}
		
		$this->_oPDO =& new PDO(
			$this->sDSN,
			$this->sDbUser,
			$this->sDbPassword
		);
		
		return true;
	}

	/**
	 * Tests if users exists and returns its password or false
	 *
	 * @param string $sUser Username
	 * @return boolean True or false depending on existence
	 */
	function getPassword($sUser) {
		$oStatement = $this->_oPDO->query(
			'SELECT ' . $this->sDbPasswordColumn . ' FROM ' . $this->sDbTable . ' WHERE ' . $this->sDbUserColumn . ' = "' . addslashes($sUser) . '"'
		);

		$aRows = $oStatement->fetchAll(3);

		if (isset($aRows[0][0])) {
			return $aRows[0][0];
		}

		return false;
	}
	
	/**
	 * Returns array with all users and its passwords
	 *
	 * @return array Returns array with all users
	 */
	function &getUsers()
	{
		$oStatement = $this->_oPDO->query(
			'SELECT ' . $this->sDbUserColumn . ',' . $this->sDbPasswordColumn . ' FROM ' . $this->sDbTable
		);

		$aResult = $oStatement->fetchAll(3);
		
		$aUsers = array();
		
		for ($i = 0, $m = count($aResult); $i < $m; $i++ ){
			$aUsers[$aResult[$i][0]] = $aResult[$i][1];
		}
		
		return $aUsers;
	}
	
	/**
	 * Add new user to user database
	 *
	 * @param string $sUser New username
	 * @param string $sPassword New password
	 * @param boolean $bEncrypt Encrypt new password
	 * @return boolean True or false depending on success
	 */
	function addUser($sUser, $sPassword, $bEncrypt = true)
	{
		if ($bEncrypt) {
			$sPassword = $this->encrypt($sPassword, $sUser);
		}
		$this->_oPDO->exec(
			'INSERT INTO ' . $this->sDbTable .
			' (' . $this->sDbUserColumn . ',' . $this->sDbPasswordColumn .
			') VALUES ("' . addslashes($sUser) . '", "' . addslashes($this->encrypt($sPassword, $sUser)) . '")'
		);
		return true;
	}

	/**
	 * Remove user from user database
	 *
	 * @param string $sUser Name of user to delete
	 * @return boolean True or false depending on success
	 */
	function removeUser($sUser)
	{
		$this->_oPDO->exec(
			'DELETE FROM ' . $this->sDbTable .
			' WHERE ' . $this->sDbUserColumn . '="' . addslashes($sUser) . '"'
		);
		return true;
	}

	/**
	 * Change password of existing user
	 *
	 * @param string $sUser Name of user
	 * @param string $sPassword New password
	 * @return boolean True or false depending on success
	 */
	function changePassword($sUser, $sPassword)
	{
		$oStatement = $this->_oPDO->exec(
			'UPDATE ' . $this->sDbTable .
			' SET ' . $this->sDbPasswordColumn . '="' .
			addslashes($this->encrypt($sPassword, $sUser)) .
			'" WHERE ' . $this->sDbUserColumn . ' = "' . addslashes($sUser) . '"'
		);
		return true;
	}
}