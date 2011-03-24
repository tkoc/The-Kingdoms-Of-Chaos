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
px.Class.define('px.action.pxGlobal_openHome',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)

		px.html.Element.addClassName(this.oDiv, 'pxHomepage')
		
		var oBorder = this.addBorder()

		var sName = pxp.oData.oProfile.sFullName
		if (!sName) {
			sName = pxp.oData['sUser']
		} else {
			sName = pxp.translateTitle(sName)
		}

		var sImageUrl = pxp.oData.sLogoUrl
		if (sImageUrl != '') {
			var oImage = new Image()
			oImage.src = sImageUrl
			oImage.id = 'logo'
			oBorder.appendChild(oImage)
		}

		oBorder.appendChild(document.createTextNode(oTranslation['welcome'] + ' ' + sName + ','))

		oBorder.appendChild(document.createElement('br'))

		oBorder.appendChild(document.createTextNode(oTranslation['home.introduction']))

		if (pxp.sUser != 'everyone')
		{
			this._oToolbar = new px.ui.toolbar.Toolbar(this, oBorder)

			this._oToolbar.addButton(
			{
					sId: 'editProfile',
					sLabel: oTranslation['editProfile'],
					oOnClick: px.lang.Function.bind(px.action.pxGlobal_openHome._editProfile, this)
				}
			)

			this._oToolbar.addButton(
				{
					sId: 'changePassword',
					sLabel: oTranslation['changePassword'],
					oOnClick: px.lang.Function.bind(px.action.pxGlobal_openHome._editPassword, this)
				}
			)
		}

		oBorder.appendChild(document.createTextNode(oTranslation['home.shares']))
		
		this._oToolbarShares = new px.ui.toolbar.Toolbar(this, oBorder, 'SharesList')
		
		for (var sBookmark in pxp.oData.aBookmarks) {
			this._oToolbarShares.addButton(
				{
					sId: sBookmark,
					sLabel: oTranslation['share.' + sBookmark] || pxp.oData.aBookmarks[sBookmark] || sBookmark,
					oOnClick: px.lang.Function.bind(pxp.oShareview.showAction, pxp.oShareview, sBookmark)
				}
			)
		}

	},

	destruct: function() {
		this._disposeObjects('_oToolbar', '_oToolbarShares')
	}
})

Object.extend(
	px.Statics,
	{
		_editProfile: function()
		{
			var sPath = 'profiles/' + pxp.oData.sUser + '.pxProfile'
			var oView = pxp.oShareview.oSelected.oActionviewFiles

			oView.addAction(
				'px:editProfile',
				sPath,
				px.action.pxGlobal___openEditorview,
				true,
				'types/pxProfile',
				{
					sShare: 'phpXplorer',
					sPath: sPath,
		  		sType: 'pxProfile'
				}
			)
			oView.showAction('px:editProfile')
			return false
		},
		
		_editPassword: function()
		{
			var sPath = pxp.oData.sUser
			var oView = pxp.oShareview.oSelected.oActionviewFiles

			oView.addAction(
				'px:editPassword',
				sPath,
				px.action.pxGlobal___openEditorview,
				true,
				'types/pxVfsAuthenticationUser',
				{
					sShare: 'Authentication',
					sPath: sPath,
		  		sType: 'pxVfsAuthenticationUser'
				}
			)
			oView.showAction('px:editPassword')
			return false
		}
	}
)
