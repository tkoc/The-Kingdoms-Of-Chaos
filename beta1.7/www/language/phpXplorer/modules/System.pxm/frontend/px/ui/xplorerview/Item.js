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
px.Class.define('px.ui.xplorerview.Item',
{
	extend: px.core.Object,
	type: 'abstract',

	construct: function(sId, oParent, oParentNode, sIcon, sLabel)
	{
		var cFunction = px.lang.Function
		var cElement = px.html.Element

		this.bRendered = false
		this.bExpanded = false

		this.sId = sId
		this.oParent = oParent
		this.oParentNode = oParentNode

		this.oDiv = document.createElement('div')
		var oDiv = this.oDiv
		oDiv.className = 'xplorerNode'

		this.oParentNode.appendChild(oDiv)

		this.oExpandImage1 = new Image()
		var oExpandImage1 = this.oExpandImage1
		oExpandImage1.className = 'expand'
		if (this.hasChildren()) {
			oExpandImage1.src = pxConst.sGraphicUrl + '/expand.png'
			oDiv.appendChild(oExpandImage1)
			oExpandImage1.onclick = cFunction.bind(this.expand, this)
		} else {
			oExpandImage1.src = pxConst.sGraphicUrl + '/dummy16.png'
			oDiv.appendChild(oExpandImage1)
		}

		if (this.oParent.bSelection) {
			this.oCheckbox = document.createElement('input')
			this.oCheckbox.type = 'checkbox'
			oDiv.appendChild(this.oCheckbox)
			this.oCheckbox.onclick = cFunction.bind(this.selectionChanged, this)
		}

		var oImage = new Image()
		oImage.src = pxConst.sGraphicUrl + '/' + sIcon
		oImage.title = sId
		oDiv.appendChild(oImage)
	
		this.oA = document.createElement('a')
		
		if (this.oParent.onNodeClick) {
			this.oA.href = pxConst.DUMMY_LINK		
			this.oA.onclick = cFunction.bind(px.ui.xplorerview.Item._itemClick, this)
		}

		oDiv.appendChild(this.oA)
		this.oA.appendChild(document.createTextNode(sLabel))
	
		if (this.oParent.oObject.aEvents[this.sId]) {
			cElement.appendImage(oDiv, pxConst.sGraphicUrl + '/bulletGo.png')
		}	
	
		if (this.expandGroups) {
			this.oExpandImage2 = new Image()
			var oExpandImage2 = this.oExpandImage2
			oExpandImage2.className = 'expand'
			oExpandImage2.src = pxConst.sGraphicUrl + '/expand.png'
			oDiv.appendChild(oExpandImage2)
			oExpandImage2.onclick = cFunction.bind(this.expandGroups, this)
	
			this.oDivActionGroups = document.createElement('div')
			this.oDivActionGroups.style.display = 'none'
			this.oDivActionGroups.className = 'xplorerNode'
			oDiv.appendChild(this.oDivActionGroups)
		}

		this.oDivChildren = document.createElement('div')
		oDiv.appendChild(this.oDivChildren)
	
		if (this.oParent.oObject.aPermissions[sId]) {
			this.oCheckbox.checked = true
			cElement.addClassName(oDiv, 'selected')
			cElement.addClassName(this.oA, 'selected')
		}
	},

	destruct: function() {
		this._disposeFields(
			'oParent', 'oParentNode', 'oDiv', 'oDivChildren', 'oDivActionGroups',
			'oA', 'oCheckbox', 'oExpandImage1', 'oExpandImage2'
		)
	}
})

Object.extend(
	px.Statics,
	{
		_itemClick: function()
		{
			var cElement = px.html.Element
			var oView = this.oParent

			if (oView.oActiveItem) {
				cElement.removeClassName(oView.oActiveItem.oA, 'active')
			}
			oView.oActiveItem = this
			cElement.addClassName(oView.oActiveItem.oA, 'active')

			if (this.oParent.onNodeClick) {
				this.oParent.onNodeClick(this)
			}		
		}
	}
)

Object.extend(
	px.Proto,
	{		
		selectionChanged: function()
		{
			var cElement = px.html.Element
			if (this.oCheckbox.checked) {
				cElement.addClassName(this.oDiv, 'selected')
				cElement.addClassName(this.oA, 'selected')
				this.oParent.oObject.aPermissions[this.sId] = true
			} else {
				cElement.removeClassName(this.oDiv, 'selected')
				cElement.removeClassName(this.oA, 'selected')
				delete this.oParent.oObject.aPermissions[this.sId]
			}
		},
		
		expand: function()
		{
			var cElement = px.html.Element
			if (this.bRendered) {
				this.bExpanded = !this.bExpanded
				this.oDivChildren.style.display = this.bExpanded ? '' : 'none'
			} else {
				var bFirst = false
				for (var sId in pxp.oTypes) {
					if (pxp.oTypes[sId].sSupertype == this.sId) {
						this.oParent.oItems[sId] = new px.ui.xplorerview.Type(sId, this.oParent, this.oDivChildren)
						if (!bFirst) {
							cElement.addClassName(this.oParent.oItems[sId].oDiv, 'firstNode')
							bFirst = true
						}
					}
				}
				this.bRendered = this.bExpanded = true
			}
			this.oExpandImage1.src = pxConst.sGraphicUrl + (this.bExpanded ? '/collapse.png' : '/expand.png')
		}
	}
)