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
px.Class.define('px.ui.xplorerview.Type',
{
	extend: px.ui.xplorerview.Item,

	construct: function(sId, oParent, oParentNode)
	{
		this.bGroupsRendered = this.bGroupsExpanded= false

		this.base(arguments,
			sId,
			oParent,
			oParentNode,
			pxp.oTypes[sId].bAbstract ? 'pxAbstract.png' : 'pxType.png',
			sId
		)
	}
})

Object.extend(
	px.Proto,
	{
		hasChildren: function()
		{
			for (var sType in pxp.oTypes) {
				if (pxp.oTypes[sType].sSupertype == this.sId) {
					return true
				}
			}
			return false
		},

		expandGroups: function()
		{
			if (this.bGroupsRendered) {
				this.bGroupsExpanded = !this.bGroupsExpanded
			} else {
				var aGroups = {}
				for (var a=0, m=pxp.oTypes[this.sId].aActions.length; a<m; a++) {
					aGroups[pxp.oActions[pxp.oTypes[this.sId].aActions[a]][2]] = true
				}
				for (var sBaseAction in aGroups) {
					var sId = this.sId + '.' + sBaseAction
					this.oParent.oItems[sId] = new px.ui.xplorerview.Actiongroup(sId, this.oParent, this.oDivActionGroups)
				}
				this.bGroupsRendered = this.bGroupsExpanded = true
			}
			
			this.oDivActionGroups.style.display = this.bGroupsExpanded ? '' : 'none'
			this.oExpandImage2.src = pxConst.sGraphicUrl + (this.bGroupsExpanded ? '/collapse.png' : '/expand.png')	
		}		
	}
)
