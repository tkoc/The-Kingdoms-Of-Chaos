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
px.Class.define('px.ui.xplorerview.Actiongroup',
{
	extend: px.ui.xplorerview.Item,

	construct: function(sId, oParent, oParentNode)
	{
		this.bGroupsRendered = this.bGroupsExpanded = false

		this.base(arguments,
			sId,
			oParent,
			oParentNode,
			'pxActiongroup.png',
			oTranslation[sId.split('.')[1]]
		)
	}
})

Object.extend(
	px.Proto,
	{
		hasChildren: function() {
			return true
		},

		expand: function()
		{
			var cElement = px.html.Element

			if (this.bRendered) {
				this.bExpanded = !this.bExpanded
				this.oDivChildren.style.display = this.bExpanded ? '' : 'none'
			} else {
				var aPart = this.sId.split('.')
				var sType = aPart[0]
				var sSelectedBaseAction = aPart[1]
				var bFirst = false
				for (var a=0, m=pxp.oTypes[sType].aActions.length; a<m; a++) {
					var sAction = pxp.oTypes[sType].aActions[a]
					var sBaseAction = pxp.oActions[sAction][2]
					if (sBaseAction != sSelectedBaseAction) {
						continue
					}
					var sId = sType + '.' + sBaseAction + '.' + sAction
					this.oParent.oItems[sId] = new px.ui.xplorerview.Action(sId, this.oParent, this.oDivChildren)
					if (!bFirst) {
						cElement.addClassName(this.oParent.oItems[sId].oDiv, 'firstNode')
						bFirst = true
					}
				}
				this.bRendered = this.bExpanded = true
			}
			this.oExpandImage1.src = pxConst.sGraphicUrl + (this.bExpanded ? '/collapse.png' : '/expand.png')
		}
	}
)