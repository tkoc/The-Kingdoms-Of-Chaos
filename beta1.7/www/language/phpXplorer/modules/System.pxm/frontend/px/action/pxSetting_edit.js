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
px.Class.define('px.action.pxSetting_edit',
{
	extend: px.action.pxObject___edit,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode, oParameters)

		this.oXplorerview = new px.ui.xplorerview.Xplorerview(this)
		this.oXplorerview.oParameters.set(oParameters)
		this.oXplorerview.oParameters.sShare = this.oShare.sId
		this.oXplorerview.onNodeClick = px.lang.Function.bind(this._onNodeClick, this)

		this.oXplorerviewPropertyview = new px.ui.xplorerview.Propertyview(this)

		this.oSplitview = new px.ui.Splitview(true, 0.4)
		this.oChild = this.oSplitview
		this.oChild2 = this.oXplorerview

		this.oChild.oChild1 = this.oXplorerview
		this.oChild.oChild2 = this.oXplorerviewPropertyview
		this.oChild.bSnap = false

		this.oChild.init(this.oDiv)

//		for (var i in pxp.oTypes['pxMeta']) {
//			alert(i + ': ' + pxp.oTypes['pxMeta'][i])
//		}

	},

	destruct: function() {
		this._disposeFields(
			'oSplitview', 'oXplorerview', 'oXplorerviewPropertyview', 'oChild2'
		)
	}
})

Object.extend(
	px.Proto,
	{
		_onNodeClick: function(oNode)
		{
			var oView = oNode.oParent
			var oPropertyView = oView.oParent.oXplorerviewPropertyview
			var sEvent = oView.oObject.aEvents[oNode.sId]
			oPropertyView.oEvent.value = sEvent || ''
		}
	}
)