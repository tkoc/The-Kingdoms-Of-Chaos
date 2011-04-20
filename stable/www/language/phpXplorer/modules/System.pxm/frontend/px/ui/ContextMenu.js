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
px.Class.define('px.ui.ContextMenu')

Object.extend(
	px.Statics,
	{
		oMenu: null,
		bActive: false,
		bOverMode: false,

		init: function()
		{
	 		this.oMenuShadow = document.createElement('div')
			this.oMenuShadow.id = 'contextMenu'
			this.oMenuShadow.className = 'pxCMShadow'
	 		document.body.appendChild(this.oMenuShadow)

	 		this.oMenu = document.createElement('div')
			this.oMenu.className = 'pxCM'
	 		this.oMenuShadow.appendChild(this.oMenu)

			this.hide()
		},

		dispose: function()
		{
			if (this.oMenuShadow)
			{
				this.clear()

	//			this.oMenuShadow.removeChild(this.oMenu)
	//			document.body.removeChild(this.oMenuShadow)

				//this.oMenu.onmouseout = null

				px.core.Object.dispose.call(this)

				//delete this.oMenu
				//delete this.oMenuShadow			
			}
		},
	
		clear: function() {
	 		while (this.oMenu.firstChild) {
	 			if (this.oMenu.firstChild.onclick) {
	 				this.oMenu.firstChild.onclick = null
				}
	 			this.oMenu.removeChild(this.oMenu.firstChild)
			}
		},
	
		addItem: function(sId, sLabel, sImage, bReadonly, sClass, sTitle)
		{
			var oA = document.createElement('a')
	
			oA.id = 'pxCM_' + sId
			this.oMenu.appendChild(oA)
	
			if (!bReadonly) {
				oA.href = pxConst.DUMMY_LINK
				oA.onclick = px.lang.Function.bindEvent(this._onItemClick, this)
			}
			
			if (sTitle) {
				oA.title = sTitle
			}
	
			if (sClass) {
				oA.className = sClass
			}
			
			if (sImage != '') {
				px.html.Element.appendImage(oA, sImage)
			}
	
			if (sLabel != '') {
				oA.appendChild(document.createTextNode(sLabel))
			}

			return oA
		},
	
		addDivider: function() {
			var oItem = document.createElement('div')
			oItem.className = 'pxCMDivider'
			this.oMenu.appendChild(oItem)
		},
		
		show: function(oEvent, iLeftOffset)
		{
			var bOffset = iLeftOffset ? true : false
			var iLeftOffset = iLeftOffset || 0

			var oElement = px.Event.element(oEvent)
			if (oElement.nodeName.toLowerCase() == 'img') {
				oElement = oElement.parentNode
			}
			var iTop = px.html.Element.getTopOffset(oElement) + oElement.offsetHeight - 1
			var iLeft = px.html.Element.getLeftOffset(oElement) - 1

			this.oMenuShadow.style.visibility = 'hidden'
			this.oMenuShadow.style.display = 'block'

			if (bOffset) {
				var iLeft = oEvent.clientX
				var iTop = oEvent.clientY
			}

			var iWidth = document.all ? document.body.offsetWidth : window.innerWidth
			var iHeight = document.all ? document.body.offsetHeight : window.innerHeight

			if (oEvent.clientX + this.oMenuShadow.offsetWidth > iWidth) {
				iLeft = iLeft - (oEvent.clientX + this.oMenuShadow.offsetWidth - iWidth) - 24
			}

			if (oEvent.clientY + this.oMenuShadow.offsetHeight > iHeight) {
				iTop = iTop - (oEvent.clientY + this.oMenuShadow.offsetHeight - iHeight) - 1
			}

			this.oMenuShadow.style.left = iLeft + iLeftOffset + 1 + 'px'
			this.oMenuShadow.style.top = iTop + 1 + 'px'
	
			this.oMenuShadow.style.visibility = ''
		},

		hide: function() {
			if (this.oMenu) {
				this.oMenuShadow.style.display = 'none'
			}
		},
		
		_onItemClick: function(oEvent)
		{
			var oElement = px.Event.element(oEvent)
			if (oElement.nodeName.toLowerCase() == 'img') {
				oElement = oElement.parentNode
			}
			var sId = oElement.id
			var sAction = sId.substr(sId.indexOf('_') + 1)

			var oSel = pxp.oSelectedControl

			if (oSel.callActionSub) {
				oSel.callActionSub(sAction, oEvent)
			} else {
				oSel.callAction(sAction, oEvent)
			}
			return false
		}
	}
)