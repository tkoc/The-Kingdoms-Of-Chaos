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
px.Class.define('px.ui.xplorerview.Xplorerview',
{
	extend: px.io.RemoteView,

	construct: function(oParent)
	{
		this.base(arguments, oParent)

		this.oParameters.bFull = true
		this.oParameters.sAction = '_openNew'
		this.oItems = {}
		this.bSelection = true
		this.onNodeClick
	},

	destruct: function() {
		this._disposeFields('oForm', 'oObject', 'onNodeClick')
		this._disposeContents('oItems')
	}
})

Object.extend(
	px.Proto,
	{
		clear: function() {
			for (var i in this.oItems) {
				this.oItems[i].dispose()
			}
		},

		init: function(oParentNode, bBuild)
		{
			this.oParentNode = oParentNode

			this.oForm = document.createElement('form')
			this.oForm.method = 'post'
			this.oForm.action = pxp.sUrl
			this.oForm.className = 'xplorerview'

			this.oParentNode.appendChild(this.oForm)
			
			if (bBuild) {
				this._build()
			}
		},
		
		_checkId: function(sId)
		{
			var aParts = sId.split('.')
			
			// Check if type still exists
			if (!pxp.oTypes[aParts[0]]) return false
			// Check if base action still exists
			if (aParts.length > 1) {
				var bIn = false
				var aActions = pxp.oTypes[aParts[0]].aActions
				for (var i=0, m=aActions.length; i<m; i++) {
					//if (px.util.get BaseAction(aActions[i]) == aParts[1]) {
					if (pxp.oActions[aActions[i]][2] == aParts[1]) {
						bIn = true
						break
					}
				}
				if (!bIn) return false
			}
			// Check if action still exists
			if (aParts.length > 2) {
				if (!px.lang.Array.contains(pxp.oTypes[aParts[0]].aActions, aParts[2])) {
					return false
				}
			}
			return true
		},
		
		save: function()
		{
			// PERMISSIONS
			var aPermissions = []
			for (var sId in this.oObject.aPermissions) {
				if (this._checkId(sId)) {
					aPermissions.push(encodeURIComponent(sId))
				}
			}
		
			// EVENTS
			var aEvents = []
			for (var sId in this.oObject.aEvents) {
				if (this._checkId(sId)) {
					aEvents.push(sId + '|' + this.oObject.aEvents[sId])
				}
			}

			var oResult = this._save(
				{sAction: 'edit'},
				'sPermissions=' + encodeURIComponent(aPermissions.join('|')) +
				'&sEvents=' + encodeURIComponent(aEvents.join('||'))
			)

			if (oResult.bOk) {
				pxp.refreshView(px.util.dirname(this.oParameters.sPath))
			}
		
			return false
		},
		
		_build: function() {
			var aTypes = new Array('pxGlobal', 'pxObject')
			for (var i=0,l=aTypes.length; i<l; i++) {
				var sType = aTypes[i]
				this.oItems[sType] = new px.ui.xplorerview.Type(sType, this, this.oForm)
			}
		},

		_update: function(sContainerPath) 
		{
			if (this.oForm.childNodes.length > 0) {
				this.clear()
			}
		
			this.oObject = this.oResults[this.oParameters.getPath()][0]

			this._build()

			for (var sId in this.oObject.aPermissions) {
				this._expandId(sId)
			}

			for (var sId in this.oObject.aEvents) {
				this._expandId(sId)
			}
		},

		_expandId: function(sId)
		{
		  var aParts = sId.split('.')

		  this._expandSupertypes(aParts[0])

		  if (aParts.length > 1) {
		  	var oItem = this.oItems[aParts[0]]
		  	if (!oItem.bGroupsExpanded) {
		  		oItem.expandGroups()
		  	}
		  }

		  if (aParts.length > 2) {
		  	var oItem = this.oItems[aParts[0] + '.' + aParts[1]]
		  	if (!oItem.bExpanded) {
		  		oItem.expand()
		  	}
		  }	
		},

		_expandSupertypes: function(sType)
		{
			if (this.oItems[sType]) {
				return
			}
			var aSupertypes = pxp.oTypes[sType].aSupertypes
			for (var i=0, m=aSupertypes.length; i<m; i++) {
				var sSupertype = aSupertypes[i]
				var oItem = this.oItems[sSupertype]
				if (!oItem.bExpanded) {
					oItem.expand()
				}
			}
		}
	}
)