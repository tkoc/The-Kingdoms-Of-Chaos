<?php
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
?>
<!-- FOOTER -->

<div class="extra-space">&nbsp; </div>

</div>
<?php
	if($fb_user) {
		echo '
		<!-- Note: Include this div markup as a workaround for a known bug in this release on IE where you may get a "operation aborted" error --> 
		<div id="FB_HiddenIFrameContainer" style="display:none; position:absolute; left:-100px; top:-100px; width:0px; height: 0px;"></div> 
		<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
		<script type="text/javascript"> 
			FB_RequireFeatures(["CanvasUtil"], function(){ 
				FB.XdComm.Server.init("xd_receiver.htm"); 
				FB.CanvasClient.startTimerToSizeToContent();
			}); 
		</script>';
	}
	?>
</body></html>