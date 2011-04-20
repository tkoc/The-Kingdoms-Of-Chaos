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
/* Template class. To assign values to blocks in a html template file.
 * 
 * Modified by Øystein Fladby	30.03.2003	Removed some unnessesary code and cleaned up a bit
 * Author: Øystein Fladby		27.03.2003
 * 
 * Version: test
 * 
 */
class Template {
	var $templates	= array();		// array of template files with usergiven name as key
	var $loaded		= array();		// array of loaded and preparsed template files represented ad a DynamicTemplateObject
	var $parsed		= array();		// array of parsed templates represented as strings with usergiven name as key
	var $lastTmp	= "";			// the last template the user worked with
	var $path		= "";			// the path to template files
	var $errMsg		= "";			// the error messages made by this object
	var $debugMsg	= "";			// message to print if debug() is called
	
	////////////////////////////////////////////
	// Template::Template
	////////////////////////////////////////////
	// Constructor
	// Takes the path to the template files
	//////////////////////////////////////////// 
	function Template( $path = "" ) {
		$this->path = $path;
		$this->debugMsg .= "\n<br>Path to templates set to: '$this->path'.";
	}
	
	////////////////////////////////////////////
	// Template::def
	////////////////////////////////////////////
	// Function to define the templates to be used
	// Takes the templates to be used / parsed
	// ( array( 'alias' => 'filename' ) )
	// or just the alias and filename ( 'alias', 'filename' )
	//////////////////////////////////////////// 
	function def( $inDefines, $value=false ) {
		$result = true; 
		if( !$value && gettype( $inDefines ) == "array" ) {
			while( list( $key, $value ) = each ($inDefines) ) {
				if( !$this->def( $key, $value ) ) {
					$result = false;
				}
			}
		} else if( gettype( $inDefines ) == "string" && gettype( $value ) == "string" ) {
			$inDefines = trim( $inDefines );
			$result = $this->prepareTemplate( $inDefines, $value );
			if( $result ) {
				$this->lastTmp = $inDefines;
				$this->debugMsg .= "\n<br>Last template used set to: '$this->lastTmp'.";
			}
		} else {
			$this->errorMsg .= "<br>The def-function only takes one array or two strings."; 
			$result = false;
		}		
		return $result;
	}
	
	////////////////////////////////////////////
	// Template::prepareTemplate
	////////////////////////////////////////////
	// Function to read a file into a string variable
	// and call preParse for each file. This splits the
	// template into pieces and registers all dynamic
	// blocks and other blocks so that assign may
	// check if the given block name exists in a template
	// or not. 
	//////////////////////////////////////////// 
	function prepareTemplate( $key, $value ) {
		$result = false;
		if( file_exists( ( $this->path.$value ) ) ) {
			if( !array_key_exists( $key, $this->loaded ) ) {
				$this->templates[$key] = $value;
				$fileAsString = implode( "", file( ( $this->path.$value ) ) );	// read file into string variable
				$fileAsString = stripslashes( $fileAsString );
				$pieces = preg_split ("([{-]+[-}]+)", $fileAsString );			// split the templatestring at each '{-' or '-}'			$this->loaded[$key] = new DynamicTemplate("$key");						// make a new DynamicTemplate object to store the document
				$this->loaded[$key] = new DynamicTemplate( $key );
				$result = $this->loaded[$key]->preParse( $pieces );				// preParse the template
				if( $result ) {	
					$this->debugMsg .= "\n<br>Template '$this->path$value' prepared. Stored in loaded['$key'].";
				}
			}else {
				$this->errMsg .= "<br>Template alias '$key' .";
			}
		} else {
			$this->errMsg .= "<br>The file $this->path$value doesn't exist.";
		}
		return $result;
	}
	
	////////////////////////////////////////////
	// Template::assign
	////////////////////////////////////////////
	// Function to assign data / values / code to
	// a block in the template
	// First parameter / key in array = block name
	// Second parameter / value in array = data (false 
	// if array is used)
	// Third parameter = template alias of the template 
	// to use (if none, uses the last template the user
	// worked on)
	//////////////////////////////////////////// 
	function assign( $normal, $value=false, $templateAlias=false ) {
		$result = true;
		if( !$value && gettype( $normal ) == "array" ) {								// if one array
			while( list( $key, $value ) = each( $normal ) ) {
				if( !$this->assign( $key, $value, $templateAlias ) ) {
					$result = false;
				}
			}
		} else if( gettype( $normal ) == "string" && gettype( $value ) == "string" ) {	// if two strings 
			$normal = trim( $normal );
			if( $templateAlias ) {														// no template given
				$this->lastTmp = $templateAlias;										// use the one most recently used
				$this->debugMsg .= "\n<br>Last template used set by user in 'assign()' to: '$this->lastTmp'.";
			}
			if( isset( $this->loaded[$this->lastTmp] ) ) {
				$result = $this->loaded[$this->lastTmp]->assign( $normal, $value );			// assign value in template
			} else {
				$this->errMsg .= "<br>The template '$this->lastTmp' doesn't exsist.";
			}
			if( $result ) {
				$this->debugMsg .= "\n<br>Block '$normal' assigned in template '$this->lastTmp'.";
			}
		}
		if( !$result ) {
			$this->errMsg .= "<br>The assign-function only takes one array or two strings.";
		}
		return $result;
	}
	
	////////////////////////////////////////////
	// Template::dynamicAssign
	////////////////////////////////////////////
	// Function to assign data / values / code to
	// a specific dynamic block in the template
	// First parameter / key in array = dynamic block name
	// Second parameter / key in array = block name
	// Third parameter / value in array = data
	// Fourth parameter = template alias of the template 
	// to use (if none, uses the last template the user
	// worked on)
	//////////////////////////////////////////// 
	function dynamicAssign( $dynamic, $normal=false, $value=false, $templateAlias=false ) {
		$result = false;
		if( !$value && !$normal && gettype( $dynamic ) == "array" ) {					// if one array
			while( list( $key, $normal ) = each( $dynamic ) ) {
				if( gettype( $value ) == "array" ) {									// if the value of the array is another array
					while( list( $normalKey, $value ) = each( $normal ) ) {
						if( !$this->dynamicAssign( $key, $normalKey, $value, $templateAlias ) ) {
							$result = false;
						}
					}
				}
			}
		} else if( 	gettype( $dynamic ) == "string" && gettype( $value ) == "string" && 
					gettype( $normal ) == "string" ) {									// if three strings 
			$dynamic = trim( $dynamic );
			$normal = trim( $normal );
			if( $templateAlias ) {														// no template given
				$this->lastTmp = $templateAlias;										// use the one most recently used
				$this->debugMsg .= "\n<br>Last template used set by user in 'dynamicAssign()' to: '$this->lastTmp'.";
			}
			if( isset( $this->loaded[$this->lastTmp] ) ) {
				$result = $this->loaded[$this->lastTmp]->dynamicAssign( $dynamic, $normal, $value );// assign value
			}else {
				$this->errMsg .= "<br>The template alias '$this->lastTmp' doesn't exsist.";
			}
			if( $result ) {
				$this->debugMsg .= "\n<br>Block '$normal' assigned in dynamic block '$dynamic' in template '$this->lastTmp'.";
			}
		}
		if ( !$result ) {
			$this->errMsg .= "<br>The dynamicAssign-function only takes one array of arrays or three strings.";
		}
		return $result;
	}	// end dynamicAssign()
	
	////////////////////////////////////////////
	// Template::parse
	////////////////////////////////////////////
	// Function to parse the template and insert the 
	// values assigned.
	// Parameter = template alias to parse
	//////////////////////////////////////////// 
	function parse( $templateAlias=false ) {
		$this->setRepeats();
		if( $templateAlias ) {
			$this->lastTmp = $templateAlias;
			$this->debugMsg .= "\n<br>Last template used set by user in 'parse()' to: '$this->lastTmp'.";
		}
		if( isset( $this->loaded[$this->lastTmp] ) ) {
			$this->parsed[$this->lastTmp] = $this->loaded[$this->lastTmp]->parse();
			$this->debugMsg .= "\n<br>Last template used in 'parse()': '$this->lastTmp'.";
			return true;
		} else {
			$this->errMsg .= "<br>The template alias '$this->lastTmp' doesn't exsist.";
		}
	}
	
	////////////////////////////////////////////
	// Template::setRepeats
	////////////////////////////////////////////
	// Function to set the number of times a dynamic
	// block should be repeated
	// no parameters -> all dynamic blocks are repeated
	// as many times as they have assigned blocks to
	// fill up. 
	//////////////////////////////////////////// 
	function setRepeats() {
		$keys = array_keys( $this->loaded );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$this->loaded[$key]->setRepeats();
		}
	}
	
	////////////////////////////////////////////
	// Template::display
	////////////////////////////////////////////
	// Function to get the parsed string
	// Parameter = the templateAlias to display
	//////////////////////////////////////////// 
	function display( $templateAlias=false ) {
		$result = false;
		if( $templateAlias ) {
			$this->lastTmp = $templateAlias;
			$this->debugMsg .= "\n<br>Last template used set by user in 'parse()' to: '$this->lastTmp'.";
		}
		if( isset( $this->parsed[$this->lastTmp] ) ) {		
			$this->debugMsg .= "\n<br>Last template used in 'display()': '$this->lastTmp'.";
			$result = $this->parsed[ $this->lastTmp ];
		} else {
			$this->errMsg .= "<br>The template alias '$this->lastTmp' doesn't exsist.";
		}
		return $result;
	}
	
	////////////////////////////////////////////
	// Template::errorMessage
	////////////////////////////////////////////
	// Function to get all error messages from 
	// all templates
	//////////////////////////////////////////// 
	function errorMessage( $templateAlias ) {
		$resultString = $this->errMsg;
		if( isset( $this->loaded[$templateAlias] ) ) {
			$resultString .= $this->loaded[$templateAlias]->errorMessage();
		}
		return $resultString;
	}
	
	////////////////////////////////////////////
	// Template::displayPieces
	////////////////////////////////////////////
	// Function to display the pieces in which the
	// preParse function splits the template file
	//////////////////////////////////////////// 
	function displayPieces( $templateAlias ) {
		$this->loaded[$templateAlias]->displayPieces();
	}
	
	////////////////////////////////////////////
	// Template::debug
	////////////////////////////////////////////
	// Function to get much more output from the functions
	// of the Template classes
	//////////////////////////////////////////// 
	function debug( $templateAlias ) {
		$resultString = $this->debugMsg;
		$resultString .= $this->loaded[$templateAlias]->debug();
		return $resultString;
	}	
}

/********************************************************************************/
/********************************************************************************/
/*																				*/
/*	This class handles the dynamic blocks in the template. No functions should	*/
/*	be called by the user, only by the Template class							*/
/*																				*/
/********************************************************************************/
/********************************************************************************/
class DynamicTemplate {
	var $variables 	= array();		// array of VariableTemplate objects inside this dynamic block, block name as key
	var $dynamics 	= array();		// array of DynamicTemplate objects inside this Dynamic block, block name as key
	var $pieces		= array();		// the data/html/text in this dynamic block cut in pieces for each '{-' or '-}'
	var $repeats	= 0;			// the number of times this dynamic block should be repeated if set by user
	var $name		= "";			// the name of this dynamic block
	var $errMsg		= "";			// the error messages made by this dynamic block
	var $debugMsg	= "";			// message to print if debug() is called
	
	////////////////////////////////////////////
	// DynamicTemplate::DynamicTemplate
	////////////////////////////////////////////
	// Constructor to initialize the name of the 
	// dynamic block
	//////////////////////////////////////////// 
	function DynamicTemplate( $name ) {
		$this->name = $name;
		$this->debugMsg .= "\n\n<br><br>Object of DynamicTemplate made. Name: '$this->name'.";
	}	
	
	////////////////////////////////////////////
	// DynamicTemplate::preParse
	////////////////////////////////////////////
	// Function to parse the pieces found in the array
	// $pieces to find all the dynamic blocks and normal
	// blocks among the pieces
	//////////////////////////////////////////// 
	function preParse( $pieces ) {
		$count = 1;						// Element 1 is a block, element 0 is html so just leave it				
		while( isset( $pieces[$count] ) ) {	
			if( is_integer( strpos( $pieces[$count], "DYNAMIC:" ) ) ) {// if it's a dynamic start block - ( DYNAMIC:name )
				$dynamicName = trim( substr( $pieces[$count], strpos( $pieces[$count], ":" )+1 ) );	
				$this->debugMsg .= "\n\t<br> &nbsp; &nbsp; &nbsp; Dynamic start tag found in DynamicTemplate '$this->name'. Name: '$dynamicName'.";
				
				$dynamicStart = ( $count + 1 );					// the dynamic block data (html) starts at this position
				$dynamicLength = 1;								// must be one html-piece between two blocks, so element 2 must be 
				$check = true;									// the end of the dynamic block or another block													
				while( $check && isset( $pieces[ ( $dynamicStart + $dynamicLength ) ] ) ){
					if( is_integer( strpos( $pieces[ ( $dynamicStart + $dynamicLength ) ], "END DYNAMIC:" ) ) ) {
						$dynamicEndName = trim( substr( $pieces[ ( $dynamicStart + $dynamicLength ) ], strpos( $pieces[ ( $dynamicStart + $dynamicLength ) ], ":" )+1 ) );
						if( !strcmp( $dynamicName, $dynamicEndName ) ) {
							$check = false;
							$this->debugMsg .= "\n\t<br> &nbsp; &nbsp; &nbsp; Dynamic end tag found in DynamicTemplate '$this->name'. Name: '$dynamicName'.";
						}
					}
					$dynamicLength += 2;						// every second element is a block
				}
				if( !$check ) {									// if the while-loop ended with the END DYNAMIC block as supposed
					$dynamicLength -= 2;						// counted once too much
					$dynamicPieces = array_splice( $pieces, $dynamicStart, $dynamicLength );// get the pieces of the dynamic block ( without the end block )
					$this->dynamics[$dynamicName] = new DynamicTemplate( $dynamicName );
					$this->dynamics[$dynamicName]->preParse( $dynamicPieces );	// call this function to preParse the dynamic block
					$this->debugMsg .= "\n<br>Dynamic tag '$dynamicName' in DynamicTemplate '$this->name' is preParsed.";
					
					array_splice( $pieces, $dynamicStart, 1 );	// extract and throw the END DYNAMIC block
					
				} else {										// if no END DYNAMIC block found
					$this->errMsg .= "<br>No 'END DYNAMIC: $dynamicName' found in DynamicTemplate '$this->name'.";
					return false;
				}
				
			} else {											// it has to be a normal block - ( name )				
				
				$pieces[$count] = trim( $pieces[$count] );
				if( !array_key_exists( $pieces[$count], $this->variables ) ) {	// if no such block name in this dynamic block before
					$this->variables[ $pieces[$count] ] = new VariableTemplate( $pieces[$count] );
					$this->debugMsg .= "\n\t<br> &nbsp; &nbsp; &nbsp; Block found in DynamicTemplate '$this->name'. Name: '".$pieces[$count]."'.";
				} else {										// can't have more than one block with the same name in one dynamic block
					$this->errMsg .= "<br>You have two or more blocks named '".$blockName."' in DynamicTemplate '$this->name'.";
					return false;
				}
			}
			$count += 2;									// every second element contains a block name
		}			
		$this->pieces = $pieces;	// this->pieces now contains all the pieces of this dynamic block with 
									// normal block names and start DYNAMIC block names. this->variables
									// contains an object of each block with block name as key, this->dynamics
									// containg an object of each dynamic block with dynamic block name as key.
		return true;
	} // end preParse()
	
	////////////////////////////////////////////
	// DynamicTemplate::assign
	////////////////////////////////////////////
	// Function to assign data / values / code to
	// a block in the Dynamic block
	// First parameter = block name
	// Second parameter = data
	//////////////////////////////////////////// 
	function assign( $name, $value ) {
		if( key_exists( $name, $this->variables ) ) {
			$this->variables[$name]->assign( $value );
			$this->debugMsg .= "\n<br>Value assigned to block '$name' in DynamicTemplate '$this->name'.";
			return true;
		} else {
			$keys = array_keys( $this->dynamics );
			while( list( $dummy, $key ) = each( $keys ) ) {
				if( $this->dynamics[$key]->assign( $name, $value ) ) {
					return true;
				}
			}
		}
		$this->errMsg .= "<br>There is no block named '$name' in the dynamic block '$this->name'.";
		return false;
	} // end assign()
	
	////////////////////////////////////////////
	// DynamicTemplate::dynamicAssign
	////////////////////////////////////////////
	// Function to assign data / values / code to
	// a block in a specified Dynamic block
	// First parameter = dynamic block name
	// Second parameter = block name
	// Third parameter = data
	//////////////////////////////////////////// 
	function dynamicAssign( $dynamicName, $name, $value ) {
		if( !strcmp( $this->name, $dynamicName ) ) {
			if( key_exists( $name, $this->variables ) ) {
				$this->variables[$name]->assign( $value );
				$this->debugMsg .= "\n<br>Value assigned to block '$name' in DynamicTemplate '$this->name'.";
				return true;
			} else {
				$this->debugMsg .= "\n<br>DynamicTemplate '$this->name' was found, but no block named '$name'.";
				$this->errMsg .= "<br>There is no block named '$name' in any dynamic block named '$this->name'.";
			}
		} else {
			$keys = array_keys( $this->dynamics );
			while( list( $dummy, $key ) = each( $keys ) ) {
				if( $this->dynamics[$key]->dynamicAssign( $dynamicName, $name, $value ) ) {
					return true;
				}
			}
		}
		return false;
	} // end dynamicAssign()
	
	////////////////////////////////////////////
	// DynamicTemplate::parse
	////////////////////////////////////////////
	// Function to parse this dynamic block and all
	// the data in it and assign values to the
	// blocks.
	//////////////////////////////////////////// 
	function parse( $parentRepeatNumber = 0 ) {
		$resultString = "";
		$repeats = $this->repeats;
		for( $repeatCount = 0; $repeatCount < $repeats; $repeatCount++ ) {
			$elementCount = 0;
			$resultString .= $this->pieces[$elementCount++];		// keep the first piece which should be html, increase $count
			while( isset( $this->pieces[$elementCount] ) ) {
				if( is_integer( strpos( $this->pieces[$elementCount], "DYNAMIC:" ) ) ) {	// if start of dynamic block
					$dynamicName = trim( substr( $this->pieces[$elementCount], strpos( $this->pieces[$elementCount], ":" )+1 ) );
					$resultString .= $this->dynamics[$dynamicName]->parse( $parentRepeatNumber * $repeats + $repeatCount  );	// this has to be set if the preParse was ok
				} else {											// it's a normal block
					$resultString .= $this->variables[ $this->pieces[$elementCount] ]->getValue( $parentRepeatNumber + $repeatCount );
					$this->debugMsg .= "<br>The block '".$this->pieces[$elementCount]."' was parsed into dynamic block '$this->name'.";
				}
				if( isset( $this->pieces[$elementCount+1] ) ) {
					$resultString .=  $this->pieces[$elementCount+1];// keep next html piece
				}
				$elementCount += 2;								// every second piece contains a block 
			}
		}				
		return $resultString;
	} // end parse()
	
	////////////////////////////////////////////
	// DynamicTemplate::setRepeats
	////////////////////////////////////////////
	// Function to set the number of repeats this
	// dynamic block and the following has enough
	// assigned block values to do.
	//////////////////////////////////////////// 
	function setRepeats() {
		$first = true;
		if( !count( $this->variables ) ) {			// if this dynamic block only contains html
			$this->repeats = 1;						// and dynamic blocks, unlimited repeats
		} else {									// if there are normal blocks in this dynamic block			
			$keys = array_keys( $this->variables );
			while( list( $dummy, $key ) = each( $keys ) ) {
				if( $first ) {
					$first = false;
					$this->repeats = $this->variables[$key]->noOfValues();
					$this->debugMsg .= "<br>'$this->name' may repeat '$this->repeats' times ";
				} else {
					$checkValue = $this->variables[$key]->noOfValues();
					if( $checkValue != $this->repeats ) {
						$this->debugMsg .= "<br>'$this->name' has blocks assigned diffrent number of times";
					}					
					if( $checkValue < $this->repeats ) {
						$this->debugMsg .= "<br>'$this->name' may repeat '$checkValue' times";
						$this->repeats = $checkValue;			// find the smallest amount of block values 
					}											// for the variables in this dynamic block
				}				
			}
		}
		$keys = array_keys( $this->dynamics );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$this->dynamics[$key]->setRepeats();
		}
		$this->debugMsg .= "<b><br>Dynamic block '$this->name' may be repeated '$this->repeats' times.</b>";		
		return $this->repeats;
	}	
	
	////////////////////////////////////////////
	// DynamicTemplate::getRepeats
	////////////////////////////////////////////
	// Function to get the number of times this dynamic
	// block may be repeated (due to number of assigned
	// block values)
	//////////////////////////////////////////// 
	function getRepeats() {
		return $this->repeats();
	}
	
	////////////////////////////////////////////
	// DynamicTemplate::errorMessage
	////////////////////////////////////////////
	// Function to get the error messages from this
	// dynamic block and all the dynamic and normal
	// blocks inside it.
	//////////////////////////////////////////// 
	function errorMessage() {
		$resultString = $this->errMsg;		
		$keys = array_keys( $this->variables );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$resultString .= $this->variables[$key]->errorMessage();
		}
		$keys = array_keys( $this->dynamics );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$resultString .= $this->dynamics[$key]->errorMessage();
		}
		return $resultString;
	}
	
	////////////////////////////////////////////
	// DynamicTemplate::displayPieces
	////////////////////////////////////////////
	// Function to display the pieces in which the
	// preParse function splits the template file
	//////////////////////////////////////////// 
	function displayPieces($ident = "\n<br>") {
		echo "$ident START DYNAMIC : $this->name.";
		while( list( $key, $value ) = each( $this->pieces ) ) {			
			echo "$ident Key: $key.          Value: ".htmlspecialchars($value).".";
		}
		$keys = array_keys( $this->dynamics );
		echo print_r( $keys);
		while( list( $key, $value ) = each( $keys ) ) {
			$this->dynamics[$value]->displayPieces($ident."&nbsp; &nbsp; &nbsp;");
		}
		echo "$ident END DYNAMIC : $this->name.";
	}
	
	////////////////////////////////////////////
	// DynamicTemplate::debug
	////////////////////////////////////////////
	// Function to get much more output from the functions
	// of the Template classes
	//////////////////////////////////////////// 
	function debug() {
		$resultString = $this->debugMsg;
		$keys = array_keys( $this->variables );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$resultString .= $this->variables[$key]->debug();
		}
		$keys = array_keys( $this->dynamics );
		while( list( $dummy, $key ) = each( $keys ) ) {
			$resultString .= $this->dynamics[$key]->debug();
		}		
		return $resultString;
	}
}
/********************************************************************************/
/********************************************************************************/
/*																				*/
/*	This class handles the normal blocks in the template. No functions should	*/
/*	be called by the user, only by the DynamicTemplate class					*/
/*																				*/
/********************************************************************************/
/********************************************************************************/
class VariableTemplate {
	var $variables	= array();	// array of user-assigned data for this block
	var $name		= "";		// the name of this block
	var $errMsg		= "";		// the error messages made by this block
	var $debugMsg	= "";		// string to print if debug() is called
		
	////////////////////////////////////////////
	// VariableTemplate::VariableTemplate
	////////////////////////////////////////////
	// Constructor to initialize the name of the block
	//////////////////////////////////////////// 
	function VariableTemplate( $name ) {
		$this->name = $name;
		$this->debugMsg .= "\n\t\t<br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
							Object of VariableTemplate made. Name: '$this->name'.";
	}
	
	////////////////////////////////////////////
	// VariableTemplate::assign
	////////////////////////////////////////////
	// Function to assign data / values / code to
	// this block
	// Parameter = data
	////////////////////////////////////////////
	function assign( $value ) {
		array_push( $this->variables, $value );
		$this->debugMsg .= "\n\t\t\t<br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
							Value assigned to VariableTemplate '$this->name'. Value: '$value'.";
	}
	
	////////////////////////////////////////////
	// VariableTemplate::noOfValues
	////////////////////////////////////////////
	// Function to get the number of data assigned
	// to this block name
	////////////////////////////////////////////
	function getValue( $number ) {
		if( isset( $this->variables[$number] ) ){		
			$result = $this->variables[$number];
		} else {
			$result = "{-".$this->name."-}";
		}
		$this->debugMsg .= "\n\t\t\t<br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
							Value parsed for VariableTemplate '$this->name'. Value: '$result'.";
		return $result;
	}
	
	////////////////////////////////////////////
	// VariableTemplate::noOfValues
	////////////////////////////////////////////
	// Function to get the number of data assigned
	// to this block name
	////////////////////////////////////////////
	function noOfValues() {
		$this->debugMsg .= "\n\t\t\t<br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
							VariableTemplate '$this->name' has ".count( $this->variables )." values assigned.";
		return count( $this->variables );
	}
	
	////////////////////////////////////////////
	// VariableTemplate::errorMessage
	////////////////////////////////////////////
	// Function to get the error messages from this 
	// block
	//////////////////////////////////////////// 
	function errorMessage() {
		return $this->errMsg;
	}
	
	////////////////////////////////////////////
	// VariableTemplate::debug
	////////////////////////////////////////////
	// Function to get much more output from the functions
	// of the Template classes
	//////////////////////////////////////////// 
	function debug() {
		return $this->debugMsg;
	}	
}
?>