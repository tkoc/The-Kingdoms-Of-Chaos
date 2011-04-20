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
px.Class.define('px.action.pxGlobal___openShareview',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)

		var cFunction = px.lang.Function

		this.bSearchInProgress = false

		this.oShare = px.io.Request.get('sAction=openShare&sShare=' + encodeURIComponent(this.sId))
		if (!this.oShare) {
			return false
		}

		this.oActionviewSelection = new px.ui.Actionview(this)
		var oView = this.oActionviewSelection
		oView.iMenuType = pxConst.MENU_TABS
		oView.sClass = 'selectionBar'
		oView.sTitle = oTranslation['sAction']
		
		this.oActionviewSelection.addActions(
			Array('select'),
			{
				sType: this.oShare.sBaseType,
				_sCalledAction: this.oShare.sDefaultSelection
			}
		)


		this.oActionviewList = new px.ui.Actionview(this)
		var oView = this.oActionviewList
		oView.iMenuType = pxConst.MENU_BUTTON
		oView.iIconSize = 28
		oView.sClass = 'listBar'
		oView.sTitle = oTranslation['sView']
		oView.bTitleOnly = true
		oView.sIcon = 'toolbar/pxMetaDirectories_openDetails.png'
		oView.sDropDownIcon = 'dropDown.png'
		oView.bToolbarFirst = true
		oView.bSyncParameter = true
		oView.iToolbarType = pxConst.TOOLBAR_VERTICAL
		oView.showContextMenu = px.action.pxGlobal___openShareview.showListContextMenu
		oView.callAction = px.action.pxGlobal___openShareview.callListActions

		this.oActionviewList.addActions(
			Array('open', 'batch'),
			{
				sType: this.oShare.sBaseType,
				_sCalledAction: this.oShare.sDefaultView
			}
		)

		this.oActionviewFiles = new px.ui.Actionview(this)
		var oView = this.oActionviewFiles
		oView.iMenuType = pxConst.MENU_BUTTON
		oView.sClass = 'fileBar'
		oView.sIcon = 'files.png'
		oView.sLabelDrop = 'item'

		this.oSplitviewSelection = new px.ui.Splitview(true, this.oShare.sTreeviewWidth)
		this.oSplitviewSelection.sId = 'pxSplitSelection'
		this.oSplitviewSelection.oActionview = this.oActionviewSelection

		this.oSplitviewList = new px.ui.Splitview(false, 0.45)
		var oSplitviewList = this.oSplitviewList
		oSplitviewList.sId = 'pxSplitList'
		this.oChild = this.oSplitviewSelection
		this.oSplitviewSelection.oChild1 = this.oActionviewSelection
		this.oSplitviewSelection.oChild2 = this.oSplitviewList
		oSplitviewList.oChild1 = this.oActionviewList
		oSplitviewList.oChild2 = this.oActionviewFiles

		this.oChild.init(this.oDiv)
		
		if (!this.oShare.bSelectionView) {
			this.oSplitviewSelection.hideFirst()
		}

/*
		var sName = pxp.o Data.oProfile.sFullName
		if (sName) {
			sName += ' (' + pxp.sUser + ')'
		} else {
			sName = pxp.sUser
		}
*/

		var oShareAction = px.action.pxGlobal___openShareview
	
		this.oToolbar.addButton(
			{
				sId: 'search',
				sLabel: oTranslation['search'],
				iType: pxConst.TOOLBAR_BUTTON_INPUT,
				sIcon: 'cancelSearch.png',
				oOnIconClick: cFunction.bindEvent(oShareAction._iconClick, this),
				oOnClick: cFunction.bindEvent(oShareAction._showSearchMenu, this),
				bDropDown: true,
				sClass: 'pxTbSearch',
				oInputOnKeyUp: cFunction.bindEvent(oShareAction._searchKeyUp, this),
				oInputOnFocus: cFunction.bindEvent(oShareAction._searchFocus, this),
				oInputOnBlur: cFunction.bindEvent(oShareAction._searchBlur, this)
			}
		)
	
		// open start page
		if (this.oShare.sStartpage && !this.oShare.bBookmark) {
			var sPath = px.util.buildPath('/', this.oShare.sStartpage)
			this.oActionviewFiles.addAction(
				sPath,
				sPath.indexOf('/') == 0 ? sPath.substr(1) : sPath,
				px.action.pxGlobal___openEditorview,
				true,
				'types/pxHtml',
				{
					sPath: sPath,
	  			sType: 'pxHtml',
	  			_sCalledAction: 'pxFiles__openInline'
				}
			)
			this.oActionviewFiles.showAction(sPath)
		}
	},

	destruct: function() {
		this._disposeFields(
			'oActionviewSelection', 'oActionviewList', 'oActionviewFiles',
			'oSplitviewSelection', 'oSplitviewList'
		)
	}
})


Object.extend(
	px.Statics,
	{
		_iconClick: function(oEvent) {
			this.cancelSearch(true)
		},

		_searchKeyUp: function(oEvent) {
			if (this.oShare.bLiveSearch) {
				this.updateSearch()
//				this.startSearch(px.Event.element(oEvent).parentNode)
			} else {
				if (oEvent.keyCode == 13) {
					this.updateSearch()
				}
			}
		},
		
//		_searchLiveTimeout: function()
		
		_searchFocus: function(oEvent) {
			var oElement = px.Event.element(oEvent)
			if (oElement.value == oTranslation['search']) {
				oElement.value = ''
			}
			this.startSearch(oElement.parentNode)
		},
		
		_searchBlur: function(oEvent) {
			var oElement = px.Event.element(oEvent)
			if (oElement.value == '') {
				this.cancelSearch()
			}
		},
		
		_showSearchMenu: function(oEvent) {
			return false
		},

		showListContextMenu: function(oEvent)
		{
			var cArray = px.lang.Array
			pxp.setActiveControl(this)
			var oCm = px.ui.ContextMenu
			oCm.clear()

			var bActions = false

			for (var sAction in this.oActionData)
			{
				if (this.aAllowedActions) {
					if (!cArray.contains(this.aAllowedActions, sAction)) {
						continue
					}
				}

				if (this.oActions[sAction] == this.oSelected) {
					continue
				}

				var sIcon = pxConst.sModuleUrl + '/' + pxp.oActions[sAction][0] + '.pxm/graphics/toolbar/' + sAction + '.png'

				oCm.addItem(
					sAction,
					'',
					sIcon,
					false,
					'pxCMToolbar',
					oTranslation['action.' + sAction]
				)

				bActions = true
			}

			if (bActions) {
				oCm.addDivider()
			}

			oCm.addItem(
				'refreshView',
				oTranslation['refreshView'],
				'./modules/System.pxm/graphics/refreshView.png'
			)

			oCm.addDivider()

			var aSortColumns = new Array('sName', 'iBytes', 'dModified', 'sType')

			for (var i=0; i<aSortColumns.length; i++) {
				oCm.addItem(
					'sortBy_' + aSortColumns[i],
					oTranslation['sortBy'] + ' ' + oTranslation['property.' + aSortColumns[i]],
					'./modules/System.pxm/graphics/sortBy.png'
				)
			}

			oCm.show(oEvent)

			return false
		},

		callListActions: function(sAction)
		{
			if (sAction == 'refreshView') {
				pxp.refreshView(this.oSelected.oChild.oParameters.sPath)
			}
			else if (sAction.indexOf('sortBy_') > -1) {
				var sColumn = sAction.substr(sAction.indexOf('_') + 1)
				var oListview = pxp.getListview()
				oListview.sort(sColumn)
			}
			else {
				px.html.Element.changeImage(
					this.oLabel.firstChild,
					pxConst.sModuleUrl + '/' + pxp.oActions[sAction][0] + '.pxm/graphics/toolbar/' + sAction + '.png'
				)
				this.changeAction(sAction)
			}
			return false
		}
	}
)


Object.extend(
	px.Proto,
	{
		dirUp: function() {
			this.oActionviewList.oSelected.oChild.dirUp()
		},
		
		updateSearch: function()
		{
			var sSearchQuery = ''
			var oAction = this.oActionviewSelection.oActions.pxDirectories_selectTags
			if (oAction && oAction.bInitialized) {
				sSearchQuery = oAction.getTagQuery()
			}

			if (this.bSearchInProgress) {
				var sSearchTerm = px.util.trim(
					this.oToolbar.oButtons['search'].oInput.value
				)
				if (sSearchTerm != '') {
					if (sSearchTerm.indexOf(':') == -1) {
						sSearchTerm = '"' + sSearchTerm  + '%"'
					}
					if (sSearchQuery != '') {
						sSearchQuery += ' and '
					}
					sSearchQuery += sSearchTerm
				}
			}

			if (sSearchQuery == '') {
				sSearchQuery = null
			}

			var oList = this.oActionviewList.oSelected.oChild
			if (oList.oParameters.sSearchQuery != sSearchQuery) {
				oList.oParameters.sSearchQuery = sSearchQuery
				oList.update()
			}
		},

		startSearch: function(oElement) {
			if (!this.bSearchInProgress) {
				this.bSearchInProgress = true
				px.html.Element.addClassName(oElement, 'pxTbSearchActive')
			}
		},

		cancelSearch: function(bUpdate)
		{
			if (this.bSearchInProgress) {
				var oToolbar = this.oToolbar
				var oSearch = oToolbar.oButtons['search']
				var oA = oSearch.oA
				oSearch.oInput.value = oTranslation['search']
				oSearch.oInput.blur()

				this.bSearchInProgress = false
				px.html.Element.removeClassName(oA, 'pxTbSearchActive')

				if (bUpdate == null || bUpdate == true) {
					this.updateSearch()
				}
			}
		},

		refreshView: function(sDirectory)
		{
			var oTree = this.oActionviewSelection.oActions.pxDirectories_selectTree
			if (oTree) {
				var oNode = oTree.oChild.getNode(sDirectory)
				if (oNode.bRendered) {
					oTree.oChild.update({sPath: sDirectory})
				}
			}
			if (this.oActionviewList.oSelected.oChild.oParameters.sPath == sDirectory) {
				this.oActionviewList.oSelected.oChild.update()
			}
		}
	}
)