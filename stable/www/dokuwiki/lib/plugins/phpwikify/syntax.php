<?php
/**
 * PHP-Wikify plugin: lets the parser wikify output of php scripts
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Kasper Sandberg <redeeman@metanurb.dk>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'inc/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
class syntax_plugin_phpwikify extends DokuWiki_Syntax_Plugin
{
	function syntax_plugin_phpwikify(){
		global $PARSER_MODES;
		$this->allowedModes = $PARSER_MODES['formatting'];
	}
	
	function getInfo(){
		return array(
			'author' => 'Kasper Sandberg',
			'email'  => 'redeeman@metanurb.dk',
			'date'   => '2005-07-22',
			'name'   => 'PHP-Wikify Plugin',
			'desc'   => 'Enables php to be processed by renderer',
			'url'    => 'http://wiki.kaspersandberg.com/doku.php?projects:dokuwiki:phpwikify',
		);
	}
 
	function getType(){
		return "protected";
	}
 
	function getPType(){
		return "normal";
	}
 
	function getSort(){
		return 0;
	}
 
	function connectTo( $mode ) {
		$this->Lexer->addEntryPattern("<phpwikify>",$mode,"plugin_phpwikify");
	}
 
	function postConnect() {
		$this->Lexer->addExitPattern( "</phpwikify>","plugin_phpwikify");
	}
 
	function handle( $match, $state, $pos, &$handler ){
		$match = ereg_replace( "<phpwikify>", "", $match );
		$match = ereg_replace( "</phpwikify>", "", $match );
		return $match;
	}
 
	function render( $mode, &$renderer, $data ) {
		if($mode == 'xhtml'){
			ob_start();
			eval( $data );
			$renderer->doc .= p_render( "xhtml", p_get_instructions( ob_get_contents() ), $info );
			ob_end_clean();
			return true;
		}
		return false;
	}
}
?>