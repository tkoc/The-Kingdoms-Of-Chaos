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

// Define token constants for PHP 4 compatibility
if (!defined('T_ML_COMMENT')) {
	define ('T_ML_COMMENT', T_COMMENT);
} else {
	define('T_DOC_COMMENT', T_ML_COMMENT);
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

/**
 * Include class, variable and function data type classes
 */
require_once dirname(__FILE__) . '/pxClassParserClass.php';
require_once dirname(__FILE__) . '/pxClassParserFunction.php';
require_once dirname(__FILE__) . '/pxClassParserVariable.php';

/**
 * Parses a PHP file and fills $this->aClasses with class metadata.
 */
class pxClassParser
{
	/**
	 * Array of pxClassParserClass objects with class meta data and comment tags
	 * 
	 * @var array
	 */
	var $aClasses = array();
	var $bParseFunctions = false;

	/**
	 * Array of tokens returned by tokenizer
	 * 
	 * @access private
	 * @var array
	 */
	var $_aTokens = array();

	/**
	 * Last class name while passing token array
	 * 
	 * @access private
	 * @var array
	 */
	var $_sCurrentClass;

	/**
	 * Parse content of $sFile and fill $aClasses with class information.
	 *
	 * Pass through token array of the given file and collect information about
	 * classes, methods and variables.
	 *
	 * @param string $sFile Path to PHP file
	 */
	function parse($sFile) {
		global $pxp;
		if ($pxp->oVfs->file_exists($pxp->sCacheDir . '/classes/' . basename($sFile))) {
			if ($pxp->oVfs->filemtime($sFile) < $pxp->oVfs->filemtime($pxp->sCacheDir . '/classes/' . basename($sFile))) {
				$this->aClasses = unserialize(
					$pxp->oVfs->file_get_contents(
						$pxp->sCacheDir . '/classes/' . basename($sFile)
					)
				);
				return true;
			}
		}

		$this->aClasses = array();		

		$this->_aTokens = token_get_all(file_get_contents($sFile));
		
		$this->bFirstFunction = false;

    foreach ($this->_aTokens as $iIndex => $aToken) {

    	if (is_string($aToken)) {

    		// simple 1-character token

    	} else {
			
				// Token array

				// Skip whitespace

				if ($aToken[0] == T_WHITESPACE) {
					continue;
				}

    		switch ($aToken[0])
    		{
    			case T_EXTENDS:
    				
    				$sSuperClassName = $sClassName = $this->_getNextTokenValue(T_STRING, $iIndex);
    				
    				if ($sSuperClassName !== false) {
    					$this->aClasses[$this->_sCurrentClass]->sSuperClass = $sSuperClassName;
    				}
    			break;
					
					case T_CLASS:
					
						$sClassName = $this->_getNextTokenValue(T_STRING, $iIndex);

						$sComment = $this->_getPreviousTokenValue(array(T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT), $iIndex, array(T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_CLASS, T_FUNCTION));
						
						if ($sClassName !== false) {
							$this->aClasses[$sClassName] =& new pxClassParserClass($sClassName, $sComment);
							$this->_sCurrentClass = $sClassName;
						}
						
					break;

					case T_VAR:
					case T_PUBLIC:
					case T_PROTECTED:
					case T_PRIVATE:
					
						if ($this->bFirstFunction) {
							continue;
						}

						$sVariableName = $this->_getNextTokenValue(T_VARIABLE, $iIndex);
						
						$sComment = $this->_getPreviousTokenValue(array(T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT), $iIndex, array(T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_CLASS, T_FUNCTION));
						
						$sValue = $this->_getMemberValue($iIndex);

						if ($sVariableName !== false) {
							$this->aClasses[$this->_sCurrentClass]->addVariable($aToken[0], $sVariableName, $sValue, $sComment);
						}
					break;

    			case T_FUNCTION:
    			
    				if (!$this->bParseFunctions) {
    					continue;
    				}

						$sFunctionName = $this->_getNextTokenValue(T_STRING, $iIndex);

						$sComment = $this->_getPreviousTokenValue(array(T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT), $iIndex, array(T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_CLASS, T_FUNCTION));

						if ($sFunctionName !== false) {
							$this->aClasses[$this->_sCurrentClass]->addFunction($aToken[0], $sFunctionName, $sComment);
						}
						$this->bFirstFunction = true;
    			break;
    		}
    	}
    }

    $pxp->oVfs->file_put_contents($pxp->sCacheDir . '/classes/' . basename($sFile), serialize($this->aClasses));
	}
	
	/**
	 * Tries to extract a name and a list of parameters out of strings
	 * with the following syntax: 'valueName(param1=123, param2=abc)'.
	 * Returns the name and an assoc array with parameter names and values.
	 * 
	 * @param string $sTagValue Tag value to parse
	 * @return array Assoc array with 'sName' and 'aParameters' as keys 
	 */
	function parseTagValue($sTagValue)
	{
		$sName = '';
		$aParameters = array();	

		$iParenthesisStart = strpos($sTagValue, '(');

		if ($iParenthesisStart !== false) {

			$sName = trim(substr($sTagValue, 0, $iParenthesisStart));

			$iParenthesisStop = strpos($sTagValue, ')');

			if ($iParenthesisStop !== false) {
				$sParameters = substr(
					$sTagValue,
					$iParenthesisStart + 1,
					$iParenthesisStop - $iParenthesisStart - 1
				);
			} else {
				$sParameters = substr(
					$sTagValue,
					$iParenthesisStart + 1,
					strlen($sTagValue) - $iParenthesisStart - 1);
			}

			$aParts = explode(',', $sParameters);

			for ($p = 0, $m = count($aParts); $p < $m; $p++) {

				$iEqualCharPos = strpos($aParts[$p], '=');

				if ($iEqualCharPos !== false) {

					$mValue = trim(substr($aParts[$p], $iEqualCharPos + 1));

					if (strtolower($mValue) == 'true') {
						$mValue = true;
					}

					if (strtolower($mValue) == 'false') {
						$mValue = false;
					}

					if (is_numeric($mValue)) {
						$mValue = (integer)$mValue;
					}

					$aParameters[trim(substr($aParts[$p], 0, $iEqualCharPos))] =
						$mValue;
				} else {
					$aParameters[] = trim($aParts[$p]);
				}
			}
		} else {
			$sName = $sTagValue;
		}

		return array
		(		
			'sName' => $sName,
			'aParameters' => $aParameters
		);
	}
	
	/**
	 * Function for static use which parses phpXplorer tags out of PHP comment blocks.
	 *
	 * @access private
	 * 
	 * @param string $sComment Comment block text
	 * 
	 * @return array Assoc array with tag names as keys and arrays with all occured key values as values
	 */
	function _splitComment($sComment)
	{
		$aTags = array();

		if (isset($sComment)) {

			$sComment = str_replace(chr(13) . chr(10), chr(13), $sComment);
			$sComment = str_replace(chr(10), chr(13), $sComment);

			$aLines = explode(chr(13), $sComment);

			for ($i = 0, $max = count($aLines); $i < $max; $i++) {

				$sLine = trim($aLines[$i]);

				if (substr($sLine, 0, 1) == '*') {

					$sLine = trim(substr($sLine, 1));

					if (substr($sLine, 0, 1) == '@') {

						// Remove leading @ char
						$sLine = substr($sLine, 1);

						if (strpos($sLine, ' ') === false) {
							$sTagName = $sLine;
							$aTags[$sLine] = '';
						} else {
							$sTagName = substr($sLine, 0, strpos($sLine, ' '));
							$aTags[$sTagName] = trim(substr($sLine, strpos($sLine, ' ') + 1));
						}
					} else {
						if (isset($sTagName) && !empty($sTagName)){
							$sLine = trim($sLine);
							if (!empty($sLine) && $sLine != '/') {
								$aTags[$sTagName] .= ' ' . $sLine;
							}
						}
					}
				}
			}
			return $aTags;
		}
	}

	/**
	 * Returns the value of the next given token type. 
	 *
	 * Pass through token array until there is a token type out of $mTokenTypes array.
	 * Returns null if there are no more tokens or a token out of $mBreakTokenTypes.
	 *
	 * @access private
	 * 
	 * @param mixed $mTokenTypes Single value or array with token types to look up
	 * @param integer $iStart Token array start position
	 * @param mixed $mBreakTokenTypes Single value or array with token types that should stop the pass through
	 * 
	 * @return mixed Token value or null
	 */
	function _getNextTokenValue($mTokenTypes, $iStart, $mBreakTokenTypes = array())
	{
		if (!is_array($mTokenTypes)) {
			$mTokenTypes = array($mTokenTypes);
		}

		if (!is_array($mBreakTokenTypes)) {
			$mBreakTokenTypes = array($mBreakTokenTypes);
		}

		$iEnd = count($this->_aTokens);
		$iPos = $iStart;

		while ($iPos < $iEnd) {

			if (in_array($this->_aTokens[$iPos][0], $mTokenTypes)) {
				return $this->_aTokens[$iPos][1];
			}
			
			if ($iPos != $iStart and in_array($this->_aTokens[$iPos][0], $mBreakTokenTypes)) {
				return null;
			}

			$iPos++;
		}
					
		return null;
	}

	/**
	 * Returns the value of the previous given token type.  
	 *
	 * Pass through token array until there is a token type out of $mTokenTypes array.
	 * Returns null if there are no more tokens or a token out of $mBreakTokenTypes.
	 *
	 * @access private
	 * 
	 * @param mixed $mTokenTypes Single value or array with token types to look up
	 * @param integer $iStart Token array start position
	 * @param mixed $mBreakTokenTypes Single value or array with token types that should stop the pass through
	 * 
	 * @return mixed Token value or null
	 */
	function _getPreviousTokenValue($mTokenTypes, $iStart, $mBreakTokenTypes = array())
	{
		if (!is_array($mTokenTypes)) {
			$mTokenTypes = array($mTokenTypes);
		}
		
		if (!is_array($mBreakTokenTypes)) {
			$mBreakTokenTypes = array($mBreakTokenTypes);
		}
	
		$iEnd = 0;
		$iPos = $iStart;

		while ($iPos > $iEnd) {

			if (in_array($this->_aTokens[$iPos][0], $mTokenTypes)) {
				return $this->_aTokens[$iPos][1];
			}
			
			if ($iPos != $iStart and in_array($this->_aTokens[$iPos][0], $mBreakTokenTypes)) {
				return null;
			}

			$iPos--;
		}
					
		return null;
	}

	/**
	 * 
	 */
	function _getMemberValue($iMemberTokenIndex)
	{
		$iIndex = $iMemberTokenIndex;
		$sValue = '';

		$bStart = false;

		while (
			isset($this->_aTokens[$iIndex])
			and
			$this->_aTokens[$iIndex] != ';')
		{
			if ($bStart) {
				if (isset($this->_aTokens[$iIndex][1])) {
					$sValue .= $this->_aTokens[$iIndex][1];
				} else {
					$sValue .= $this->_aTokens[$iIndex];
				}
			}
			if ($this->_aTokens[$iIndex] == '=') {
				$bStart = true;
			}
			$iIndex++;
		}

		if (trim($sValue) != '') {
			$sValue = 'null';
		}
#echo $this->_sCurrentClass . '::' . $sValue ."<br/><br/>";
		return eval('return ' . $sValue . ';');
	}
}

?>