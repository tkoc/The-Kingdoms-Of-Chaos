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
px.Class.define('px.html.Element')

Object.extend(
	px.Statics,
	{
		addClassName: function(oElement, sClass) {
			var aClasses = oElement.className.split(/\s+/)
			if (!px.lang.Array.contains(aClasses, sClass)) {
				aClasses.push(sClass)
				oElement.className = aClasses.join(' ')
			}
		},

		removeClassName: function(oElement, sClass) {
			var aClasses = oElement.className.split(/\s+/)
			px.lang.Array.remove(aClasses, sClass)
			oElement.className = aClasses.join(' ')
		},

		hasClassName: function(oElement, sClass) {
			var aClasses = oElement.className.split(/\s+/)
			return px.lang.Array.contains(aClasses, sClass)
		},

		appendImage: function(oNode, sSrc, oInsertBefore, iSize, sClass)
		{
			var oImage = new Image()

			if (pxp.bIe && sSrc.indexOf('.png') > -1) {
				oImage.src = pxConst.sGraphicUrl + '/dummy.png'
				oImage.style.filter = pxConst.ALPHA_IMAGE_START + sSrc + pxConst.ALPHA_IMAGE_STOP
			} else {
				oImage.src = sSrc
			}

			if (iSize) {
				oImage.style.width = iSize + 'px'
				oImage.style.height = iSize + 'px'
			}

			if (sClass) {
				oImage.className = sClass
			}

			if (oInsertBefore) {
				return oNode.insertBefore(oImage, oInsertBefore)
			} else {
				return oNode.appendChild(oImage)
			}
		},

		changeImage: function(oImage, sSrc) {
			if (pxp.bIe && sSrc.indexOf('.gif') == -1) {
				oImage.style.filter = pxConst.ALPHA_IMAGE_START + sSrc + pxConst.ALPHA_IMAGE_STOP
			} else {
				oImage.src = sSrc
			}
		},
		
		getLeftOffset: function(oNode, oUpperNode) {
			var x = 0
			for (var o=oNode; o; o=o.offsetParent) {
				x += o.offsetLeft
				if (oUpperNode && o == oUpperNode) {
					break
				}
			}
			return x
		},

		getTopOffset: function(oNode, oUpperNode) {
			var x = 0
			for (var o=oNode; o; o=o.offsetParent) {
				x += o.offsetTop
				if (oUpperNode && o == oUpperNode) {
					break
				}
			}
			return x
		},

		hideElements: function(sElement) {
			var oNodes = document.getElementsByTagName(sElement)
			for (var n=0; n<oNodes.length; n++) {
				oNodes[n].style.visibility = 'hidden'
			}
		},

		showElements: function(sElement) {
			var oNodes = document.getElementsByTagName(sElement)
			for (var n=0; n<oNodes.length; n++) {
				if (oNodes[n].style.visibility) {
					oNodes[n].style.visibility = ''
				}
			}
		},

	  hide: function(oElement) {
	    oElement.style.display = 'none'
	  },

	  show: function(oElement) {
	    oElement.style.display = ''
	  },
	  
	  switchElement: function(oElement) {
	  	oElement.style.display =
	  		oElement.style.display == 'none' ? '' : 'none'
	  },
	  
	  setOpacity: function(oElement, iAlpha)
	  {
			var oStyle = oElement.style
			
			if (oStyle.MozOpacity != undefined ) {
				oStyle.MozOpacity = iAlpha
			}
			else if (oStyle.filter != undefined ) {
				oStyle.filter = 'alpha(opacity=0)'
				oElement.filters.alpha.opacity = iAlpha * 100
			}
			else if (oStyle.opacity != undefined ) {
				oStyle.opacity = iAlpha
			}
		}
	}
)