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
px.Class.define('px.action.pxFiles_openPreview')

Object.extend(
	px.Statics,
	{
		bInitialized: false,
		oLightbox: null,
		oCurrentView: null,
		oActiveImage: null,
		_oLoadingImage: new Image(),
		sCurrentImage: null,

		init: function()
		{
			if (this.bInitialized) {
				return
			}

			var cFunction = px.lang.Function

			this.oLightbox = $('lightbox')
			var oLightbox = this.oLightbox
			oLightbox.onmousemove = cFunction.bindEvent(this.boxMouseMove, this)
			oLightbox.onmouseout = cFunction.bindEvent(this.boxMouseOut, this)
			oLightbox.onclick = cFunction.bindEvent(this.boxClick, this)

			this.oPrevious = $('previousImage')
			this.oNext = $('nextImage')
			
			this.oPrevious.onclick = cFunction.returnFalse
			this.oNext.onclick = cFunction.returnFalse

			this.oActiveImage = oLightbox.firstChild
			this.bInitialized = true
		},

		run: function(oView, sAction)
		{
			this.init()
			this.oCurrentView = oView
			pxp.showOverlay(true)
			this.sCurrentImage = this.oCurrentView.oActiveItem.sFilename.substr(1)
			this.loadImage()
			
			
			/*
			 * 
			for (var sSelected in oView.oSelected)
			{
				var oItem = oView.oSelected[sSelected]
				var sType = oItem.getType()
				var aExtensions = pxp.oTypes[sType].aExtensions

				for (var e=0, m=aExtensions.length; e<m; e++) {
					if (px.util.previewCheck(aExtensions[e])) {
						px.action.pxImage_openView.run(oView, sAction)
						return true
					}
				}

				px.action.pxMetaFiles_openView.run(oView, sAction)
			}
			return false
			 * 
			 */
			
			
		},

		loadImage: function()
		{
			var oShare = this.oCurrentView.oParent.oShare
			
			if (oShare.iImageResize == 0) { // Server resize
				var sSrc = './preview.php?sShare=' + oShare.sId +
					'&sAction=_openPreview&sPath=' + encodeURIComponent(this.sCurrentImage) +
					'&iWidth=' + Math.round(pxp.oParentNode.offsetWidth * 0.75) + '&iHeight=' + Math.round(pxp.oParentNode.offsetHeight * 0.75)
			} else { // Client resize
				var sSrc = px.util.buildPath(
					oShare.sUrl,
					this.sCurrentImage
				)
			}

			$('loading').style.visibility = 'visible'

			this._oLoadingImage.onload = px.lang.Function.bind(this._onImageLoad, this)
			this._oLoadingImage.onerror = px.lang.Function.bind(this._onImageError, this)
			
			this._oLoadingImage.src = sSrc

		},
		
		_onImageError: function() {
			pxp.hideOverlay()
		},

		_onImageLoad: function()
		{
			this._oLoadingImage.onload = null

			if ($('overlay').style.display == 'none') {
				return false
			}

			this.resize()

			this.oActiveImage.src = this._oLoadingImage.src
			
			this.oActiveImage.title = this.oCurrentView.oActiveItem.sFilename

			$('loading').style.visibility = 'hidden'

			if (this.oLightbox.style.display != 'block') {
				this.oLightbox.style.display = 'block'
			}

			if (navigator.appVersion.indexOf('MSIE') != -1) {
				px.util.sleep(200)
			}

			this.setArrows()
		},

		setArrows: function()
		{
			var iArrowHeight = this.oPrevious.offsetHeight
			var iArrowWidth = this.oPrevious.offsetWidth
	
			this.oPrevious.style.left = '8px'
			this.oPrevious.style.top = this.oActiveImage.height / 2 - (iArrowHeight / 2) + 'px'
			this.oNext.style.left = this.oActiveImage.width + 8 - iArrowWidth + 'px'
			this.oNext.style.top = this.oActiveImage.height / 2 - (iArrowHeight / 2) + 'px'	
		},
	
		boxMouseOut: function(oEvent) {
			this.oPrevious.style.visibility = 'hidden'
			this.oNext.style.visibility = 'hidden'				
		},
	
		boxMouseMove: function(oEvent)
		{
			var oLightbox = this.oLightbox
			var iBoxX = oLightbox.offsetLeft
			var iBoxY = oLightbox.offsetTop
			var iBoxWidth = oLightbox.offsetWidth
			var iBoxHeight = oLightbox.offsetHeight
			
			this.oPrevious.style.visibility = 'hidden'
			this.oNext.style.visibility = 'hidden'

			this.setArrows()
			
			if (this._oLoadingImage.width > 80 && this._oLoadingImage.height > 60) {
				if (oEvent.clientY > iBoxY && oEvent.clientY < iBoxY + iBoxHeight) {		
					if (oEvent.clientX > iBoxX && oEvent.clientX < iBoxX + (iBoxWidth / 2)) {
						this.oPrevious.style.visibility = 'visible'
					}
					if (oEvent.clientX > iBoxX + (iBoxWidth / 2) && oEvent.clientX < iBoxX + iBoxWidth) {
						this.oNext.style.visibility = 'visible'
					}
				}
			}
			
		},
	
		boxClick: function(oEvent) {
			this.oLightbox.style.display = 'none'
			if (this.oNext.style.visibility == 'hidden') {
				this._nextImage(true)
			} else {
				this._nextImage()
			}
			this.oPrevious.style.visibility = 'hidden'
			this.oNext.style.visibility = 'hidden'
			this.loadImage()
		},

		_nextImage: function(bPrevious)
		{
			var oView = this.oCurrentView
			//var sActiveFilename = oView.oActiveItem.sFilename
			var aResult = oView.oResults[oView.oParameters.getPath()]
			var sNewImage
			var bFound = false
			var sCurrentName = px.util.basename(this.sCurrentImage)

			var i = bPrevious ? aResult.length-1 : 0;
			while(!sNewImage)
			{
				if (px.util.previewCheck(aResult[i].sExtension)) {
					if (bFound) {
						sNewImage = px.util.buildPath(aResult[i].sRelDir, aResult[i].sName).substr(1)
						break
					} else {
						if (aResult[i].sName == sCurrentName) {
							bFound = true
						}
					}
				}
				
				i = bPrevious ? i-1 : i+1
				
				if (i<0) {
					i = aResult.length-1
				}
				
				if (i == aResult.length) {
					i = 0
				}
			}

			this.sCurrentImage = sNewImage
		},
	
		resize: function()
		{
			if (!this.bInitialized) {
				return
			}

			var iWidth = document.body.offsetWidth || window.innerWidth
			var iHeight = document.body.offsetHeight || window.innerHeight

			var iImageWidth = this._oLoadingImage.width
			var iImageHeight = this._oLoadingImage.height
			
			if (this.oCurrentView.oParent.oShare.iImageResize == 1) { // Client resize
				if (iImageWidth > iWidth || iImageHeight > iHeight) {
					if (iImageWidth > iWidth) {
						iNewWidth = iWidth - 60
						iImageHeight = iImageHeight * (iNewWidth / iImageWidth)
						iImageWidth = iNewWidth
					}
					if (iImageHeight > iHeight) {
						iNewHeight = iHeight - 60
						iImageWidth = iImageWidth * (iNewHeight / iImageHeight)
						iImageHeight = iNewHeight
					}
				}
			}

			this.oActiveImage.width = iImageWidth
			this.oActiveImage.height = iImageHeight

			this.oLightbox.style.left = Math.round(iWidth / 2 - iImageWidth / 2 - 5) + 'px'
			this.oLightbox.style.top = Math.round(iHeight / 2 - iImageHeight / 2 - 5) + 'px'
		},

		dispose: function()
		{
			this.oCurrentView = null
			delete this.oActiveImage
			delete this._oLoadingImage
	
			if (this.bInitialized) {
				this.oLightbox.onmousemove = null
				this.oLightbox.onmouseout = null
				this.oLightbox.onclick = null
				this.oPrevious.onclick = null
				this.oNext.onclick = null
				this.oPrevious = null
				this.oNext = null
				this.oLightbox = null
			}

			px.core.Object.dispose.call(this)

			pxp.log('dispose pxFiles_openPreview')
		}		
	}
)