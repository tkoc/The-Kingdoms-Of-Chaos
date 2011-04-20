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
px.Class.define('px.action.pxGlobal___openEditorview',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)

		this.oChild = new px.ui.Actionview(this)
		var oChild = this.oChild
		oChild.iMenuType = pxConst.MENU_BUTTON
		oChild.sClass = 'fileActionBar'

		if (oParameters && oParameters._sCalledAction) {
			var sBaseAction = pxp.oActions[oParameters._sCalledAction][2]
		} else {
			var sBaseAction = 'edit'
		}

		oChild.sIcon = sBaseAction + '.png'

		oChild.addActions(Array(sBaseAction), oParameters)
		oChild.init(this.oDiv, oParameters)

		this.oToolbar.addButton(
			{
				sId: 'close',
				sIcon: 'close2.png',
				oOnClick: px.lang.Function.bindEvent(this.oParent.removeAction, this.oParent),
				bRight: true
			}
		)
		
		this.oToolbar.addButton(
			{
				sId: 'maximize',
				sIcon: 'maximize.png',
				oOnClick: px.lang.Function.bindEvent(this.switchSize, this),
				bRight: true
			}
		)

		this.bChanged = false
		this._aChanged = []
	},

	destruct: function() {
		this._disposeFields('_aChanged')
	}
})

Object.extend(
	px.Proto,
	{
/*
		save: function() {
			for (var a in this.oChild.oActions) {
				this.oChild.oActions[a].save()
			}
		},
*/
		switchSize: function() {
			var oShareview = pxp.oShareview.oSelected
			oShareview.oSplitviewSelection.hideFirst()
			oShareview.oSplitviewList.hideFirst()
			return false
		},

		rename: function() {
			this.oChild.oDefaultParameters.sPath = this.sId
			for (var a in this.oChild.oActions) {
				if (this.oChild.oActions[a].rename) {
					this.oChild.oActions[a].rename(this.sId)
				}
			}
		},

		setChanged: function(sId, bState)
		{
			if (bState) {
				if (!px.lang.Array.contains(this._aChanged, sId)) {
					this._aChanged.push(sId)
				}
			} else {
				px.lang.Array.remove(this._aChanged, sId)
			}

			this.bChanged = this._aChanged.length > 0

			this._updateTitle(this.oChild.oActions[sId], bState)
			this._updateTitle(this.oParent.oActions[this.sId], this.bChanged)
		},

		_updateTitle: function(oAction, bState)
		{
			
			var oView = oAction.oParent
			var oActionData = oView.oActionData[oAction.sId]
			
			var sTitle = oActionData.sTitle

			if (bState) {
				if (sTitle.indexOf('*') == -1) {
					oActionData.sTitle = '*' + sTitle
				}
			} else {
				if (oActionData.sTitle.indexOf('*') == 0) {
					oActionData.sTitle = oActionData.sTitle.substr(1)
				}
			}

			oView.updateMenu()
		},

		showChanged: function() {
			if (!px.lang.Array.contains(this._aChanged, this.oChild.oSelected.sId)) {
				this.oChild.changeAction(this._aChanged[0]) 
			}
		}
	}
)