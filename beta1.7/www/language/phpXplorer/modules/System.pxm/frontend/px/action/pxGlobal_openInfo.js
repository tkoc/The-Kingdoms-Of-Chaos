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
px.Class.define('px.action.pxGlobal_openInfo')

Object.extend(
	px.Statics,
	{
		bInitialized: false,
		oPanels: {},

		init: function()
		{
			if (this.bInitialized) {
				return
			}

			this.oBox = $('infoBox')
			this.oBox.style.display = 'none'

			this.oTabview = new px.ui.tabview.Tabview(this, this.oBox, this.oBox.firstChild)

			var oInfoBox = $('infoBox')
			for (var c=0, l=oInfoBox.childNodes.length*2; c<l; c=c+2) {
				var oNode = oInfoBox.childNodes[c]
				this.oPanels[oNode.id] = oNode
				this.oTabview.addTab(oNode.id, oNode.title)
			}

			this.oTabview.onTabClick = px.lang.Function.bind(this.tabClick, this)
			this.oTabview.setSelected('debugPane')
			//var oPane = $('infoPane')
			//oPane.innerHTML = pxGlobal___openDoc.loadDoc('license.html')
			
			//pxp. get('sAction=pxGlobal_openInfo')
	
			if (pxp.bDebug) {
				this.oDisposeButton = $('unloadButton')
				this.oDisposeButton.onclick = pxUnload
				this.oPhpInfoButton = $('phpInfoButton')
				this.oPhpInfoButton.onclick = px.action.pxGlobal_openPhpInfo.run
				var oPhpRuntime = $('phpRuntime')
				oPhpRuntime.appendChild(document.createTextNode(pxp.iPhpRuntime))
			}
	
			this.bInitialized = true
		},
	
		run: function(sDefaultPaneId) {
			this.init()
			this.resize()
			this.oTabview.setSelected(sDefaultPaneId)
			pxp.showOverlay(true)
			this.oBox.style.display = 'block'
			return false
		},
	
		resize: function()
		{
			if (!this.bInitialized) {
				return
			}
	
			var iWidth = document.body.offsetWidth || window.innerWidth
			var iHeight = document.body.offsetHeight || window.innerHeight
	
			var iBoxWidth = iWidth > 640 ? 640 : Math.round(iWidth * 0.75)
			var iBoxHeight = iHeight > 480 ? 480 : Math.round(iHeight * 0.75)
	
			this.oBox.style.width = iBoxWidth + 'px'
			this.oBox.style.height = iBoxHeight + 'px'
	
			this.oBox.style.left = iWidth / 2 - iBoxWidth / 2 - 5 + 'px'
			this.oBox.style.top = iHeight / 2 - iBoxHeight / 2 - 5 + 'px'
	
			var oPane = $('cachePane')
			if (oPane) {
				$('cachePane').firstChild.style.height = iBoxHeight + 'px'
			}
		},
	
		dispose: function()
		{
			if (this.bInitialized)
			{	
				this.oTabview.dispose()
	
				this.oBox.onclick = null
				if (pxp.bDebug) {
					this.oDisposeButton.onclick = null
					this.oPhpInfoButton.onclick = null
				}

				px.core.Object.prototype._disposeFields.call(
					this, 'oBox', 'oTabview', 'oPanels', 'oDisposeButton', 'oPhpInfoButton'
				)
			}
			pxp.log('Dispose pxGlobal_openInfo')
		},
	
		tabClick: function(sId)
		{		
			var oPanel = this.oPanels[sId]
	
			switch (sId) {
				case 'cachePane':
					if (oPanel.firstChild.src.indexOf('dummy') > -1) {
						oPanel.firstChild.src = './../build.php'
					}
					break
			}
	
			for (var t in this.oTabview.oTabs) {
				this.oPanels[this.oTabview.oTabs[t].sId].style.display = 'none'
			}
			oPanel.style.display = 'block'
		},
	
		build: function() {
			window.open(
				'./../build.php',
				px.util.getRandomId(),
				'toolbar=no,location=yes,menubar=yes,resizable=yes,scrollbars=yes'
			)
		}
	}
)