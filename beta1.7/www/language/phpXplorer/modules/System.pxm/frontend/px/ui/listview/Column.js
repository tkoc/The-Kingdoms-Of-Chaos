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
px.Class.define('px.ui.listview.Column',
{
	extend: px.core.Object,
  
  construct: function(oParent, sId, iWidth, sType, sAlign, oFormat, bTemp)
  {
		this.oParent = oParent
		this.sId = sId
		this.iWidth = iWidth
		this.sType = sType
		this.sAlign = sAlign
		this.oFormat = oFormat
		this.bTemp = bTemp
		this.oTh
	},

	destruct: function()
	{
		var oTh = this.oTh
		if (oTh) {
			oTh.onmousemove = null
			oTh.onmousedown = null
			oTh.onmouseup = null
			oTh.onmouseover = null
			oTh.onmouseout = null
		}
		this._disposeFields('oParent', 'oFormat', 'oTh')
	}
})

Object.extend(
	px.Statics,
	{
		//oActiveItem: null, ???

		resize: function(oEvent) {
			var oEvent = oEvent || window.event
			var iWidth = oEvent.clientX - px.html.Element.getLeftOffset(oActiveItem) + pxp.oSelectedControl.oParentNode.scrollLeft
			if (iWidth < 8) {
				iWidth = 8
			}
			pxp.oSelectedControl.oColumns[oActiveItem.id.substr(oActiveItem.id.indexOf('_') + 1)]['iWidth'] = iWidth
			oActiveItem.style.width = iWidth + 'px'
		},

		move: function(oEvent)
		{
			var oEvent = oEvent || window.event
			var iLeft = oEvent.clientX - iDragOffset
			var oSel = pxp.oSelectedControl
			var iPos = oSel.bChecklist ? 2 : 1

			var oDragColumn = $('dragColumn')
			if (!oDragColumn) {
				var oDragColumn = document.createElement('div')
				oDragColumn.id = 'dragColumn'
				oDragColumn.appendChild(document.createTextNode(oActiveItem.firstChild.nodeValue))
				document.body.appendChild(oDragColumn)
			} else {
				oDragColumn.firstChild.nodeValue = oActiveItem.firstChild.nodeValue
			}
			
			oDragColumn.style.width = oActiveItem.style.width
			oDragColumn.style.top = px.html.Element.getTopOffset(oActiveItem) + 'px'
		
			if (iLeft > 0)
				oDragColumn.style.left = iLeft + 'px'
		
			oDragColumn.style.display = ''
		
			var aHeaders = oSel._oTableHead.firstChild.childNodes
		
			for (var i=iPos; i<aHeaders.length; i++)
			{
				var oHeader = aHeaders[i]
				var iLeftOffset = px.html.Element.getLeftOffset(oHeader)

				if (iLeft > iLeftOffset && iLeft < (iLeftOffset + oHeader.offsetWidth)) {
					if (oActiveItem != oHeader && oActiveItem.nextSibling != oHeader) {
						if (oHeader.className != 'drop') {
							oHeader.className = 'drop'
						}
					}
				} else {
					if (oHeader.className != null) {
						oHeader.className = null
					}
				}
			}
			oDragColumn = null	
		}
	}
)

Object.extend(
	px.Proto,
	{
		headerMouseOver: function(oEvent) {
			oEvent.cancelBubble = true
			if (document.onmousemove == null) {
				this.oTh.className = 'over'
			}
		},

		headerMouseOut: function(oEvent) {
			if (pxp.bDragging) {
				return
			}
			this.oTh.className = null
		},

		headerMouseMove: function(oEvent)
		{
			if (pxp.bDragging) {
				return
			}

			var cElement = px.html.Element
			var iOffsetLeft = cElement.getLeftOffset(this.oTh)
			var iRange = iOffsetLeft + this.oTh.offsetWidth - oEvent.clientX - this.oParent.oParentNode.scrollLeft

			if (iRange < 16) {
				this.oTh.style.cursor = 'e-resize'
				px.ui.listview.Listview.bHeaderEdgeOver = true
			} else {
				this.oTh.style.cursor = 'move'
				iDragOffset = oEvent.clientX - cElement.getLeftOffset(this.oTh)
				delete px.ui.listview.Listview.bHeaderEdgeOver
			}

			oActiveItem = this.oTh
		},
		
		headerMouseDown: function(oEvent)
		{
			this.oParent.oActiveItem = this
			pxp.setActiveControl(this.oParent)

			pxp.startDrag('listviewColumn')

			if (px.ui.listview.Listview.bHeaderEdgeOver) {
				document.onmousemove = px.ui.listview.Column.resize
			} else {
				document.onmousemove = px.ui.listview.Column.move
			}
		},
		
		headerMouseUp: function(oEvent) {
			if (!px.ui.listview.Listview.bHeaderEdgeOver) {
				this.oParent.sort(this.sId)	
			}
		}
	}
)