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
FILE:							OFTemplate.class.php
DESCRIPTION:			This file contains a very simple and fast template class to easily seperate 
									PHP code from HTML code.
AUTOR:						Fladby, Øystein (copyright)
DATE: 						20.06.2004
VERSION:					1.0
**/

if( !class_exists("OFTemplate") ) {
/**
CLASS:						OFTemplate
VISIBILITY:				public
DESCRIPTION:			This class is a very fast and easy to use template class. It supports dynamic blocks as
									long as they are not nested. Normal use would be to make an instant of the class, add 
									template files, add variable values to those files and then get the result html in the end.
									If the class encounters any serious errors, it will die, displaying an error message.
**/
class OFTemplate {
	var $templatePath = "";
	var $files = array();
	var $variables = array();
	
	var $tagStart = "{--";
	var $tagEnd = "--}";
	var $tagVariable = "VARIABLE:";
	var $tagDynamicStart = "START_DYNAMIC:";
	var $tagDynamicEnd = "END_DYNAMIC:";
	
	var $text = array( "Error" => array( 	"addTemplateFile" => "OFTemplate: Parameter must be an assosiative array of filenames as strings.<br>
																															All files must be .html, .htm or .css<br>
																															All aliases (array keys) must be combinations of english letters, 
																															digits and/or underscore<br>",
																				"addVariable" => "OFTemplate: Parameters must be aliases as the keys in addTemplateFile and
																													an array of variable names from HTML file as keys and their values<br>
																													All aliases and variable names (array keys) must be combinations of 
																													english letters, digits and/or underscore<br>",
																				"getHTML" => "OFTemplate: Parameter must be alias string as the keys in addTemplateFile<br>",
																				"clearVariable" => "OFTemplate: Parameter must be alias string as the keys in addTemplateFile<br>",
																				"parse" => "This variable was not given any value at all<br>" ) );
	/**
	*FUNCTION:				OFTemplate( $templatePath = "" )
	*VISIBILITY:			public
	*DESCRIPTION:			the only constructor
	*PARAMETERS:			$templatePath string path to the template files
	**/	
	function OFTemplate( $templatePath = "" ) {
		$this->templatePath = $templatePath;
	}
	/**
	*END_FUNCTION:		OFTemplate( $templatePath = "" )
	**/
	
	/**
	*FUNCTION:				addTemplateFile( $inFiles )
	*VISIBILITY:			public
	*DESCRIPTION:			All the template files (only .html, .htm and .css) must be given an alias and added 
										by using this function
	*PARAMETERS:			$inFiles array the keys will be aliases to the filenames, while the value will 
										be the filenames	
	**/	
	function addTemplateFile( $inFiles ) {
		if( is_array( $inFiles ) ) {
			foreach( $inFiles as $alias => $file ) {
				$alias = trim( $alias );
				if( @preg_match( "/(\.htm[l]?)|(\.css)$/", $file ) && 
						is_readable( $this->templatePath.$file ) && 
						@preg_match( "/^[A-Za-z0-9_]+$/", $alias ) ) {
					$this->files[$alias] = $file;
					$this->variables[$alias] = array();
				} else {
					die( "1: ".$this->text["Error"]["addTemplateFile"] );
				}
			}
		} else {
			die( "2: ".$this->text["Error"]["addTemplateFile"] );
		}
	}
	/**
	*END_FUNCTION:		addTemplateFile( $inFiles )
	**/
	
	/**
	*FUNCTION:				addVariable( $alias, $inVariables )
	*VISIBILITY:			public
	*DESCRIPTION:			This function is used to set the value of the variables in a given template file.
										If the variable is inside a dynamic block, the variable might be added several 
										times and the dynamic block will be repeated as many times as there are variables 
										added.
	*PARAMETERS:			$alias string this is one of the aliases defined in addTemplateFile()	
										$inVariables array the keys are variable names from the template file. The 
										values are the value that should be inserted.
	**/	
	function addVariable( $alias, $inVariables ) {
		$alias = trim( $alias );
		if( @preg_match( "/^[A-Za-z0-9_]+$/", $alias ) &&
				is_array( $inVariables ) && 
				isset( $this->files[$alias] ) ) {
			foreach( $inVariables as $variableAlias => $variable ) {
				$variableAlias = trim( $variableAlias );
				if( @preg_match( "/^[A-Za-z0-9_]+$/", $variableAlias ) ) {
					if( !isset( $this->variables[$alias][$variableAlias] ) ) {
						$this->variables[$alias][$variableAlias] = array();
					}
					array_push( $this->variables[$alias][$variableAlias], $variable );
				} else {
					die( "1: ".$this->text["Error"]["addVariable"] );
				}
			}
		} else {
			die( "2: ".$this->text["Error"]["addVariable"] );
		}
	}
	/**
	*END_FUNCTION:		addVariable( $alias, $inVariables )
	**/
	
	/**
	*FUNCTION:				getHTML( $alias )
	*VISIBILITY:			public
	*DESCRIPTION:			Function to get the resulting - ready to display - HTML as a string
	*PARAMETERS:			$alias string this is one of the aliases defined in addTemplateFile()	
	*RETURN:					string returns the resulting HTML as a string	
	**/	
	function getHTML( $alias ) {
		if( @preg_match( "/^[A-Za-z0-9_]+$/", $alias ) &&
				isset( $this->files[$alias] ) && 				
				( $fileString = @file_get_contents( $this->templatePath.$this->files[$alias] ) ) ) {
			return $this->parse( $alias, @preg_split( "/(".$this->tagStart."[A-Za-z0-9:_ ]+".$this->tagEnd.")/", $fileString, -1, PREG_SPLIT_DELIM_CAPTURE ), true );
		} else {
			die( "2: ".$this->text["Error"]["getHTML"] );
		}
	}	
	/**
	*END_FUNCTION:		getHTML( $alias )			
	**/
	
	/**
	*FUNCTION:				clearVariable( $alias, $variableName=false )
	*VISIBILITY:			public
	*DESCRIPTION:			Function to clear all variables for an alias file or just one variable for an alias
										file if a variable name is given.
	*PARAMETERS:			$alias string this is one of the aliases defined in addTemplateFile()	
										$variableName string default to false. If given, only this variable's values will be 
										cleared. Otherwise all variables and their values for the given alias will be cleared.
	*RETURN:					string returns the resulting HTML as a string	
	**/	
	function clearVariable( $alias, $variableName=false ) {
		if( @preg_match( "/^[A-Za-z0-9_]+$/", $alias ) && 
				@isset( $this->files[$alias] ) ) {
			if( $variableName ) {
				if(	@preg_match( "/^[A-Za-z0-9_]+$/", $variableName ) && 
						@isset( $this->variables[$alias][$variableName] ) ) {
						$this->variables[$alias][$variableName] = array();
				}	
			} else {
				$this->variables[ $alias ] = array();
			}
		} else {
			die( "1: ".$this->text["Error"]["clearVariable"] );
		}
	}
	/**
	*END_FUNCTION:		clearVariable( $alias, $variableName=false )			
	**/
	
	/**
	*FUNCTION:				parse( $alias, &$stringArray )
	*VISIBILITY:			private
	*DESCRIPTION:			This function substitutes the variables in the template with the values 
										added by using addVariable(). When a dynamic block is encountered, the 
										contents of the block will be put into another array and then a recursive 
										call to parse( $alias, &$stringArray ) will be made to handle the block.
	*PARAMETERS:			$alias string this is one of the aliases defined in addTemplateFile()	
										$stringArray array array with pieces of text from the template
										$first boolean true if this is the first call to the parse function
	*RETURN:					string the resulting HTML from parsing this piece of the template file
	**/	
	function parse( $alias, &$stringArray, $first=false ) {
		$html = "";
		$count = false;
		$noOfArrayElements = count( $stringArray );
		do{
			for( $i = 0; $i < $noOfArrayElements; $i++ ) {			
				$stringPart = $stringArray[ $i ];
				if( @preg_match( "/".$this->tagStart."[ ]*".$this->tagVariable."[ ]*([A-Za-z0-9_]+)[ ]*".$this->tagEnd."/", $stringPart, $variableName ) ) {					
					$oldCount = 0;
					$newCount = 0;
					if( isset( $this->variables[ $alias ][ $variableName[ 1 ] ] ) && ( $oldCount = count( $this->variables[ $alias ][ $variableName[ 1 ] ] ) ) ) {
						$stringPart = array_shift( $this->variables[ $alias ][ $variableName[ 1 ] ] );
						$newCount = $oldCount -1;
					} else {
						$stringPart = "<!--empty template variable-->&nbsp;";
					}
					if( $oldCount && ( $count === false || $newCount < $count ) ) {
						$count = $newCount;
					}
				} else if( @preg_match( "/".$this->tagStart."[ ]*".$this->tagDynamicStart."[ ]*([A-Za-z0-9_]+)[ ]*".$this->tagEnd."/", $stringPart, $dynamicName ) ) {
					$dynamicStrings = array();
					for( $i = $i + 1; $i < $noOfArrayElements; $i++ ) {
						$dynamicStringPart = $stringArray[ $i ];
						if( @preg_match( "/".$this->tagStart."[ ]*".$this->tagDynamicEnd."[ ]*".$dynamicName[1]."[ ]*".$this->tagEnd."/", $dynamicStringPart ) ) {
							break;
						}
						if( @trim( $dynamicStringPart ) ) {
							array_push( $dynamicStrings, $dynamicStringPart );
						}
					}	
					$stringPart = $this->parse( $alias, $dynamicStrings );			
				} 
				$html .= $stringPart;
			}
		} while ( $count );
		if( ( $count === false ) && ( $first === false ) ) {
			$html = "";
		}
		return $html;
	}
	/**	
	*END_FUNCTION:		parse( $alias, &$stringArray )		
	**/
	
}
/**
END_CLASS:				OFTemplate
**/

} // end if ! class exists
?>