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
px.Class.define('px.action.pxObject_editProperties',
{
	extend: px.action.pxObject___edit,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode, oParameters)

		this.oToolbar.addButton(
			{
				sId: 'metaData',
				sTitle: oTranslation['toolbar.loadFileMetaData'],
				sIcon: 'loadFileMetaData.png',
				oOnClick: px.lang.Function.bind(this.loadMetaData, this)
			}
		)

		this.oChild = new px.ui.Propertyview(this, this.oDiv)

		var oParam = this.oChild.oParameters

		oParam.set(oParameters)

		if (!oParam.sShare) {
			oParam.sShare = this.oShare.sId
		}
		
		oParam._sOptionCacheKey = 'options.' + oParam.sShare + '_' + px.util.dirname(oParam.sPath) + '_' + oParam.sType	
		oParam.bFillOptions = !pxp.oData[oParam._sOptionCacheKey]
		
//		alert(oParam.bFillOptions)

//		iStart2 = new Date().getTime();

		if (oParam._bNew) {
			this.oChild.update({sAction: '_openNew'})
		} else {
			this.oChild.update({sAction: '_openJson'})
		}
	}
})

Object.extend(
	px.Proto,
	{
		loadMetaData: function() {
			this.oChild.oParameters.bReloadFileMetaData = true
			this.oChild.update()
			return false
		}
	}
)