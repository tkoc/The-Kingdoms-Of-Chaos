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
px.Class.define('px.action.pxGlobal___openXplorerview',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)

		this.oChild = new px.ui.xplorerview.Xplorerview(this)
		var oChild = this.oChild

		oChild.oParameters.set(oParameters)
		oChild.oParameters.sShare = this.oShare.sId
		oChild.bSelection = false
		oChild.onNodeClick = px.lang.Function.bind(this._onNodeClick, this)
		
		oChild.oObject = {
			aEvents: [],
			aPermissions: [],
			aParameters: []
		}

		oChild.init(this.oDiv, true)
	}
})

Object.extend(
	px.Proto,
	{
		_onNodeClick: function(oNode)
		{
			var oEditorview = pxp.oShareview.oSelected.oActionviewFiles
			var sPath = 'px:xplorerview'

			var sType = oNode.sId
			var iPos = sType.indexOf('.')
			if (iPos > -1) {
				sType = sType.substr(0, iPos)
			}

 			if (!oEditorview.oActions[sPath]) {
 				oEditorview.addAction(
 					sPath,
 					'xplorer',
 					px.action.pxGlobal___openEditorview,
 					true,
					'types/pxType',
	 				{
	 					sShare: 'kpw3',
						sPath: '/css.php',
						sType: 'pxType',
	 					_sCalledAction: 'pxObject_editProperties'
	 				}
 				)
 			}

 			oEditorview.showAction(sPath)
 			
 			oEditorview.oSelected.oChild.oSelected.show(sType)
		}
	}
)