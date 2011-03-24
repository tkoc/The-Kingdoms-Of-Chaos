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
px.Class.define('px.ui.tabview.Tabview',
{
	extend: px.core.Object,

	construct: function(oParent, oParentNode, oInsertBefore) {
		this.oParent = oParent
		this.oParentNode = oParentNode	
		this.oInsertBefore = oInsertBefore
		//this.iTabCount = 0
		this.oSelected
		this.onTabClick
		this.oTabs = {}
	},

	destruct: function() {
		this._disposeFields('oParent', 'oParentNode', 'oInsertBefore', 'oSelected', 'onTabClick')
		this._disposeContents('oTabs')
	}
})

Object.extend(
	px.Proto,
	{
		addTab: function(sId, sTitle, bSelected) {
			this.oTabs[sId] = new px.ui.tabview.Button(this, sId, sTitle)
			//this.iTabCount++
			if (bSelected) {
				this.setSelected(sId, true)
			}
		},

		removeTab: function(sId) {
			var oDiv = this.oTabs[sId].oDiv
			this.oTabs[sId].dispose()
			this.oParentNode.removeChild(oDiv)
			delete this.oTabs[sId]
			//this.iTabCount--
		},

		setSelected: function(sId, bInitializing)
		{
			if (!this.oTabs[sId]) {
				return false
			}

			if (this.oSelected == this.oTabs[sId]) {
				return false
			}

			for (var t in this.oTabs) {
				this.oTabs[t].oDiv.className = 'pxTab'
			}
			
			this.oTabs[sId].oDiv.className = 'pxTab pxSelectedTab'

			this.oSelected = this.oTabs[sId]
			if (this.onTabClick && !bInitializing) {
				this.onTabClick(sId)
			}
		}
	}
)