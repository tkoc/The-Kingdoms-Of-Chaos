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
px.Class.define('px.ui.ListItem',
{
	extend: px.core.Object,
	type: 'abstract'
})

Object.extend(
	px.Proto,
	{
		getFilename: function() {
			return px.util.basename(this.sFilename)
		},

		getDirname: function() {
			return px.util.dirname(this.sFilename)
		},

		getPath: function() {
			if (this.oParent.oParameters.sSearchQuery || this.oParent.oParameters.bRecursiveFlat) {
				return this.oParent.oParameters.getPath()
			} else {
				return px.util.buildPath(
					this.oParent.oParameters.sShare + ':',
					this.getDirname()
				)
			}
		},

		getType: function() {
			if (this.sFilename == '/') return 'pxDirectory'
			return this.oParent.oResults[this.getPath()][this.iIndex].sType
		},

		getExtension: function() {
			return this.oParent.oResults[this.getPath()][this.iIndex].sExtension
		},

		getShare: function() {
			return this.oParent.oParameters.sShare
		},

		move: function(oEvent) {
			var oDragable = $('dragable')
			if (!oDragable) {
				oDragable = document.createElement('ul')
				oDragable.id = 'dragable'
				document.body.appendChild(oDragable)
				for (var sSelected in this.oParent.oSelected) {
					var oRow = this.oParent.oSelected[sSelected]
					var oItem = document.createElement('li')
					oDragable.appendChild(oItem)
					oItem.appendChild(document.createTextNode(oRow.getFilename()))
				}
			}
			oDragable.style.top = oEvent.clientY + 10 + 'px'
			oDragable.style.left = oEvent.clientX + 10 + 'px'
			oDragable = null
		},
	
		mouseDown: function(oEvent)
		{
			oEvent.cancelBubble = true
			if (pxp.bHold) {
				return false
			}

			this.selectItem(
				oEvent,
				px.Event.element(oEvent).nodeName.toLowerCase() == 'a' &&
				this.oParent.oSelected[this.sFilename] && !oEvent.ctrlKey
			)
	
			this.oParent.oActiveItem = this
			pxp.setActiveControl(this.oParent)
	
			pxp.startDrag('item')
			document.onmousemove = px.lang.Function.bindEvent(this.move, this)
		},
	
		itemClick: function(oEvent)
		{
			oEvent.cancelBubble = true

			var oParent = this.oParent
			
			if (oParent.bChecklist) {
				return false
			}

			var sPath = this.getPath()
			var sType = oParent.oResults[sPath][this.iIndex].sType

			oParent.showContextMenu(
				this,
				oEvent,
				sType,
				oParent.oSettings[sPath].aAllowedActions[sType]
			)
		},

		itemDblClick: function(oEvent) {
			px.ui.ContextMenu.hide()
			this.fileClick(oEvent)
		},

		fileClick: function(oEvent) {
			oEvent.cancelBubble = true
			if (pxp.bHold) {
				return false
			}
			var oParent = this.oParent
			if (!oEvent.ctrlKey && (!oParent.bChecklist || this.bDirectory)) {
				pxp.bHold = true
				if (this.bDirectory) {
					oParent.oSelected = {}
					oParent.oParameters.sPath = this.sFilename
					oParent.oParameters.sSearchQuery = null
					oParent.update({_bReload: false})
					if (oParent.onDirChange) oParent.onDirChange()
				} else {
					this.selectItem(oEvent)
					oParent.callAction(pxp.oTypes[this.getType()].aDefaultActions[0], oEvent)
				}
				window.setTimeout('pxp.bHold=false', 250)
			}
			return false
		}
	}
)