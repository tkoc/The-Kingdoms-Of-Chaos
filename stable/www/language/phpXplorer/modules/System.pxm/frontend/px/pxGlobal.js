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
var iDragOffset = 0
var oActiveItem

function $(sId) {
	return document.getElementById(sId)
}

function pxInit()
{
	pxp.init($('main')) // document.body

;;;	document.title = new Date().getTime() - iStart

	//alert(pxp.iPhpRuntime)
//	pxp.init($('cont'))
}

function pxUnload()
{
	delete oActiveItem

	pxp.oSelectedControl = null

	px.ui.ContextMenu.dispose()

	var aActions = px.action
	for (var a in aActions) {
		if (aActions[a].dispose) {
			aActions[a].dispose()
		}
	}

	var oOverlay = $('overlay')
	if (oOverlay) oOverlay.onclick = null
	if (pxp.bIe) {
		for (var i=0; i<document.images.length; i++) {
			if (document.images[i].style.filter) {
				document.images[i].style.filter = pxConst.EMPTY
			}
		}
		if (oOverlay && oOverlay.style.filter) {
			oOverlay.style.filter = pxConst.EMPTY
		}
	}

	pxp.dispose()

// pxp.checkSubTree(document.body)
//	pxp.checkSubTree($('cont'))

	delete pxp
	delete px	
}


px.Event.addListener(window, 'load', pxInit)
px.Event.addListener(window, 'unload', pxUnload)
px.Event.addListener(window, 'unload', px.Event.unloadCache);