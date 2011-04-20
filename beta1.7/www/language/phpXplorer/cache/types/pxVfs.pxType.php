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
 * Virtual filesystem base class
 *
 * Defines the interface for Vfs drivers and implements functionality
 * like indexing which could be used by every Vfs driver.
 *
 * @abstract
 * @belongsTo :shares
 * @expandSubtypes pxVfs
 */
class pxVfs extends pxMetaFiles{
	var $aPossibleActions = array(
		'upload' => true,
		'share' => true,
		'edit' => true,
		'md5' => true
	);

  /**
	 * Use RDBMS index to speed up file access
	 *
	 * @var boolean
	 * @edit Checkbox
   */
	var $bIndexed = false;


	/**
	 * A PHP PDO data source name like 'mysql:host=localhost;dbname=my_database'
	 * for index database connection.
	 *
	 * Have a look at http://de3.php.net/manual/de/function.pdo-construct.php or
	 * http://wiki.cc/php/PDO_Basics for details
	 *
	 * @var string
	 * @edit Input
	 */
	var $sIndexDSN = 'mysql:host=localhost;dbname=phpXplorer';
	var $sDriver;


	/**
	 * Table name for RDBMS index
	 * 
	 * @var string
	 * @edit Input
   */
	var $sIndexTable = 'pxFilesystem';
	

	/**
	 * Table name for RDBMS index keywords
	 * 
	 * @var string
	 * @edit Input
	 */
	var $sIndexPropertyTable = 'pxIndex';	


	/**
	 * User name for index database connection. 
	 * 
	 * @var string(64)
	 * @edit Input
   */
	var $sIndexUser = 'phpXplorer';


	/**
	 * Password for index database connection. 
	 * 
	 * @var string
	 * @edit Password
   */
	var $sIndexPassword = '';


	/**
	 * Write log file ?
	 * 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bLog = false;


	/** 
	 * Enable parsing of settings made locally in the share filesystem tree
	 * 
	 * @var integer
	 * @edit SelectTranslated
	 */
	var $iEvalShareTree = 0;

	
	/** 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bOwnerPermission = false;
	
	
	/** 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bDbPaging = false;


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


	/**
	 * Link to share object
	 * 
	 * @var object
	 */
	var $oShare;

	/**
	 * Specifies if there is a connection to the filesystem (FTP or SQL server for example)
	 *
	 * @access protected
	 * @var boolean
   */
	var $bConnected = false;

	/**
	 * Fill objects with os file permissions
	 * 
	 * @var boolean
	 */
	var $bPermissions = false;
	
	/**
	 * Do not fill objects filesize member if set to false.
	 * Tree views for example do not need filesize information.
	 * So its much faster if we could avoid unnecessary filesize calls.
	 */
	var $bSize = true;

	/**
	 * Specifies if there is a connection to the index database
	 *
	 * @access private
	 * @var boolean
   */
	var $_bIndexConnected = false;

	/**
	 * Stores the PDO object for RDBMS indices
	 *
	 * @access private
	 * @var object
   */
	var $oPdo;

	/**
	 * Flag to detain functions from using the index while building it 
	 * 
	 * @access private
	 * @var boolean 
	 */
	var $_bIndexing = false;

	/**
	 * 
	 */
	var $_aStatements = array();

	/**
	 * 
	 */
	var $_iLogHandle;
	
	var $iLastInsertId;

	/**
	 * Default query object which is used in cases where only directory changes 
	 */
	var $_oQuery;
	var $_sCachedSqlInserts = '';
	var $bCacheSql = true;

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	/**
	 * Returns options of selection member types
	 *
	 * @param string $sMember Selection member ID
	 * @return array Associative array with options
	 */
	function _getOptions($sMember, $bManualOptions = false)
	{
		global $pxp;

		switch($sMember)
		{
			case 'iEvalShareTree':
				return array(
	 				0 => 'none',
	 				1 => 'evalUsers',
	 				2 => 'evalAll'
				);
				break;
			default:
				return parent::_getOptions($sMember, $bManualOptions);
				break;
		}
	}


	/**
	 * 
	 */
	function __sleep()
	{
		$aVars = array_flip(parent::__sleep());

		unset($aVars['bConnected']);
		unset($aVars['bPermissions']);
		unset($aVars['bSize']);
		unset($aVars['aPossibleActions']);

		return array_keys($aVars);
	}

	/**
	 * 
	 */
	function init()
	{
		global $pxp;

		if ($this->bIndexed)
		{			
			if (!class_exists('pdo')) {
				require_once $pxp->sModuleDir . '/System.pxm/includes/PDO/PDO.class.php';
			}
			$this->_connectIndexDB();
		}

		if ($this->bLog) {
			$this->_iLogHandle = fopen($pxp->sDir . '/cache/' . $this->sName . '.log', 'a');
		}
	}

	/**
	 * 
	 */
	function connect() {
		return true;
	}

	/**
	 * Return file data
	 * 
	 * @param string Path to file
	 * @return string Data of file
	 */
	function file_get_contents($sPath) {
		if ($this->bLog) $this->_writeLog('file_get_contents', $sPath);
	}

	/**
	 * 
	 */
	function file_put($sPath, $sTmpPath, $bCreate)
	{
		if ($bCreate && $this->bIndexed && !$this->_bIndexing) {
			$this->_bIndexing = true;
			$iSize = $this->filesize($sTmpPath);
			$this->_bIndexing = false;
			$this->_file_put(
				$sPath,
				$iSize
			);
		}

		if ($this->bLog) $this->_writeLog('file_put', (int)$bCreate . ' ' . $sPath . ' ' . $sTmpPath);
	}

	/**
	 * 
	 */
	function file_put_contents($sPath, $mData, $bCreate)
	{
		if ($bCreate && $this->bIndexed && !$this->_bIndexing) {
			$this->_file_put(
				$sPath,
				strlen($mData)
			);
		}

		if ($this->bLog) {
			$this->_writeLog('file_put_contents', (int)$bCreate . ' ' . $sPath);
		}

		return true;
	}

	/**
	 * Helper function for file_put & file_put_contents
	 */
	function _file_put($sPath, $iSize = null)
	{
		global $pxp;

		$sRelPath = $this->oShare->getRealRelativePath($sPath);	
		$sName = basename($sRelPath);

		$sType = null;
		$sExtension = null;
		$pxp->getTypeKeyByExtension(
			$sName,
			($iSize === null),
			$sType,
			$sExtension
		);
		
		if ($iSize === null || !$this->aPossibleActions['md5']) {
			$sMd5 = null;
		} else {
			$sMd5 = md5_file($sPath);
		}

		if ($this->file_exists($sPath))
		{
			$this->_prepareUpdateStatement();

			$this->_aStatements['pxp:update']->execute(
				array(
					':share' => $this->oShare->sRealId,
					':directory' => pxUtil::dirname($sRelPath),
					':name' => $sName,
					':is_file' => (int)!is_null($iSize),
					':title' => null,
					':typ' => $sType,
					':extension' => $sExtension,
					':filesize' => $iSize,
					':filemtime' => time(),
					':owner' => $pxp->sUser,
					#':link_directory' => null,
					#':link_name' => null,
					':os_permissions' => null,
					':os_owner' => null,
					':os_group' => null,
					#':content' => null,
					':md5' => $sMd5
				)
			);

			return null;	
		}
		else
		{
			$this->_prepareInsertStatement();

			$this->_aStatements['pxp:insert']->execute(
				array(
					':share' => $this->oShare->sRealId,
					':directory' => pxUtil::dirname($sRelPath),
					':name' => $sName,
					':is_file' => (int)!is_null($iSize),
					':title' => null,
					':filesize' => $iSize,
					':typ' => $sType,
					':extension' => $sExtension,
					':filectime' => time(),
					':filemtime' => time(),
					':owner' => $pxp->sUser,
					#':link_directory' => null,
					#':link_name' => null,
					':os_permissions' => null,
					':os_owner' => null,
					':os_group' => null,
					':serialized' => null,
					#':content' => null,
					':md5' => $sMd5,
					':active' => 1,
					':visible' => 1,
					':position' => 0,
					':tagsOnly' => 1
				)
			);

			return $this->oPdo->lastInsertId();
		}
	}

	/**
	 * 
	 */
	function is_file($sPath)
	{
		if ($this->bLog) $this->_writeLog('is_file', $sPath);

		if ($this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:is_file'])) {
				$this->_aStatements['pxp:is_file'] = $this->oPdo->prepare(
					'SELECT count(name) FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ? AND name = ? AND filesize IS NOT NULL'
				);
			}

			$this->_executeStatement('pxp:is_file', $sPath);
			$aRows = $this->_aStatements['pxp:is_file']->fetchAll(3);

			return (boolean)$aRows[0][0];
		}
		return null;
	}

	/**
	 * 
	 */
	function filesize($sPath) {
		$mResult = $this->_getInfo($sPath, 'filesize');	
		if (is_numeric($mResult)) {
			return (int)$mResult;
		} else {
			return $mResult;
		}
	}

	/**
	 * 
	 */
	function touch($sPath)
	{
		if ($this->bLog) $this->_writeLog('touch', $sPath);

		if ($this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:touch'])) {
				$this->_aStatements['pxp:touch'] = $this->oPdo->prepare(
					'UPDATE `' . $this->sIndexTable . '` SET filemtime = :filemtime WHERE share = :share AND directory = :directory AND name = :name'
				);
			}

			$sRelPath = $this->oShare->getRealRelativePath($sPath);

			$this->_aStatements['pxp:touch']->execute(
				array(
					':filemtime' => time(),
					':share' => $this->oShare->sRealId,
					':directory' => pxUtil::dirname($sRelPath),
					':name' => basename($sRelPath)
				)
			);
		}
	}

	/**
	 * 
	 */
	function unlink($sPath, $bDelete) {
		$this->_delete($sPath, 'unlink', $bDelete);
	}

	/**
	 * Reads a directory or subtree and returns an array of objects
	 *
	 * If you make use of bRecursive each object has got an
	 * array member aObjects filled with its child objects.
	 *
	 * @param object pxQuery instance
	 *
	 * @return array Array of objects 
	 */
	function &ls(&$oQuery)
	{
		global $pxp;

		$aResult = array();

		if ($this->bLog) $this->_writeLog('ls', $oQuery->sDirectory . ' ' . (isset($oQuery->aNames) ? implode(',', $oQuery->aNames) : '') . ' ' . (isset($oQuery->aTypes) ? implode(',', $oQuery->aTypes) : ''));

		$aObjects = array();
		$iCounter = 0;

		if (!$this->_bIndexing && empty($oQuery->aNames))
		{
			if ($oQuery->sDirectory == '/') {
				$sType = $this->oShare->sBaseType;	
			} else {
				$sType = null;
				$sExtension = null;
				$pxp->getTypeKeyByExtension(basename($oQuery->sDirectory), true, $sType, $sExtension);
			}
	
			if (!class_exists($sType)) {
				$pxp->loadType($sType);
			}
	
			if (method_exists($sType, 'import')) {
				if (pxUtil::buildPath($pxp->oObject->sRelDir, $pxp->oObject->sName) == $oQuery->sDirectory) {
					$pxp->oObject->import();
				} else {
					$oImport = $this->get_object($oQuery->sDirectory, false, false);
					if (isset($oImport)) {
						$oImport->import();
					}				
				}
			}
		}		

		if ($this->bIndexed && !$this->_bIndexing) {

			/**
			 * The filesystem makes use of a RDMBS index.
			 * In this case the whole object creation is done in this class.
			 * The code to access the concrete datasource (OS, FTP, WebDAV) is
			 * implemented in the Vfs sub classes and gets called if there
			 * is no index or during index creation.
			 */

			$this->oPdo->setAttribute(3, 2);
			$oQuery->oVfs = &$this;
			if (isset($oQuery->sDirectory)) {
				$oQuery->sDirectory = $this->oShare->getRealRelativePath($oQuery->sDirectory);
			}
			$oQuery->sShare = $this->oShare->sRealId;


			if ($this->bDbPaging && $oQuery->bPermissionCheck) {
				if (!empty($oQuery->aTypes)) {
					$oQuery->aTypes = array_intersect($oQuery->aTypes, $this->oShare->getAllowedTypes('/', null));
				} else {
					$oQuery->aTypes = $this->oShare->getAllowedTypes('/', null);
				}
			}


			$sSqlQuery = $oQuery->getSql();

			#echo $sSqlQuery . "<br/><br/>\r\n\r\n";
			#if ($this->bLog) $this->_writeLog('query', $sSqlQuery);
			
			$oStatement = $this->oPdo->prepare($sSqlQuery);

			$aObjectDirectory = array($oQuery->sDirectory => &$aObjects);

			if (!isset($oStatement)) {
				$bResult = array();
				return $bResult;
			}
			
			$iTime = pxUtil::getMicrotime();

			$oStatement->execute();
			
			#foreach ($this->oPdo->query('SELECT FOUND_ROWS() as count') as $aRow) {
			#	print_r($aRow);
			#}
			#echo $oStatement->rowCount() . "<br/>";

			$oQuery->iLastTime = pxUtil::getMicrotime() - $iTime;

			#$aResult = $oStatement->fetchAll(2);
			#foreach ($aResult as $aRow)
			while($aRow = $oStatement->fetch(2))
			{
				if ($this->oShare->bBookmark) {
					$sShare = $this->oShare->sRealId;
					$sRelDir = substr($aRow['directory'], $this->oShare->iRealDirOffset);
					if (!$sRelDir) {
						$sRelDir = '/';
					}
				} else {
					$sShare = $aRow['share'];
					$sRelDir = $aRow['directory'];
				}

				// Check if user is allowed to do anything with objects of current file type
				if (
					//!$this->bDbPaging &&
					$oQuery->bPermissionCheck &&
					!$this->oShare->checkTypePermission(
						$sRelDir,
						$aRow['typ'],
						$aRow['owner']
					)
				) {
					continue;
				}

				/*
				 * If there is a user defined order function we will have
				 * to apply it to the whole object collection before limitation 
				 */
				if (!$this->bDbPaging && !isset($oQuery->sOrderFunction))
				{
					// Handle limit
					if ($oQuery->iLimit > 0) {
						if ($iCounter >= ($oQuery->iLimit + $oQuery->iOffset)) {
							if ($oQuery->bFullResultCount) {
								$iCounter++;
								continue;
							} else {
								break;
							}
						}
					}

					// Handle offset
					if ($oQuery->iOffset > 0) {
						if ($iCounter < $oQuery->iOffset) {
							$iCounter++;
							continue;
						}
					}
				}

				if ($oQuery->bFull)
				{
					// Return native objects filled with all their meta data
				 	$pxp->loadType($aRow['typ']);
				 	
				 	if ($aRow['serialized'] != null) {
				 		$oObject = unserialize($aRow['serialized']);
				 		$oObject->_bMetaDataExists = true;
				 	} else {
				 		$oObject =& new $aRow['typ'];
				 	}
				}
				else
				{
					// Return pxObject objects filled with filesystem meta data
					$oObject =& new pxObject;
				}

				/**
				 * Set filesystem meta data properties.
				 */
				$oObject->iDatabaseRowId = $aRow['id'];
				$oObject->sShare = $sShare;
				$oObject->sName = $aRow['name'];
				$oObject->sRelDir = $sRelDir;
				$oObject->sType = $aRow['typ'];
				$oObject->sExtension = $aRow['extension'];

				if (!empty($oObject->sExtension)) {
					$oObject->sId = substr($oObject->sName, 0, strpos($oObject->sName, $oObject->sExtension) - 1);
				} else {
					$oObject->sId = $oObject->sName;
				}

				$oObject->dModified = $aRow['filemtime'];
				#$oObject->dCreated = $aRow['filectime'];
				$oObject->sOwner = $aRow['owner'];
				$oObject->sTitle = $aRow['title'];
				$oObject->iBytes = (int)$aRow['filesize'];
				$oObject->bDirectory = $aRow['filesize'] === null;

				if ($oQuery->bOsPermissions) {
					$oObject->sOSystemPermissions = $aRow['os_permissions'];
					$oObject->sOSystemOwner = $aRow['os_owner'];
					$oObject->sOSystemGroup = $aRow['os_group'];
				}

				#$oObject->bActive = $aRow['active'];

				// Handle bGetFirst
				if ($oQuery->bGetFirst) {
					$oResult = $oObject;
					return $oResult;
				}

				if ($oQuery->bRecursive) {
					
					$aObjectDirectory[$aRow['directory']][] =& $oObject;

					#$aObjects[] =& $oObject;

					if ($oObject->bDirectory) {
						$aObjectDirectory[
							pxUtil::buildPath($aRow['directory'], $oObject->sName)
						] =& $oObject->aObjects;
					}

				} else {
					$aObjects[] =& $oObject;
				}

				$iCounter++;

				unset($oObject);
			}
			
			#$oStatement->closeCursor();
			
			// Handle failed bGetFirst
			if ($oQuery->bGetFirst) {
				$bResult = null;
				return $bResult;
			}
			
			$oQuery->iFullResultCount = $iCounter;
			
			// Handle user defined order function.
			if (isset($oQuery->sOrderFunction))
			{
				#if ($oQuery->bRecursive) {
				#	foreach ($aObjectDirectory as $sPath => $aObjects2) {
				#		usort($aObjectDirectory[$sPath], $oQuery->sOrderFunction);
	 			#		if ($oQuery->sOrderDirection == 'desc') {
		  	#			$aObjectDirectory[$sPath] = array_reverse($aObjectDirectory[$sPath]);
  			#		}
				#	}
				#} else {
					usort($aObjects, $oQuery->sOrderFunction);
					
 					if ($oQuery->sOrderDirection == 'desc') {
		  			$aObjects = array_reverse($aObjects);
  				}
				#}

				//$oQuery->iFullResultCount = count($aObjects);

  			if (!empty($oQuery->iLimit)) {
	  			if (empty($oQuery->iOffset)) {
  					$oQuery->iOffset = 0;
  				}
					for ($i = count($aObjects); $i >= 0; $i--) {
						if (
							$i >= ($oQuery->iLimit + $oQuery->iOffset)
							or
							$i < $oQuery->iOffset
						) {
							unset($aObjects[$i]);
						}
					}
				}

			}

			unset($aObjectDirectory);

		} else {

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * common filesystem list code
 *~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

			$sDirectory = $oQuery->sDirectory;

			if (strpos($sDirectory, $this->oShare->sBaseDir) !== 0 && $oQuery->_bExtendDirectory) {
				$sDirectory = pxUtil::buildPath(
					$this->oShare->sBaseDir,
					$sDirectory
				);
			}

			$aStack = array(array($sDirectory, &$aObjects));

	  	#$iTime = getMicrotime();

	  	$sRegExp = $oQuery->getRegExp();
	  	
	  	#if (!empty($oQuery->sSearchQuery)) {
	  	#	echo getMicrotime() - $iTime . "<br/>";
	  	#}

	  	#print_r($oQuery);
	  	#die;

	  	while (!empty($aStack))
	  	{
	  		$aStackInfo = array_pop($aStack);
				$sCurrentDir = $aStackInfo[0];
	  		$sRelDir = $this->oShare->getRelativePath($sCurrentDir);

	  		$aFiles = array();
				$this->_ls($oQuery, $aFiles, $sCurrentDir);

				if ($sCurrentDir == $sDirectory) {
					$oQuery->iFullResultCount = count($aFiles);
				}

				switch ($oQuery->sOrderBy) {
					case 'sName':
						usort($aFiles, array($this, 'sortBy_sName'));
						break;
					case 'iBytes':
						usort($aFiles, array($this, 'sortBy_iBytes'));
						break;
					case 'dModified':
						usort($aFiles, array($this, 'sortBy_dModified'));
						break;
					case 'sType':
						usort($aFiles, array($this, 'sortBy_sType'));
						break;
				}

				if ($oQuery->sOrderDirection == 'desc') {
					$aFiles = array_reverse($aFiles);
				}

				$oObject = null;

	  		foreach ($aFiles as $aFile)
	  		{
	  			unset($oObject);

	  			$bDirectory = $aFile['bDirectory'];
	  			$sName = $aFile['sName'];

					if ($oQuery->bOnlyDirectories && !$bDirectory) {
						continue;
					}

					if (isset($aFile['sType'])) {
						$sType = $aFile['sType'];
						$sExtension = $aFile['sExtension'];
					} else {
				 		$sType = null;
				 		$sExtension = null;
				 		$pxp->getTypeKeyByExtension(
					 		$sName,
				 			$bDirectory,
				 			$sType,
				 			$sExtension
				 		);
					}

					if ($oQuery->bPermissionCheck)
					{
						if ($this->bOwnerPermission)
						{
							#require_once $pxp->sModuleDir . '/' . $pxp->aTypes[$sType]->sModule . '.pxm/types/' . $sType . '.pxType.php';
							$pxp->loadType($sType);

							$mContent = @$this->file_get_contents(
								$sCurrentDir . '/.phpXplorer/.objects/' . $sName . '.serializedPhp');

							if ($mContent !== false) {
								$oObject = unserialize($mContent);
								$oObject->_bMetaDataExists = true;
							} else {
								$oObject =& new $sType;
							}
							if (!empty($aFile['sOwner'])) {
								$oObject->sOwner = $aFile['sOwner'];
							}
						}

					 	// Check if user is allowed to do anything with objects of current file type
						if (!$this->oShare->checkTypePermission(
							$sRelDir,
							$sType,
							isset($oObject->sOwner) ? $oObject->sOwner : null 
						)) {
					 		continue;
						}
					}
					
	
					// Search in all sub directories too
					#if ($bDirectory && (!empty($oQuery->sSearchQuery) || $oQuery->bRecursiveFlat) && $sType != 'pxVirtualDirectory') {
					if ($bDirectory && $oQuery->bRecursiveFlat && $sType != 'pxVirtualDirectory') {		
						$aStack[] = array($sCurrentDir . '/' . $sName, &$aObjects);
					}

					// Handle type selection
				 	if (isset($oQuery->aTypes)) {
						if (!$pxp->aTypes[$sType]->isSupertypeInArray($oQuery->aTypes)) {
				 			continue;
				 		}
				 	}
					
					if (isset($sRegExp)) {
						if (!preg_match($sRegExp, $sName)) {
							continue;
						}
					}
	
					/*
					 * If there is a user defined order function we will have
					 * to apply it to the whole object collection before limitation
					 */
					if (!isset($oQuery->sOrderFunction))
					{
	  		  	// Handle limit
	 		  		if (!empty($oQuery->iLimit)) {
		  		  	if ($iCounter >= ($oQuery->iLimit + $oQuery->iOffset)) {
		  		  		#if ($oQuery->bFullResultCount) {
	  		  			#	$iCounter++;
	 		  				#	continue;
		  		  		#} else {
	 		  					break;
		  		  		#}
		 		  		}
	 		  		}

		 		  	// Handle offset
	 		  		if (!empty($oQuery->iOffset)) {
		  		  	if ($iCounter < $oQuery->iOffset) {
	  		  			$iCounter++;
	 		  				continue;
	 		  			}
	 		  		}
	  			}

					if (!isset($oObject)) 
					{
						if ($oQuery->bFull)
						{
							#require_once $pxp->sModuleDir . '/' . $pxp->aTypes[$sType]->sModule . '.pxm/types/' . $sType . '.pxType.php';
							$pxp->loadType($sType);
							$sDataPath = $sCurrentDir . '/.phpXplorer/.objects/' . $sName . '.serializedPhp';
							$mContent = @$this->file_get_contents($sDataPath);

							if ($mContent !== false) {
								$oObject = unserialize($mContent);
								$oObject->_bMetaDataExists = true;
							} else {
								$oObject =& new $sType;
							}
						} else {
							//Return pxObject objects filled with filesystem meta data
							$oObject =& new pxObject;
						}
	  			}

					$oObject->sShare = $this->oShare->sId;
					$oObject->sName = $sName;
					$oObject->sRelDir = $sRelDir;
					$oObject->bDirectory = $bDirectory;
					$oObject->sType = $sType;
					$oObject->sExtension = $sExtension;
					if (!empty($aFile['sOwner'])) {
						$oObject->sOwner = $aFile['sOwner'];
					}

					if (empty($oObject->sExtension)) {
						$oObject->sId = $oObject->sName;
					} else {
						$oObject->sId = substr($oObject->sName, 0, strpos($oObject->sName, $oObject->sExtension) - 1);
					}

					if (isset($aFile['dModified'])) {
						$oObject->dModified = $aFile['dModified'];
					} else {
						$oObject->dModified = @$this->filemtime($sCurrentDir . '/' . $sName);
						if ($oObject->dModified === false) {
							continue;
						}
					}

					if ($oQuery->bFilesize) {
						if (!$bDirectory) {
							if (isset($aFile['iBytes'])) {
								$oObject->iBytes = $aFile['iBytes'];
							} else {
								$oObject->iBytes = $this->filesize($sCurrentDir . '/' . $sName);
							}
						}
					}

					#if (isset($aFile['dCreated'])) {
					#	$oObject->dCreated = $aFile['dCreated'];
					#} else {
					#	$oObject->dCreated = $this->filectime($sCurrentDir . '/' . $sName);
					#}

					if ($oQuery->bOsPermissions) {
						if (isset($aFile['sPermissions'])) {
							$oObject->sOSystemPermissions = $aFile['sPermissions'];
							$oObject->sOSystemOwner = $aFile['sOwner'];
							$oObject->sOSystemGroup = $aFile['sGroup'];
						} else {
							$oObject->sOSystemPermissions = $this->os_permissions($sCurrentDir . '/' . $sName);
							$oObject->sOSystemOwner = $this->os_owner($sCurrentDir . '/' . $sName);
							$oObject->sOSystemGroup = $this->os_group($sCurrentDir . '/' . $sName);						
						}
					}

					#if (!isset($sRegExp) && !empty($oQuery->sSearchQuery)) {
					if (!empty($oQuery->oConditions->aSubConditions)) {
						if (!$oQuery->checkObject($oObject)) {
							continue;
						}
					}
	
					// Handle bGetFirst
					if ($oQuery->bGetFirst) {
						$oResult = $oObject;
						return $oResult;
					}
	
					// Add directories to the stack if we have to go down the tree
					#if ($oQuery->bRecursive && $bDirectory && empty($oQuery->sSearchQuery) && !$oQuery->bRecursiveFlat) {
					if ($bDirectory && $oQuery->bRecursive && !$oQuery->bRecursiveFlat) {
						$aStack[] = array($sCurrentDir . '/' . $sName, &$oObject->aObjects);
					}
	
					// Add new object - $aStackInfo[1] refers to aObjects arrays
					$aStackInfo[1][] =& $oObject;
	
					unset($oObject);

					$iCounter++;
				}
	
				unset($aResult);
	
	 			// Handle failed bGetFirst
				if ($oQuery->bGetFirst) {
					$bResult = null;
					return $bResult;
				}
	
				// Handle user defined order function.
				if (isset($oQuery->sOrderFunction))
				{
					usort($aStackInfo[1], $oQuery->sOrderFunction);
	
	 				if ($oQuery->sOrderDirection == 'desc') {
		  			$aStackInfo[1] = array_reverse($aStackInfo[1]);
	  			}
	
	  			// Limit the result of the top directory only
	  			if ($sCurrentDir == $sDirectory) {
	
						#$oQuery->iFullResultCount = count($aObjects);
	
	  				if (!empty($oQuery->iLimit)) {
		  				if (empty($oQuery->iOffset)) {
	  						$oQuery->iOffset = 0;
	  					}
							for ($i = count($aStackInfo[1]); $i >= 0; $i--) {
								if (
									$i >= ($oQuery->iLimit + $oQuery->iOffset)
									or
									$i < $oQuery->iOffset
								) {
									unset($aStackInfo[1][$i]);
								}
							}
						}
	  			}
				} else {
					#if ($sCurrentDir == $sDirectory) {
					#	if ($oQuery->bFullResultCount) {
					#		$oQuery->iFullResultCount = $iCounter;
					#	}
					#}
				}
	  	}
		}
		
		return $aObjects;

		$mResult = null;
		return $mResult;
	}

	/**
	 *
	 */
	function &get_object($sPathIn, $bCheckPermission = true, $bRaiseError = true)
	{
		global $pxp;

		if ($this->bLog) $this->_writeLog('get_object', $sPathIn);

		#if ($this->file_exists($sPathIn))
		#{
		$oQuery = new pxQuery();
		$oQuery->_bExtendDirectory = false;
		$oQuery->bPermissionCheck = $bCheckPermission;
		$oQuery->bFull = true;

		if ($this->bIndexed && !$this->_bIndexing) {	
			$sRelPath = $this->oShare->getRealRelativePath($sPathIn);
			$oQuery->sDirectory = pxUtil::dirname($sRelPath);
			$oQuery->aNames = array(basename($sRelPath));
		} else {
			$oQuery->sDirectory = pxUtil::dirname($sPathIn);
			$oQuery->aNames = array(basename($sPathIn));
		}

		$aObjects =& $this->ls($oQuery);

		if (isset($aObjects[0])) {
			return $aObjects[0];
		} else {		
			if ($bRaiseError) {
				$pxp->raiseError('fileNotFound', __FILE__, __LINE__, array($sPathIn));
			}
			$bResult = null;
			return $bResult;
		}
	}

	/**
	 * 
	 */
	function store_object($oObject)
	{
		global $pxp;

		if ($this->bLog) $this->_writeLog('store_object', $oObject->getFullPath());

		if ($this->bIndexed && !$this->_bIndexing)
		{
			if ($this->oShare->bBookmark) {
				$sRelDir =
					substr($this->oShare->sBaseDir, -$this->oShare->iRealDirOffset) . 
					$oObject->sRelDir;
			} else {
				$sRelDir = $oObject->sRelDir;
			}

			if ($oObject->isNew())
			{
				$bDirectory = $pxp->aTypes[$oObject->sType]->bDirectory;
				$sType = null;
				$sExtension = null;
				$pxp->getTypeKeyByExtension($oObject->sName, $bDirectory, $sType, $sExtension);

				$this->_prepareInsertStatement();
				$this->_aStatements['pxp:insert']->execute(
					array(
						':share' => $this->oShare->sRealId,
						':directory' => $sRelDir,
						':name' => $oObject->sName,
						':is_file' => (int)!$bDirectory,
						':title' => $oObject->sTitle,
						':filesize' => $bDirectory ? null : 0,
						':typ' => $sType,
						':extension' => $sExtension,
						':filectime' => $oObject->dCreated,
						':filemtime' => $oObject->dModified,
						':owner' => $oObject->sOwner,
						#':link_directory' => null,
						#':link_name' => null,
						':os_permissions' => null,
						':os_owner' => null,
						':os_group' => null,
						':serialized' => serialize($oObject),
						#':content' => null,
						':md5' => null,
						':active' => 1,
						':visible' => 1,
						':position' => 0,
						':tagsOnly' => 0
					)
				);
			}
			else
			{
				if (!isset($this->_aStatements['pxp:store_object'])) {
					$this->_aStatements['pxp:store_object'] = $this->oPdo->prepare(
						'UPDATE `' . $this->sIndexTable . '` SET' .
						' serialized = :serialized,' .
						' owner = :owner,' .
						' title = :title,' .
						' active = :active,' .
						' visible = :visible,' .
						' position = :position,' .
						' tagsOnly = :tagsOnly,' .
						' filemtime = :modified,' .
						' filectime = :created' .
						' WHERE share = :share AND directory = :directory AND name = :name'
					);
				}
				$this->_aStatements['pxp:store_object']->execute(
					array(
						':serialized' => serialize($oObject),
						':owner' => $oObject->sOwner,
						':title' => $oObject->sTitle,
						':created' => $oObject->dCreated,
						':modified' => $oObject->dModified,
						':active' => $oObject->bActive ? 1 : 0,
						':visible' => $oObject->bVisible ? 1 : 0,
						':position' => is_numeric($oObject->fPosition) ? (float)$oObject->fPosition : 0,
						':tagsOnly' => isset($oObject->bTagsOnly) ? (int)$oObject->bTagsOnly : 0,
						':share' => $this->oShare->sRealId,
						':directory' => $sRelDir,
						':name' => $oObject->sName
					)
				);				
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 
	 */
	function mkdir($sPath, $bCreate)
	{
		if ($this->bLog) $this->_writeLog('mkdir', (int)$bCreate . ' ' . $sPath);
		
		if ($bCreate && $this->bIndexed && !$this->_bIndexing) {
			return $this->_file_put(
				$sPath
			);
		}
	}

	/**
	 * 
	 */
	function is_dir($sPath)
	{
		if ($this->bLog) $this->_writeLog('is_dir', $sPath);

		if ($this->bIndexed && !$this->_bIndexing) {

			if (!isset($this->_aStatements['pxp:is_dir'])) {
				$this->_aStatements['pxp:is_dir'] = $this->oPdo->prepare(
					'SELECT count(name) FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ? AND name = ? AND filesize IS NULL'
				);
			}

			$this->_executeStatement('pxp:is_dir', $sPath);

			$aRows = $this->_aStatements['pxp:is_dir']->fetchAll(3);

			return (boolean)$aRows[0][0];
		}
		return null;
	}

	/**
	 * 
	 */
	function &scandir($sDir)
	{
		if ($this->bLog) $this->_writeLog('scandir', $sDir);

		if ($this->bIndexed && !$this->_bIndexing) {

			$aFiles = array('.', '..');

			if (!isset($this->_aStatements['pxp:scandir'])) {
				$this->_aStatements['pxp:scandir'] = $this->oPdo->prepare(
					'SELECT name FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ?'
				);
			}

			$sRelPath = $this->oShare->getRealRelativePath($sDir);

			$this->_aStatements['pxp:scandir']->execute(
				array(
					$this->oShare->sRealId,
					$sRelPath,
				)
			);

			foreach ($this->_aStatements['pxp:scandir']->fetchAll(2) as $aRow) {
				$aFiles[] = $aRow['name'];
			}

			return $aFiles;
		}
		$mResult = null;
		return $mResult;
	}

	/**
	 * 
	 */
	function rmdir($sPath, $bDelete)
	{
		$this->_delete($sPath, 'unlink', $bDelete);

		$sRelPath = $this->oShare->getRealRelativePath($sPath);

		if ($bDelete && $this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:rmdir'])) {
				$this->_aStatements['pxp:rmdir'] = $this->oPdo->prepare(
					'DELETE FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND (directory = ? OR directory LIKE ?)'
				);
			}

			$this->_aStatements['pxp:rmdir']->execute(
				array(
					$this->oShare->sRealId,
					$sRelPath,
					$sRelPath . '/%'
				)
			);
		}
	}

	/**
	 * 
	 */
	function file_exists($sPath)
	{
		if ($this->bLog) $this->_writeLog('file_exists', $sPath);

		if ($this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:file_exists'])) {
				$this->_aStatements['pxp:file_exists'] = $this->oPdo->prepare(
					'SELECT count(name) FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ? AND name = ?'
				);
			}

			$this->_executeStatement('pxp:file_exists', $sPath);

			$aRows = $this->_aStatements['pxp:file_exists']->fetchAll(3);

			return (boolean)$aRows[0][0];
		}
		return null;
	}

	/**
	 * 
	 */
	function filemtime($sPath) {
		$mResult = $this->_getInfo($sPath, 'filemtime');
		return $mResult === null ? null : $mResult;
	}

	/**
	 * 
	 */
	function filectime($sPath) {
		$mResult = $this->_getInfo($sPath, 'filectime');
		return $mResult === null ? null : $mResult;
	}

	/**
	 * 
	 */
	function os_permissions($sPath) {
		return $this->_getInfo($sPath, 'os_permissions');
	}

	/**
	 * 
	 */
	function os_owner($sPath) {
		return $this->_getInfo($sPath, 'os_owner');
	}

	/**
	 * 
	 */
	function os_group($sPath) {
		return $this->_getInfo($sPath, 'os_group');
	}

	/**
	 * 
	 */
	function getimagesize($sPath) {
		if ($this->bLog) $this->_writeLog('getimagesize', $sPath);
	}

	/**
	 * 
	 */
	function imagecreatefromjpeg($sPath) {
		if ($this->bLog) $this->_writeLog('imagecreatefromjpeg', $sPath);
	}

	/**
	 * 
	 */
	function imagecreatefromgif($sPath) {
		if ($this->bLog) $this->_writeLog('imagecreatefromgif', $sPath);
	}

	/**
	 * 
	 */
	function imagecreatefrompng($sPath) {
		if ($this->bLog) $this->_writeLog('imagecreatefrompng', $sPath);
	}

	/**
	 * 
	 */
	function imagejpeg($iImageOut, $sPath = null, $iQuality = 90) {
		if ($this->bLog) $this->_writeLog('imagejpeg', $sPath);
	}
	
	/**
	 * 
	 */
	function imagegif ($iImageOut, $sPath = null) {
		if ($this->bLog) $this->_writeLog('imagegif', $sPath);
	}
	
	/**
	 * 
	 */
	function imagepng($iImageOut, $sPath = null) {
		if ($this->bLog) $this->_writeLog('imagepng', $sPath);
	}

	/**
	 * 
	 */
	function _renameRecursive($sRelPathOld, $sRelPathNew)
	{
		$oQuery = new pxQuery();
		$oQuery->sDirectory = $sRelPathOld;
		$oQuery->bOnlyDirectories = true;
			
		$aResult = $this->oShare->oVfs->ls($oQuery);

		foreach ($aResult as $oObject) {
			$this->_renameRecursive(
				pxUtil::buildPath($sRelPathOld, $oObject->sName),
				pxUtil::buildPath($sRelPathNew, $oObject->sName)
			);
		}

		if (!isset($this->_aStatements['pxp:rename2'])) {
			$this->_aStatements['pxp:rename2'] = $this->oPdo->prepare(
				'UPDATE `' . $this->sIndexTable . '` SET ' .
				'directory = :newDirectory' .
				' WHERE share = :share AND directory = :directory'
			);
		}

		$this->_aStatements['pxp:rename2']->execute(
			array(
				':newDirectory' => $sRelPathNew,
				':share' => $this->oShare->sRealId,
				':directory' => $sRelPathOld
			)
		);
	}

	/**
	 * 
	 */
	function rename($sPathOld, $sPathNew, $bRename)
	{
		global $pxp;

		$sRelPathOld = $this->oShare->getRealRelativePath($sPathOld);
		$sRelPathNew = $this->oShare->getRealRelativePath($sPathNew);

		$sOldDir = pxUtil::dirname($sRelPathOld);
		$sOldName = basename($sRelPathOld);

		$sNewDir = pxUtil::dirname($sRelPathNew);
		$sNewName = basename($sRelPathNew);

		$bDir = $this->is_dir($sPathOld);

		$sNewType = null;
		$sNewExtension = null;
		$pxp->getTypeKeyByExtension(
			$sNewName,
			$bDir,
			$sNewType,
			$sNewExtension
		);

		$sOldType = null;
		$sOldExtension = null;
		$pxp->getTypeKeyByExtension(
			$sOldName,
			$bDir,
			$sOldType,
			$sOldExtension
		);

		$bNewType = $sNewType != $sOldType;

		if ($bRename && $this->bIndexed && !$this->_bIndexing)
		{

			if ($bNewType) {
				$oObject = $this->get_object($sPathOld, false, false);
				$pxp->loadType($sNewType);
				$oNewObject = new $sNewType;
				$aNewProperties = (array)$oNewObject;
				$aNewProperties = array_keys($aNewProperties);
				foreach ((array)$oObject as $sKey => $sValue) {
					if (in_array($sKey, $aNewProperties)) {
						$oNewObject->{$sKey} = $sValue;
					}
				}
			}

			if (!isset($this->_aStatements['pxp:rename' . $bNewType])) {
				$this->_aStatements['pxp:rename' . $bNewType] = $this->oPdo->prepare(
					'UPDATE `' . $this->sIndexTable . '` SET ' .
					'directory = :newDirectory,' .
					'name = :newName,' .
					'typ = :newType,' .
					'extension = :newExtension' .
					($bNewType ? ', serialized = :serialized' : '') .
					' WHERE share = :share AND directory = :directory AND name = :name'
				);
			}
			
			$aValues = array(
				':newDirectory' => $sNewDir,
				':newName' => $sNewName,
				':newType' => $sNewType,
				':newExtension' => $sNewExtension,
				':share' => $this->oShare->sRealId,
				':directory' => $sOldDir,
				':name' => $sOldName
			);
			
			if ($bNewType) {
				$aValues[':serialized'] = serialize($oNewObject);
			}

			$this->_aStatements['pxp:rename' . $bNewType]->execute($aValues);

			if ($bDir) {
				$this->_renameRecursive($sRelPathOld, $sRelPathNew);
			}

		} else {
			
			// Rename locally stored serialized object file 
			if ($bRename) {
				$sObjectPath = $this->_getObjectPath($sPathOld);
				if ($this->is_file($sObjectPath)) {
					$this->rename($sObjectPath, $this->_getObjectPath($sPathNew));
				}
			}
		}

		if ($this->bLog) $this->_writeLog('rename', (int)$bRename . ' ' .  $sPathOld . ' -> ' . $sPathNew);
	}

	/**
	 * 
	 */
	function copy($sPathFrom, $sPathTo)
	{	
		if ($this->bLog) $this->_writeLog('copy', $sPathFrom . ' -> ' . $sPathTo);
		
		if ($this->bIndexed) {
			if (!isset($this->_aStatements['pxp:copy'])) {

				$sQuery = <<<EOD
					INSERT INTO `$this->sIndexTable`
					(
						share,
						directory,
						name,
						is_file,
						title,
						filesize,
						typ,
						extension,
						filectime,
						filemtime,
						owner,
						#link_directory,
						#link_name,
						os_permissions,
						os_owner,
						os_group,
						serialized,
						/*content,*/
						md5,
						active,
						visible,
						position,
						tagsOnly
					)
					SELECT
						:shareTo,
						:directoryTo,
						:nameTo,
						is_file,
						title,
						filesize,
						typ,
						extension,
						:created,
						filemtime,
						owner,
						#link_directory,
						#link_name,
						os_permissions,
						os_owner,
						os_group,
						serialized,
						/*content,*/
						md5,
						active,
						visible,
						position,
						tagsOnly
					FROM `$this->sIndexTable`
					WHERE share = :share AND directory = :directory AND name = :name
EOD;

				$this->_aStatements['pxp:copy'] = $this->oPdo->prepare($sQuery);
			}

			$sRelPath = $this->oShare->getRealRelativePath($sPathFrom);
			$sRelPathTo = $this->oShare->getRealRelativePath($sPathTo);

			$this->_aStatements['pxp:copy']->execute(
				array(
					':share' => $this->oShare->sRealId,
					':directory' => pxUtil::dirname($sRelPath),
					':name' => basename($sRelPath),
					':created' => time(),
					':shareTo' => $this->oShare->sRealId,
					':directoryTo' => pxUtil::dirname($sRelPathTo),
					':nameTo' => basename($sRelPathTo)
				)
			);

			return $this->oPdo->lastInsertId();

		} else {

			$oObject = $this->get_object($sPathFrom, false, false);
			if ($oObject->_bMetaDataExists) {
				$sRelPathTo = $this->oShare->getRelativePath($sPathTo);
				$oObject->sRelDir = pxUtil::dirname($sRelPathTo);
				$oObject->sName = basename($sRelPathTo);
				$this->store_object($oObject);				
			}
		}
	}

	function copyRec($sSource, $sDest)
	{
		if (is_file($sSource)) {
			return $this->copy($sSource, $sDest);
		}

		if (!is_dir($sDest))
		{
			$this->mkdir($sDest);
			
			$oObject = $this->get_object($sSource, false, false);
			if ($oObject->_bMetaDataExists) {
				$sRelPathTo = $this->oShare->getRelativePath($sDest);
				$oObject->sRelDir = pxUtil::dirname($sRelPathTo);
				$oObject->sName = basename($sRelPathTo);
				$this->store_object($oObject);				
			}
		}

		$oDir = dir($sSource);
		while (false !== $sEntry = $oDir->read()) {
			if ($sEntry == '.' || $sEntry == '..') {
				continue;
			}

			if ($sDest !== $sSource . '/' . $sEntry) {
				$this->copyRec($sSource . '/' . $sEntry, $sDest . '/' . $sEntry);
			}
		}

		$oDir->close();
		return true;
	}
	
	/**
	 * 
	 */
	function import(&$oQuery) {}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	/**
	 * Determines the location of the OS temporary directory.
	 * 
	 * @access private
	 * 
	 * @return boolean Path to temporary directory or false if one could not be found
	 */
	function _getTempDir() {
		$aLocations = array('/tmp', '/var/tmp', 'c:\WUTemp', 'c:\temp', 'c:\windows\temp', 'c:\winnt\temp');
		$sTmp = ini_get('upload_tmp_dir');
		if (empty($sTmp)) $sTmp = getenv ('TMPDIR');
  	while (empty($sTmp) && count($aLocations)) {
			$sCheck = array_shift($aLocations);
			if (@is_dir($sCheck)) {
				$sTmp = $sCheck;
			}
    }
		return empty($sTmp) ? false : $sTmp;
	}

	/**
	 * Return a temporary filename
	 * 
	 * @access protected
	 * @return string Temporary filename
	 */
	function getTempPath() {
		$sTmp = $this->_getTempDir();
		if ($sTmp === false) return false;
		return tempnam($sTmp, 'phpXplorer');
	}
	
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	
	/**
 	 * Syncronize index database with filesystem
	 * @param string $sBaseDir Base directory for indexing out of the share(d) filesystem
	 */
	function buildFSIndex($sBaseDir, $bRebuild = false, $bImportMetaData = true)
	{
		global $pxp;

		$iStartTime = pxUtil::getMicrotime();

		$this->connect();
		$this->_connectIndexDB();
		$this->_prepareInsertStatement();
		$this->_prepareUpdateStatement(true);

		$this->oPdo->setAttribute(3, 2);

		$pxp->loadShare($this->oShare->sId);

		if (!$this->bIndexed) {
			die('Filesystem is not indexed or index not active');
		}

		if (empty($this->sIndexDSN)  || empty($this->sIndexTable)) {
			die('Please set DSN and table of filesystem object ("' . $pxp->oObject->sVfs . '")');
		}

		if ($bRebuild) {
			$this->oPdo->query(
				"DELETE `$this->sIndexPropertyTable` FROM `$this->sIndexTable` INNER JOIN `$this->sIndexPropertyTable` WHERE `$this->sIndexTable`.id = `$this->sIndexPropertyTable`.id AND `$this->sIndexTable`.share = '" . $this->oShare->sId . "'"
			);
		}

		// Flag to detain $this->ls() function from using the index table while building index 
		$this->_bIndexing = true;
		
		header('Content-Type: text/plain');

		echo 'Building index for \'' . $pxp->oObject->sVfs . '\'' .chr(13);

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	 *	Load index database
	 *~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

		$oStmtSelect = $this->oPdo->prepare(
			'SELECT id, directory, name, typ, extension, filemtime, serialized, md5 FROM `' . $this->sIndexTable . '` WHERE share = :share'
		);
		$oStmtSelect->execute(array(':share' => $this->oShare->sId));
		$aIndexResult = $oStmtSelect->fetchAll(2);

		$aIndexRows = array();
		for ($i = 0, $m = count($aIndexResult); $i < $m; $i++) {
			$aIndexRows[
				pxUtil::buildPath(
					$sBaseDir,
					pxUtil::buildPath(
						$aIndexResult[$i]['directory'],
						$aIndexResult[$i]['name']
					)
				)
			] = &$aIndexResult[$i];	
		}

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	 *	INSERT root directory
	 *~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/		

		if (!isset($aIndexRows[$sBaseDir])) {
			echo "+ /\n";
			$this->_aStatements['pxp:insert']->execute(
				array(
					':share' => $this->oShare->sId,
					':directory' => '/',
					':name' => '',
					':is_file' => 0,
					':title' => null,
					':filesize' => null,
					':typ' => 'pxDirectory',
					':extension' => null,
					':filectime' => $this->filectime($sBaseDir),
					':filemtime' => $this->filemtime($sBaseDir),
					':owner' => $pxp->sUser,
					#':link_directory' => null,
					#':link_name' => null,
					':os_permissions' => $this->os_permissions($sBaseDir),
					':os_owner' => $this->os_owner($sBaseDir),
					':os_group' => $this->os_group($sBaseDir),
					':serialized' => null,
					#':content' => null,
					':md5' => null,
					':active' => 1,
					':visible' => 1,
					':position' => 0,
					':tagsOnly' => 0,
				)
			);
		}

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	 *	Traverse filesystem / INSERT / UPDATE
	 *~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

		$aFilesystemContent = array();
		$aFilesystemContent[$sBaseDir] = true;

		$aDirectoryStack = array();
		$aDirectoryStack[] = $sBaseDir;
		
		$iCounter = 0;
		
		$oQuery =& new pxQuery;
		$oQuery->bFull = $bImportMetaData;

		while ($sCurrentDir = array_pop($aDirectoryStack))
		{			
			$oQuery->sDirectory = $sCurrentDir;

			$aFilesystemResult = $this->ls($oQuery);

			for ($i = 0, $m = count($aFilesystemResult); $i < $m; $i++)
			{
				$iCounter++;

				$oObject = &$aFilesystemResult[$i];

				if ($oObject->sName == '.objects' or strpos($oObject->sRelDir, '/.objects') !== false) {
					continue;
				}

				$bDirectory = $oObject->bDirectory;
				$sFullPath = pxUtil::buildPath(
					$sCurrentDir,
					$oObject->sName
				);

				if ($bDirectory || !$this->aPossibleActions['md5']) {
					$sMd5 = null;
					$aFilesystemContent[$sFullPath] = '';
				} else {
					$sMd5 = md5_file($sFullPath);
					$aFilesystemContent[$sFullPath] = $sMd5;
				}

				$bIndexProperties = false;

				// *** INSERT ***
				if (!isset($aIndexRows[$sFullPath]))
				{
					echo '+ ' . $sFullPath . chr(13);
					$oObject->dCreated = $this->filectime($sFullPath);
					if (method_exists($oObject, 'loadFileMetaData')) {
						$oObject->loadFileMetaData();
					}
					
					$this->_aStatements['pxp:insert']->execute(
						array(
							':share' => $this->oShare->sRealId,
							':directory' => $oObject->sRelDir,
							':name' => $oObject->sName,
							':is_file' => (int)!$oObject->bDirectory,
							':title' => $oObject->sTitle,
							':filesize' => $oObject->bDirectory ? null : $oObject->iBytes,
							':typ' => $oObject->sType,
							':extension' => $oObject->sExtension,
							':filectime' => $oObject->dCreated,
							':filemtime' => $oObject->dModified,
							':owner' => $pxp->sUser,
							#':link_directory' => null,
							#':link_name' => null,
							':os_permissions' => $oObject->sOSystemPermissions,
							':os_owner' => $oObject->sOSystemOwner,
							':os_group' => $oObject->sOSystemGroup,
							':serialized' => serialize($oObject),
							#':content' => null,
							':md5' => $sMd5,
							':active' => $oObject->bActive,
							':visible' => $oObject->bVisible,
							':position' => $oObject->fPosition,
							':tagsOnly' => isset($oObject->bTagsOnly) ? (int)$oObject->bTagsOnly : 0
						)
					);
					
					$oObject->iDatabaseRowId = $this->oPdo->lastInsertId();

					$bIndexProperties = true;
				}
				else
				{
					$bUpdate = false;
					$bTypeCheck = true;
					
					$aRow =& $aIndexRows[$sFullPath];

					// Check for intermediately changed filetypes
					if ($bTypeCheck) {
						$sNewType = null;
						$sNewExtension = null;
						$pxp->getTypeKeyByExtension(
							$oObject->sName,
							$oObject->bDirectory,
							$sNewType,
							$sNewExtension
						);
						if ($sNewType != $aRow['typ'] || $sNewExtension != $aRow['extension']) {
							$bUpdate = true;
						}
					}

					// Check for modified directories and files
					if ($bDirectory || !$this->aPossibleActions['md5']) {
						if ($aRow['filemtime'] != $this->filemtime($sFullPath)) {
							$bUpdate = true;
						}
					} else {
						if ($aRow['md5'] != $aFilesystemContent[$sFullPath]) {
							$bUpdate = true;
						}
					}

					if ($bUpdate || $bRebuild)
					{
						if (isset($aRow['serialized']))
						{							
							if (!$bImportMetaData) {
								$pxp->loadType($oObject->sType);
							}

							$oNewObject = unserialize($aRow['serialized']);

							$aMembers = (array)$oObject;
							foreach ($aMembers as $mKey => $mValue) {
								if (empty($oNewObject->{$mKey})) {
									$oNewObject->{$mKey} = $mValue;
								}
							}
							$oObject = &$oNewObject;
						}
					}

					$oObject->iDatabaseRowId = $aRow['id'];
					
					if (isset($sNewType)) {
						#$oObject->sType = $sNewType;
						#$oObject->sExtension = $sNewExtension;
					}

					if ($bUpdate)
					{
						// *** UPDATE ***
						echo '~ ' . $sFullPath . chr(13);
						if (method_exists($oObject, 'loadFileMetaData')) {
							$oObject->loadFileMetaData();
						}

						$this->_aStatements['pxp:updateSerialized']->execute(
							array(
								':share' => $this->oShare->sId,
								':directory' => $oObject->sRelDir,
								':name' => $oObject->sName,
								':is_file' => (int)!$oObject->bDirectory,
								':title' => $oObject->sTitle,
								':typ' => $oObject->sType,
								':extension' => $oObject->sExtension,
								':filesize' => $oObject->bDirectory ? null : $oObject->iBytes,
								':filemtime' => $oObject->dModified,
								':owner' => $pxp->sUser,
								#':link_directory' => null,
								#':link_name' => null,
								':os_permissions' => $oObject->sOSystemPermissions,
								':os_owner' => $oObject->sOSystemOwner,
								':os_group' => $oObject->sOSystemGroup,
								#':content' => null,
								':md5' =>$sMd5,
								':serialized' => serialize($oObject)
							)
						);
						$bIndexProperties = true;
					}
				}

				$oObject->_bMetaDataExists = true;

				if ($bIndexProperties || $bRebuild) {
					if (method_exists($oObject, 'call')) {
						$oObject->call(
							'_editIndex',
							array(
								'sTriggeringAction' => 'editIndex',
								'bRebuild' => $bRebuild
							)
						);
					}
				}

				// Add directories to stack
				if ($bDirectory && !file_exists($sFullPath . '/nocache')) {
					$aDirectoryStack[] =  $sFullPath;
				}

				unset($oObject);
				unset($oNewObject);
			}
			unset($aFilesystemResult);
		}

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	 *	Traverse index / DELETE / RENAME
	 *~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

		$oStmtDelete = $this->oPdo->prepare(
			'DELETE FROM `' . $this->sIndexTable . '` WHERE id = :id'
		);

		foreach ($aIndexRows as $sPath => $aRow) {
			// *** DELETE ***
			if (!isset($aFilesystemContent[$sPath])) {
				echo '- ' . $sPath . chr(13);
				$oStmtDelete->execute(array(':id' => $aRow['id']));
				$this->clearKeywords($aRow['id']);
			}
		}

		if ($this->bCacheSql) {
			$this->flushSqlCache();
		}

		echo 'Records: ' . (count($aIndexResult) - 1) . "\n";
		echo 'Files: ' . $iCounter . "\n";
		echo 'finished in ', (pxUtil::getMicrotime() - $iStartTime), ' seconds' . "\n";
		
		$this->_optimizeIndex();
		
		$this->_bIndexing = false;
	}
	
	/**
	 * 
	 */
	function _optimizeIndex() {
		if ($this->sDriver == 'mysql') {
			echo 'Optimize: ' . $this->sIndexTable . "\n";
			$this->oPdo->query('OPTIMIZE TABLE ' . $this->sIndexTable);
			echo 'Optimize: ' . $this->sIndexPropertyTable . "\n";
			$this->oPdo->query('OPTIMIZE TABLE ' . $this->sIndexPropertyTable);
		}
	}

	/**
	 * 
	 */
	function clearKeywords($iRowId) {
		if (!isset($this->_aStatements['pxp:clearKeywords'])) {
			$this->_aStatements['pxp:clearKeywords'] = $this->oPdo->prepare(
				'DELETE FROM `' . $this->sIndexPropertyTable . '` WHERE id = ?'
			);
		}

		$this->_aStatements['pxp:clearKeywords']->execute(
			array(
				$iRowId
			)
		);		
	}

	/**
	 * 
	 */
	function insert_keyword($iRowId, $sProperty, $sKeyword) {
		if ($this->bCacheSql) {
			$this->_sCachedSqlInserts .= ' (' . $iRowId . ', \'' . addslashes($sProperty) . '\', \'' . addslashes(substr($sKeyword, 0, 255)) . '\'),';
			if (strlen($this->_sCachedSqlInserts) > 800000) {
				$this->flushSqlCache();
			}
		} else {
			if (!isset($this->_aStatements['pxp:insertKeyword'])) {
				$this->_aStatements['pxp:insertKeyword'] = $this->oPdo->prepare(
					'INSERT INTO `' . $this->sIndexPropertyTable . '` (id, property, keyword) VALUES (?, ?, ?)'
				);
			}
			$this->_aStatements['pxp:insertKeyword']->execute(
				array(
					$iRowId,
					$sProperty,
					substr($sKeyword, 0, 255)
				)
			);
		}
	}

	function flushSqlCache() {
		if ($this->bCacheSql && !empty($this->_sCachedSqlInserts)) {
			#echo $this->_sCachedSqlInserts;
			$this->oPdo->exec(
				'INSERT INTO `' . $this->sIndexPropertyTable . '` (id, property, keyword) VALUES' .
				substr($this->_sCachedSqlInserts, 0, strlen($this->_sCachedSqlInserts) -1)
			);
			$this->_sCachedSqlInserts = '';
		}
	}

	/**
	 * Returns array with index information
	 */
	function getIndexInfo()
	{
		$aInfo = array();

		$this->_connectIndexDB();

		$oStmt = $this->oPdo->prepare(
			'SELECT count(share) FROM `' . $this->sIndexTable . '`' .
			' WHERE share = ? AND filesize IS NULL'
		);

		$oStmt->execute(array($this->oShare->sId));
		$aRows = $oStmt->fetchAll(3);
		$aInfo['directories'] = $aRows[0][0];

		$oStmt = $this->oPdo->prepare(
			'SELECT count(share) FROM `' . $this->sIndexTable . '`' .
			' WHERE share = ? AND filesize IS NOT NULL'
		);
		$oStmt->execute(array($this->oShare->sId));
		$aRows = $oStmt->fetchAll(3);
		$aInfo['files'] = $aRows[0][0];

		$aInfo['sum'] = $aInfo['directories'] + $aInfo['files'];

		return $aInfo;
	}

	/**
	 * Attempts to connect to the index database 
	 * 
	 * @access private
	 * @return boolean True or false depending on success
	 */
	function _connectIndexDB()
	{
		if (isset($this->oPdo) or $this->_bIndexConnected === true) {
			return true;
		}

		$this->oPdo =& new PDO(
			$this->sIndexDSN,
			$this->sIndexUser,
			$this->sIndexPassword
		);

		$this->sDriver = substr($this->sIndexDSN, 0, strpos($this->sIndexDSN, ':'));

		$this->_bIndexConnected = isset($this->oPdo);

		return isset($this->oPdo);
	}

	/**
	 * 
	 */
	function _prepareInsertStatement()
	{
		global $pxp;

		if (!isset($this->_aStatements['pxp:insert'])) {

			$sQuery = <<<EOD
				INSERT INTO `$this->sIndexTable`
				(
					share,
					directory,
					name,
					is_file,
					title,
					filesize,
					typ,
					extension,
					filectime,
					filemtime,
					owner,
					#link_directory,
					#link_name,
					os_permissions,
					os_owner,
					os_group,
					serialized,
					#content,
					md5,
					active,
					visible,
					position,
					tagsOnly
				) VALUES (
					:share,
					:directory,
					:name,
					:is_file,
					:title,
					:filesize,
					:typ,
					:extension,
					:filectime,
					:filemtime,
					:owner,
					#: link_directory,
					#: link_name,
					:os_permissions,
					:os_owner,
					:os_group,
					:serialized,
					#: content,
					:md5,
					:active,
					:visible,
					:position,
					:tagsOnly
				)
EOD;

			$this->_aStatements['pxp:insert'] = $this->oPdo->prepare($sQuery);
		}
	}

	/**
	 * 
	 */
	function _prepareUpdateStatement($bSerialized = false)
	{
		global $pxp;

		if ($bSerialized) {
			$sStmtId = 'pxp:updateSerialized';
			$sSerializedSql = ',serialized = :serialized';
		} else {
			$sStmtId = 'pxp:update';
			$sSerializedSql = '';
		}

		if (!isset($this->_aStatements[$sStmtId])) {

			$sQuery = <<<EOD
				UPDATE `$this->sIndexTable`
				SET
					is_file = :is_file,
					title = :title,
					typ = :typ,
					extension = :extension,
					filesize = :filesize,
					filemtime = :filemtime,
					owner = :owner,
					#link_directory = : link_directory,
					#link_name = : link_name,
					os_permissions = :os_permissions,
					os_owner = :os_owner,
					os_group = :os_group,
					#content = : content,
					md5 = :md5
					$sSerializedSql
				WHERE
					share = :share AND
					directory = :directory AND
					name = :name
EOD;

			$this->_aStatements[$sStmtId] = $this->oPdo->prepare($sQuery);
		}
	}
	
	/**
	 * 
	 */
	function _writeLog($sFunction, $sText) {
		global $pxp;
		fwrite($this->_iLogHandle, $pxp->sCallId . ' ' . $pxp->sUser . ' ' . $pxp->sAction . ' ' . $sFunction . ' ' . $sText . "\r\n");
	}

	/**
	 * 
	 */
	function _executeStatement($sStatementId, $sPath)
	{
		$sRelPath = $this->oShare->getRealRelativePath($sPath);
		
		$this->_aStatements[$sStatementId]->execute(
			array(
				$this->oShare->sRealId,
				pxUtil::dirname($sRelPath),
				basename($sRelPath)
			)
		);
	}
	
	/**
	 * 
	 */
	function _getInfo($sPath, $sInfo)
	{
		if ($this->bLog) $this->_writeLog($sInfo, $sPath);

		if ($this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:' . $sInfo])) {
				$this->_aStatements['pxp:' . $sInfo] = $this->oPdo->prepare(
					'SELECT ' . $sInfo . ' FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ? AND name = ?'
				);
			}
			$this->_executeStatement('pxp:' . $sInfo, $sPath);
			$aRows = $this->_aStatements['pxp:' . $sInfo]->fetchAll(3);
			
			if (isset($aRows[0])) {
				return $aRows[0][0];
			} else {
				return false;
			}
		}
		return null;
	}
	
	/**
	 * 
	 */
	function _delete($sPath, $sAction, $bDelete)
	{		
		if ($this->bLog) $this->_writeLog($sAction, (int)$bDelete . ' ' . $sPath);

		if ($bDelete && $this->bIndexed && !$this->_bIndexing) {
			if (!isset($this->_aStatements['pxp:' . $sAction])) {
				$this->_aStatements['pxp:' . $sAction] = $this->oPdo->prepare(
					'DELETE FROM `' . $this->sIndexTable . '`' .
					' WHERE share = ? AND directory = ? AND name = ?'
				);
			}
			$this->_executeStatement('pxp:' . $sAction, $sPath);
		}
		else
		{
			// Remove locally stored serialized object file 
			if ($bDelete) {
				$sObjectPath = $this->_getObjectPath($sPath);
				if ($this->is_file($sObjectPath)) {
					$this->unlink($sObjectPath);
				}
			}
		}
	}
	
	/**
	 * 
	 */
	function makeMetaDataDirectory($sDir)
	{
		if (!$this->is_dir($sDir . '/.phpXplorer/.objects')) {
			$this->mkdir($sDir . '/.phpXplorer/.objects');
		}
		if (!$this->file_exists($sDir . '/.phpXplorer/.htaccess')) {
			$this->file_put_contents(
				$sDir . '/.phpXplorer/.htaccess',
				'Order allow,deny' . "\n" . 'Deny from all'
			);
		}
	}

	/**
	 * 
	 */
	function _getObjectPath($sPath) {
		return dirname($sPath) . '/.phpXplorer/.objects/' . basename($sPath) . '.serializedPhp';
	}
	
	/**
	 * 
	 */
	function strtolower($sString) {
		$sLanguage = setlocale(LC_CTYPE, 0);
		setlocale(LC_CTYPE, 'en');
		$sString = strtolower($sString);
		setlocale(LC_CTYPE, $sLanguage);
		return $sString;
	}
	
	/**
	 * 
	 */
	function sortBy_sName($aArray1, $aArray2) {
		if ($aArray1['bDirectory'] == $aArray2['bDirectory']) {
			if ($aArray1['sName'] == $aArray2['sName']) {
				return 0;
			} else {
				return strnatcmp($aArray1['sName'], $aArray2['sName']);
			}
		} else {
			return $aArray1['bDirectory'] ? -1 : 1;
		}
 	}
 
 	function sortBy_iBytes($aArray1, $aArray2) {
		if ($aArray1['bDirectory'] == $aArray2['bDirectory']) {
			if ($aArray1['iBytes'] == $aArray2['iBytes']) {
				return strnatcmp($aArray1['sName'], $aArray2['sName']);
			} else {
				return $aArray1['iBytes'] < $aArray2['iBytes'] ? -1 : 1;
			}
		} else {
			return $aArray1['bDirectory'] ? -1 : 1;
		}
 	}

 	function sortBy_sType($aArray1, $aArray2) {
		if ($aArray1['bDirectory'] == $aArray2['bDirectory']) {
			if ($aArray1['sType'] . $aArray1['sExtension'] == $aArray2['sType'] . $aArray2['sExtension']) {
				return strnatcmp($aArray1['sName'], $aArray2['sName']);
			} else {
				return $aArray1['sType'] . $aArray1['sExtension'] < $aArray2['sType'] . $aArray2['sExtension'] ? -1 : 1;
			}
		} else {
			return $aArray1['bDirectory'] ? -1 : 1;
		}
 	}
 	
 	function sortBy_dModified($aArray1, $aArray2) {
		if ($aArray1['bDirectory'] == $aArray2['bDirectory']) {
			if ($aArray1['dModified'] == $aArray2['dModified']) {
				return strnatcmp($aArray1['sName'], $aArray2['sName']);
			} else {
				return $aArray1['dModified'] < $aArray2['dModified'] ? -1 : 1;
			}
		} else {
			return $aArray1['bDirectory'] ? -1 : 1;
		}
 	}
	var $sType = 'pxVfs';}

?>