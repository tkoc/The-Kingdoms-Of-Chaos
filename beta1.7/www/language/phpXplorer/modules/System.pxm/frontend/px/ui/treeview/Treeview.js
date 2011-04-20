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
px.Class.define('px.ui.treeview.Treeview',
{
	extend: px.io.RemoteView,

	construct: function(oParent, oParentNode, sTitle)
	{
		this.base(arguments, oParent, oParentNode)

		var cFunction = px.lang.Function

		this.oParameters.bHierarchical = true
		this.oParameters.bFilesize = false

		this.sTitle = sTitle
		this.aItems = []
		this.oSelected = {}

		this.sActiveItem = '/'

		this.onNodeClick

		px.html.Element.addClassName(oParentNode, 'noSelect')
		
		this.oDiv = document.createElement('div')

		this.oDiv.className = 'pxTreeview'
		oParentNode.appendChild(this.oDiv)
		
		var oImage = new Image()
		oImage.src = pxConst.sGraphicUrl + '/dummy16.png'
		oImage.style.width = 0
		oImage.style.margin = 0
		this.oDiv.appendChild(oImage)
		
		//px.html.Element.appendImage(this.oRoot, pxConst.sGraphicUrl + '/types/pxDirectory_opened.png')
		
		oImage = new Image()
		oImage.src = pxConst.sGraphicUrl + '/types/pxDirectory_opened.png'
		this.oDiv.appendChild(oImage)
		
		this.oRootItem = new px.ui.treeview.Node()
		var oRoot = this.oRootItem
			
		oRoot.oParent = this
		oRoot.sFilename = '/'
		oRoot.bDirectory = true
		oRoot.bExpanded = true
		oRoot.oDiv = this.oDiv
		
		this.oDiv.onmouseover = cFunction.bindEvent(oRoot.nodeMouseOver, oRoot)
		this.oDiv.onmouseout = cFunction.bindEvent(oRoot.nodeMouseOut, oRoot)
		this.oDiv.onmouseup = cFunction.bindEvent(oRoot.nodeMouseUp, oRoot)

		this.aItems.push(oRoot)

		var oA = document.createElement('a')
		oA.href = pxConst.DUMMY_LINK
		this.oDiv.appendChild(oA)

		oA.appendChild(document.createTextNode(this.sTitle))
		oA.onclick = cFunction.bindEvent(this.oRootItem.linkClick, this.oRootItem)

		oImage = null
		oA = null
	},
	
	destruct: function()
	{
		this.oDiv.onmouseover = null
		this.oDiv.onmouseout = null
		this.oDiv.onmouseup = null

		this._disposeFields('oSelected', 'onNodeClick', 'oDiv', 'oRootItem')
		this._disposeContents('aItems')
	}
})

Object.extend(
	px.Proto,
	{
		getNode: function(sPath) {
			for (var n=0, m=this.aItems.length; n<m; n++) {
				if (this.aItems[n].sFilename == sPath) {
					return this.aItems[n]
				}
			}
			return false
		},
		
		removeNode: function(sPath) {
			for (var n=0, m=this.aItems.length; n<m; n++) {
				if (this.aItems[n].sFilename == sPath) {
					
					var oDiv = this.aItems[n].oDiv
					this.aItems[n].dispose()
					oDiv.parentNode.removeChild(oDiv)
					oDiv = null

					/*
		if (bRemove) {
			while (this.oDiv.firstChild) {
				this.oDiv.removeChild(this.oDiv.firstChild)
			}
			this.oDiv.parentNode.removeChild(this.oDiv)
		}
*/
					
					

					delete this.aItems[n]
					this.aItems.splice(n, 1)
					return true
				}
			}
			return false
		},
		
		switchIconState: function(oNode, bOn) {
			if (oNode.iIndex > -1) {
				var oObject = this.oResults[oNode.getPath()][oNode.iIndex]
				var sExtendedType = pxp.getExtendedType(oObject.sType, oObject.sExtension)
				var sModule = pxp.oTypes[oObject.sType]['sModule']
				oNode.oDiv.childNodes[1].src = pxConst.sModuleUrl + '/' + sModule + '.pxm/graphics/types/' + sExtendedType + (bOn ? '_opened' : '') + '.png'
			} else {
				// root node
				oNode.oDiv.childNodes[1].src = pxConst.sModuleUrl + '/System.pxm/graphics/types/pxDirectory' + (bOn ? '_opened' : '') + '.png'
			}
		},
		
		setActiveItem: function(sPath) {
			if (sPath) {
				var oNode = this.getNode(this.sActiveItem)
				if (oNode) {
					this.switchIconState(oNode)
				}
				this.sActiveItem = sPath
			}
			var oSelected = this.getNode(this.sActiveItem)
			if (oSelected.bDirectory) {
				this.switchIconState(oSelected, true)
			}
		//	var iScroll = this.oParentNode.scrollTop
		//	if (!(oSelected.oDiv.offsetTop > iScroll && oSelected.oDiv.offsetTop < iScroll + this.oParentNode.offsetHeight - 20)) {
		//		this.oParentNode.scrollTop = oSelected.oDiv.offsetTop
		//	}
		},

		collapseAll: function() {
			for (var n=0, m=this.aItems.length; n<m; n++) {
				if (this.aItems[n].sFilename != '/') {
					if (this.aItems[n].bExpanded) {
						this.aItems[n].expand()
					}
				}
			}
			return false
		},
		
		_update: function(sContainerPath)
		{
			var cFunction = px.lang.Function
			var oDoc = document
			var oLastNode
		
			var sBaseDir = sContainerPath.substr(sContainerPath.indexOf(':') + 1)
			var oContainerNode = this.getNode(sBaseDir)
			var oParentNode = oContainerNode.oDiv
		
			if (sBaseDir != '/'){
				sBaseDir += '/'
				var bIndent = true
			} else {
				var bIndent = false
			}
		
		/*
			alert(sBaseDir)
			if (sBaseDir.indexOf('pxVirtualDirectory') > -1) {
				sBaseDir = px.util.dirname(px.util.dirname(sBaseDir))
				alert(sBaseDir)
			}
		*/
		
			var aObjects = this.oResults[sContainerPath]
		
			if (!aObjects.length || aObjects.length == 0) {
				oParentNode.firstChild.onclick = null
				oParentNode.firstChild.src = pxConst.sGraphicUrl + '/dummy16.png'
				oContainerNode.bExpanded = false
			} else {
				oContainerNode.bExpanded = true
			}
		
			var oExistingPaths = {}
			if (oContainerNode.bRendered) {
				for (var c=3, m=oParentNode.childNodes.length; c<m; c++) {
					oExistingPaths[sBaseDir + oParentNode.childNodes[c].childNodes[2].firstChild.nodeValue] = true
				}
			}
		
			for (var r=0, m=aObjects.length; r<m; r++)
			{
				var oObject = aObjects[r]
		
				var sPath = sBaseDir + oObject.sName
		
				delete oExistingPaths[sPath]
		
				var oNode = this.getNode(sPath)
				if (oNode) {
					oNode.iIndex = r
					oLastNode = oNode
					continue
				}
		
				oNode = new px.ui.treeview.Node()
				oNode.oParent = this
				oNode.sFilename = sPath
				oNode.bDirectory = oObject.bDirectory
				oNode.iIndex = r
				this.aItems.push(oNode)
		
				oNode.oDiv = oDoc.createElement('div')
				var oDiv = oNode.oDiv
				oDiv.className = 'pxTreeviewNode'
				if (bIndent) {
					oDiv.style.marginLeft = '1.818em'
				}
		
				if (oContainerNode.bRendered && oLastNode && oLastNode.oDiv.nextSibling) {			
					oParentNode.insertBefore(oDiv, oLastNode.oDiv.nextSibling)
				} else {
					oParentNode.appendChild(oDiv)
				}
		
				if (!oContainerNode.bExpanded) {
					oDiv.style.display = 'none'
				}
		
				var sExtendedType = pxp.getExtendedType(oObject.sType, oObject.sExtension)
				var sModule = pxp.oTypes[oObject.sType]['sModule']
		
				var oImage = new Image()
				if (oObject.bDirectory) {
					oImage.src = pxConst.sGraphicUrl + '/expand.png'
					oImage.onclick = cFunction.bindEvent(oNode.expand, oNode)
					oImage.onmouseover = cFunction.bindEvent(oNode.expandOver, oNode)
				} else {
					oImage.src = pxConst.sGraphicUrl + '/dummy16.png'
				}
				oDiv.appendChild(oImage)
		
				oImage = new Image()
				oImage.src = pxConst.sModuleUrl + '/' + sModule + '.pxm/graphics/types/' + sExtendedType + '.png'
				oDiv.appendChild(oImage)
				//pxp.appendImage(oDiv, pxConst.sModuleUrl + '/' + sModule + '.pxm/graphics/types/' + sExtendedType + '.png')
		
				var oA = document.createElement('a')
				oA.href = pxConst.DUMMY_LINK
				oA.onclick = cFunction.bindEvent(oNode.linkClick, oNode)
				oDiv.appendChild(oA)
				oA.appendChild(oDoc.createTextNode(oObject.sName))
		
				oNode.oNameNode = oA
		
				oDiv.onclick = cFunction.bindEvent(oNode.itemClick, oNode)
				oDiv.onmouseover = cFunction.bindEvent(oNode.nodeMouseOver, oNode)
				oDiv.onmouseout = cFunction.bindEvent(oNode.nodeMouseOut, oNode)
				oDiv.onmousedown = cFunction.bindEvent(oNode.mouseDown, oNode)
				
				if (oObject.bDirectory) {
					oDiv.onmouseup = cFunction.bindEvent(oNode.nodeMouseUp, oNode)
				}
		
				oLastNode = oNode
			}
		
			if (oContainerNode.bRendered) {
				for (var sPath in oExistingPaths) {
					this.removeNode(sPath)
				}
			}
		
			oContainerNode.bRendered = true
		
			this.setActiveItem()
		
			oNode = null
			oDiv = null
			oImage = null
			oA = null
		},
		
		dragCleanup: function() {
			px.io.RemoteView.dragCleanup.call(this)
		},
		
		clearSelection: function() {
			for (var sSelected in this.oSelected) {
				// Item could already be moved
				try {
					this.oSelected[sSelected].oDiv.childNodes[2].className = null
				} catch (e) {
					
				}
			}
		}
	}
)