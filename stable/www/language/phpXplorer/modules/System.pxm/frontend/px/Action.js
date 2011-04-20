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
px.Class.define('px.Action',
{
	extend: px.core.Object,

	construct: function(sId, oParent, oParentNode)
	{
		this.sId = sId
		this.oParent = oParent
		this.oParentNode = oParentNode

		this.sTitle
		this.sIcon

		this.oChild

		this.sBaseAction
		this.sModule

		this.bInitialized = false

		this.oToolbar = this.oParent.oToolbars[sId]

		try {
			this.oShare = this.oParent.oParent.oShare
		} catch(e) {}

		this.oDiv = document.createElement('div')
		this.oDiv.className = 'pxAction ' + this.oParent.sClass
		this.oParentNode.appendChild(this.oDiv)

/*
		if (bFrame) {

		}
*/

		this.resize(true)
	},

	destruct: function() {
		if (this._oBorder) {
			this._disposeFields('_oBorder')
		}
		this._disposeFields('oParent', 'oParentNode', 'oDiv', '_oFrame', 'oToolbar', 'oShare')
		this._disposeObjects('oChild')
	}
})

Object.extend(
	px.Proto,
	{
		addBorder: function() {
			this._oBorder = document.createElement('div')
			this._oBorder.className = 'contentFrame'
			return this.oDiv.appendChild(this._oBorder)
		},

		resize: function(bFinished)
		{
			var iHeight = this.oParentNode.offsetHeight - this.oDiv.offsetTop
			if (iHeight > 0) {
				this.oDiv.style.height = iHeight + 'px'
			}

			if (this.oChild) {
				this.oChild.resize(bFinished)
			}
			
			if (!this._bResizing) {
				this._bResizing = true
				if (this._resizeStart) {
					this._resizeStart()
				}
			}
			
			if (bFinished) {
				if (this._resizeStop) {
					this._resizeStop()
				}
				delete this._bResizing
			}
		},

		save: function() {
			if (this.oParent.saveNew(this)) {
				if (this.oChild && this.oChild.save) {
					this.oChild.save()
				}
			}
			return false
		},

		setChanged: function(bState) {
			this.oParent.oParent.setChanged(this.sId, bState)
		},

		rename: function(sPath)
		{
			var oChild = this.oChild2 || this.oChild
			if (oChild) {
				if (oChild.oParameters) {
					oChild.oParameters.sPath = sPath
				}
				if (oChild.rename) {
					oChild.rename(sPath)
				}
			}
		}
	}
)