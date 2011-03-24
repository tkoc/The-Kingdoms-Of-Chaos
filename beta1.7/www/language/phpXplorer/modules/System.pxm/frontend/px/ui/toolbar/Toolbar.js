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
px.Class.define('px.ui.toolbar.Toolbar',
{
	extend: px.core.Object,

	construct: function(oParent, oParentNode, sId)
	{
		this.oParent = oParent
		this.oParentNode = oParentNode
		
		this.oButtons = {}

		this.iIconSize = 16
		
		this.oDiv = document.createElement('div')
		this.oDiv.className = 'pxToolbar'
		if (sId) {
			this.oDiv.id = sId
		}

		if (this.oParent.bToolbarFirst) {
			this.oParentNode.insertBefore(this.oDiv, this.oParentNode.firstChild)
		} else {
			this.oParentNode.appendChild(this.oDiv)
		}
	},

	destruct: function() {
		this._disposeFields('oParent', 'oParentNode', 'oDiv')
		this._disposeContents('oButtons')
	}
})

Object.extend(
	px.Proto,
	{
		addButton: function(oOptions) {
			this.oButtons[oOptions.sId] = new px.ui.toolbar.Button(this, oOptions)
			if (oOptions.bHidden) {
				this.hideButton(oOptions.sId)
			}
		},

		showButton: function(sId) {
			if (this.oButtons[sId]) {
				this.oButtons[sId].oA.style.display = 'block'
			}
		},

		hideButton: function(sId) {
			if (this.oButtons[sId]) {
				this.oButtons[sId].oA.style.display = 'none'
			}
		},

		removeButton: function(sId) {
			var oA = this.oButtons[sId].oA
			this.oButtons[sId].dispose()
			this.oDiv.removeChild(oA)
			delete this.oButtons[sId]
		},

		enableButton: function(sId) {
			var oButton = this.oButtons[sId]
			px.html.Element.removeClassName(oButton.oA, 'pxTbDisabled')
			if (oButton.oOnClick && oButton.oOnClick instanceof Function) {
				oButton.oA.onclick = oButton.oOnClick
				oButton.changeImage(pxConst.sGraphicUrl + '/' + oButton.sIcon)
			}
		},

		disableButton: function(sId) {
			var oButton = this.oButtons[sId]
			px.html.Element.addClassName(oButton.oA, 'pxTbDisabled')
			oButton.oA.onclick = px.lang.Function.cancelEvent
			var sDir = px.util.dirname(oButton.sIcon)
			var sName = px.util.basename(oButton.sIcon)
			var sPath = px.util.buildPath(sDir, 'disabled_' + sName)
			oButton.changeImage(pxConst.sGraphicUrl + '/' + sPath)
		},

		addDivider: function() {
			var oDivider = document.createElement('div')
			oDivider.className = 'pxTbDivider'
			this.oDiv.appendChild(oDivider)
		}
	}
)