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

function begtoend($htmltag){
	return preg_replace('/<([A-Za-z]+)>/','</$1>',$htmltag);
}
function replace_pcre_array($text,$array){
	$pattern = array_keys($array);
	$replace = array_values($array);
	$text = preg_replace($pattern,$replace,$text);
	return $text;
}
class bbcode{
	var $tags;
	var $settings;
	function bbcode(){
		$this->tags = array();
		$this->settings = array('enced'=>true);
	}
	function get_data($name,$cfa = ''){
		if(!array_key_exists($name,$this->tags)) return '';
		$data = $this->tags[$name];
		if($cfa) $sbc = $cfa; else $sbc = $name;
		if(!is_array($data)){
			$data = preg_replace('/^ALIAS(.+)$/','$1',$data);
			return $this->get_data($data,$sbc);
		}else{
			$data['Name'] = $sbc;
			return $data;
		}
	}
	function change_setting($name,$value){
		$this->settings[$name] = $value;
	}
	function add_alias($name,$aliasof){
		if(!array_key_exists($aliasof,$this->tags) or array_key_exists($name,$this->tags)) return false;
		$this->tags[$name] = 'ALIAS'.$aliasof;
		return true;
	}
	function onparam($param,$regexarray){
		$param = replace_pcre_array($param,$regexarray);
		if(!$this->settings['enced']){
			$param = htmlentities($param);
		}
		return $param;
	}
	function export_definition(){
		return serialize($this->tags);
	}
	function import_definiton($definition,$mode = 'append'){
		switch($mode){
			case 'append':
			$array = unserialize($definition);
			$this->tags = $array + $this->tags;
			break;
			case 'prepend':
			$array = unserialize($definition);
			$this->tags = $this->tags + $array;
			break;
			case 'overwrite':
			$this->tags = unserialize($definition);
			break;
			default:
			return false;
		}
		return true;
	}
	function add_tag($params){
		if(!is_array($params)) return 'Paramater array not an array.';
		if(!array_key_exists('Name',$params) or empty($params['Name'])) return 'Name parameter is required.';
		if(preg_match('/[^A-Za-z]/',$params['Name'])) return 'Name can only contain letters.';
		if(!array_key_exists('HasParam',$params)) $params['HasParam'] = false;
		if(!array_key_exists('HtmlBegin',$params)) return 'HtmlBegin paremater not specified!';
		if(!array_key_exists('HtmlEnd',$params)){
			 if(preg_match('/^(<[A-Za-z]>)+$/',$params['HtmlBegin'])){
			 	$params['HtmlEnd'] = begtoend($params['HtmlBegin']);
			 }else{
			 	return 'You didn\'t specify the HtmlEnd parameter, and your HtmlBegin parameter is too complex to change to an HtmlEnd parameter.  Please specify HtmlEnd.';
			 }
		}
		if(!array_key_exists('ParamRegexReplace',$params)) $params['ParamRegexReplace'] = array();
		if(!array_key_exists('ParamRegex',$params)) $params['ParamRegex'] = '[^\\]]+';
		if(!array_key_exists('HasEnd',$params)) $params['HasEnd'] = true;
		if(array_key_exists($params['Name'],$this->tags)) return 'The name you specified is already in use.';
		$this->tags[$params['Name']] = $params;
		return '';
	}
	function parse_bbcode($text){
		foreach($this->tags as $tagname => $tagdata){
			if(!is_array($tagdata)) $tagdata = $this->get_data($tagname);
			$startfind = "/\\[{$tagdata['Name']}";
			if($tagdata['HasParam']){
				$startfind.= '=('.$tagdata['ParamRegex'].')';
			}
			$startfind.= '\\]/';
			if($tagdata['HasEnd']){
				$endfind = "[/{$tagdata['Name']}]";
				$starttags = preg_match_all($startfind,$text,$ignore);
				$endtags = substr_count($text,$endfind);
				if($endtags < $starttags){
					$text.= str_repeat($endfind,$starttags - $endtags);
				}
				$text = str_replace($endfind,$tagdata['HtmlEnd'],$text);
			}
			$replace = str_replace(array('%%P%%','%%p%%'),'\'.$this->onparam(\'$1\',$tagdata[\'ParamRegexReplace\']).\'','\''.$tagdata['HtmlBegin'].'\'');
			$text = preg_replace($startfind.'e',$replace,$text);
		}
		return $text;
	}
}
?>