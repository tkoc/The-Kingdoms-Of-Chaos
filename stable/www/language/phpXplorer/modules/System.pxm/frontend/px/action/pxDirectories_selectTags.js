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
px.Class.define('px.action.pxDirectories_selectTags',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)

		this.sCurrentPath
		this.oContainer
		this.oTagList
		this.bAndOperator = true

		px.html.Element.addClassName(this.oDiv, 'pxTagList')

		this.oTagList = document.createElement('ul')
		this.oTagList.className = 'checklist'
		this.oDiv.appendChild(this.oTagList)
		this.loadTags()

		// Add toolbar buttons	
		this.oToolbar.addButton(
			{
				sId: 'clearTagSelection',
				sLabel: '',
				sIcon: 'tagSettings.png',
				oOnClick: px.lang.Function.bindEvent(px.action.pxDirectories_selectTags.showMenu, this),
				sTitle: oTranslation['toolbar.settings']
//				bRight: true
			}
		)
		this.oToolbar.hideButton('previousType')		
	},

	destruct: function() {
		this.clear(true)
		this._disposeFields('oTagList')
	}
})

Object.extend(
	px.Statics,
	{
		showMenu: function(oEvent)
		{
			pxp.setActiveControl(this)
			
			var oCm = px.ui.ContextMenu
			oCm.clear()
		
			oCm.addItem(
				'clearSelection',
				oTranslation['clearSelection'],
				'./modules/System.pxm/graphics/clearSelection.png'
			)
			oCm.addDivider()
		
			oCm.addItem(
				'andOperator',
				oTranslation['andOperator'],
				'./modules/System.pxm/graphics/' + (this.bAndOperator ? 'tick.png' : 'dummy16.png')
			)
		
			oCm.addItem(
				'orOperator',
				oTranslation['orOperator'],
				'./modules/System.pxm/graphics/' + (!this.bAndOperator ? 'tick.png' : 'dummy16.png')
			)
		
			oCm.show(oEvent)
		
			return false
		},
		
		itemMouseOver: function(oEvent) {
			var cElement = px.html.Element
			if (pxp.bIe) {
				cElement.addClassName(this, 'hover')
			}
			if (pxp.bDragging && pxp.sDragType == 'item' ) {
				cElement.addClassName(this, 'drop')
			}
		},
		
		itemMouseOut: function(oEvent) {
			var cElement = px.html.Element
			if (pxp.bIe) {
				cElement.removeClassName(this, 'hover')
			}
			cElement.removeClassName(this, 'drop')
		},
		
		itemMouseUp: function(oEvent)
		{
			if (px.html.Element.hasClassName(this, 'drop'))
			{
				for (var sSelectedPath in pxp.oSelectedControl.oSelected) {
					var	oSelectedItem = pxp.oSelectedControl.oSelected[sSelectedPath]
					var sShare = oSelectedItem.getShare()
					var sPath = oSelectedItem.sFilename
					var sTag = this.childNodes[1].nodeValue

					px.io.Request.post(
						'sShare=' + encodeURIComponent(sShare) +
						'&sAction=_editSwitchTag' +
						'&sPath=' + encodeURIComponent(sPath) +
						'&sTag=' + sTag			
					)

					pxp.getListview().update()
				}		
			}
		}
	}
)

Object.extend(
	px.Proto,
	{		
		resize: function()
		{
			var cElement = px.html.Element

			px.Action.prototype.resize.call(this)

			if (this.oParent.oParent.oSplitviewSelection.bVertical) {
				//Element.removeClassName(this.oParent.oTabview.oSelected.oDiv, 'landscape')
				//cElement.removeClassName(this.oDiv, 'landscape')
				//cElement.removeClassName(this.oTagList, 'landscape')
			} else {
				//Element.addClassName(this.oParent.oTabview.oSelected.oDiv, 'landscape')
				//cElement.addClassName(this.oDiv, 'landscape')
				//cElement.addClassName(this.oTagList, 'landscape')
			}
		},
		
		clear: function(bDispose)
		{
			if (this.oTagList) {
				for (var i=this.oTagList.childNodes.length - 1; i>-1; i--) {
					var oChild = this.oTagList.childNodes[i]

					oChild.firstChild.firstChild.onclick = null
					oChild.firstChild.onmouseover = null
					oChild.firstChild.onmouseout = null
					oChild.firstChild.onmouseup = null
		
					if (!bDispose) {
						this.oTagList.removeChild(oChild)
					}	
				}
			}
		},

		loadTags: function()
		{
			var cFunction = px.lang.Function
			var oShareview = this.oParent.oParent
			if (!oShareview.oActionviewList.oSelected) {
				return
			}
			var sShare = this.oShare.sId
			var sPath = oShareview.oActionviewList.oSelected.oChild.oParameters.sPath
			if (this.sCurrentPath == sPath) {
				return
			}
			this.sCurrentPath = sPath
			this.clear()
			
			
			var sCacheKey = 'tags.' + sShare + '_' + sPath
			
			if (!pxp.oData[sCacheKey]) {
				pxp.oData[sCacheKey] =
					px.io.Request.get(
						'sAction=_openDefaultTags&sShare=' + encodeURIComponent(sShare) +
						'&sPath=' + encodeURIComponent(sPath))
			}

			for (var t in pxp.oData[sCacheKey])
			{
				var sTag = pxp.oData[sCacheKey][t]
				sTag = oTranslation['tag.' + sTag] || sTag

				var oLi = document.createElement('li')
				oLi.title = sTag

				if (this.oTagList.childNodes.length == 0) {
					this.oTagList.appendChild(oLi)
				} else {
					this.oTagList.insertBefore(oLi, this.oTagList.firstChild)
				}

				var oLabel = document.createElement('label')
				oLabel.htmlFor = sShare + '_tagLabel_' + t
				oLi.appendChild(oLabel)

				var oTagAction = px.action.pxDirectories_selectTags
				oLabel.onmouseover = cFunction.bindEvent(oTagAction.itemMouseOver, oLabel)
				oLabel.onmouseout = cFunction.bindEvent(oTagAction.itemMouseOut, oLabel)
				oLabel.onmouseup = cFunction.bindEvent(oTagAction.itemMouseUp, oLabel)

				var oInput = document.createElement('input')
				oInput.type = 'checkbox'
				oInput.id = sShare + '_tagLabel_' + t
				oInput.name = sShare + '_tagLabel_' + t
				oInput.value = sTag
				oLabel.appendChild(oInput)

				oInput.onclick = cFunction.bindEvent(this._checkboxClick, this)

				oLabel.appendChild(document.createTextNode(sTag))
			}
		},
		
		_checkboxClick: function(oEvent)
		{
			var cElement = px.html.Element
			var oInput = px.Event.element(oEvent)
			var oLabel = oInput.parentNode
			if (oInput.checked) {
				cElement.addClassName(oLabel, 'active')
			} else {
				cElement.removeClassName(oLabel, 'active')
			}
			this.oParent.oParent.updateSearch()
		},

		getTagQuery: function()
		{
			var aTagConditions = []
			if (this.bInitialized) {
				for (var t=0, m=this.oTagList.childNodes.length; t<m; t++) {
					if (this.oTagList.childNodes[t].firstChild.firstChild.checked) {
						aTagConditions.push('aTags:"' + this.oTagList.childNodes[t].firstChild.firstChild.value + '"')
					}
				}
			}
			return aTagConditions.join(this.bAndOperator ? ' and ' : ' or ')
		},
		
		clearTagSelection: function(bUpdate) {
			if (this.bInitialized) {
				for (var t=0, m=this.oTagList.childNodes.length; t<m; t++) {
					px.html.Element.removeClassName(this.oTagList.childNodes[t].firstChild, 'active')
					this.oTagList.childNodes[t].firstChild.firstChild.checked = false
				}
				if (bUpdate == null || bUpdate == true) {
					this.oParent.oParent.updateSearch()
				}
			}
		},
		
		callAction: function(sAction)
		{
			switch (sAction) {
				case 'clearSelection':
					this.clearTagSelection()
				break
				case 'andOperator':
					this.bAndOperator = true
					this.oParent.oParent.updateSearch()
				break
				case 'orOperator':
					this.bAndOperator = false
					this.oParent.oParent.updateSearch()
				break
			}
		}
	}
)