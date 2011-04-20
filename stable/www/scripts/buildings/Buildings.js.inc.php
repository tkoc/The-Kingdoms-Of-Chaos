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
<script language='JavaScript'>
<!-- hide if java Script not enabled
/* Java Scripts for the Buildings class
 * 
 * Author: Øystein Fladby	25.02.2003
 *
 * Version: test
 * 
 */
	var lastGoldValue=0;
	var lastMetalValue=0;
	var lastAcresValue=0;
		
	////////////////////////////////////////////
	// calcFields( int, int )	
	////////////////////////////////////////////
	// Function to update the total cost of all 
	// buildings in showAllBuildings()
	////////////////////////////////////////////
	function calcFields( goldCost, metalCost, acresCost ) {
		document.Build.totalGoldCost.value=(document.Build.totalGoldCost.value-lastGoldValue+goldCost);
		document.Build.leftGold.value=(document.Build.totalGold.value-document.Build.totalGoldCost.value);
		document.Build.totalMetalCost.value=(document.Build.totalMetalCost.value-lastMetalValue+metalCost);
		document.Build.leftMetal.value=(document.Build.totalMetal.value-document.Build.totalMetalCost.value);
		document.Build.totalAcresCost.value=(document.Build.totalAcresCost.value-lastAcresValue+acresCost);
		document.Build.leftAcres.value=(document.Build.totalAcres.value-document.Build.totalAcresCost.value);		
	}
		
	////////////////////////////////////////////
	// checkResources()	
	////////////////////////////////////////////
	// Display a error if the user tries to build 
	// too much, then return false 
	// Remove this function to let the user build 
	// as much as possible and get a message
	// afterwards of what were built and not built
	////////////////////////////////////////////		
	function checkResources(){
		if( ( parseInt(document.Build.leftMetal.value) < 0 ) || 
			( parseInt(document.Build.leftGold.value)  < 0 ) ||
			( parseInt(document.Build.leftAcres.value) < 0 ) ) {
			alert('You don\'t have enough gold and/or metal and/or free acres to build all the selected buildings!');
			return false;
		} else {
			return true;
		}
	}	
	
	////////////////////////////////////////////
	// showDescription()	
	////////////////////////////////////////////
	// Open a new window and send info through 'get' in the URL
	////////////////////////////////////////////
	function showDescription( bID, acres, num ) {
		var linkPage = 'buildings/buildingDescription.php?bID='+bID+'&acres='+acres+'&num='+num;
		var descriptionWindow = 
window.open(linkPage,'buildingDescription','location=0,statusbar=0,width=500,height=400,menubar=0,toolbar=0,directories=0,resizable=1,scrollbars=1,');
		descriptionWindow.focus();
		return false;
	}
// un-hide -->
</script>
