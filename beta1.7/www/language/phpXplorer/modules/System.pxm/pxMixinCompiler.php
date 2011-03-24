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

/**
 * Define token constants for PHP 4 compatibility
 */
if (!defined('T_ML_COMMENT')) {
	define ('T_ML_COMMENT', T_COMMENT);
} else {
	if (!defined('T_DOC_COMMENT')) {
		define('T_DOC_COMMENT', T_ML_COMMENT);
	}
}

if (!defined('T_PUBLIC')) {
	define('T_PUBLIC', 341);
}

if (!defined('T_PROTECTED')) {
	define('T_PROTECTED', 342);
}

if (!defined('T_PRIVATE')) {
	define('T_PRIVATE', 343);
}

class pxMixinCompiler
{
	function compile($aClasses, $sClassName)
	{
		$aData = array();

		#$sClassName = null;
		#$sExtends = null;
		#$sData = null;

		$iCounter = 0;
		$bFirst = true;
		
		$aParts = Array('head', 'name', 'extends', 'constructor', '_init', 'body', 'foot');

		foreach ($aClasses as $sClassFile)
		{
			$aClass =& $aData[$iCounter++];
			
			foreach ($aParts as $sPart) {
				$aClass[$sPart] = '';
			}

			$sCurrentPart = 'head';

			$aTokens = token_get_all(file_get_contents($sClassFile));

			$iBracketCount = null;
			$bCapture = true;

			foreach ($aTokens as $iIndex => $mToken)
			{
				if (is_string($mToken))
				{
					if ($mToken == '{') {
						$iBracketCount ++;
						if (!$bCapture) {
							$bCapture = true;
							continue;
						}
						$bCapture = true;
					}

					if ($mToken == '}')
					{
						$iBracketCount --;
						
						if ($iBracketCount == 0) {
							$sCurrentPart = 'foot';
						} else if ($iBracketCount == 1 && $sCurrentPart == '_init') {
							$sCurrentPart = 'foot';
							continue;
						} else if ($iBracketCount == 1 && $sCurrentPart == 'constructor') {
							$sCurrentPart = 'body';
							continue;
						}

						#if ($iBracketCount == 0) {
						#	$sPart = 'foot';
						#}
					}
					
					if ($bCapture) {
						$aClass[$sCurrentPart] .= $mToken;
					}
				}
				else
				{
					switch ($mToken[0])
					{
						case T_CLASS:
							$aClass['name'] = pxMixinCompiler::_getNextTokenValue(
								$aTokens,
								T_STRING,
								$iIndex
							);
							$sCurrentPart = 'body';
							$bCapture = false;
						break;

						case T_EXTENDS:
							$aClass['extends'] = pxMixinCompiler::_getNextTokenValue(
								$aTokens,
								T_STRING,
								$iIndex
							);
						break;

						case T_FUNCTION:

							$sFunctionName = pxMixinCompiler::_getNextTokenValue(
								$aTokens,
								T_STRING,
								$iIndex
							);
							
							if (!$bFirst) {
								if ($sFunctionName == $aClass['name']) {
									$sCurrentPart = 'constructor';
									$bCapture = false;
								}
							}
							
							if ($sFunctionName == '_init') {
								$sCurrentPart = '_init';
								$bCapture = false;
							}

							if ($bCapture) {
								$aClass[$sCurrentPart] .= $mToken[1];
							}							

						break;						

						default:
							if ($bCapture) {
								$aClass[$sCurrentPart] .= $mToken[1];
							}
						break;
					}
				}
			}
			$bFirst = false;
		}

		$aFirst =& $aData[0];

		$sPhp = $aFirst['head'];
		$sPhp .= 'class ' . $aFirst['name'];
		
		if (!empty($aFirst['extends'])) {
			$sPhp .= ' extends ' . $aFirst['extends'];
		}
		$sPhp .= chr(13) . '{';

		$sPhp .= $aFirst['body'];
		
		#echo $sClassName . "<br/>";
		
		if ($sClassName != 'pxObject') {
			$sPhp .= chr(13) . '	var $sType = \'' . $aFirst['name'] . '\';' . chr(13);
		}

		$sInitPhp = '';

		for ($i=1, $m=count($aData); $i<$m; $i++) {
			$sPhp .= $aData[$i]['body'];
			$sInitPhp .= $aData[$i]['constructor'];
			$sInitPhp .= $aData[$i]['_init'];
		}

		if (!empty($sInitPhp)) {
			$sPhp .=
				chr(13) .
				'function _init() {' . chr(13) .
					$sInitPhp . chr(13) .
				'}' . chr(13);
			;
		}

		$sPhp .= $aFirst['foot'];

		$aParts = explode('var $sType = \'', $sPhp);
		$sPhp = '';
		for ($i=0,$m=count($aParts); $i<$m; $i++) {
			$sPart = $aParts[$i];
			if ($i > 0)
			{
				$iPos = strpos($sPart, '\';');
				$sType = substr($sPart, 0, $iPos);

				if ($sType == $sClassName) {
					
					#echo "$sType $sClassName <br/>";
					
					$sPart = 'var $sType = \'' . $sPart;
				} else {
					$sPart = substr($sPart, $iPos + 2);
				}
			}
			$sPhp .= $sPart;
		}

		return $sPhp;
	}

	function _getNextTokenValue($aTokens, $mTokenTypes, $iStart)
	{
		if (!is_array($mTokenTypes)) {
			$mTokenTypes = array($mTokenTypes);
		}

		$iEnd = count($aTokens);
		$iPos = $iStart;

		while ($iPos < $iEnd) {
			if (in_array($aTokens[$iPos][0], $mTokenTypes)) {
				return $aTokens[$iPos][1];
			}
			$iPos++;
		}
					
		return null;
	}
	
	
}

?>