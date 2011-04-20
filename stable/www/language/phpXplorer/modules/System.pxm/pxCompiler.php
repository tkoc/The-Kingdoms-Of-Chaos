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

require_once dirname(__FILE__) . '/pxClassParser.php';
require_once dirname(__FILE__) . '/pxMixinCompiler.php';
require_once dirname(__FILE__) . '/pxJson.php';

class pxCompiler
{
	/**
	 * @access private
	 * @var array
	 */
	var $_aModules = array();

	/**
	 * @access private
	 * @var object
	 */
	var $_oParser;

	/**
	 * @access private
	 * @var string
	 */
	var $_sSerializedTypes;

	/**
	 * @access private
	 * @var string
	 */
	var $_sSerializedActions;

	/**
	 * @access private
	 * @var string
	 */
	var $_sSerializedRoles;

	/**
	 * @access private
	 * @var string
	 */
	var $_sSerializedExtension;
	var $_oJson;
	var $_aLanguages = array();



	/**
	 * 
	 */
	function pxCompiler() {
		$this->_oJson = new pxJson();
		if (function_exists('token_get_all')) {
			$this->_oParser =& new pxClassParser;
		}
		$this->_aModules = $this->getModules();
	}
	


		
	var $_aTypeFiles = array();
	var $_aTypeInfo = array();
	


	function _loadTypes($bFillTypeArray = true)
	{
		global $pxp;

		if (!empty($this->_aTypeFiles)) {
			return;
		}

		if ($bFillTypeArray) {
			unset($pxp->aTypes);
			$pxp->aTypes = array();
		}

		foreach ($this->_aModules as $sModuleId => $sModuleName)
		{
			foreach ($this->_getEntities('types', $sModuleId) as $sTypeName => $sModuleId)
			{
				$sId = substr($sTypeName, 0, strpos($sTypeName, '.'));
				
				$this->_aTypeFiles[$sId][] =
					$pxp->sModuleDir . '/' . $sModuleName . '/types/' . $sTypeName;

				if (!isset($pxp->aTypes[$sId]) && $bFillTypeArray) {
					$oType =& new pxType($sId);
					$oType->sModule = $sModuleId;
					$pxp->aTypes[$sId] = $oType;
				}
			}
		}
	}
	


	function compileType($sType, $bIndipendent = false)
	{
		global $pxp;

		$this->_loadTypes(!$bIndipendent);

		if (!isset($this->_aTypeInfo[$sType]))
		{
			foreach ($this->_aTypeFiles[$sType] as $iIndex => $sTypePath)
			{
				$this->_oParser->parse($sTypePath);
				$this->_aTypeInfo[$sType][] = $this->_oParser->aClasses[$sType];
				
				$oInfo =& $this->_aTypeInfo[$sType][$iIndex];
				
				$sMixins = trim($oInfo->getTagValue('mixin'));
				if (!empty($sMixins))
				{
					$aMixins = explode(' ', $sMixins);

					foreach ($aMixins as $sMixin)
					{
						$iPos = strpos($sMixin, '.');
						if ($iPos !== false) {
							$sModule = substr($sMixin, 0, $iPos);
							$sMixinType = substr($sMixin, $iPos + 1);
						} else {
							$sModule = 'System';
							$sMixinType = $sMixin;
						}
						
						$this->compileType($sMixinType);

						$sMixinPath = $pxp->sCacheDir . '/types/' . $sMixinType . '.pxType.php';

						$this->_aTypeFiles[$sType][] = $sMixinPath;
					}
				}
			}
		}

		$pxp->oVfs->file_put_contents(
			pxUtil::buildPath($pxp->sCacheDir, 'types/' . $sType . '.pxType.php'),
			pxMixinCompiler::compile($this->_aTypeFiles[$sType], $sType)
		);
	}



	/**
	 * 
	 */
	function compileTypes($bIndividually = true)
	{
		global $pxp;

		if (!isset($this->_oParser)) {
			if (!$pxp->oVfs->file_exists($pxp->sCacheDir . '/_types.php')) {
				$pxp->raiseError('tokenizerNotFound', __FILE__, __LINE__);
			} else {
				$this->_sSerializedTypes = $pxp->oVfs->file_get_contents($pxp->sCacheDir . '/_types.php');
				return true;
			}
		}

		$this->_loadTypes();

		foreach ($this->_aTypeFiles as $sType => $aFiles)
		{
			$this->compileType($sType);

			foreach ($aFiles as $iIndex => $sFile)
			{
				$oInfo =& $this->_aTypeInfo[$sType][$iIndex];

				$oType =& $pxp->aTypes[$sType];

				$oType->bCreate = $oInfo->tagExists('edit');
				$oType->bAbstract = $oInfo->tagExists('abstract');
				$oType->sSupertype = $oInfo->sSuperClass;

				$sTypes = trim($oInfo->getTagValue('extensions'));
				$sTypes = preg_replace('/\s\s+/', ' ', $sTypes);
				$sTypes = str_replace(' =>', '=>', $sTypes);
				$sTypes = str_replace('=> ', '=>', $sTypes);

				$oType->aExtensions = array();
				$oType->aMimeTypes = array();
	
				if (!empty($sTypes)) {
					$aTypes = explode(' ', $sTypes);
					foreach ($aTypes as $sTypeAssignment) {
						$sTypeAssignment = trim($sTypeAssignment);
						if (strpos($sTypeAssignment, '=>') !== false) {
							$oType->aExtensions[] = substr($sTypeAssignment, 0, strpos($sTypeAssignment, '=>'));
							$oType->aMimeTypes[] = substr($sTypeAssignment, strpos($sTypeAssignment, '=>') + 2);
						} else {
							if (!empty($sTypeAssignment)) {
								$oType->aExtensions[] = $sTypeAssignment;
								$oType->aMimeTypes[] = null;
							}
						}
					}
				}

				$sContenttypes = trim($oInfo->getTagValue('contains'));
				$oType->aContenttypes = !empty($sContenttypes) ? explode(' ', $sContenttypes) : array();

				$sContainertypes = trim($oInfo->getTagValue('belongsTo'));
				$oType->aContainertypes = !empty($sContainertypes) ? explode(' ', $sContainertypes) : array();

				#$oType->bDirectory = !empty($oType->aContenttypes);

				$sDefaultActions = $oInfo->getTagValue('defaultActions');
				if (trim($sDefaultActions) != '') {
					$oType->aDefaultActions = explode(' ', $sDefaultActions);
				}
				
				$sExpandSubtypes = $oInfo->getTagValue('expandSubtypes');
				if (trim($sExpandSubtypes) != '') {
					$oType->aExpandSubtypes = explode(' ', $sExpandSubtypes);
				}		
			}
		}

		unset($oType);

		// Fill aSubtypes arrays for passing type hierarchy in left order
		foreach ($pxp->aTypes as $sType => $oType) {
			$pxp->aTypes[$sType]->fillSupertypes();
			$pxp->aTypes[$sType]->fillPossibleActions();
			foreach ($pxp->aTypes as $sType2 => $oType2) {
				if ($oType2->sSupertype == $sType) {
					$pxp->aTypes[$sType]->aSubtypes[] = $sType2;
				}
			}

			$pxp->aTypes[$sType]->bDirectory =
				in_array('pxDirectories', $pxp->aTypes[$sType]->aSupertypes) ||
				$oType->sId == 'pxDirectories';
		}

		foreach ($pxp->aTypes as $sType => $oType) {
			$pxp->aTypes[$sType]->fillAllSubtypes();
		}

		// Sort type array in left tree order

		$aNewTypes = array();
		$aSearchStack = array('pxObject', 'pxGlobal');

		foreach ($pxp->aTypes as $sType => $oType) {
			if ($oType->sSupertype == null) {
				$aSearchStack[] = $sType;
			}
		}

		$aSearchStack = array_unique($aSearchStack);

		#$aSearchStack = array('pxObject', 'pxGlobal', 'pxMixin');

		while($sSearchType = array_pop($aSearchStack)) {
			$aNewTypes[$sSearchType] =& $pxp->aTypes[$sSearchType];
			foreach ($pxp->aTypes[$sSearchType]->aSubtypes as $sType) {
				array_push($aSearchStack, $sType);
			}
		}
		$pxp->aTypes =& $aNewTypes;

		//Transmit belongsTo and defaultActions information down the type hierarchy
		
		foreach ($pxp->aTypes as $sType => $oType) {
			if ($oType->sSupertype != null) {
				if (empty($oType->aContainertypes)){
					$pxp->aTypes[$sType]->aContainertypes = $pxp->aTypes[$oType->sSupertype]->aContainertypes;
				}
				if (empty($oType->aDefaultActions)) {
					$pxp->aTypes[$sType]->aDefaultActions = $pxp->aTypes[$oType->sSupertype]->aDefaultActions;
				}
				#if (empty($oType->aExpandSubtypes)) {
					$pxp->aTypes[$sType]->aExpandSubtypes = array_merge(
						$pxp->aTypes[$sType]->aExpandSubtypes,
						$pxp->aTypes[$oType->sSupertype]->aExpandSubtypes
					);
				#}
			}
		}

		// Fill extension to type array
		foreach ($pxp->aTypes as $sKey => $oType) {
			#if (!$oType->bAbstract) {
				foreach ($oType->aExtensions as $sExtension) {
					$pxp->_aExtensionToType[$sExtension] = $sKey;
				}
			#}
		}

		// Serialize type array to PHP and JavaScript

		$aPropertyIds = array();

		$aPropertyIds['pxType'] = array('sId', 'aProperties');
		$aPropertyIds['pxType'] = array_merge($pxp->aTypes['pxType']->getVariables(), $aPropertyIds['pxType']);

		$aPropertyIds['pxProperty'] = array('sId');
		$aPropertyIds['pxProperty'] = array_merge($pxp->aTypes['pxProperty']->getVariables(), $aPropertyIds['pxProperty']);

		$sPhp = '<?php ';
		foreach ($pxp->aTypes as $oType) {
			$sPhp .=
				$oType->serializeToPhp(
					array_diff($aPropertyIds['pxType'], array('aSubtypes'))
				) .
				'$this->aTypes[\'' . $oType->sId . '\'] =& $o;';
		}
		$sPhp .= '$this->_aExtensionToType = ' . pxUtil::getArrayString($pxp->_aExtensionToType) . ';';
		$sPhp .= '?>';

		$this->_sSerializedTypes =& $sPhp;

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/_types.php',
			$sPhp
		);

		// Build JavaScript type file

		$sJs = 'oTypes = new Array();';

		foreach ($pxp->aTypes as $sTypeId => $oTypeCopy)
		{
			$oType =& $pxp->aTypes[$sTypeId];

			$oType->getProperties();

			if (empty($oType->aExpandSubtypes)) {
				unset($oType->aExpandSubtypes);
			}

			// Unset unnecessary and private class members

			foreach ($oType->aProperties as $sId => $oProperty)
			{
				$aPropertyMembers = (array)$oProperty;
				foreach ($aPropertyMembers as $sPropertyMember => $sPropertyMemberValue) {
					if (
						!in_array($sPropertyMember, $aPropertyIds['pxProperty']) ||
						substr($sPropertyMember, 0, 1) == '_'
					) {
						unset($oType->aProperties[$sId]->{$sPropertyMember});
					}
				}
				unset($oType->aProperties[$sId]->sValidation);
				unset($oType->aProperties[$sId]->aValidationParameters);
				unset($oType->aProperties[$sId]->sValue);
				if (empty($oType->aProperties[$sId]->sFormat)) {
					unset($oType->aProperties[$sId]->sFormat);
					unset($oType->aProperties[$sId]->aFormatParameters);
				}
				if (empty($oType->aProperties[$sId]->sPermission)) {
					unset($oType->aProperties[$sId]->sPermission);
				}
				if (empty($oType->aProperties[$sId]->sStore)) {
					unset($oType->aProperties[$sId]->sStore);
				}
			}

			$aTypeMembers = (array)$pxp->aTypes[$oType->sId];		
			foreach ($aTypeMembers as $sTypeMember => $sTypeMemberValue) {
				if (
					!in_array($sTypeMember, $aPropertyIds['pxType']) ||
					substr($sTypeMember, 0, 1) == '_'
				) {
					unset($pxp->aTypes[$oType->sId]->{$sTypeMember});
				}
			}
		}

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/types.js',
			'pxp.oTypes = ' . $this->_oJson->encode($pxp->aTypes)
		);

		if ($bIndividually) {
			$this->compileParts();
		}
	}



	/**
	 * 
	 */
	function compileActions($bIndividually = true)
	{
		global $pxp;

		unset($pxp->aActions);
		$pxp->aActions = array();
		
		$aAction = array_merge(
			$this->_getEntities('actions'),
			$this->_getEntities('frontend/px/action')
		);

		foreach ($aAction as $sActionName => $sModuleId)
		{
			$sAction = substr($sActionName, 0, strpos($sActionName, '.'));
			$sExtension = substr($sActionName, strrpos($sActionName, '.') + 1);

			// Skip abstract classes
			if (strpos($sAction, '___') !== false) {
				continue;
			}

			if (!isset($pxp->aActions[$sAction]) || !$pxp->aActions[$sAction][1]) {
				$pxp->aActions[$sAction] = array(
					$sModuleId,
					$sExtension == 'php',
					pxUtil::getBaseAction($sAction)
				);				
			}

			#$pxp->aActions[$sAction] = $sModuleId;
		}

		$sPhp =
			'<?php ' .
				'$this->aActions = ' . pxUtil::getArrayString($pxp->aActions) . ';' .
			'?>';

		$this->_sSerializedActions =& $sPhp;

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/_actions.php',
			$sPhp
		);

		$sJs = 'pxp.oActions = ' . $this->_oJson->encode($pxp->aActions);

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/actions.js',
			$sJs
		);

		if ($bIndividually) {
			$this->compileParts();
		}
	}


	
	/**
	 * 
	 */
	function compileRoles($bIndividually = true)
	{
		global $pxp;

		$pxp->aRoles = array();

		foreach ($this->_getEntities('roles') as $sRoleName => $sModuleId) {
			if (strpos($sRoleName, '.pxRole') !== false) {
				$sRoleId = substr($sRoleName, 0, strpos($sRoleName, '.'));

				$oRole = unserialize(
					$pxp->oShare->oVfs->file_get_contents(
						$pxp->sModuleDir . '/' . $sModuleId . '.pxm' .
						'/roles/.phpXplorer/.objects/' . $sRoleName . '.serializedPhp'
					)
				);

				if (isset($pxp->aRoles[$sRoleId]))
				{
					$oNewRole =& $pxp->aRoles[$sRoleId];

					$oNewRole->aPermissions = array_merge($oNewRole->aPermissions, $oRole->aPermissions);
					$oNewRole->aEvents = array_merge($oNewRole->aEvents, $oRole->aEvents);
					$oNewRole->aParameters = array_merge($oNewRole->aParameters, $oRole->aParameters);

				} else {
					$pxp->aRoles[$sRoleId] = $oRole;
				}
			}
		}

		// Fill sub roles
		foreach ($pxp->aRoles as $sId => $oRole) {
			$aStack = array($sId);
			while ($sCurrentId = array_pop($aStack)) {
				foreach ($pxp->aRoles as $sId2 => $oRole2) {
					if ($oRole2->sParentRole == $sCurrentId) {
						$pxp->aRoles[$sId]->aSubRoles[] = $sId2;
						$aStack[] = $sId2;
					}
				}
			}
		}

		$sPhp =
			'<?php ' .
				'$this->aRoles = unserialize(\'' .
					str_replace(
						chr(39),
						chr(92) . chr(39),
						serialize($pxp->aRoles)
					) .
				'\');' .
			'?>';

		$this->_sSerializedRoles =& $sPhp;

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/_roles.php',
			$sPhp
		);

		if ($bIndividually) {
			$this->compileParts();
		}
	}
	

	
	/**
	 * 
	 */
	function compileFacets()
	{
		global $pxp;

		$pxp->aFacets = array();

		foreach ($this->_getEntities('facets') as $sFacetName => $sModuleId) {
			if (strpos($sFacetName, '.pxFacet') !== false) {
				$sFacetId = substr($sFacetName, 0, strpos($sFacetName, '.'));
				$pxp->aFacets[$sFacetId] = $sModuleId;
			}
		}

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/facets.php',
			'<?php $pxp->aFacets = unserialize(\'' . serialize($pxp->aFacets) . '\'); ?>'
		);
	}
	

	
	function compileDocumentation()
	{
		global $pxp;

		$sJs = 'pxp.oDocs = ' . $this->_oJson->encode(
			$this->_getDocs()
		);

		$pxp->oVfs->file_put_contents(
			$pxp->sCacheDir . '/documentation.js',
			$sJs
		);
	}
	

	
	function extendDocumentation()
	{
		global $pxp;

		foreach ($this->_aModules as $sModuleId => $sModuleName)
		{
			$aLanguages = array();
			$oQuery =& new pxQuery;

			$oQuery->sDirectory = $pxp->sModuleDir . '/' . $sModuleName . '/documentation';
			$oQuery->bPermissionCheck = false;

			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$sLanguage = str_replace('.php', '', $oObject->sName);
				if (strlen($sLanguage) == 2) {
					$aLanguages[] = $sLanguage;
				}
			}

			foreach ($aLanguages as $sLanguage)
			{
				foreach ($pxp->aTypes as $sType => $oType)
				{
					if ($oType->sModule != $sModuleId) {
						continue;
					}

					$pxp->loadTranslation();

					$sDescription = null;
					$aProperties = $pxp->aTypes[$sType]->getProperties();
					$aNewProperties = array();

					foreach ($aProperties as $sId => $oProperty) {
						$aNewProperties[$sId] = null;
					}

					$sNewXml =
						'<?xml version="1.0" encoding="' . $pxp->aConfig['sEncoding'] . '"?>' . "\r\n" .
						'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\r\n" .
						'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $sLanguage . '" lang="' . $sLanguage . '">' . "\r\n" .
						'<head><title id="' . $sType . '">' . $pxp->aTranslation['type.' . $sType] . '</title></head>'  . "\r\n" .
						'<body>' . "\r\n" .
						'<p>' . "\r\n"
						#'<type title="' . $pxp->aTranslation['type.' . $sType] . '" id="' . $sType . '">'
					;

					$sDir = $pxp->sModuleDir . '/' . $sModuleName . '/documentation/';

					$aSources = array(
						'current' => $sLanguage . '/types/' . $sType . '.html',
						$pxp->aConfig['sSystemLanguage'] => $pxp->aConfig['sSystemLanguage'] . '/types/' . $sType . '.html',
						'de' => 'de/types/' . $sType . '.html'
					);

					foreach ($aSources as $sKey => $sPath)
					{
						$sXml = $pxp->oVfs->file_get_contents($sDir . $sPath);
						if (!empty($sXml))
						{
							$oXml = new SimpleXMLElement($sXml);
	
							if (!isset($sDescription) && isset($oXml->body->p)) {
								$s = utf8_decode($oXml->body->p->asXml());
								$s = substr($s, 3, -4);
								if (trim($s) != '') {
									$s = str_replace(chr(10), chr(13) . chr(10), $s);
									$s = str_replace(chr(9), '  ', $s);
									$s = trim($s);
									if ($sKey != 'current') {
										$sDescription = "\r\n" . '  <!--' . $s . '-->' . "\r\n  ";
									} else {
										$sDescription = $s;
									}
								}
							}

							if (!isset($oXml->body->ol)) {
								continue;
							}

							foreach ($oXml->body->ol->li as $oPropertyNode) {
								$oAttributes = $oPropertyNode->attributes();
								$sId = (string)$oAttributes->id;
								if (!isset($aNewProperties[$sId]) && !empty($oPropertyNode) && !isset($oPropertyNode->comment)) {
									$s = utf8_decode($oPropertyNode->asXML());
									$iPos = strpos($s, '>'); 
									$s = substr($s, $iPos + 1, -5);
									if (trim($s) != '') {
										$s = str_replace(chr(10), chr(13) . chr(10), $s);
										$s = str_replace(chr(9), '  ', $s);
										if ($sKey != 'current') {
											$aNewProperties[$sId] = "\r\n" . '    <!--' . $s . '-->' . "\r\n    ";
										} else {
											$aNewProperties[$sId] = $s;
										}
									}
								}
							}
						}
					}

					$sNewXml .= $sDescription . "\r\n</p>\r\n";

					if (count($aNewProperties) > 0)
					{
						$sNewXml .=  '<ol title="' . $pxp->aTranslation['properties'] . '">' . "\r\n";

						foreach ($aNewProperties as $sId => $sXml)
						{
							$sTitle = '';
							if (isset($pxp->aTranslation['property.' . $sId])) { 
								$sTitle = 'title="' . $pxp->aTranslation['property.' . $sId] . '" ';
							}

							$sNewXml .= '<li ' . $sTitle . 'id="' . $sId . '">';

							$s = trim($aNewProperties[$sId]);
							$sNewXml .= $s . "\r\n";
						
							$sNewXml .= '</li>' . "\r\n";
						}
						$sNewXml .= '</ol>' . "\r\n";
					}
					
					$sNewXml .= '</body></html>' . "\r\n";
	
					#$aNewProperties[$sId] = '';

					$pxp->oVfs->file_put_contents(
						$sDir . $sLanguage . '/types/' . $sType . '.html',
						$sNewXml
					);
				}
			}
		}
	}
	


	function extendTranslations()
	{
		global $pxp;

		if (isset($GLOBALS['pxTransCompCheck'])) {
			echo '<b><i>...skipped due to prior call to compileTranslations. You will have to call it again</i></b><br/>';
			return false;
		}

		$aLanguages = $this->_getLanguages();

		
		$aKeywords = array();
		$sSystemLanguage = $pxp->aConfig['sSystemLanguage'];

		foreach ($this->_aModules as $sModuleId => $sModuleName)
		{
			$aKeywords[$sModuleId] = array();

			$this->aLanguages = array($sSystemLanguage => array());
			$sPhp = $pxp->oVfs->file_get_contents($pxp->sModuleDir . '/' . $sModuleName . '/translations/' . $sSystemLanguage . '.php');
			$sPhp = str_replace('<?php', '', $sPhp);
			$sPhp = str_replace('?>', '', $sPhp);
			eval($sPhp);


			// Remove keywords with automatic namespace
			foreach ($this->aLanguages[$sSystemLanguage] as $sKey => $sValue) {
				$sNamespace = substr($sKey, 0, strpos($sKey, '.'));				
				if (!in_array($sNamespace, array('action', 'type', 'property', 'facet'))) {
					$aKeywords[$sModuleId][] = $sKey;
				}
			}
			
			
			// Add action keywords 
			$aAction = array_merge(
				$this->_getEntities('actions', $sModuleId),
				$this->_getEntities('frontend/px/action', $sModuleId)
			);
			foreach ($aAction as $sActionName => $sModuleId) {
				$sActionId = substr($sActionName, 0, strpos($sActionName, '.'));
				$aKeywords[$sModuleId][] = 'action.' . $sActionId;
			}
			
			
			// Add keyword for each type extension combination
			foreach ($this->_getEntities('types', $sModuleId) as $sTypeName => $sModuleId)
			{
				$sTypeId = substr($sTypeName, 0, strpos($sTypeName, '.'));
				$oType = &$pxp->aTypes[$sTypeId];

				if (count($oType->aExtensions) > 0) {
					foreach ($oType->aExtensions as $sExtension) {
						if ($sExtension != $sTypeId) {
							$aKeywords[$sModuleId][] = 'type.' . $sTypeId . '_' . $sExtension;	
						} else {
							$aKeywords[$sModuleId][] = 'type.' . $sTypeId;
						}
					}
				}

				if (!in_array('type.' . $sTypeId, $aKeywords[$sModuleId])) {
					$aKeywords[$sModuleId][] = 'type.' . $sTypeId;
				}

				$aProperties = $oType->getProperties();

				foreach ($aProperties as $oProperty) {
					$aKeywords[$sModuleId][] = 'property.' . $oProperty->sId;
				}				
			}

			// Add facet keywords 
			foreach ($this->_getEntities('facets') as $sFacetName => $sModuleId) {
				if (strpos($sFacetName, '.pxFacet') !== false) {
					$sFacetId = substr($sFacetName, 0, strpos($sFacetName, '.'));
					$aKeywords[$sModuleId][] = 'facet.' . $sFacetId;
				}
			}

			// Add keyword for each documentation item
			/*
			$aDocs = $this->_getDocs();
			foreach ($aDocs['instructions'] as $sInstruction => $sInstructionModule) {
				if ($sModuleId == $sInstructionModule) {
					$aKeywords[] = 'doc.' . $sInstruction;
				}
			}
			*/

			$aKeywords[$sModuleId] = array_unique($aKeywords[$sModuleId]);
			sort($aKeywords[$sModuleId]);
			if (!isset($aKeywords[$sModuleId][''])) {
				array_unshift($aKeywords[$sModuleId], '');
			}
		}


		foreach ($this->_aModules as $sModuleId => $sModuleName)
		{
			if (!$pxp->oVfs->is_dir($pxp->sModuleDir . '/' . $sModuleName . '/translations')) {
				$pxp->oVfs->mkdir($pxp->sModuleDir . '/' . $sModuleName . '/translations');
			}

			foreach ($aLanguages as $sLanguage)
			{
				$pxp->loadLanguage($sLanguage, $sModuleName);

    		$sOutput = '';

				foreach ($aKeywords[$sModuleId] as $sKey)
				{
					if (!empty($pxp->aLanguages[$sLanguage][$sKey])) {
						$sOutput .= "  '$sKey' => '" . $pxp->aLanguages[$sLanguage][$sKey] . "',\n";
   				}
   				else
   				{
   					$sHelp = '';
   					if ($sLanguage != $pxp->aConfig['sSystemLanguage']) {
	   					if (isset($pxp->aLanguages[$pxp->aConfig['sSystemLanguage']][$sKey])) {
   							$sHelp = '';
   							$sValue = $pxp->aLanguages[$pxp->aConfig['sSystemLanguage']][$sKey];
   							if (!empty($sValue)) {
	   							$sHelp = ' // ' . $sValue;
   							}
   						}
   					}
   					$sOutput = "  '$sKey' => '',$sHelp\n" . $sOutput;
   				}
   			}

    		$sOutput = substr($sOutput, 0, strlen($sOutput) - 2);

    		$sOutput =
					"<?php\n\n" .
						'$this->aLanguages[\'' . $sLanguage . '\'] = array_merge($this->aLanguages[\'' . $sLanguage . '\'], array(' .
						"\n" .
						$sOutput .
						"\n));" .
					"\n\n?>";

			   $pxp->oVfs->file_put_contents(
    			$pxp->sModuleDir . '/' . $sModuleName . '/translations/' . $sLanguage . '.php',
    			$sOutput
    		);
			}
		}
	}
	


	function compileTranslations()
	{
		global $pxp;

		if (isset($GLOBALS['pxTransCompCheck'])) {
			echo '<b><i>...skipped due to additional call to compileTranslations</i></b><br/>';
			return false;
		}
		$GLOBALS['pxTransCompCheck'] = true;

		$aLanguages = $this->_getLanguages();
		$this->_loadAllLanguages();

		// Build one big PHP/JavaScript language file per language.
		foreach ($aLanguages as $sLanguage)
		{
			$sPhp = '<?php $this->aTranslation=array(';

			foreach ($pxp->aLanguages[$sLanguage] as $sKey => $sValue) {
				if (empty($sValue)) {
					if (isset($pxp->aLanguages[$pxp->aConfig['sSystemLanguage']][$sKey])) {
						$sPhp .= '\'' . $sKey . '\'=>\'' . $pxp->aLanguages[$pxp->aConfig['sSystemLanguage']][$sKey] . '\',';
						// To pass value to JavaScript too
						$pxp->aLanguages[$sLanguage][$sKey] = $pxp->aLanguages[$pxp->aConfig['sSystemLanguage']][$sKey];
					}
				} else {
					$sPhp .= '\'' . $sKey . '\'=>\'' . $sValue . '\',';
				}
			}

			$sPhp = substr($sPhp, 0, strlen($sPhp) -1) . '); ?>';

			$pxp->oVfs->file_put_contents(
				$pxp->sCacheDir . '/translations/' . $sLanguage . '.php',
				$sPhp
			);

			$sJs = 'var oTranslation =' . $this->_oJson->encode($pxp->aLanguages[$sLanguage]); 
			$pxp->oVfs->file_put_contents(
				$pxp->sCacheDir . '/translations/' . $sLanguage . '.js', $sJs
			);
		}
	}
	


	/**
	 * 
	 */
	function checkImages()
	{
		global $pxp;

		foreach ($this->_aModules as $sModuleId => $sModuleName)
		{			
			$aActions = $this->_getEntities('actions', $sModuleId);
			foreach ($aActions as $sActionName => $aInfo) {
				$sActionId = substr($sActionName, 0, strpos($sActionName, '.'));
				if (!$pxp->oVfs->file_exists($pxp->sModuleDir . '/' . $sModuleName . '/graphics/actions/' . $sActionId . '.png')) {
					if (strpos($sActionName, '___') === false) {
						echo '&nbsp;&nbsp;missing ' . $sActionName . '<br/>';
					}
				}
			}

			$oQuery = new pxQuery();
			$oQuery->sDirectory = $pxp->sModuleDir . '/' . $sModuleName . '/graphics/actions';
			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$sImageId = substr($oObject->sName, 0, strpos($oObject->sName, '.'));
				if (!isset($aActions[$sImageId . '.pxAction.php']) && !isset($aActions[$sImageId . '.js'])) {
					#echo '&nbsp;&nbsp;redundant ' . $oObject->sName . '<br/>';
				}
			}

			$aTypes = $this->_getEntities('types', $sModuleId);
			foreach ($aTypes as $sTypeName => $sModuleId) {
				$sTypeId = substr($sTypeName, 0, strpos($sTypeName, '.'));
				$oType = &$pxp->aTypes[$sTypeId];				

				if (!$pxp->oVfs->file_exists($pxp->sModuleDir . '/' . $sModuleName . '/graphics/types/' . $sTypeId . '.png')) {
					echo '&nbsp;&nbsp;missing ' . $sTypeId . '<br/>';
				}

				foreach ($oType->aExtensions as $sExtension) {
					if ($sExtension != $sTypeId) {
						$sExtendedName = $sTypeId . '_' . $sExtension;	
					} else {
						$sExtendedName = $sTypeId;
					}
					if (!$pxp->oVfs->file_exists($pxp->sModuleDir . '/' . $sModuleName . '/graphics/types/' . $sExtendedName . '.png')) {
						echo '&nbsp;&nbsp;missing ' . $sExtendedName . '<br/>';
					}
				}
			}

			$oQuery = new pxQuery();
			$oQuery->sDirectory = $pxp->sModuleDir . '/' . $sModuleName . '/graphics/types';
			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$sImageId = substr($oObject->sName, 0, strpos($oObject->sName, '.'));

				if (strpos($sImageId, '_') !== false) {
					$sType = substr($sImageId, 0, strpos($sImageId, '_'));
				} else {
					$sType = $sImageId;
				}

				if (!isset($aTypes[$sType . '.pxType.php'])) {
					#echo '&nbsp;&nbsp;redundant ' . $oObject->sName . '<br/>';
				}
			}
		}	
	}



	/**
	 * 
	 */
	function compileAll()
	{
		global $pxp;

		$this->compileActions(false);
		$this->compileTypes(false);
		$this->compileRoles(false);
		$this->compileFacets();
		$this->compileDocumentation();
		$this->compileTranslations();

		$sPhp = $this->_getConfiguration();
		$sPhp .=
			$this->_sSerializedActions .
			$this->_sSerializedTypes .
			$this->_sSerializedRoles
		;

		$this->_writePhpCacheFile($sPhp);
	}
	


	function compileParts()
	{
		global $pxp;

		$sPhp = $this->_getConfiguration();

		$sPhp .= $pxp->oVfs->file_get_contents($pxp->sCacheDir . '/_actions.php');
		$sPhp .= $pxp->oVfs->file_get_contents($pxp->sCacheDir . '/_types.php');
		$sPhp .= $pxp->oVfs->file_get_contents($pxp->sCacheDir . '/_roles.php');

		$this->_writePhpCacheFile($sPhp);
	}


	
	function _getConfiguration()
	{
		global $pxp;

		$sPhp = $pxp->oVfs->file_get_contents($pxp->sDir . '/config.php');
		
		$aAllUsersRoles = array();

		$oQuery = new pxQuery();
		$oQuery->sDirectory = $pxp->sDir . '/shares';
		$oQuery->aTypes = array('pxShare');
		$oQuery->bFull = true;
		$oQuery->bPermissionCheck = false;
		
		$aResult = $pxp->oVfs->ls($oQuery);

		foreach ($aResult as $oShare) {
			if (!empty($oShare->aAllUsersRoles)) {
				$aAllUsersRoles[] = $oShare->sId;
			}
		}

		$sPhp .= '$this->aConfig[\'_aAllUsersRolesShares\'] = ' .
			pxUtil::getArrayString($aAllUsersRoles) . ';';

		return $sPhp; 
	}



	function compressCss($sCssIn, $bPhpBased = false)
	{
		global $pxp;
		
		if ($bPhpBased)
		{
			$sCss = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCssIn);
			$sCss = str_replace(
				array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),
				'', $sCss
			);
			$sCss = str_replace(
				array(' {', ', ', ': '),
				array( '{', ',', ':'),
				$sCss
			);
			return $sCss;
		}
		else {
			return pxCompiler::_compress($sCssIn, 'yui', 'css');
		}
	}



	function compressJs($sJsIn, $sMode = 'dojo')
	{
		$sJs = preg_replace('/;;;.+/', '', $sJsIn);

		switch ($sMode) {
			case 'packer':
				require_once dirname(__FILE__) . '/../Development.pxm/includes/JavaScriptPacker/class.JavaScriptPacker.php';
				$oPacker = new JavaScriptPacker($sJs, 'None', true, true);
				return $oPacker->pack();
				break;
			case 'dojo':
				return pxCompiler::_compress($sJs, 'dojo');
			break;
			case 'yui':
				return pxCompiler::_compress($sJs, 'yui');
			break;
			case 'jsmin':
				require_once dirname(__FILE__) . '/includes/jsmin.php';
				return JSMin::minify($sJs);
			break;
			case 'dojo_yui':
				return pxCompiler::_compress(
					pxCompiler::_compress($sJs, 'dojo'),
					'yui'
				);
			break;
		}
	}


	
	function _compress($sCode, $sCompressor = 'dojo', $sType = 'js')
	{
		global $pxp;

		$sCompressorDir = dirname(__FILE__) . '/../Development.pxm/includes';
		$sTempFileIn = $pxp->sCacheDir . '/_tempIn.' . $sType;

		$pxp->oVfs->file_put_contents($sTempFileIn, $sCode);

		switch ($sCompressor) {
			case 'dojo':
				$sTempFileOut = $pxp->sCacheDir . '/_tempOut.' . $sType;
				passthru("java -jar $sCompressorDir/custom_rhino.jar -c $sTempFileIn > $sTempFileOut 2>&1");
				$sCodeOut = $pxp->oVfs->file_get_contents($sTempFileOut);
				unlink($sTempFileOut);
			break;
			case 'yui':
				$aCode = array();
				exec("java -jar $sCompressorDir/yuicompressor.jar $sTempFileIn", $aCode);
				$sCodeOut = implode('', $aCode);
			break;
		}
		
		unlink($sTempFileIn);

		return $sCodeOut;
	}


	/**
	 * Fill & sort module array
	 */
	function getModules($bFullObjects = false)
	{
		global $pxp;

		$aModules = array();

		foreach ($pxp->oVfs->scandir($pxp->sModuleDir) as $sName) {
			if (strpos($sName, '#') === 0 || in_array($sName, array('.', '..', '.phpXplorer'))) {
    		continue;
    	}
		  $sModuleId = substr($sName, 0, strpos($sName, '.'));
    	$aModules[$sModuleId] = $sName;
		}

		$aOrderedModules = array();
		
		foreach ($pxp->aConfig['aModuleOrder'] as $sModuleId) {
			if (isset($aModules[$sModuleId])) {
				$aOrderedModules[$sModuleId] = $aModules[$sModuleId];
			}
		}

		foreach ($aModules as $sModuleId => $sModuleName) {
			if (!isset($aOrderedModules[$sModuleId])) {
				$aOrderedModules[$sModuleId] = $aModules[$sModuleId];
			}
		}

		if ($bFullObjects) {
			$aModuleObjects = array();
			foreach ($aOrderedModules as $sModuleName) {
				#require_once $pxp->sModuleDir . '/System.pxm/types/pxModule.pxType.php';
				$pxp->loadType('pxModule');
				$sData = $pxp->oVfs->file_get_contents(
					$pxp->sModuleDir . '/.phpXplorer/.objects/' . $sModuleName . '.serializedPhp' 
				);
				
				if (!empty($sData)) {
					$oModuleObject = unserialize($sData);
					$oModuleObject->sName = $sModuleName;
					$aModuleObjects[] =& $oModuleObject;
					unset($oModuleObject);
				}
			}
			return $aModuleObjects;
		}
		
		return $aOrderedModules;
	}



	function _writePhpCacheFile($sPhp)
	{
		global $pxp;
		
	  $sPhp = str_replace('<?php', '', $sPhp);
		$sPhp = str_replace('?>', '', $sPhp);
		$sPhp = '<?php ' . $sPhp . ' ?>';

	  $pxp->oVfs->file_put_contents(
	  	$pxp->sCacheDir . '/data.php',
	  	$sPhp
	  );

		if (function_exists('php_strip_whitespace')) {
			$sPhp = php_strip_whitespace($pxp->sCacheDir . '/data.php');
	  	$pxp->oVfs->file_put_contents(
	  		$pxp->sCacheDir . '/data.php',
	  		$sPhp
		  );				
		}
	}
	


	/**
	 * 
	 */
	function &_getEntities($sEntityPath, $sModuleIdIn = null)
	{		
		global $pxp;

		$aEntities = array();

		foreach ($this->_aModules as $sModuleId => $sModuleName) {
			if (isset($sModuleIdIn)) {
				if ($sModuleId != $sModuleIdIn) {
					continue;
				}
			}
			$sDir = $pxp->sModuleDir . '/' . $sModuleName . '/' . $sEntityPath;

			if (!$pxp->oVfs->is_dir($sDir)) {
				continue;
			}

			foreach ($pxp->oVfs->scandir($sDir) as $sName) {
				if ($sName == '.' or $sName == '..' or strpos($sName, '#') === 0) {
					continue;
				}
				$aEntities[$sName] = $sModuleId;
			}
		}
		return $aEntities;
	}
	


	/**
	 * 
	 */
	function _getLanguages() {
		global $pxp;
		if (count($this->_aLanguages) == 0) {
			$oQuery =& new pxQuery;
			$oQuery->sDirectory = $pxp->sModuleDir . '/System.pxm/translations';
			$oQuery->bPermissionCheck = false;
			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$sLanguage = str_replace('.php', '', $oObject->sName);
				if (strlen($sLanguage) == 2) {
					$this->_aLanguages[] = $sLanguage;
				}
			}
		}
		return $this->_aLanguages;
	}
	


	/**
	 * 
	 */
	function _loadAllLanguages() {
		global $pxp;
		$aLanguages = $this->_getLanguages();
		foreach ($this->_aModules as $sModuleId => $sModuleName) {
			foreach ($aLanguages as $sLanguage) {
				$pxp->loadLanguage($sLanguage, $sModuleName);
			}
		}
	}
	


	function _getDocs()
	{
		global $pxp;

		$aInstructions = array();
		$aDevelopment = array();
		
		$oQuery = new pxQuery();
		
		$oQuery->sDirectory = $pxp->sModuleDir;
		$oQuery->aTypes[] = 'pxModule';
		$oQuery->bFull = true;
		$oQuery->bPermissionCheck = false;

		$oModules = $pxp->oVfs->ls($oQuery);

		foreach ($oModules as $oModule) {
			if (!$oModule->bIndipendent && is_array($oModule->aTutorials))
			{
				foreach ($oModule->aTutorials as $sTutorial)
				{
					$iPos = strpos($sTutorial, '/');
					$sCategory = substr($sTutorial, 0, $iPos);
					$sDocument = substr($sTutorial, $iPos + 1);				

					if ($sCategory == 'instructions') {
						$aInstructions[$sDocument] = $oModule->sId;
					} else {
						$aDevelopment[$sDocument] = $oModule->sId;
					}
				}
			}
		}

		return array(
			'instructions' => $aInstructions,
			'development' => $aDevelopment	
		);
	}
}

?>