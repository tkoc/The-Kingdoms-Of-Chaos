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

$iCondId = 0;

class pxQueryCondition
{
	var $oContainer;

	var $iId;
	var $sOperator = 'and';
	var $sSign = null;
	var $sField = null;
	var $sTerm = '';
	var $sSubOperator;
	var $bLike = false;
	var $bGroup = false;
	var $bMustGroup = false;
	var $bSubExists = false;

	var $aSubConditions;

	function pxQueryCondition() {
		global $iCondId;
		$this->iId = $iCondId++;
	}
}

/**
 * 
 */
class pxQuery
{
	var $oVfs;

	var $sShare;
	var $sDirectory; // Directory to list

	var $aAdditionalShares = array();
	var $aAdditionalSharesTag = array();

	var $aIds;
	var $aNames; // Filename selection
	var $aTypes; // Type selection
	var $aTags;

	var $sSearchQuery; // Query string

	var $bFull = false; // Return objects with meta data
	var $bRecursive = false; // Traverse the subtree
	var $bRecursiveFlat = false; // Traverse the subtree but return all objects in one collection

	var $bGetFirst = false; // Return first object or null
	var $bOnlyActive = false;
	var $bOnlyVisible = false;
	var $bOnlyDirectories = false; // Return only directories

	var $iLimit = 0; // Limit the number of object to return
	var $iOffset = 0; // Number of objects to skip

	var $sOrderBy = 'sName'; // Order result by this property
	var $sOrderDirection = 'asc'; // Order direction 'asc' or 'desc'
	var $sOrderFunction; // User defined function to sort objects by individual (non pxObject) class members

	var $bOsPermissions = false;
	var $bFilesize = true;
	var $bPermissionCheck = true;

	var $bSearchMatchCase = false; // Handle search queries case-sensitive
	var $bFullResultCount = false; // Result count before limitation
	var $iFullResultCount = -1; // Record count of the the last result

	var $sPropertyStatistic;

	var $sWhere;

	var $sLastQuery;
	
	var $bSpecificSql = true;

	var $_bExtendDirectory = true; // Automatically extend directories with share base directory
	var $_sJoinType = ' INNER JOIN ';


	var $oConditions;
	var $sDefaultField = '%';

	var $_sJoin = '';
	var $_iJoin = 1;
	var $_aWhere = array();
	
	var $_bLimit = true;
	var $_sDriver = 'mysql';
	
	var $_aColumns = array(
		'sName' => 'name',
		'sDirectory' => 'directory',
		'iBytes' => 'filesize',
		'dModified' => 'filemtime',
		'dCreated' => 'filectime',
		'sType' => 'typ, extension',
		'permissions' => 'os_permissions',
		'owner' => 'os_owner',
		'group' => 'os_group'
	);

	function pxQuery()
	{
		global $pxp;

		if (empty($this->sShare)) {
			$this->sShare = $pxp->sShare;
		}

		$this->oConditions = new pxQueryCondition();
	}

	/**
	 * 
	 */
	function parseQuery()
	{	
		$oContainer = &$this->oConditions;

		$bQuotes = false;
		$bQuotedToken = false;
		$sField = null;
		$sToken = '';
		$sOperator = 'and';
		$sSign = null;
		$iBracketCount = 0;
		
		$bStatGroup = false;

		if (empty($this->sSearchQuery)) return false;

		$sQuery = trim($this->sSearchQuery);
		$iLen = strlen($sQuery);

		for ($i=0, $m=$iLen+1; $i<$m; $i++)
		{
			$sChar = isset($sQuery{$i}) ? $sQuery{$i} : null;

			if (ctype_alnum($sChar) || $sChar == '_' || $sChar == '%') {
				$sToken .= $sChar;
			}
			else if ($sChar == '\\')
			{
				$i++;
				if (isset($sQuery{$i})) {
					$sToken .= $sQuery{$i};
				}
			}
			else if ($sChar == '"' || $sChar == '\'') {
				$bQuotes = !$bQuotes;
				$bQuotedToken = true;
			}
			else if ($bQuotes) {
				$sToken .= $sChar;
			}
			else if ($sChar == '+' || $sChar == '-' || $sChar == '!') {
				$sSign = $sChar;
			}
			else if ($sChar == ':') {
				$sField = $sToken;
				$sToken = '';
			}
			else
			{
				if (strlen($sToken))
				{
					$sLowerToken = strtolower($sToken);

					if (!$bQuotedToken && ($sLowerToken == 'and' || $sLowerToken == 'or')) {
						$sOperator = $sLowerToken;
						$sToken = '';
					}
					else if (!$bQuotedToken && $sLowerToken == 'not') {
						$sSign = '!';
						$sToken = '';
					}
					else
					{
						/*
						if ($sSign == '-' || $sSign == '!') {
							foreach ($oContainer->aSubConditions as $oCondition) {
								if ($oCondition->sField == $sField) {
								}
							}
						}
						*/

						$oCondition =& new pxQueryCondition();
						$oCondition->oContainer = &$oContainer;
						$oCondition->sField = addslashes($sField);
						$oCondition->sTerm = addslashes($sToken);
						$oCondition->sOperator = $sOperator;
						$oCondition->sSign = $sSign;
						$oCondition->bLike = strpos($sToken, '%') !== false;
						
						$oContainer->aSubConditions[] =& $oCondition;

						$bQuotes = false;
						$bQuotedToken = false;
						$sField = null;
						$sToken = '';
						$sOperator = 'and';
						$sSign = null;
					}
				}

				if ($sChar == '(')
				{
					$oNewContainer =& new pxQueryCondition();
					$oNewContainer->oContainer = &$oContainer;
					$oNewContainer->sOperator = $sOperator;
					$oNewContainer->bGroup = true;

					$oContainer->aSubConditions[] =& $oNewContainer;

					$oContainer = &$oNewContainer;

					$sOperator = 'and';

					$iBracketCount++;
				}
				else if ($sChar == ')')
				{
					// Remove unnecessary groups
					if (count($oContainer->aSubConditions) == 1) {
						$oNew =& $oContainer->aSubConditions[0];
						$oContainer =& $oContainer->oContainer;
						$oNew->oContainer =& $oContainer;
						array_pop($oContainer->aSubConditions);
						$oContainer->aSubConditions[] =& $oNew;
					} else {
						$oContainer =& $oContainer->oContainer;
					}

					$iBracketCount--;

					if ($iBracketCount < 0) {
						die("bracket error");
					}
				}
			}
		}

		if ($bQuotes) {
			die("quotes error");
		}

		if ($iBracketCount != 0) {
			die("bracket error");
		}

		unset($oContainer);
		unset($oCondition);

		$aStack = array();
		$aStack[] = &$this->oConditions;

		while(!is_null($oContainer = array_pop($aStack)))
		{
			$iSub = count($oContainer->aSubConditions);

			$sType = null;
			for ($i=0, $m=$iSub; $i<$m; $i++) {
				$oCondition =& $oContainer->aSubConditions[$i];
				if ($i > 0) {
	 				if (isset($sType)) {
		 				if ($sType != $oCondition->sOperator) {
	 						$sType = 'mix';
	 						break;
	 					}
	 				} else {
		 				$sType = $oCondition->sOperator;
	 				}
	 			}
			}

			if (!isset($sType)) $sType = 'and';

			if ($sType == 'mix') {
				$sType = 'or';
				$aNewConditions = array();
				$oNew =& new pxQueryCondition();
				$oNew->oContainer = $oContainer;
				$oNew->sOperator = 'or';
				$aNewConditions[] =& $oNew;
				for ($i=0, $m=$iSub; $i<$m; $i++) {
					$oCondition =& $oContainer->aSubConditions[$i];
					if ($oCondition->sOperator == 'or') {
						$oNew =& new pxQueryCondition();
						$oNew->oContainer = $oContainer;
						$oNew->sOperator = 'or';
						$aNewConditions[] =& $oNew;
					}
					$oCondition->sOperator = 'and';
					$oCondition->oContainer =& $oNew;
					$oNew->aSubConditions[] =& $oCondition;
				}
				$oContainer->aSubConditions =& $aNewConditions;
			}

			$oContainer->sSubOperator = $sType;
			
			
			if (
				$oContainer->bGroup &&
				isset($oContainer->oContainer) &&
				$oContainer->sSubOperator == 'and' &&
				$oContainer->oContainer->sSubOperator == 'and'
			) {
				#echo "reduce";
			}

			$iSub = count($oContainer->aSubConditions);

			if ($sType == 'and') {
				for ($i=0, $m=$iSub; $i<$m; $i++) {
					$oCondition =& $oContainer->aSubConditions[$i];
					if (!$oCondition->bGroup && (empty($oCondition->sField) || $oCondition->bLike)) {
						$oContainer->bMustGroup = true;
						break;
					}
				}
			} else {
				for ($i=0, $m=$iSub; $i<$m; $i++) {
					$oCondition =& $oContainer->aSubConditions[$i];
					if (!empty($oCondition->aSubConditions)) {
						$oContainer->bSubExists = true;
						break;
					}
				}
			}

			for ($i=0, $m=$iSub; $i<$m; $i++) {
				$oCondition =& $oContainer->aSubConditions[$i];
				if (!empty($oCondition->aSubConditions)) {
					$aStack[] =& $oCondition;
				}
			}
		}

		$bMustGroup =
			(
				$this->oConditions->sSubOperator != 'and' ||
				$this->oConditions->bMustGroup
			) && !$this->oConditions->bSubExists
			;

		if (!empty($this->sPropertyStatistic) && $bMustGroup)
		{ 
			$aSub =& $this->oConditions->aSubConditions;
			
			if (count($aSub) > 1 || !$aSub[0]->bGroup)
			{
				$oNew =& new pxQueryCondition();
				$oNew->oContainer =& $this->oConditions;
				$oNew->bMustGroup = $this->oConditions->bMustGroup;
				$oNew->bSubExists = $this->oConditions->bSubExists;
				$oNew->sSubOperator = $this->oConditions->sSubOperator;
				$oNew->bGroup = true;
				for ($i=0, $m=count($aSub); $i<$m; $i++) {
					$aSub[$i]->oContainer =& $oNew;
				}
				$oNew->aSubConditions =& $this->oConditions->aSubConditions;
				unset($this->oConditions->aSubConditions);
				$this->oConditions->aSubConditions[] =& $oNew;
				$this->oConditions->sSubOperator = 'and';
			}
		}
	}

	/**
	 * 
	 */
	function _addTagConditions()
	{
		$bTags = false;
		if (!empty($this->aTags)) {
			foreach ($this->aTags as $sTag) {
				$oCondition =& new pxQueryCondition();
				$oCondition->oContainer = &$this->oConditions;
				$oCondition->sField = 'aTags';
				$oCondition->sTerm = $sTag;
				#$oCondition->sOperator = 'or';
				$this->oConditions->aSubConditions[] =& $oCondition;
				$bTags = true;
			}
		}
		return $bTags;
	}

	/**
	 * 
	 */
	function _reset() {
		unset($this->oConditions);
		$this->oConditions = new pxQueryCondition();
	}
	
	/**
	 * 
	 */
	function getSql()
	{
		global $pxp;

		$this->_reset();
		
		if (isset($this->oVfs)) {
			$this->_bLimit = $this->oVfs->bDbPaging;
			$this->_sDriver = $this->oVfs->sDriver;
		}

		$this->_iJoin = 1;

		$sJoin = '';
		$aWhere = array();
		$sGroup = '';
		$sOrder = '';

		$this->parseQuery();

		$this->_addTagConditions();

		$iPreviousJoin = $this->_getSql($this->oConditions, $sJoin, $aWhere, $sGroup);



		// Additional shares

		if (empty($this->aAdditionalShares) || empty($this->aAdditionalSharesTags))
		{
			$sWhere = 'f1.share = "' . $this->sShare . '"';
		}
		else
		{
			if (!empty($sJoin)) {
				$sJoin .= $this->_sJoinType . 'pxIndex AS i' . $this->_iJoin . ' ON i' . $this->_iJoin . '.id = i' . ($this->_iJoin-1) . '.id';
			} else {
				$sJoin .= 'pxIndex AS i' . $this->_iJoin;
			}

			$sWhere =
				'(' .
					'f1.share = "' . $this->sShare . '"' .
					' OR (' .
						'i' . $this->_iJoin . '.property = "aTags" AND ' .
						'i' . $this->_iJoin . '.keyword IN ("' . implode('","', $this->aAdditionalSharesTags) . '") AND ' .
						'f1.share IN ("' . implode('","', $this->aAdditionalShares) . '")' .
					')' .
				')';

			$this->_iJoin++;
			
			if (empty($sGroup)) {
				$sGroup = 'f1.id';
			}
		}

		$aWhere[] = $sWhere;



		$bDown = $this->bRecursive || $this->bRecursiveFlat; # || !empty($this->sSearchQuery);

		if (isset($this->sDirectory)) {
			if ($bDown) {
				$aWhere[] = 'f1.directory LIKE "' . $this->sDirectory . '%"';	
			} else {
				$aWhere[] = 'f1.directory = "' . $this->sDirectory . '"';
			}
		}


		if ($this->bOnlyActive) $aWhere[] = 'f1.active = 1';
		if ($this->bOnlyVisible) $aWhere[] = 'f1.visible = 1';
		if ($this->bOnlyDirectories) $aWhere[] = 'f1.filesize IS NULL';

		// Id selection
		if (!empty($this->aIds)) {
			$aWhere[] = 'f1.id IN (' . implode(',', $this->aIds) . ')';
		}

		// Exclude root directory
		if (empty($this->aNames)){
			$aWhere[] = 'f1.name != ""';
		} else {
		// Filename selection
			$sRecDirs = '';
			foreach ($this->aNames as $sName) {
				$sDir = pxUtil::buildPath($this->sDirectory, $sName);
				$sRecDirs .= ' OR f1.directory = "' . $sDir . '" OR f1.directory LIKE "' . $sDir . '/%"';
			}
			$aWhere[] =
				'(' .
					'(f1.name IN ("' . implode('","', $this->aNames) . '") AND f1.directory = "' . $this->sDirectory . '")' .
					($bDown ? $sRecDirs : '') .
				')';
		}


		// Type selection
		if (isset($this->aTypes) && !in_array('pxObject', $this->aTypes))
		{
			$aTypes = array();
			foreach ($this->aTypes as $sType) {
				$aTypes[] = $sType;
				$aTypes = array_merge($aTypes, $pxp->aTypes[$sType]->aAllSubtypes);			
			}
			// Filter abstract
			$aTypes2 = array();
			foreach (array_unique($aTypes) as $sType) {
				if (!$pxp->aTypes[$sType]->bAbstract) {
					$aTypes2[] = $sType;
				}
			}
			#$aTypes3 = array();
			#foreach ($pxp->aTypes as $sType => $oType) {
			#	if (!in_array($sType, $aTypes2) && !$pxp->aTypes[$sType]->bAbstract) {
			#		$aTypes3[] = $sType;
			#	}
			#}
			$aWhere[] = 'f1.typ IN ("' . implode('","', $aTypes2) . '")';
		}


		// Add user defined conditions
		if (!empty($this->sWhere)) {
			$aWhere[] = $this->sWhere;
		}
		
		if (isset($this->sPropertyStatistic))
		{
			$i = $this->_iJoin;

			$sQuery = 'SELECT i' . $i . '.keyword, count(i' . $i . '.keyword) AS number';			

			if (empty($sJoin)) {
				$sJoin .= 'pxIndex AS i' . $i;
			} else {
				$sJoin .= $this->_sJoinType . 'pxIndex AS i' . $i . ' ON i' . $i . '.id = i' . ($iPreviousJoin > -1 ? $iPreviousJoin : $i-1) . '.id';
			}

			$aWhere[] = 'i' . $i . '.property = "' . $this->sPropertyStatistic . '"';

			$sGroup = 'i' . $i . '.keyword';

			if ($this->sOrderBy == 'sName') {
				$sOrder = 'i' . $i . '.keyword';
			} else {
				$sOrder = $this->sOrderBy;
			}

			$iPreviousJoin = $i;
			$this->_iJoin++;
		}
		else
		{
			$sQuery =
				'SELECT ' . # SQL_CALC_FOUND_ROWS
					'f1.share, ' .
					'f1.id, ' .
					'f1.directory, ' .
					'f1.name, ' .
					'f1.filesize, ' .
					'f1.typ, ' .
					'f1.extension, ' .
					'f1.filemtime, ' .
					'f1.filectime, ' .
					'f1.title, ' .
					'f1.owner, ' .
					'f1.serialized';

			if ($this->bOsPermissions) {
				$sQuery .=
					', f1.os_permissions' .
					', f1.os_owner' .
					', f1.os_group';
			}
		}

		$sQuery .= ' FROM ';

		if (empty($sJoin)) {
			$sQuery .= 'pxFilesystem AS f1';
		} else {
			$sQuery .= $sJoin;
			$sQuery .= $this->_sJoinType . 'pxFilesystem AS f1 ON f1.id = i' . ($iPreviousJoin > -1 ? $iPreviousJoin : $this->_iJoin-1) . '.id';
		}

		if (!empty($aWhere)) {
			$sQuery .= ' WHERE ' . implode(' AND ', $aWhere);
		}

		if (!empty($sGroup)) {
			$sQuery .= ' GROUP BY ' . $sGroup;
		}

		if (!empty($sOrder)) {
			$sQuery .= ' ORDER BY ' . $sOrder;
		}
		else
		{
			$sQuery .= ' ORDER BY ' . ($this->bRecursive ? 'share, directory,' : '') . 'is_file, position';

			if (!isset($this->sOrderFunction)) {
				if (!empty($this->sOrderBy)) {
					$sQuery .=
						', ' . (isset($this->_aColumns[$this->sOrderBy]) ? $this->_aColumns[$this->sOrderBy] : $this->sOrderBy) .
						' ' . $this->sOrderDirection;
				}
			}
		}

		if (empty($this->sPropertyStatistic))
		{
			$bLimit = $this->iLimit > 0 && $this->_bLimit;

			if ($bLimit) {
				switch ($this->_sDriver) {
					case 'pgsql':
						$sQuery .= ' LIMIT ' . $this->iLimit . ',' . $this->iOffset;
					break;
					default: // mysql, sqlite, sqlite2
						$sQuery .= ' LIMIT ' . $this->iOffset . ',' . $this->iLimit;
					break;
				}
			}
		}

		#$handle = fopen('./sql.log', 'a');
		#fwrite($handle, $sQuery . "\r\n\r\n");
		#fclose($handle);

		#echo $sQuery . "\n\n\n\n";

		$pxp->sLastQuery = $sQuery;

		return $sQuery;
	}

	function _getSql(&$oCondition, &$sJoin, &$aWhere, &$sGroup) 
	{
		$aSub =& $oCondition->aSubConditions;
		
		if (empty($aSub)) {
			return;
		}

		// Skip empty groups
		if (empty($this->sPropertyStatistic) || $oCondition !== $this->oConditions) {
			if (count($aSub) == 1 && !empty($aSub[0]->aSubConditions)) { 
				$oCondition =& $aSub[0];
			}
		}

	 	switch ($oCondition->sSubOperator) {
	 		default:
	 		case 'and':
	 			return $this->_getSqlAnd(
	 				$oCondition->aSubConditions, $sJoin, $aWhere, $sGroup);
	 		break;
	 		case 'or':
	 			return $this->_getSqlOr(
	 				$oCondition->aSubConditions, $sJoin, $aWhere, $sGroup);
	 		break;
	 	}
	}
	
	/**
	 * 
	 */
	function _getSqlAnd(&$aSubConditions, &$sJoin, &$aWhere, &$sGroup)
	{
		$sWhere = '';

		$oContainer =& $aSubConditions[0]->oContainer;

		foreach ($aSubConditions as $iIndex => $oCondition)
		{
			$iJoin = $this->_iJoin;
			$this->_iJoin++;

			if (!empty($oCondition->aSubConditions))
			{
				$sSubJoin = 'SELECT i' . $this->_iJoin . '.id FROM ';
				$aSubWhere = array();
				$sSubGroup = '';

				$this->_getSql(
					$aSubConditions[$iIndex],
					$sSubJoin, $aSubWhere, $sSubGroup
				);

				if ($iIndex > 0) {
					$sJoin .= $this->_sJoinType;
				}

				$sJoin .= '(' . $sSubJoin;
				if (!empty($aSubWhere)) {
					$sJoin .= ' WHERE ' . implode(' AND ', $aSubWhere);
				}
				
				if (!empty($sSubGroup)) $sJoin .= ' GROUP BY ' . $sSubGroup;

				$sJoin .= ') AS i' . $iJoin;

				if ($iIndex > 0) {
					$sJoin .= ' ON i' . $iJoin . '.id = i' . (isset($iPreviousJoin) ? $iPreviousJoin : $iJoin-1) . '.id';
				}

				$iPreviousJoin = $iJoin;
			}
			else
			{
				$sKey = '';
				if (
					$this->bSpecificSql && $oCondition->bLike &&
					$this->_sDriver == 'mysql' && $oCondition->sField != '%' &&
					$oCondition->sTerm{0} != '%'
				) {
					$sKey =  ' FORCE INDEX (keyword)';
				}

				if ($iIndex == 0) {
					$sJoin .= 'pxIndex as i' . $iJoin . $sKey;
				} else {
					$sJoin .=
						$this->_sJoinType . 'pxIndex as i' . $iJoin . $sKey .
						' ON i' . $iJoin . '.id = i' . (isset($iPreviousJoin) ? $iPreviousJoin : $iJoin-1) . '.id';
				}

				if (!empty($sWhere)) $sWhere .= ' AND ';
				$sWhere .= $this->_getWhere($oCondition, $iJoin);
			}
		}

		if (!empty($sWhere)) $aWhere[] = $sWhere;
		if ($oContainer->bMustGroup) $sGroup = 'i' . $iJoin . '.id';

		return isset($iPreviousJoin) ? $iPreviousJoin : -1;
	}

	/**
	 * 
	 */
	function _getSqlOr(&$aSubConditions, &$sJoin, &$aWhere, &$sGroup)
	{
		$oContainer =& $aSubConditions[0]->oContainer;
		
		$iJoin = $this->_iJoin;
		$this->_iJoin++;

		if ($oContainer->bSubExists)
		{
			$sJoin .= '(';

			foreach ($aSubConditions as $iIndex => $oItem)
			{
				if ($iIndex > 0) $sJoin .= ' UNION DISTINCT ';

				$sSubJoin = 'SELECT i' . $this->_iJoin . '.id FROM ';
				$aSubWhere = array();
				$sSubGroup = '';

				$this->_getSql(
					$aSubConditions[$iIndex],
					$sSubJoin, $aSubWhere, $sSubGroup
				);

				$sJoin .= '(' . $sSubJoin;
				if (!empty($aSubWhere)) {
					$sJoin .= ' WHERE ' . implode(' AND ', $aSubWhere);
				}

				if (!empty($sSubGroup)) $sJoin .= ' GROUP BY ' . $sSubGroup;

				$sJoin .= ')';				
			}

			$sJoin .= ') AS i' . $iJoin;
			
			$iPreviousJoin = $iJoin;
		}
		else
		{
			$sWhere = '';

			$sJoin .= 'pxIndex as i' . $iJoin;

			if ($this->bSpecificSql && $this->_sDriver == 'mysql') {
				$sJoin .= ' FORCE INDEX (keyword)';
			}

			$bMultiple = count($aSubConditions) > 1;

			if ($bMultiple) $sWhere .= '(';

			foreach ($aSubConditions as $iIndex => $oCondition) {
				if ($iIndex > 0) $sWhere .= ' OR ';
				$sWhere .= $this->_getWhere($oCondition, $iJoin);
			}

			if ($bMultiple) $sWhere .= ')';

			$sGroup = 'i' . $iJoin . '.id';

			if (!empty($sWhere)) $aWhere[] = $sWhere;
		}

		return isset($iPreviousJoin) ? $iPreviousJoin : -1;
	}
	
	/**
	 * 
	 */
	function _getWhere(&$oCondition, $iJoin)
	{
		$sWhere = '';

		if (!empty($oCondition->sField) && $oCondition->sField != '%') {
			$sWhere .=
				'i' . $iJoin . '.property = "' . $oCondition->sField . '" AND ';
		}

		$sWhere .=
			'i' . $iJoin . '.keyword ' . ($oCondition->bLike ? 'LIKE' : '=') . ' "' . $oCondition->sTerm . '"';

		return $sWhere;
	}
	
	
	
	
	
	/**
	 * 
	 */	
	function checkObject($oObject)
	{
		global $pxp;
		
		$bSum = null;

		if (empty($this->oConditions->aSubConditions))	{
			return true;
		}

		#$this->parseQuery();

		foreach ($this->oConditions->aSubConditions as $oCondition)
		{
			$sField = 'sName';
			if (!empty($oCondition->sField)) {
				$sField = $oCondition->sField;
			}

			$bResult = false;

			if (is_array($oObject->{$sField})) {
				$bResult = in_array($oCondition->sTerm, $oObject->{$sField});
			} else {
				$sFunction = $this->bSearchMatchCase ? 'strpos' : 'stristr'; # stripos
				$bResult = $sFunction($oObject->{$sField}, $oCondition->sTerm) !== false;
			}

			if (!isset($bSum)) {
				$bSum = $bResult;
			} else {
				if ($oCondition->sOperator == 'and') {
					$bSum = $bSum && $bResult;
				} else {
					$bSum = $bSum || $bResult;
				}
			}
		}
			
		#echo $bSum;

		return $bSum;
	}
	
	/**
	 * 
	 */
	function getRegExp()
	{
		global $pxp;

		$this->_reset();

		$sRegExp = null;
		$aRegExp = array();
		$bField = false;

		$this->sSearchQuery = str_replace(array('%', '"'), '', $this->sSearchQuery);

		if (!empty($this->sSearchQuery)) {
			$this->parseQuery();
		}

		if ($this->_addTagConditions()) {
			$this->bFull = true;
		}

		if (!empty($this->oConditions->aSubConditions))
		{
			foreach ($this->oConditions->aSubConditions as $oCondition) {
				if (!empty($oCondition->sField)) {
					$this->bFull = true;
					$bField = true;
					break;
				}
			}

			if (!$bField) {
				foreach ($this->oConditions->aSubConditions as $oCondition) {
					$aRegExp[] = $oCondition->sTerm;
				}
				$sRegExp = '/' . implode('|', $aRegExp) . '/';
				if (!$this->bSearchMatchCase) {
					$sRegExp .= 'i';
				}
			}
		}

		return $sRegExp;
	}
}

?>