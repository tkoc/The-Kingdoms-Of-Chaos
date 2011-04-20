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
px.Class.define('px.Event')

Object.extend(
	px.Statics,
	{
		element	: function(oEvent) {
			return oEvent.target || oEvent.srcElement
		},

		_aListeners: [],

		_addAndCache: function(oElement, sName, oListener, bCapture) {
			if (oElement.attachEvent) {
				oElement.attachEvent('on' + sName, oListener)
			} else {
				oElement.addEventListener(sName, oListener, bCapture)
			}
			px.Event._aListeners.push([oElement, sName, oListener, bCapture])
		},

		unloadCache: function() {
			var cEvent = px.Event
			for (var i=0, l=cEvent._aListeners.length; i<l; i++) {
				cEvent.removeListener.apply(this, cEvent._aListeners[i])
				cEvent._aListeners[i][0] = null
			}
			cEvent._aListeners = null
		},

		addListener: function(oElement, sName, oListener, bCapture) {
			//if (sName == 'keypress' && (Prototype.Browser.WebKit || oElement.attachEvent)) {
			//	sName = 'keydown'
			//}
			px.Event._addAndCache(oElement, sName, oListener, bCapture || false)
		},

		removeListener: function(oElement, sName, oListener, bCapture) {
			//if (sName == 'keypress' && (Prototype.Browser.WebKit || oElement.attachEvent)) {
			//	sName = 'keydown'
			//}
			if (oElement.removeEventListener) {
				oElement.removeEventListener(sName, oListener, bCapture || false)
			} else if (oElement.detachEvent) {
				try {
					oElement.detachEvent('on' + sName, oListener)
				} catch (e) {}
			}
		},
		
		pointerX: function(oEvent) {
			return oEvent.pageX || (oEvent.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft))
		},
		
		pointerY: function(oEvent) {
			return oEvent.pageY || (oEvent.clientY + (document.documentElement.scrollTop || document.body.scrollTop))
		}
	}
)