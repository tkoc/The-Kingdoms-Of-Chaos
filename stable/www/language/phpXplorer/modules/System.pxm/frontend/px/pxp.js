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

;;; var iStart = new Date().getTime()

var pxp =
{
	oTypes: null,
	oActions: null,
	oData: null,
	oDocs: null,

	_bInitialized: false,
	bStop: false,
	sShare: null,
	oParentNode: null,
	oMainBar: null,
	aClipboard: [],
	oClipboardSourceShare: null,
	sUser: null,
	bDebug: false,
	bDevelopment: false,
	sEncoding: 'UTF-8',
	iNewFileCount: 1,
	sUrl: './action.php',

	oShareview: null,
	_oSplitview: null,
	bAltDown: false,
	bShiftDown: false,
	bDragging: false,
	sDragType: null,
	oSelectedControl: null,
	bIe: document.all && navigator.userAgent.toLowerCase().indexOf('opera') == -1,
	bHold: false,

	init: function(oParentNode)
	{
		if (this.bStop) {
			return
		}

		this.oParentNode = oParentNode

		this.oData = px.io.Request.get('sAction=openIndexData&sShare=' + this.sShare)
		if (this.oData == false) {
			alert('Could not open index data')
		}

		this.bDebug = this.oData.bDebug
		this.bDevelopment = this.oData.bDevelopment
		this.sUser = this.oData.sUser

		this.initMainBar()
		this.resizeMain()

		this.oGlobalView = new px.ui.Actionview()
		this.oGlobalView.sClass = 'globalBar'
		this.oGlobalView.sIcon = 'globalview.png'
		this.oGlobalView.sDropDownIcon = 'dropDownDark.png'

		this.oGlobalView.addAction(
			'home',
			oTranslation['action.pxGlobal_openHome'],
			px.action.pxGlobal_openHome,
			true,
			pxConst.sGraphicUrl + '/home.png'
		)

		this.oGlobalView.addAction(
			'doc',
			oTranslation['documentation'],
			px.action.pxFiles__openInline,
			false,
			pxConst.sGraphicUrl + '/help.png',
			{
				sPath: './docType.php'
			}
		)

		if (this.oData.bContact) {
			this.oGlobalView.addAction(
				'contact',
				oTranslation['contact'],
				px.action.pxFiles__openInline,
				false,
				pxConst.sGraphicUrl + '/mail.png',
				{
					sPath: './action.php?sAction=openContact'
				}
			)
		}

		if (this.bDevelopment && this.oData.sUser == 'root') {
			this.oGlobalView.addAction(
				'development',
				oTranslation['development'],
				px.action.pxFiles__openInline,
				false,
				pxConst.sGraphicUrl + '/development.png',
				{
					sPath: './../development.php'
				}
			)
		}

		if (this.bDebug) {
			this.oGlobalView.addAction(
				'phpInfo',
				oTranslation['action.pxGlobal_openPhpInfo'],
				px.action.pxFiles__openInline,
				false,
				pxConst.sGraphicUrl + '/phpInfo.png',
				{
					sPath: './action.php?sAction=openPhpInfo'
				}
			)
		}

		this.oShareview = new px.ui.Actionview()
		this.oShareview.sClass = 'shareBar'
		this.oShareview.sIcon = 'shares.png'
		this.oShareview.sTitle = oTranslation['sShare']
		this.oShareview.iMenuType = pxConst.MENU_BUTTON
		this.oShareview.bHighlightLoaded = true

		for (var sBookmark in this.oData.aBookmarks)
		{
			var sTitle = oTranslation['share.' + sBookmark] || this.oData.aBookmarks[sBookmark] || sBookmark

			this.oShareview.addAction(
				sBookmark,
				sTitle,
				px.action.pxGlobal___openShareview,
				sBookmark == this.oData.sDefaultBookmark,
				pxConst.sGraphicUrl + '/pxShare.png'
			)
		}

		this._oSplitview = new px.ui.Splitview(true, '0.24', true)
		this._oSplitview.sId = 'pxSplitGlobal'
		this._oSplitview.sSnapIcon = 'Dark'
		this._oSplitview.iSnapSize = 320
		this._oSplitview.oActionview = this.oGlobalView
		this._oSplitview.aPersistentButtons = ['doc']

		this._oSplitview.oChild1 = this.oGlobalView
		this._oSplitview.oChild2 = this.oShareview
		this._oSplitview.init(this.oParentNode)

		// show start document
		if (pxp.sRelPathIn != '/')
		{
			var oView = this.oShareview.oSelected.oActionviewFiles
			oView.addAction(
				pxp.sRelPathIn,
				pxp.sRelPathIn,
				px.action.pxGlobal___openEditorview,
				true,
				'types/pxObject',
				{
					sPath: pxp.sRelPathIn,
					sType: 'pxObject',
					_sCalledAction: 'pxObject_editProperties'
				}
			)
			oView.showAction(pxp.sRelPathIn)

			var oListview = this.getListview()
			oListview.oParameters.sPath = pxp.sRelDir
			oListview.update()
		}


		var oTextarea = $('debugLog')
		if (oTextarea) {
			oTextarea.setAttribute('wrap', 'off')
		}

		px.Event.addListener(window, 'resize', px.lang.Function.bind(this.resize, this))

		px.Event.addListener(document, 'mouseup', pxp._bodyMouseUp)
		px.Event.addListener(document, 'keyup', pxp._bodyKeyUp)
		px.Event.addListener(document, 'keydown', pxp._bodyKeyDown)

		px.ui.ContextMenu.init()

		//px.dev.Pollution.check()
		//this.showClasses(null)

		this._bInitialized = true
	},
	
	/*
	bodyMouseOver: function(oEvent) {
		document.title = px.Event.element(oEvent).nodeName

//		px.ui.ContextMenu.hide()
	},
	*/

	dispose: function()
	{
		if (!this._bInitialized) {
			return
		}

		this.oMainBar.dispose()
		//this.oShareview.dispose()
		//this.oGlobalView.dispose()
		this._oSplitview.dispose()

//		px.core.Object.dispose.call(this)

		delete this.oData
		delete this.oTypes
		delete this.oActions
		delete this.oDocs

		this.oParentNode = null
		this.oMainBar = null
		this.aClipboard = null
		this.oShareview = null
		this._oSplitview = null
		this.oGlobalView = null
	},


	showLogin: function()
	{
		if (!this._oLogin)
		{
			this._oLogin = document.createElement('div')
			this._oLogin.id = 'login'
			document.body.appendChild(this._oLogin)

			var oLabel = document.createElement('label')
			oLabel.appendChild(document.createTextNode(oTranslation['property.sUsername']))
			this._oLogin.appendChild(oLabel)

			var oInputName = document.createElement('input')
			oInputName.type = 'text'
			oInputName.id = oInputName.name = 'pxAuthUser'
			this._oLogin.appendChild(oInputName)

			var oLabel = document.createElement('label')
			oLabel.appendChild(document.createTextNode(oTranslation['property.sPassword']))
			this._oLogin.appendChild(oLabel)
	
			oInputPassword = document.createElement('input')
			oInputPassword.type = 'password'
			oInputPassword.id = oInputPassword.name = 'pxAuthPassword'
			this._oLogin.appendChild(oInputPassword)
			
			this.oButtonLogin = document.createElement('button')
			this._oLogin.appendChild(this.oButtonLogin)
			//px.Event.addListener(this.oButtonLogin, 'click', px.action.pxGlobal_openLogin.login)
			this.oButtonLogin.appendChild(document.createTextNode(oTranslation['action.pxGlobal_openLogin']))
		
			this.oButtonLogin = document.createElement('button')
			this._oLogin.appendChild(this.oButtonLogin)
			//px.Event.addListener(this.oButtonLogin, 'click', px.action.pxGlobal_openLogin.guestLogin)
			this.oButtonLogin.appendChild(document.createTextNode(oTranslation['guestAccess']))
		}

		this.showOverlay(false)
		this._oLogin.style.display = ''		
	},

	
//	showObject: function(oParameter, sAction) {
		
//	},

	log: function(sEntry) {
		if (this.bDebug) {
			var oTextarea = $('debugLog')
			if (oTextarea) {
				oTextarea.value = sEntry + "\r\n" + oTextarea.value
			}
		}
	},

	setActiveControl: function(oControl) {
		if (this.oSelectedControl && oControl != this.oSelectedControl && this.oSelectedControl.clearSelection) {
			this.oSelectedControl.clearSelection()
		}
		this.oSelectedControl = oControl
	},

	showError: function(mError)
	{
		if (oTranslation['error.' + mError.sId]) {
			var sMessage = oTranslation['error.' + mError.sId]
		}
		else if (mError.sId) {
			var sMessage = mError.sId
		}
		else {
			var sMessage = mError
		}

		if (mError.aValues) {
			for (var i=0; i<mError.aValues.length; i++) {
				sMessage = sMessage.replace('%s', mError.aValues[i])
			}
		} else {
			sMessage = sMessage.replace(/%s/g, '')
		}

		sMessage = sMessage.replace(/<br\/>/g, "\r\n")

		if (this.bDebug) {
			if (mError.sFileIn) {
				sMessage += ' - ' + mError.sFileIn + ' on line ' + mError.sLine
			}
			//alert(oError.aValues)
			pxp.log(oTranslation['error'] + ': ' + sMessage)
			px.action.pxGlobal_openInfo.run('debugPane')
		} else {
			alert(sMessage)
		}
	},

	resizeMain: function() {
		px.util.setMaxHeight($('main'))
	},

	resize: function(oEvent)
	{
		this.resizeMain()

		//this.oShareview.resize()
		this._oSplitview.resize(true)

		for (var a in this.oActions) {
			if (px.action[a] && px.action[a].resize) {
				px.action[a].resize()
			}
		}
	},

	showOverlay: function(bLoading) {
		var oOverlay = $('overlay')
		if (this.bIe) {
			if (!oOverlay.style.filter) {
				oOverlay.style.filter = pxConst.ALPHA_IMAGE_START + pxConst.sGraphicUrl + '/overlay.png' + pxConst.ALPHA_IMAGE_SCALE_STOP
			}
			px.html.Element.hideElements('select')
			document.body.ondragstart = px.lang.Function.cancelEvent
		}
		oOverlay.style.display = 'block'
		if (bLoading) {
			$('loading').style.display = 'inline'
		}
		if (!oOverlay.onclick) {
			oOverlay.onclick = px.lang.Function.bind(this.hideOverlay, this)
		}
	},

	hideOverlay: function()
	{
		if (px.action.pxFiles_openPreview.oActiveImage) {
			px.action.pxFiles_openPreview.oActiveImage.onload = null
		}

		$('loading').style.display = 'none'
		$('overlay').style.display = 'none'
		$('lightbox').style.display = 'none'
		$('infoBox').style.display = 'none'
		if($('login')) {
			$('login').style.display = 'none'
		}

		if (this.bIe) {
			px.html.Element.showElements('select')
			document.body.ondragstart = null
		}
	},

	logout: function() {
		//pxp.showLogin();
	
		top.location.href = './action.php?sAction=openLogout'
		return false
	},

	startDrag: function(sType) {
		var cFunction = px.lang.Function
		this.sDragType = sType
		this.bDragging = true
		document.body.onselectstart = cFunction.cancelEvent
		document.body.ondragstart = cFunction.cancelEvent
		if (document.body.addEventListener) {
			document.body.addEventListener('draggesture', cFunction.cancelEvent, false)
			px.html.Element.addClassName(document.body, 'noSelect')
		}
	},

	stopDrag: function() {
		this.bDragging = false
		document.body.onselectstart = null
		document.body.ondragstart = null
		if (document.body.removeEventListener) {
			document.body.removeEventListener('draggesture', px.lang.Function.cancelEvent, false)
			px.html.Element.removeClassName(document.body, 'noSelect')
		}
	},

	getExtendedType: function(sType, sExtension) {
		var oType = pxp.oTypes[sType]
		if (sExtension) {
			return sType + ((sExtension && sExtension != sType) ? '_' + sExtension : '')
		}
		if (oType.aExtensions[0] && oType.sId != oType.aExtensions[0] && !oType.bAbstract) {
			return sType + '_' + oType.aExtensions[0];
		} else {
			return sType
		}
	},

	addValidExtension: function(sPath, sType) {
		var oType = this.oTypes[sType]
		if (oType.aExtensions && oType.aExtensions.length) {
			var bIn = false
			for (var e=0; e<oType.aExtensions.length; e++) {
				var sExtension = oType.aExtensions[e]
				var iPos = sPath.lastIndexOf('.' + sExtension)
				if (iPos > -1 && iPos == (sPath.length - sExtension.length - 1)) {
					bIn = true
				}
			}
			if (!bIn && oType.aExtensions[0] && oType.aExtensions[0] != '') {
				sPath += '.' + oType.aExtensions[0]
			}
		}
		return sPath
	},

	showDoc: function(sPath)
	{
		this.oGlobalView.showAction('doc')
		
		var aPart = sPath.split('/')

		if (aPart[0] == 'type') {
			this.oGlobalView.oActions['doc'].oIframe.src =
				'./docType.php?sType=' + aPart[1]
		} else if (aPart[0] == 'action') {
			
		} else {
			
		}
		
		this._oSplitview.showFirst()
		
		return false
	},
	
	editDoc: function(sDocType, sId)
	{
		var sLanguage = pxp.oData.sUserLanguage
		switch (sDocType) {
			case 'type':
				var sPath = './modules/' + pxp.oTypes[sId].sModule + '.pxm/documentation/' + sLanguage + '/types/' + sId + '.xml'
			break;
			case 'action':
			break;
		}

		var sAction = 'pxTextFiles_edit'
		
		var oView = pxp.oShareview.oSelected.oActionviewFiles


			if (!oView.oActions[sPath]) {
				oView.addAction(
		 			sPath,
					sPath.indexOf('/') == 0 ? sPath.substr(1) : sPath,
					px.action.pxGlobal___openEditorview,
					true,
					'types/pxHtml',
					{
						sShare: 'phpXplorer',
						sPath: sPath,
						sType: 'pxHtml',
						_sCalledAction: sAction
					}
				)
			}
			oView.showAction(sPath)

	},
	
	refreshView: function(sPath) {
		this.oShareview.oSelected.refreshView(sPath)
	},

	getListview: function() {
		return this.oShareview.oSelected.oActionviewList.oSelected.oChild
	},
	
	initMainBar: function()
	{
		this.oMainBar = new px.ui.toolbar.Toolbar(this, $('mainBar'))
//		this.oMainBar.init()

		var sName = this.oData.oProfile.sFullName
		if (!sName) {
			sName = this.oData['sUser']
		} else {
			sName = this.translateTitle(sName)
		}

		var sUserLabel = sName
		if (sName != this.sUser) {
			sUserLabel += ' (' + this.sUser + ')'
		}

		this.oMainBar.addButton(
			{
				sId: 'title',
				sLabel: this.oData.sTitle,
				sIcon: 'logoIcon.png',
				sClass: 'pxTb pxTitle pxBarButton',
				oOnClick: pxp.openTitleLink,
				bDisabled: pxp.oData.sTitleLink == ''
			}
		)
		
		this.oMainBar.addButton(
			{
				sId: 'subTitle',
				bLabel: true,
				sLabel: this.oData.sSubTitle,
				bDisabled: true
			}
		)

		this.oMainBar.addButton(
			{
				sId: 'login',
				sLabel: oTranslation['toolbar.logInOut'],
				sIcon: 'keyGrey.png',
//				oOnClick: px.lang.Function.bind(pxp.showLogin, this),
				oOnClick: pxp.logout,
//				sTitle: oTranslation['user'] + ': ' + sUserLabel,
				bRight: true
			}
		)

		if (this.sUser != 'everyone') {
			this.oMainBar.addButton(
				{
					sId: 'user',
					sIcon: 'user.png',
					sLabel: sUserLabel,
					oOnClick: px.action.pxGlobal_openHome._editProfile,
					bRight: true
				}
			)
		}

		this.oMainBar.addButton(
			{
				sId: 'contact',
				sLabel: oTranslation['contact'],
				oOnClick: px.lang.Function.bind(pxp.showGlobalAction, this, 'contact'),
				sIcon: 'mail.png',
				bRight: true
			}
		)
	},

	translateTitle: function(sTitle) {
		if (sTitle && sTitle.indexOf('~') == 0) {
			return oTranslation[sTitle.substr(1)] || sTitle.substr(1)
		} else {
			return sTitle
		}
	},

	openTitleLink: function() {
		window.open(pxp.oData.sTitleLink)
		return false
	},
	
	showGlobalAction: function(sAction) {
		this._oSplitview.showFirst(null, true, sAction)
		return false
	},
	
	_bodyMouseUp: function(oEvent)
	{
		var oEvent = oEvent || window.event
		document.onmousemove = null

		px.ui.ContextMenu.hide()

		if (pxp.bDragging) {
			if(pxp.oSelectedControl && pxp.oSelectedControl.dragCleanup) {
				pxp.oSelectedControl.dragCleanup(oEvent)
			}
			pxp.stopDrag()
		}
	},
	
	_bodyKeyDown: function(oEvent)
	{
		var oEvent = window.event || oEvent
		if (oEvent.keyCode) {
			switch (oEvent.keyCode) {
				case 16:
					pxp.bShiftDown = true
					break
				case 17:
					pxp.bAltDown = true
					break
			}
		}
	},

	_bodyKeyUp: function(oEvent)
	{
		var oEvent = window.event || oEvent
		if (oEvent.keyCode) {
			switch (oEvent.keyCode) {
				case 16:
					pxp.bShiftDown = false
					break
				case 17:
					pxp.bAltDown = false
					break
				case 27:
					var oOverlay = $('overlay')
					if (oOverlay && oOverlay.style.display == 'block') {
						pxp.hideOverlay()
					}
					px.ui.ContextMenu.hide()
					break
				case 119: // F8
					px.action.pxGlobal_openInfo.run()
					break
				case 113: // F2
					px.action.pxObject_editRename.run(pxp.oSelectedControl)
					break
				case 72: // ALT + H = Help
					if (pxp.bAltDown)  alert(pxp.oShareview.oSelected.oChild)
					break
			}
		}
	}
	
	/*
	showClasses: function(oSuperclass, iSub)
	{		
		alert(oSuperclass)

		if (!iSub) iSub = 0

		for (var i in px.Class.__registry) {
			if (px.Class.__registry[i].superclass == oSuperclass) {
				var s = ''
				for (var ii=0; ii<iSub; ii++) {
					s += '  ';
				}
				this.showClasses(px.Class.__registry[i], iSub+1)
				pxp.log(s + i)
			}
		}
	}
	*/

	/*
	oContextMenuTimeout: null,	
	initContextMenuTimeout: function()
	{
		if (this.oContextMenuTimeout) {
			window.clearTimeout(this.oContextMenuTimeout)
		}
		this.oContextMenuTimeout = window.setTimeout('oContextMenu.hide()', 5000)
	},
	*/
/*
	,checkSubTree: function(oNode) {
		if (oNode.onblur) alert('onblur: ' + oNode.innerHTML)
		if (oNode.onchange) alert('onchange: ' + oNode.innerHTML)
		if (oNode.onclick) alert('onclick: ' + oNode.innerHTML)
		if (oNode.ondblclick) alert('ondblclick: ' + oNode.innerHTML)
		if (oNode.onerror) alert('onerror: ' + oNode.innerHTML)
		if (oNode.onfocus) alert('onfocus: ' + oNode.innerHTML)
		if (oNode.onkeydown) alert('onkeydown: ' + oNode.innerHTML)
		if (oNode.onkeypress) alert('onkeypress: ' + oNode.innerHTML)
		if (oNode.onkeyup) alert('onkeyup: ' + oNode.innerHTML)
		if (oNode.onload) alert('onload: ' + oNode.innerHTML)
		if (oNode.onmousedown) alert('onmousedown: ' + oNode.innerHTML)
		if (oNode.onmousemove) alert('onmousemove: ' + oNode.innerHTML)
		if (oNode.onmouseout) alert('onmouseout: ' + oNode.innerHTML)
		if (oNode.onmouseover) alert('onmouseover: ' + oNode.innerHTML)
		if (oNode.onmouseup) alert('onmouseup: ' + oNode.innerHTML)
		if (oNode.onreset) alert('onreset: ' + oNode.innerHTML)
		if (oNode.onselect) alert('onselect: ' + oNode.innerHTML)
		if (oNode.onsubmit) alert('onsubmit: ' + oNode.innerHTML)
		if (oNode.onunload) alert('onunload: ' + oNode.innerHTML)
		for (var c = 0, m = oNode.childNodes.length; c < m; c++) {
			this.checkSubTree(oNode.childNodes[c])
		}
	}
*/
}