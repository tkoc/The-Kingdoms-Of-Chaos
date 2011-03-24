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
<?php

	function modColor($sColor, $iValue)
	{
		$sNewColor = '#';
		if (strpos($sColor, '#') === 0) {
			$sColor = substr($sColor, 1);
		}
		for ($i=0, $m=strlen($sColor); $i<$m; $i+=2) {
			$iNum = hexdec(substr($sColor, $i, 2) ) + $iValue;			
			if ($iNum < 0) $iNum = 0;
			if ($iNum > 255) $iNum = 255;
			$sNewColor .= ($iNum < 16 ? '0' : '') . dechex($iNum);
		}
		return strtoupper($sNewColor);
	}
	
	header('Content-Type: text/css');
	
#	define('BACKGROUND', '#DCDADA');
	#define('BACKGROUND', '#E2E0E0');
	#define('BACKGROUND', '#DCDADA');
	#define('BACKGROUND', '#DAD8D8');

	define('BACKGROUND', 'red');


#	define('BORDER_SHADOW', '#DDD');
#	define('BORDER_MEDIUM', '#EEE');

	define('BORDER_SHADOW', '#D0DDDD');
	define('BORDER_MEDIUM', '#EAE8E8');



#	define('ERROR', '#FF3399');
	define('ERROR', '#E28');

	define('TEXT', '#123456');
	
	define('TEXT_LIGHT', '#666');

	#define('DROP', '#a8d052');
	#define('DROP', '#a5d500');
	define('DROP', '#9C0');

	#define('SELECTION', '#316AC5');
	#define('SELECTION', '#73c6ef');
	#define('SELECTION', '#0066cc');
	define('SELECTION', '#009DDF');

	define('SERIF', '"Palatino Linotype", Georgia, serif');
	define('SANS_SERIF', 'Verdana, Tahoma, Arial, sans-serif');
	define('MONOSPACE', '"Lucida Console", monospace');

	define('FONT_SIZE', '68.75%');
	
#----------------------------------------------------------------
	
	define('BAR_MAIN_BG', '#525050');
	define('BAR_MAIN_FONT', '#F8F6F6');

	#define('BAR_GLOBAL_BG', '#CECCCC');
	define('BAR_GLOBAL_BG', '#D0CECE');
	define('BAR_GLOBAL_FONT', '#000');

	define('BAR_SHARE_BG', '#E2E0E0');
	define('BAR_SHARE_FONT', '#444');
	define('BAR_SHARE_BUTTON', '#222');

	define('BAR_LIST_BG', '#FAF8F8');
	define('BAR_LIST_FONT', '#666');
	
	define('BAR_FILE_BG', '#F2F0F0');
	define('BAR_FILE_FONT', '#000');
	
	define('BAR_ACTION_BG', '#FAF8F8');
	define('BAR_ACTION_FONT', '#444');

?>
/*<style*/

html, body {
	border: 0;
	height: 100%;
	margin: 0;
	overflow: auto;
	padding: 0;
	width: 100%;
	overflow: hidden;
}

body {
	color: <?=TEXT_LIGHT?>;
	font-size: <?=FONT_SIZE?>; 
}

::-moz-selection {
	background-color: <?=SELECTION?>;
	color: white;
}

#main {
	overflow: hidden;
	position: relative;
	background-color: <?=BAR_GLOBAL_BG?>;
}

.noSelect,
.pxTb,
#dragColumn,
div.pxTab,
.checklist,
table.pxPropertyview td.label,
table.pxPropertyview td.group
{
	/*-moz-user-focus: ignore;*/
  /*-moz-user-input: disabled;*/
	-moz-user-select: none;
	/*user-select: none;*/
	/* -khtml-user-select: none */
}

* {
	font-family: <?=SANS_SERIF?>;
}

a, span {
	text-decoration: none;
	color: <?=TEXT?>;
	padding: 0.181em;
}

img {
	border: none;
}

form {
	height: 100%;
	margin: 0;
	padding: 0;
	width: 100%;
}

table {
	font-size: 1em;
}

input, select, textarea {
	color: <?=TEXT?>;
	/*font-size: 1em;*/
	/*vertical-align: middle;*/
}

textarea {
	line-height: 1.545em;
}

textarea.pxSearch,
#infoBox textarea,
textarea.pxTextview {
	font-family: <?=MONOSPACE?>;
	color: black;
	font-size: 1.181em;
}

label {
	cursor: pointer;
}

iframe {
	border: 0;
	height: 100%;
	width: 100%;
	z-index: 1;
}

ul {
	list-style: none;
}

h1, h2, h3, h4 {
	margin: 0;
	padding: 0;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	LIGHTBOX
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

#overlay {
	background-image: url(./graphics/overlay.png);
	cursor: pointer;
	display: block;
	height: 100%;
	left: 0;
	position: absolute;
	top: 0;
	width: 100%;
	z-index: 100;
}

#lightbox {
	background-color: white;
	cursor: pointer;
	position: absolute;
	padding: 8px;
	z-index: 102;
}

* html #overlay {
	z-index: 100;
	background-color: #333;
	back\ground-color: transparent;
	background-image: url(blank.gif);
}

#previousImage, #nextImage {
	position: absolute;
	display: block;
	z-index: 103;
	padding: 0;
	-moz-opacity: 0.8;
}

#loading {
	left: 43.75%;
	position: absolute;
	top: 50%;
	z-index: 103;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	INFOBOX
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

#infoBox {
	position: absolute;
	z-index: 104;
}

#cachePane,
#cachePane iframe {
	border: 0;
	height: 100%;
	padding: 0;
	width: 100%;
}

div.infoBoxPane {
	background-color: white;
	border: 1px solid <?=BORDER_MEDIUM?>;
	clear: both;
	height: 100%;
	line-height: 2.090em;
	overflow: auto;
	padding: 1em;
	width: 100%;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	LISTVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.scrollBar {	
	height: 100%;
	overflow: auto;
	overflow-x: hidden;
	overflow-y: scroll;
	position: absolute;
	right: 0;
	top: 0;
	z-index: 1000;
}

table.pxListview {
	font-size: <?=FONT_SIZE?>;
	font-size: 1em;
	table-layout: fixed;
	width: 100px;
	margin-right: 20px;
	margin-left: 0.545em;
	margin-top: 0.181em;
}

table.pxListview tr {
	cursor: pointer;
	line-height: 2.090em;
}

#dragColumn {
	line-height: 2.090em;
}

table.pxListview th,
table.pxListview td,
#dragColumn
{
	overflow: hidden;
	padding-right: 0.454em;
	padding-left: 0.454em;
	white-space: nowrap;
}

#dragable {
	background-color: <?=SELECTION?>;
	color: white;
	line-height: 2.090em;
	margin: 0;
	padding: 0 0.818em 0 0.636em;
	position: absolute;
	z-index: 200;
}

table.pxListview tr th, #dragColumn {
	color: #AAA;
	font-weight: normal;
	text-align: left;
	padding-top: 0.272em;
}

table.pxListview th {
	background-repeat: repeat-x;
	background-image: url(./graphics/header.png);
	background-position: bottom;
}

table.pxListview th.over {
	background-repeat: repeat-x;
	background-image: url(./graphics/headerResize.png);
	background-position: right bottom;
}

table.pxListview tr.hover,
table.pxListview tr:hover,
div.pxTreeview a.hover,
div.pxCM a:hover,
a.hover
{
	background-color: #F4F2F2;
}

table.pxListview tr.header:hover {
	background-color: transparent;
}

table.pxListview th.drop {
	background-repeat: repeat-x;
	background-image: url(./graphics/headerDrop.png);
	background-position: left bottom;
}

#dragColumn {
	cursor: move;
	position: absolute;
	background-color: <?=SELECTION?>;
	color: white;
}

img.orderIndicator {
	margin-left: 0.272em;
	vertical-align: text-bottom;
}

table.pxListview th.icon {
	padding: 0;
	margin: 0;
	/*text-align: center;*/
	width: 16px;
}

table.pxListview th.checkbox {
	padding: 0;
	margin: 0;
	width: 23px;
	text-align: left;
}

table.pxListview td.icon {
	/*text-align: center;*/
	padding: 0;
	margin: 0;
	width: 16px;
}

table.pxListview td.icon img {
	vertical-align: top;
	height: 16px;
	width: 16px;
}

table.pxListview th {
	padding-bottom: 0.636em;
}


/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	SPLITVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxSplitV, div.pxSplitH {
	overflow: hidden;
	position: relative;
}

div.pxSplitV {
	float: left;
}

div.pxSplitSeparatorV, div.pxSplitSeparatorH {
	font-size: 1px;
	background-color: transparent;
}

div.pxSplitSeparatorH {
	height: 5px;
}

div.pxSplitSeparatorV {
	width: 5px;
	cursor: w-resize;
	float: left;
}

div.pxSplitSeparatorH {
	cursor: s-resize;
}

div.fileActionBar div.pxSplitSeparatorV,
div.fileActionBar div.pxSplitSeparatorH {
	background-color: <?=BAR_SHARE_BG?>;
}

div.fileActionBar div.pxSplitV,
div.fileActionBar div.pxSplitH {
	overflow: auto;
}

div.fileActionBar div.drag {
	background-color: <?=SELECTION?>;
}

div.pxSplitToolbar {
	position: absolute;
	z-index: 40;
}

div.pxSplitToolbar .pxTb {
	margin-left: 0.272em;
}

div.pxSplitToolbarVertical .pxTb {
	display: block;
	float: none;
	margin-bottom: 0.545em;
	margin-left: 0;
}

#pxSplitSelectionFrame1 {
	background-color: <?=BAR_LIST_BG?>;
	border-top: 1px solid <?=modColor(BAR_SHARE_BG, -16)?>;
}

#pxSplitListSeparator {
	background-color: <?=BAR_SHARE_BG?>;
	
}

#pxSplitListFrame2 {
	background-image: url(./graphics/background.png);
	background-position: bottom right;
	background-repeat: no-repeat;
	background-color: white;
}


#pxSplitGlobalSeparator {
	background-color: <?=BAR_GLOBAL_BG?>;
}

#pxSplitGlobalSeparator.drag,
div.drag {
	background-color: <?=SELECTION?>;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	TREEVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxTreeview {
	margin: 0.818em;
	text-align: left;
	white-space: nowrap;
}

div.pxTreeview img {
	width: 16px;
	height: 16px;
	margin-right: 0.363em;
	vertical-align: text-bottom;
}

div.pxTreeviewNode {
	padding-top: 0.545em;
	/*padding-top: 0.636em;*/
	font-family: <?=SANS_SERIF?>;
	cursor: pointer;
	text-align: left;
}

div.pxTreeviewNode input {
	position: absolute;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	ACTIONVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxTbHorizontal a.pxBarButton img {
	margin-right: 0.727em;
}


div.pxAction {
	background-color: white;
	overflow: hidden;
	text-align: left;
	position: relative;
	width: 100%;
	clear: both;
	outline: none;
}

div.pxBar {
	border-bottom: 1px solid <?=BORDER_MEDIUM?>;
	width: 100%;
	overflow: hidden;
	position: relative;
	white-space: nowrap;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

.pxTitle {
	font-weight: bold;
}

.pxTitle img {
	margin-right: 3px;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.globalBarMenu {
	background-color: <?=BAR_GLOBAL_BG?>;
	border-bottom: 1px solid <?=modColor(BAR_GLOBAL_BG, -16)?>;
	height: 2.272em;
}

div.globalBarMenu a.pxTb {
	padding: 0.545em 0.454em 0.363em 0.454em;
	color: <?=BAR_GLOBAL_FONT?>;
}

div.globalBarMenu a.pxTb:hover {
	background-color: <?=modColor(BAR_GLOBAL_BG, 32)?>;
	color: #000;
}

div.globalBar {
	overflow: auto;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.mainBar {
	background-color: <?=BAR_MAIN_BG?>;
	/*border-top: 1px solid <?=modColor(BAR_MAIN_BG, -16)?>;*/
	border-bottom: 1px solid <?=modColor(BAR_MAIN_BG, -16)?>;
}

div.mainBar a.pxTb:hover {
	background-color: <?=modColor(BAR_MAIN_BG, -32)?>;
	color: white;
}

div.mainBar a.pxTbDisabled:hover {
	background-color: transparent;
}

div.mainBar .pxTb,
div.mainBar a {
	color: <?=BAR_MAIN_FONT?>;
	background-color: <?=BAR_MAIN_BG?>;
	padding: 0.545em 0.454em 0.363em 0.454em;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.shareBarMenu {
	border: 0;
	background-color: <?=BAR_SHARE_BG?>;
}

div.shareBarMenu a.pxTb {
	color: <?=BAR_SHARE_FONT?>;
	padding: 0.545em 0.454em 0.545em 0.454em;
}

div.shareBarMenu a.pxBarButton {
	color: <?=BAR_SHARE_BUTTON?>;
	padding-left: 0.454em;
	padding-bottom: 0.272em;
	margin-top: 0;
}

div.shareBarMenu a.pxBarButton img {
	margin-right: 0.636em;
}

div.shareBarMenu a.pxTb:hover {
	background-color: <?=modColor(BAR_SHARE_BG, 32)?>;
	color: #222;
}

a.pxTbSearch:hover {
	background-color: #E8E6E6;
}

div.shareBar {
	background-color: <?=BAR_SHARE_BG?>;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.listBarMenu {
	padding: 0.727em 0 0.636em 0.454em;
	background-color: <?=BAR_LIST_BG?>;
	border-top: 1px solid <?=modColor(BAR_SHARE_BG, -16)?>;
	border-bottom: 1px solid <?=modColor(BAR_LIST_BG, -16)?>;
}

div.listBarMenu a {
	color: <?=BAR_LIST_FONT?>;
	border: 1px solid <?=BAR_LIST_BG?>;
}

div.listBarMenu a:hover {
	border: 1px solid <?=modColor(BAR_LIST_BG, -32)?>;
	color: #444;
}

div.listBar {
	/*overflow: hidden;*/
	overflow-x: auto;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.fileBarMenu {
	background-color: transparent;
	background-color: <?=BAR_FILE_BG?>;
	border-bottom: 1px solid <?=modColor(BAR_FILE_BG, -16)?>;
	border-top: 1px solid <?=modColor(BAR_SHARE_BG, -16)?>;
}

div.pxBarDisabled {
	background-color: transparent;
	border: 0;
	/*border-top: 1px solid <?=modColor('FFFFFF', -48)?>;*/
}

div.fileBarMenu a.pxTb {
	padding: 0.545em 0.454em 0.454em 0.454em;
	margin-right: 0.8em;
	color: #222;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.fileActionBar {
	overflow: auto;
}

div.fileActionBarMenu {
	border-bottom: 1px solid <?=modColor(BAR_ACTION_BG, -16)?>;
	background-color: <?=BAR_ACTION_BG?>;
	/*margin-bottom: 1px;*/
}

div.fileActionBarMenu a.pxTb {
	padding: 0.545em 0.454em 0.454em 0.454em;
	padding: 0.727em 0.454em 0.636em 0.454em;
	color: <?=BAR_ACTION_FONT?>;
}

div.fileActionBarMenu a.pxTb:hover {
	background-color: <?=modColor(BAR_ACTION_BG, 32)?>;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.selectionBarMenu {
	margin-top: 3.454em;
	overflow: visible;
	float: left;
}

div.selectionBar {
	overflow: auto;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

#upload div.mainBar {
	padding: 0.363em 0.545em 0.272em 0.272em;
	padding-bottom: 0.272em;
	color: white;
}

#upload div.mainBar .pxTb {
	padding-top: 0.272em;
}

#upload p {
	padding: 1em;
	padding-left: 0.727em;

}

#upload p span {
	padding: 0;
}

#upload .error {
	background-color: <?=ERROR?>;
	color: white;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	TOOLBAR
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxTbDivider {
	float: left;
	margin-right: 0.6em;
}

.pxTb {
	display: block;
	float: left;
}

.pxTb img {
	vertical-align: top;
}

div.pxTbVertical a.pxTb {
	text-align: center;
	margin-right: 1em;
}

div.pxTbVertical a.pxTb img.icon {
	display: block;
	margin: auto;
	margin-bottom: 0.2em;
}

a.pxTbSearch {
	padding-top: 0.454em;
	padding-bottom: 0.272em;
	float: right;
	padding-right: 0.818em;
	padding-left: 0.363em;
}

a.pxTbSearch img.icon {
	display: none;
	margin-right: 0.4em;
}

a.pxTbSearch * {
	float: left;
}

a.pxTbSearch input {
	border: 1px solid #E2E0E0;
	border: 0;
	background-color: #FFF;
	color: #666;
	padding-left: 0.2em;
	margin-right: 0.2em;
}

a.pxTbSearchActive input {
	background-color: #FFFFCC;
}

a.pxTbSearchActive img.icon {
	display: inline;
}


a.pxTb:hover {
	background-color: #F8F8F8;
}


/*
div.fileActionBarMenu a.pxTb:hover {
	background-color: #F4F2F2;
}
*/

a.pxTbDisabled {
	cursor: auto;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	CONTEXT MENU
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxCMShadow {
	position: absolute;
	z-index: 50;
	top: -800px;
	left: -800px;
}

div.pxCM {
	z-index: 51;
	padding: 0.363em 0 0.363em 0;
	background-color: white;
	border: 1px solid #AAA;
	white-space: nowrap;
}

div.pxCMDivider {
	border-top: 1px dotted #D2D0D0;
	margin: 0.454em;
	clear: both;
}

div.pxCM a {
	display: block;
	padding: 4px 8px 4px 6px;
}

div.pxCM a img  {
	vertical-align: top;
	margin-right: 0.818em;
}

div.pxCM a.pxCMSelected {
	font-weight: bold;
}

div.pxCM a.pxCMLoaded {

}

div.pxCM a.pxCMToolbar {
	display: block;
	float: left;
	padding: 0.636em 0.818em 0.636em 0.727em;
	position: relative;
	
}

div.pxCM a.pxCMToolbar img {
	margin: 0;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	PROPERTYVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*
div.pxPropertyview {
	background-color: #F8F6F6;
}
*/

table.pxPropertyview {
	margin: 0.454em 0 3em 0.454em;
	width: 97%;
}

table.pxPropertyview input,
table.pxPropertyview select,
table.pxPropertyview textarea {
/*
	border-style: solid;
	border-color: #F8F1D8;
	border-color: #f4f2f2;
	border-width: 0.181em 0.181em 0.181em 0.272em; 
	background-color: #F8F1D8;
	background-color: #f4f2f2;
*/
}

table.pxPropertyview td {
	padding: 0.181em;
}

table.pxPropertyview td.label {
	vertical-align: top;
	padding-left: 2.181em;
	white-space: nowrap;
	width: 10%;
}

table.pxPropertyview td.value {
	color: <?=TEXT?>;
	padding-left: 0.454em;
}

table.pxPropertyview tr.error td.label {
	background-color: <?=ERROR?>;	
	color: white;
	cursor: help;
}

table.pxPropertyview tr {
	line-height: 1.818em;
}

table.pxPropertyview textarea {
	font-size: 1em;
}

table.pxPropertyview td.group a {	
	color: #06C;
/*	display: block;*/
}

table.pxPropertyview td.first {
	padding-top: 0.272em;
}

table.pxPropertyview td.last {
	padding-bottom: 2em;
}

table.pxPropertyview td.group a img {
	vertical-align: middle;
}


/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	TAGS
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

.checklist,
.checklist li {
	margin: 0;
	padding: 0;
	white-space: nowrap;
	overflow: hidden;
}

.checklist input {
	vertical-align: middle;
}

.checklist {
	border: 1px solid #D2D0D0;
	list-style: none;
	overflow: auto;
}

.checklist label {
	display: block;
	height: 1%;
	padding: 0.272em 0.454em 0.272em 25px;
	text-indent: -25px;
}

.checklist label input {
	margin-left: 0.545em;
	margin-right: 0.818em;
}

.checklist label:hover,
.checklist label.hover
{
	background-color: <?=BACKGROUND?>;
	background-color: #F4F2F2;
}

.checklist label.active {
	background-color: #FFFFCC;
	/*background-color: black;*/
	/*color: white;*/
}

.checklist label.drop {
	background-color: <?=DROP?>;
	color: white;
}

.landscape {
	float: left;
	margin: 0em;
	height: auto;
	border: 0;
}

.landscape li,
.landscape label
{
	display: inline;
	float: left;
	margin-right: 1em;
}

.checklistFloat li {
	float: left;
}

/*
div.pxTagList {
	padding: 1em;
}
*/

div.pxTagList .checklist {
	border: 0;
	margin: 1em;
	background-color: white;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	TEXTEDITOR
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

textarea.pxTextview {
	border: none;
	color: black;
	height: 100%;
	width: 100%;
}

form.pxTextview {
	height: 100%;
	overflow: hidden;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	TABVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxTab {
	color: #666;
	cursor: pointer;
	float: left;
	position: relative;
	margin: 3px -1px -1px 0;
	padding: 5px 8px 5px 8px;
	z-index: 2;
}

div.pxSelectedTab {
	background-color: white;
	border-right: 1px solid #EAE8E8;
	border-left: 1px solid #EAE8E8;
/*	margin: 0 -1px -1px -2px; */
	margin: 0 -1px -1px 0;
	padding-top: 7px;
	padding-bottom: 6px;
	z-index: 3;
}

div.selectionBarMenu div.pxSelectedTab {
	border-top: 1px solid #EAE8E8;
}

/*
div.pxFirstTab {
	margin-left: 0;
	border-left: 0;
}
*/

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

#infoBox div.pxTab {
	background-color: #EEE;
}

#infoBox div.pxSelectedTab {
	background-color: white;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	LOGIN
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

#login {
	position: absolute;
	z-index: 1000;
	background-color: #999;
	top: 48%;
	left: 48%;
	border: 1px solid #444;
	padding: 2em;
	overflow:auto;
}

#login input {
	
}

#login * {
	display: block;
}

/*

#loginTable {
	height: 100%;
	text-align: center;
	width: 100%;
}

#loginTable table {
	margin: auto;
}


div.loginBarMenu div.pxTab {
	background-color: #F4F2F2;
}

div.loginBarMenu div.pxSelectedTab {
	background-color: white;
}
*/
/*
div.loginBarMenu div.pxFirstTab {
	margin-left: -1px;
}
*/

/*
div.loginBarMenu div.pxTab {
	color: white;
}

div.loginBarMenu div.pxSelectedTab {
	color: <?=TEXT?>;
}
*/
/*
div.loginBar {
	padding-bottom: 0.6em;
	padding-top: 1em;
	border-bottom: 1px solid <?=BORDER_MEDIUM?>;
	border-right: 1px solid <?=BORDER_MEDIUM?>;
	overflow: auto;
}

#loginContainer table {
	text-align: left;
}

#loginContainer div.error {
	font-family: <?=SERIF?>;
}
*/
/*

#loginContainer label {
	font-size: 0.8em;
	color: #AAA;
}

#loginContainer input {
	border: 0;
	border-bottom: 1px dashed #AAA;
	font-family: <?=MONOSPACE?>;
	font-size: 0.8em;
	height: 1.2em;
	width: 16em;
}

#loginContainer button {
	font-family: <?=SERIF?>;
	background-color: transparent;
	color: #699A00;
	border: 0;
	cursor: pointer;
	margin: 0;
	padding: 0;
}
*/

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	GALLERY VIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxGalleryview {
	cursor: pointer;
	overflow: hidden;
	position: relative;
	padding: 5px;
}

div.pxGalleryviewFloat div.pxGalleryview {
	float: left;
	text-align: center;
}

div.pxGalleryview input {
	width: 90%;
}

div.pxGalleryview img {
	display: block;
}

div.pxGalleryviewFloat div.pxGalleryview img {
	margin: auto;
}

div.pxGalleryview a {
	color: #999;
	white-space: nowrap;
}

div.pxGalleryview a.hover {
	color: #123456;
}

div.pxGalleryview a.selected,
div.pxGalleryview a.drop,
div.pxGalleryview a.drop:hover
{
	color: white;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	XPLORER VIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

form.xplorerview {
	margin-top: 0.454em;
	height: auto;
}

div.xplorerNode {
	white-space: nowrap;
	padding: 0.272em;
	padding-bottom: 0.181em;
}

div.xplorerNode div.firstNode {
	padding-top: 0.454em;
}

div.xplorerNode div.xplorerNode {
	margin-left: 1.81em;
}

div.xplorerNode img {
	margin-left: 0.454em;
	margin-right: 0.454em;
	vertical-align: middle;
}

div.xplorerNode input {
	vertical-align: middle;
}

div.xplorerNode img.expand {
	cursor: pointer;
}

div.selected div a {
	background-color: <?=SELECTION?>;
	color: white;
}

div.pxXplorerview label {
	border-bottom: 1px solid <?=BORDER_MEDIUM?>;
	display: block;
	line-height: 2.090em;
	padding-left: 0.2em;
}

div.pxXplorerview textarea {
	border: 0;
	position: relative;
	font-family: <?=MONOSPACE?>;
	overflow: auto;
	font-size: 1.181em;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	HOMEPAGE
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxHomepage {
	line-height: 2em;
	position: relative;
}

div.pxHomepage .pxTb {
	float: none;
}

div.pxHomepage .pxToolbar {
	margin-bottom: 2em;
	padding-left: 1em;
}

div.pxHomepage #logo {
	display: block;
}

#SharesList {
	
}


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.pxDebug textarea {
	height: 100%;
	width: 100%;
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

div.contentFrame {
	padding: 1.272em;
	position: relative;
}

/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	FACETVIEW
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


div.pxFacetview {

}

div.pxFacet div.resize {
	background-color: <?=BAR_FILE_BG?>;
	height: 5px;
	cursor: ns-resize;
}

div.pxFacet div.drag {
	background-color: <?=SELECTION?>;
}

div.facetBarMenu {
	background-color: <?=BAR_FILE_BG?>;
	border-bottom: 1px solid <?=modColor(BAR_FILE_BG, -16)?>;
	border-top: 1px solid <?=modColor(BAR_SHARE_BG, -16)?>;
}

div.facetBarMenu a.pxTb {
	padding: 0.545em 0.454em 0.363em 0.454em;
	margin-right: 0.8em;
	color: #222;
}

div.facetBar {
	/*overflow: hidden;*/
	overflow-x: auto;
}


/*
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	MIXED
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
a.selected {
	background-color: <?=SELECTION?>;
	color: white;
}

a.active {
	font-weight: bold;
}

a.drop,
div.drop,
div.fileBarMenu a.drop
{
	background-color: <?=DROP?>;
	color: white;
}

a.drop:hover {
	background-color: <?=DROP?>;
	color: white;
}

a.pxTbDisabled:hover {
	background-color: inherit;
}


div.listBarMenu a.pxTbDisabled {
	color: #C2C0C0;
}