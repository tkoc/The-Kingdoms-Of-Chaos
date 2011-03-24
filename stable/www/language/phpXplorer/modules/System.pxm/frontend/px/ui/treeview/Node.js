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
px.Class.define('px.ui.treeview.Node',
{
	extend: px.ui.ListItem,
	
	construct: function() {
		this.bExpanded = false
		this.bRendered = false
		this.bDirectory = false
		this.oDiv
		this.oParent
		this.sFilename
		this.iIndex = -1
		this.oNameNode
	},
	
	destruct: function()
	{		
		this.oDiv.onclick = null
		this.oDiv.onmouseover = null
		this.oDiv.onmouseout = null
		this.oDiv.onmousedown = null
		
		if (this.bDirectory) {
			this.oDiv.onmouseup = null
		}
		if (this.oDiv.firstChild.onclick) {
			this.oDiv.firstChild.onclick = null
		}
		if (this.oDiv.firstChild.onmouseover) {
			this.oDiv.firstChild.onmouseover = null
		}
		if (this.oDiv.childNodes[2].onclick) {
			this.oDiv.childNodes[2].onclick = null
		}

		this._disposeFields('oParent', 'oDiv', 'oNameNode')
	}
})

Object.extend(
	px.Proto,
	{		
		expand: function(oEvent)
		{
			var aChildren = this.oDiv.childNodes
		
			if (this.bExpanded) {
				for (var i=3, m=aChildren.length; i<m; i++) {
					aChildren[i].style.display = 'none'
				}
				this.oDiv.firstChild.src = pxConst.sGraphicUrl + '/expand.png'
			} else {
				if (this.bRendered) {
			  		for (var i=3, m=aChildren.length; i<m; i++) {
			  			aChildren[i].style.display = ''
			  		}
				} else {
					this.oParent.update({sPath: this.sFilename})
				}
				this.oDiv.firstChild.src = pxConst.sGraphicUrl + '/collapse.png'
			}
		
			this.bExpanded = !this.bExpanded
		
			if (oEvent) {
				oEvent.cancelBubble = true
			}
		},
		
		expandOver: function(oEvent) {
			if (pxp.bDragging && pxp.sDragType == 'item' && !this.bExpanded && this.oDiv.firstChild.src.indexOf('dummy') == -1) {
				this.expand(oEvent)
			}
		},

		linkClick: function(oEvent)
		{
			if (!oEvent.ctrlKey) {
				this.selectItem(oEvent)
		
				if (!this.bDirectory) {
					this.oParent.callAction(pxp.oTypes[this.getType()].aDefaultActions[0])
				}
		
				if (this.oParent.onNodeClick) {
					this.oParent.onNodeClick(this)
				}
			}
			oEvent.cancelBubble = true
			return false
		},

		selectItem: function(oEvent, bDrag) {
			var oParent = this.oParent					
			if (this == oParent.oRootItem) {
				return
			}
			if (oEvent.ctrlKey || bDrag) {
				if (oParent.oSelected[this.sFilename] && !bDrag) {
					delete oParent.oSelected[this.sFilename]
				} else {
					oParent.oSelected[this.sFilename] = this
				}
			} else {
				oParent.clearSelection()
				oParent.oSelected = {}
				oParent.oSelected[this.sFilename] = this
			}
			this.oDiv.childNodes[2].className = oParent.oSelected[this.sFilename] ? 'selected' : ''
		},
		
		nodeMouseOver: function(oEvent)
		{
			oEvent.cancelBubble = true
			
			if (!this.oParent.oSelected[this.sFilename]) {
		
				if (
					pxp.bDragging &&
					pxp.sDragType == 'item' &&
					!pxp.oSelectedControl.oSelected[this.sFilename] &&
					this.bDirectory &&
					this.getType() != 'pxVirtualDirectory'
				) {
					this.oDiv.childNodes[2].className = 'drop'
				} else {
					this.oDiv.childNodes[2].className = 'hover'
				}
			}
		},

		nodeMouseOut: function(oEvent) {
			oEvent.cancelBubble = true
			if (!this.oParent.oSelected[this.sFilename]) {
				this.oDiv.childNodes[2].className = null
			}
		},
		
		nodeMouseUp: function(oEvent) {
			if (px.html.Element.hasClassName(this.oDiv.childNodes[2], 'drop')) {
				px.action.pxObject_editClipboard.itemDrop(this)
			}
		}
	}
)