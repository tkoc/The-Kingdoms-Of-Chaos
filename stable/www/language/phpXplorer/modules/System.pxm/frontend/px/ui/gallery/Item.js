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
px.Class.define('px.ui.gallery.Item',
{
	extend: px.ui.ListItem,

	construct: function() {
		this.iIndex
		this.oDiv
		this.oParent
		this.sFilename
		this.bDirectory
		this.oNameNode
	},

	destruct: function() {		
		var oDiv = this.oDiv
		oDiv.onclick = null
		oDiv.onmousedown = null
		oDiv.onmouseover = null
		oDiv.onmouseout = null
		oDiv.onmouseup = null
		oDiv.ondblclick = null
		
		//px.dev.MemoryLeak.check(oDiv)
		
		this.oNameNode.onclick = null
		this._disposeFields('oParent', 'oDiv', 'oImage', 'oNameNode')
	}
})

Object.extend(
	px.Proto,
	{
		selectItem: function(oEvent, bDrag) {
			var oParent = this.oParent
			if (oEvent.ctrlKey || bDrag) { 
				if (oParent.oSelected[this.sFilename] && !bDrag) {
					delete oParent.oSelected[this.sFilename]
				} else {
					oParent.oSelected[this.sFilename] = this
				}
			} else {
				oParent.oSelected = {}
				oParent.oSelected[this.sFilename] = this
			}
			oParent.setSelection()
		},
		
		itemMouseOver: function(oEvent)
		{
			oEvent.cancelBubble = true
			
			if (pxp.bIe) {
				px.html.Element.addClassName(this.oNameNode, 'hover')
			}
			if (
				pxp.bDragging &&
				this.bDirectory &&
				pxp.sDragType == 'item' &&
				!pxp.oSelectedControl.oSelected[this.sFilename] &&
				this.sFilename.indexOf(pxp.oSelectedControl.oActiveItem.sFilename) != 0 &&
				this.getType() != 'pxVirtualDirectory'
			) {
				this.oNameNode.className = 'drop'
			}
		},
		
		itemMouseOut: function() {
			if (pxp.bIe) {
				px.html.Element.removeClassName(this.oNameNode, 'hover')
			}
			px.html.Element.removeClassName(this.oNameNode, 'drop')
		},
		
		itemMouseUp: function(oEvent) {
			if (px.html.Element.hasClassName(this.oNameNode, 'drop')) {
				px.action.pxObject_editClipboard.itemDrop(this)
			}
		}
	}
)