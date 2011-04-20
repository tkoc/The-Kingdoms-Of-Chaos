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

echo '<?xml version="1.0" encoding="ISO-8859-1"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<title></title>

<script type="text/javascript" src="./../frontend/px/px.js"></script>
<script type="text/javascript" src="./../frontend/px/html/Element.js"></script>
<script type="text/javascript" src="./../frontend/px/lang/Array.js"></script>
<script type="text/javascript" src="./../frontend/px/lang/Function.js"></script>
<script type="text/javascript" src="./../frontend/px/Event.js"></script>

<style type="text/css">

#image1, #image2 {
}

body {
	margin: 0;
	padding: 0;
}

.hidden {
	display: none;
}

.drag {
	position: absolute;
	z-index: 1000;
	background-color: transparent;
}

div.dragger {
	position: absolute;
	width: 10px;
	height: 10px;
	border: 1px solid black;
	background-color: white;
	font-size: 1px;
	z-index: 1001;
}

</style>
<script type="text/javascript" language="JavaScript">

function $(sId) {
	return document.getElementById(sId)
}

//var oTargetElement = 
var sImageUrl = opener.pxp.oData['temp.sImageUrl']
var oImage

function _cancelEvent(oEvent) {
	var oEvent = oEvent || window.event
	if (oEvent.preventDefault) oEvent.preventDefault()
	oEvent.returnValue = false
	oEvent.cancelBubble = true
	return false
}

function ImageClipper()
{
	var cElement = px.html.Element
	var cFunction = px.lang.Function

	document.body.onselectstart = _cancelEvent
	document.body.ondragstart = _cancelEvent

	this.oTransImage = $('image1')
	this.oTransImage.src = sImageUrl

	cElement.setOpacity(this.oTransImage, '0.4')

	this.oTransImage.onSelectStart = function () {
		return false
	}

	this.oSelectionImage = $('image2')
	this.oSelectionImage.src = sImageUrl
	cElement.addClassName(this.oSelectionImage, 'drag')

	this.oDrag1 = document.createElement('div')
	this.oDrag1.setAttribute('id', 'drag1')
	cElement.addClassName(this.oDrag1, 'dragger')
	cElement.setOpacity(this.oDrag1, '0.7')
	
	this.oDrag2 = document.createElement('div')
	cElement.addClassName(this.oDrag2, 'dragger')
	this.oDrag2.setAttribute('id', 'drag2')

	this.oDrag3 = document.createElement('div')
	cElement.addClassName(this.oDrag3, 'dragger')
	this.oDrag3.setAttribute('id', 'drag3')

	this.oDrag4 = document.createElement('div')
	cElement.addClassName(this.oDrag4, 'dragger')
	this.oDrag4.setAttribute('id', 'drag4')

	document.body.appendChild(this.oDrag1)
	document.body.appendChild(this.oDrag2)
	document.body.appendChild(this.oDrag3)
	document.body.appendChild(this.oDrag4)

	this.iX = Math.round(this.oTransImage.width * 0.1)
	this.iY = Math.round(this.oTransImage.height * 0.1)
	this.iWidth = Math.round(this.oTransImage.width * 0.8)
	this.iHeight = Math.round(this.oTransImage.height * 0.8)

	this.oSelectionImage.style.left = '0'
	this.oSelectionImage.style.top = '0'

	this.oDrag1.onmousedown = cFunction.bindEvent(this._startDrag, this)
	this.oDrag1.onmouseup = cFunction.bindEvent(this._stopDrag, this)

	this.oDrag2.onmousedown = cFunction.bindEvent(this._startDrag, this)
	this.oDrag2.onmouseup = cFunction.bindEvent(this._stopDrag, this)

	this.oDrag3.onmousedown = cFunction.bindEvent(this._startDrag, this)
	this.oDrag3.onmouseup = cFunction.bindEvent(this._stopDrag, this)

	this.oDrag4.onmousedown = cFunction.bindEvent(this._startDrag, this)
	this.oDrag4.onmouseup = cFunction.bindEvent(this._stopDrag, this)

	//this.oSelectionImage.style.clip = 'rect(' + this.iY + 'px, ' + this.iWidth + 'px, ' + this.iHeight + 'px, ' + this.iX + 'px)'
	
	this.updateSelection()
	
	cElement.removeClassName(this.oSelectionImage, 'hidden')
}

ImageClipper.prototype.updateSelection = function()
{	
	this.oSelectionImage.style.clip = 'rect(' + this.iY + 'px, ' + this.iWidth + 'px, ' + this.iHeight + 'px, ' + this.iX + 'px)'

	this.oDrag1.style.left = (this.iX - 5) + 'px'
	this.oDrag1.style.top = (this.iY - 5) + 'px'
	this.oDrag2.style.left = (this.iWidth - 5) + 'px'
	this.oDrag2.style.top = (this.iY - 5) + 'px'
	this.oDrag3.style.left = (this.iX - 5) + 'px'
	this.oDrag3.style.top = (this.iHeight - 5) + 'px'
	this.oDrag4.style.left = (this.iWidth - 5) + 'px'
	this.oDrag4.style.top = (this.iHeight - 5) + 'px'
	
	var iFactor = 1
	
	$('coordinates').value = (this.iX * iFactor) + ',' + (this.iY * iFactor) + '-' + ((this.iWidth) * iFactor) + ',' + ((this.iHeight) * iFactor)
}

ImageClipper.prototype._startDrag = function(oEvent) {
	this.oActiveDrag = px.Event.element(oEvent)
	this.oActiveDragNumber = Number(this.oActiveDrag.id.substr(4, 1))
	document.body.onmousemove = px.lang.Function.bindEvent(this.drag, this)
}

ImageClipper.prototype._stopDrag = function(oEvent) {
	document.body.onmousemove.onmousemove = null
	this.oActiveDrag = null
	this.oActiveDragNumber = -1
	this.updateSelection()
}

ImageClipper.prototype.getCoordinates = function () {
	return $('coordinates').value
}

ImageClipper.prototype.drag = function(oEvent) {
	switch (this.oActiveDragNumber) {
		case 1:
			this.iX = px.Event.pointerX(oEvent)
			this.iY = px.Event.pointerY(oEvent)
			break
		case 2:
			this.iWidth = px.Event.pointerX(oEvent)
			this.iY = px.Event.pointerY(oEvent)
			break
		case 3:
			this.iX = px.Event.pointerX(oEvent)
			this.iHeight = px.Event.pointerY(oEvent)
		 break
		case 4:
			this.iWidth = px.Event.pointerX(oEvent)
			this.iHeight = px.Event.pointerY(oEvent)
			break
	}
	this.updateSelection()
}

var oClipper

function loaded() {
	var oImage = $('image1')
	oImage.onload = null
	$('image2').src = oImage.src
	oClipper = new ImageClipper()
}

function init() {
	var oImage = $('image1')
	oImage.onload = loaded
	$('image1').src = sImageUrl	
}

function ok() {
	opener.pxp.oData['temp.oTargetElement'].value = oClipper.getCoordinates()
	window.close()
}

</script>
</head>
<body onload="init()">

<img src="" alt="" id="image1" />
<img src="" alt="" id="image2" class="hidden" />
<br/>
<input type="text" id="coordinates" value="" />
<button onclick="ok()">übernehmen</button>
</body>
</html>