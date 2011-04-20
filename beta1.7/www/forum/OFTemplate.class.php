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

/**********************************************************
* File:		OFTemplate.class.php
* Author:	Øystein Fladby
* Date:		26.11.2003
* Version:	1.0
* Description:
*		This file contains three classes: 	OFTemplate 
*														OFTemplateVariable
*														OFTemplateDynamic
*		The OFTemplateVariable class stores the assigned variables.
*		The OFTemplateDynamics class store the dynamic blocks and 
*		handles the objects of the OFTemplateVariable class.
*		The OFTemplate class is the interface between the php
*		programmer and the OFTemplateDynamics class.
*
*		OBS! A programmer should never call functions in other classes 
*		than OFTemplate.
*		OBS!! You should include the OFTemplate.config.class.php
*		file yourself
***********************************************************
* Changelog:
*	08.12.2003	Øystein Fladby	Added a === in the OFTemplateDynamic->parse
*								function for the returned value from 
*								OFTemplateVariable->parse function
* 10.12.2003	Øystein Fladby	Added a if count $this->variables in the
*								OFTemplateDynamic->getMinValues function
**********************************************************/

if( !class_exists( "OFTemplate" ) ) {

/** require **/
@require_once( "OFTemplate.config.class.php" );

/**
* class OFTemplate
**
* Description:
* 		Interface to the php programmer.
**
**/
class OFTemplate {
	var $userMessage = "";				// error messages
	var $debugMessage = "";				// messages to simplify debugging
	var $lastAlias = "";					// the last alias referenced/ used by the php programmer
	var $templateFiles = array();		// filenames associated with their aliases
	var $preparedFiles = array();		// files ready for variable assignment associated with their aliases		
	var $parsedFiles = array();		// files ready for output associated with their aliases
	
	/**
	* OFTemplate::OFTemplate()
	**
	* Description:
	*		Empty constructor.
	**
	**/
	function OFTemplate(){
		$this->debugMessage .= "\n\n<br><br>OFTemplate: Created";
	}
	
	/**
	* OFTemplate::addTemplateFile( $filename, $alias )
	**
	* Description:
	*		Function to associate a template file with a given alias.
	**
	* $filename: The path to and name of the file.
	* $alias: The alias you want to give this file.
	* $return: True if all ok. False if something wrong.
	**/
	function addTemplateFile( $filename, $alias ) {
		$filename = trim( $filename );
		$alias = trim( $alias );
		if( file_exists( $filename ) ) {
			if( array_key_exists( $alias, $this->templateFiles ) ) {
				$this->userMessage .= 	"\n<br>OFTemplate: The alias '$alias' ($filename) is already used 
												as alias to the file '".$this->templateFiles[$alias]."'.";
				return false;
			} else {
				$this->templateFiles[$alias] = $filename;			
				$this->readTemplateFile( $alias );
				$this->debugMessage .= "\n<br>OFTemplate: Added template file '$filename' with alias '$alias'";
				return true;
			}
		} else {
			$this->userMessage .= 	"\n<br>OFTemplate: The file '$filename' does not exist.";
			return false;
		}
	}
	
	/**
	* OFTemplate::readTemplateFile( $alias )
	**
	* Description:
	*		Function to read a file into a string and then split the 
	*		string in pieces wherever it finds a Tag (@OFTemplateConfig).
	*		OBS! This function should not be called by php programmer as 
	*		it is automatically called in @addTemplateFile( $filename, $alias ).
	**
	* $alias: The alias you want to read.
	* $return: True if all ok. False if something wrong.
	**/
	function readTemplateFile( $alias ) {
		$alias = trim( $alias );
		$this->lastAlias = $alias;
		if( file_exists( $this->templateFiles[$alias] ) ) {
			if( !array_key_exists( $alias, $this->preparedFiles ) ) {
				$fileAsString = implode( "", file( ( $this->templateFiles[$alias] ) ) );	// read file into string variable
				$fileAsString = stripslashes( $fileAsString );
				$pieces = preg_split (	"([".$GLOBALS['OFTemplateConfig']->templateOpeningTag."]+[".$GLOBALS['OFTemplateConfig']->templateClosingTag."]+)",
												$fileAsString, -1, PREG_SPLIT_NO_EMPTY );			// split the templatestring at each start and end tag			
				$this->preparedFiles[$alias] = new OFTemplateDynamic( $alias );
				if( $this->preparedFiles[$alias]->preParse( $pieces ) ) {
					$this->debugMessage .= "\n<br>OFTemplate: '$alias' (".$this->templateFiles[$alias].") is read and prepared.";
				} else {
					return false;
				}
			}else {
				$this->userMessage .= 	"\n<br>OFTemplate: The template '$alias' (".$this->templateFiles[$alias].") 
												is already read.";
				return false;
			}
		} else {
			$this->userMessage .= "\n<br>OFTemplate: The file '".$this->templateFiles[$alias]."' doesn't exist.";
			return false;
		}
		return true;
	}
	
	/**
	* OFTemplate::assign( $value, $variable, $dynamic=false, $alias=false )
	**
	* Description:
	*		Function to assign a value to the given variable in the optionally 
	*		given dynamic block in the optionally given alias. Remember that all 
	* 		dynamic blocks must have a variable inside and that no dynamic blocks 
	*		may have the same name as another. The number of times a dynamic block 
	*		is parsed depends on the minimum number of values among all the 
	*		variables in that block.
	**
	* $alias: The alias where the variable is. If no alias is given, the function 
	*			will try to use the last alias used.
	* $dynamic: The dynamic block where the variable is. If no dynamic block is given,
	*			the variable should not be in a dynamic block.
	* $variable: The variable you want to assign a value to.
	* $value: The value you want to give the variable.
	* $return: True if all ok. False if something wrong.
	**/
	function assign( $value, $variable, $dynamic=false, $alias=false ) {
		$alias = trim( $alias );
		$dynamic = trim( $dynamic );
		$variable = trim( $variable );
		if( !$alias && $this->lastAlias ) {
			$alias = $this->lastAlias;
		} else if( !$alias && !$this->lastAlias ) {
			$this->userMessage .= "\n<br>OFTemplate: You have to enter an alias";
			return false;
		} else if ( !array_key_exists( $alias, $this->preparedFiles ) ) {
			$this->userMessage .= "\n<br>OFTemplate: The alias '$alias' was not found in the prepared array.";
			return false;
		}
		$this->lastAlias = $alias;
		if( $this->preparedFiles[$alias]->assign( $value, $variable, $dynamic ) ) {
			$this->debugMessage .= "\n<br>OFTemplate: Assigned value in alias '$alias'";		
		} else {
			$this->userMessage .= 	"\n<br>OFTemplate: Could not assign variable '$variable' ".
											($dynamic?"in the dynamic block '$dynamic' ":"").
											"in the alias '$alias'.";
			return false;
		}
	}
	
	/**
	* OFTemplate::parse( $alias=false )
	**
	* Description:
	*		Function to parse the alias file with all variables and dynamic 
	*		blocks and store the result as a HTML string.
	**
	* $alias: The alias where the variable is. If no alias is given, the function 
	*			will try to use the last alias used.
	* $return: True if all ok. False if something wrong.
	**/
	function parse( $alias=false ) {
		$alias = trim( $alias );
		if( !$alias && $this->lastAlias ) {
			$alias = $this->lastAlias;
		} else if( !$alias && !$this->lastAlias ) {
			$this->userMessage .= "\n<br>OFTemplate: You have to enter an alias";
			return false;
		} else if ( !array_key_exists( $alias, $this->preparedFiles ) ) {
			$this->userMessage .= "\n<br>OFTemplate: The alias '$alias' was not found in the prepared array.";
			return false;
		} else if( array_key_exists( $alias, $this->parsedFiles ) ) {
			$this->userMessage .= "\n<br>OFTemplate: The alias '$alias' is already parsed.";
			return false;
		}
		$this->lastAlias = $alias;
		if( $this->parsedFiles[$alias] = $this->preparedFiles[$alias]->parse( 1 ) ) {
			$this->debugMessage .= "\n<br>OFTemplate: parsed alias '$alias'";
			return true;		
		} else {
			$this->userMessage .= 	"\n<br>OFTemplate: Could not parse the alias '$alias'.";
			return false;
		}
	}
	
	/**
	* OFTemplate::getFile( $alias=false )
	**
	* Description:
	*		Function to get the finished HTML string.
	**
	* $alias: The alias where the variable is. If no alias is given, the function 
	*			will try to use the last alias used.
	* $return: HTML string if all ok. False if something wrong.
	**/
	function getFile( $alias=false ) {
		$alias = trim( $alias );
		if( !$alias && $this->lastAlias ) {
			$alias = $this->lastAlias;
		} else if( !$alias && !$this->lastAlias ) {
			$this->userMessage .= "\n<br>OFTemplate: You have to enter an alias";
			return false;
		} else if ( !array_key_exists( $alias, $this->parsedFiles ) ) {
			$this->userMessage .= "\n<br>OFTemplate: The alias '$alias' was not found in the parsed array.";
			return false;
		}
		$this->lastAlias = $alias;
		return $this->parsedFiles[$alias];
	}
	
	/**
	* OFTemplate::getUserMessage()
	**
	* Description:
	*		Function to get all error messages generated by template classes
	**
	* $return: HTML string with errors (empty string if no errors)
	**/
	function getUserMessage() {
		foreach( $this->preparedFiles as $pf ) {
			$this->userMessage .= $pf->getUserMessage();
		}
		return $this->userMessage;
	}
	
	/**
	* OFTemplate::getDebugMessage()
	**
	* Description:
	*		Function to get all debug messages generated by template classes
	**
	* $return: HTML string with debug info (empty string if no debug messages)
	**/
	function getDebugMessage() {
		foreach( $this->preparedFiles as $pf ) {
			$this->debugMessage .= $pf->getDebugMessage();
		}
		return $this->debugMessage;
	}
	
}// end class OFTemplate


/***********************************************************************************/

/**
* class OFTemplateDynamic
**
* Description:
* 		This class handles the HTML template itself and all dynamic blocks. It also
*		creates @OFTemplateVariable objects for all variables which lies directly 
*		in this block (not inside another block) and new @OFTemplateDynamic objects 
*		for all dynamic blocks which lies directly in this block.
*		OBS! This class should never be used by the php programmer
**
**/
class OFTemplateDynamic {
	var $userMessage = "";
	var $debugMessage = "";
	var $name;
	var $pieces = array();
	var $variables = array();
	var $dynamics = array();
	
	/**
	* OFTemplateDynamic::OFTemplateDynamic( $name )
	**
	* Description:
	*		Constructor which initialises the name of this dynamic block. If this 
	*		is the first dynamic block, the name is the alias given by the php programmer.
	**
	* $name: The name of this dynamic block.
	**/
	function OFTemplateDynamic( $name ) {
		$this->name = $name;
		$this->debugMessage .= "\n\n<br><br>OFTemplateDynamic (".$this->name."): Created '$name'";
	}	
	
	/**
	* OFTemplateDynamic::preParse( $pieces )
	**
	* Description:
	*		Function to make sure there are only unique names on dynamic blocks and variables.
	*		Creates @OFTemplateVariable objects and @OFTemplateDynamic objects whenever an element 
	*		of the given pieces array contains an identifier (@OFTemplateConfig).
	**
	* $pieces: Array with all HTML for this dynamic block split in pieces for each 
	*			Tag (@OFTemplateConfig)
	* $return: True if all ok. False if something went wrong. 
	**/
	function preParse( $pieces ) {
		$pieces = array_reverse( $pieces );
		$dynamicPieces = array();
		$insideDynamicBlock = false;
		while( $element = array_pop( $pieces ) ) {	// No pieces are empty because of the PREG_SPLIT_NO_EMPTY flag
			if( $insideDynamicBlock ) {
				if( preg_match( "(^".$GLOBALS['OFTemplateConfig']->endDynamicHeader.")", trim( $element ) ) ) {
					$dynamicName = trim( str_replace( $GLOBALS['OFTemplateConfig']->endDynamicHeader, "", trim( $element ) ) );
					if( !strcmp( $dynamicName, $insideDynamicBlock ) ) {
						$this->dynamics[$dynamicName] = new OFTemplateDynamic( $dynamicName );
						$this->dynamics[$dynamicName]->preParse( $dynamicPieces );
						$insideDynamicBlock = false;
						$dynamicPieces = array();
						$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): stored dynamic '$dynamicName'";
					} else {
						array_push( $dynamicPieces, $element );
					}
				} else {
					array_push( $dynamicPieces, $element );
				}
			} else if( preg_match( "(^".$GLOBALS['OFTemplateConfig']->variableHeader.")", trim( $element ) ) ) {
				$variableName = trim( str_replace( $GLOBALS['OFTemplateConfig']->variableHeader, "", trim( $element ) ) );
				if( array_key_exists( $variableName, $this->variables ) ) {
					$this->userMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): variable name 
													'$variableName' is duplicated.";
					return false;
				} else {
					array_push( $this->pieces, $element );	// Just store the piece with variable name
					$this->variables[$variableName] = new OFTemplateVariable( $variableName );
					$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): stored variable '$variableName'";
				}
			} else if( preg_match( "(^".$GLOBALS['OFTemplateConfig']->startDynamicHeader.")", trim( $element ) ) ) {
				$dynamicName = trim( str_replace( $GLOBALS['OFTemplateConfig']->startDynamicHeader, "", trim( $element ) ) );
				if( array_key_exists( $dynamicName, $this->dynamics ) ) {
					$this->userMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): dynamic name 
													'$dynamicName' is duplicated.";
					return false;
				} else {
					$insideDynamicBlock = $dynamicName;
					array_push( $this->pieces, $element );	// Just store the piece with start dynamic name
					$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): inside dynamic '$dynamicName'";
				}
			} else {												// must be HTML
				array_push( $this->pieces, $element );	// Just store the piece
				$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): stored HTML '$element'";
			}
		}
		if( $insideDynamicBlock ) {
			$this->userMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): dynamic start  
											'$insideDynamicBlock' doesn't have an end. Remember to 
											make sure no dynamic blocks have the same name.";
		} else {
			return true;
		}
	}
	
	/**
	* OFTemplateDynamic::assign( $value, $variable, $dynamic=false )
	**
	* Description:
	*		Function to assign a value to the given variable in the optionally 
	*		given dynamic block. 
	**
	* $dynamic: The dynamic block where the variable is. If no dynamic block is given,
	*			the variable should be in this dynamic block.
	* $variable: The variable you want to assign a value to.
	* $value: The value you want to give the variable.
	* $return: True if all ok. False if something wrong.
	**/
	function assign( $value, $variable, $dynamic=false ) {
		if( $dynamic ) {
			if( array_key_exists( $dynamic, $this->dynamics ) ) {
				if( $this->dynamics[$dynamic]->assign( $value, $variable, false ) ) {
					$this->debugMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): Assigned value in 
													dynamic block '$dynamic'.";
					return true;
				} else {
					return false;
				}
			} else {
				foreach( $this->dynamics as $d ) {
					if( $d->assign( $value, $variable, $dynamic ) ) {
						return true;
					}
				}
				return false;
			}		
		} else if( $variable ) {
			if( array_key_exists( $variable, $this->variables ) ) {
				if( $this->variables[$variable]->assign( $value ) ) {
					$this->debugMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): Assigned value in 
													variable '$variable'.";
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			$this->userMessage .= 	"\n<br>OFTemplateDynamic (".$this->name."): You must enter a <b>variable 
											name</b> and optionally a dynamic block name.";
		}
	}
	
	/**
	* OFTemplateDynamic::getminValues()
	**
	* Description:
	*		Function to get the minimum number of values assigned to the variables in 
	*		this dynamic block.
	**
	* $return: Minimum number of values given to the variables in this dynamic block.
	**/
	function getMinValues() {
		$minValues = 1000000000;
		if( count( $this->variables ) ) {
			foreach( $this->variables as $v ) {
				$tempValues = $v->countValues();
				if( $tempValues < $minValues ) {
					$minValues = $tempValues;
				}
			}
		} else {
			$minValues = 1;
		}
		$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): Minimum number of values: '$minValues'";
		return $minValues;
	}
	
	/**
	* OFTemplateDynamic::parse( $parseTimes )
	**
	* Description:
	*		Function to parse all pieces in thic dynamic block and assign values to 
	*		the variables. Recursively parse all dynamic blocks inside this one.
	**
	* $parseTimes: The number of rounds the last parse function want to do.
	* $return: String with the parsed HTML if all ok. False if something wrong.
	**/
	function parse( $parseTimes ) {
		$text = "";
		$parseTimesOriginal = floor( $this->getMinValues() / $parseTimes );
		$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): Original Parsetime '$parseTimesOriginal'";
		$parseTimes = $parseTimesOriginal;
		while( $parseTimes-- ) {
			$pieces = array_reverse( $this->pieces );	
			while( $element = array_pop( $pieces ) ) {
				if( preg_match( "(^".$GLOBALS['OFTemplateConfig']->variableHeader.")", trim( $element ) ) ) {
					$variableName = trim( str_replace( $GLOBALS['OFTemplateConfig']->variableHeader, "", trim( $element ) ) );
					if( array_key_exists( $variableName, $this->variables ) ) {
						$value = $this->variables[$variableName]->parse();
						if( $value === false ) {
							return false;
						}else {
							$text .= $value;
							$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): parsed variable '$variableName' with value '$value'";
						}
					} else {
						return false;
					}
				} else if( preg_match( "(^".$GLOBALS['OFTemplateConfig']->startDynamicHeader.")", trim( $element ) ) ) {
					$dynamicName = trim( str_replace( $GLOBALS['OFTemplateConfig']->startDynamicHeader, "", trim( $element ) ) );
					if( array_key_exists( $dynamicName, $this->dynamics ) ) {
						if( $dynamicBlock = $this->dynamics[$dynamicName]->parse( $parseTimesOriginal ) ) {
							$text .= $dynamicBlock;
							$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): parsed dynamic block '$dynamicName'";
						} else {
							return false;
						}
					} else {
						return false;
					}
				} else {												// must be HTML
					$text .= $element;
					$this->debugMessage .= "\n<br>OFTemplateDynamic (".$this->name."): parsed HTML '$element'";
				}
			} // end while pieces
		} // end while moreValues		
		return $text;
	}
	
	/**
	* OFTemplateDynamic::getUserMessage()
	**
	* Description:
	*		Function to get all error messages generated by template classes
	**
	* $return: HTML string with errors (empty string if no errors)
	**/
	function getUserMessage() {
		foreach( $this->variables as $v ) {
			$this->userMessage .= $v->getUserMessage();
		}
		foreach( $this->dynamics as $d ) {
			$this->userMessage .= $d->getUserMessage();
		}
		return $this->userMessage;
	}
	
	/**
	* OFTemplateDynamic::getDebugMessage()
	**
	* Description:
	*		Function to get all debug messages generated by template classes
	**
	* $return: HTML string with debug info (empty string if no debug info)
	**/
	function getDebugMessage() {
		foreach( $this->variables as $v ) {
			$this->debugMessage .= $v->getDebugMessage();
		}
		foreach( $this->dynamics as $d ) {
			$this->debugMessage .= $d->getDebugMessage();
		}
		return $this->debugMessage;
	}
	
} // end class OFTemplateDynamic





/***********************************************************************************/

/**
* class OFTemplateVariable
**
* Description:
* 		This class handles the variables.
**
**/
class OFTemplateVariable {
	var $userMessage = "";
	var $debugMessage = "";	
	var $name;
	var $value = array();
	var $parsed = false;

	/**
	* OFTemplateVariable::OFTemplateVariable( $name )
	**
	* Description:
	* 		Constructor which initialises the name of this variable.
	**
	* $name: The name of this variable.
	**/ 
	function OFTemplateVariable( $name ) {
		$this->name = $name;
		$this->debugMessage .= "\n\n<br><br>OFTemplateVariable (".$this->name."): Created.";
	}
	
	/**
	* OFTemplateVariable::assign( $value )
	**
	* Description:
	* 		The function stores a new value for this variable.
	**
	* $value: The value to store.
	* $return: True if all ok. False if something went wrong.
	**/
	function assign( $value ) {
		if( !$this->parsed ) {
			array_push( $this->value, $value);
			$this->debugMessage .= "\n<br>OFTemplateVariable (".$this->name."): Stored value '$value'.";
			return true;
		} else {
			$this->userMessage .= 	"\n<br>OFTemplateVariable (".$this->name."): Can not assign value 
											'$value' because variable is already parsed.";
			return false;
		}
	}
	
	/**
	* OFTemplateVariable::parse()
	**
	* Description:
	* 		The function returns the next (unused) value stored for this variable.
	**
	* $return: String with the HTML value stored in this variable. False if something went wrong.
	**/
	function parse() {
		if( $this->value ) {
			if( !$this->parsed ) {
				$this->parsed = true;
				reset( $this->value );
				$value = current( $this->value );
			} else {
				$value = next( $this->value );
			}
			$this->debugMessage .= "\n<br>OFTemplateVariable (".$this->name."): Returned value '$value'.";
			return $value;			
		} else {
			$this->userMessage .= "\n<br>OFTemplateVariable (".$this->name."): No value assigned.";
			return false;
		}
	}
	
	/**
	* OFTemplateVariable::countValues()
	**
	* Description:
	* 		The function counts the number of values stored for this variable.
	**
	* $return: The number of values stored for this variable.
	**/
	function countValues() {
		return count( $this->value );
	}
	
	/**
	* OFTemplateVariable::getUserMessage()
	**
	* Description:
	*		Function to get all error messages generated by template classes
	**
	* $return: HTML string with errors (empty string if no errors)
	**/
	function getUserMessage() {
		return $this->userMessage;
	}
	
	/**
	* OFTemplateVariable::getDebugMessage()
	**
	* Description:
	*		Function to get all debug messages generated by template classes
	**
	* $return: HTML string with debug info (empty string if no debug info)
	**/
	function getDebugMessage() {
		return $this->debugMessage;
	}
	
}// end class OFTemplateVariable

}//end if ! class exists OFTemplate
//EOF
?>