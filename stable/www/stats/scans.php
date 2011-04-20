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
<?
require_once("private/protect.php");
require_once("private/konf.nohack.php");
//error_reporting(E_ALL);
?>
<HTML>
<HEAD>
<SCRIPT language=JavaScript type=text/javascript>
	<!--

	function LmOver(elem, clr)
	{
		elem.className = clr;
		elem.style.cursor = 'hand';
	}

	function LmOut(elem, clr)
	{
		elem.className = clr;
	}

	function LmDown(elem, clr)
	{
		elem.className = clr;
	}

	function LmUp(path)
	{
		location.href = path;
	}

	//-->
</SCRIPT>
<script language="javascript" type="text/javascript">
<!--
function copy_clip(navn) {
var meintext = document.getElementById(navn).innerHTML;   
 if (window.clipboardData) 
	{
	window.clipboardData.setData("Text", meintext);
	
	}
	else if (window.netscape) 
	{ 
	
	netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
	
	var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
	if (!clip) return;
	
	var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
	if (!trans) return;
	
	trans.addDataFlavor('text/unicode');
	
	var str = new Object();
	var len = new Object();
	
	var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
	
	var copytext=meintext;
	
	str.data=copytext;
	
	trans.setTransferData("text/unicode",str,copytext.length*2);
	
	var clipid=Components.interfaces.nsIClipboard;
	
	if (!clip) return false;
	
	clip.setData(trans,null,clipid.kGlobalClipboard);
	
	}
}
//-->
</script> 
<TITLE>The Kingdom of Chaos statistics</TITLE>
<LINK href="style.css" type=text/css rel=stylesheet>
</HEAD>
<BODY>

<?
include_once('topstat.php.inc');
?>

<BR><BR>

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TR>
	 <TD vAlign=top>

<?
include_once('meny.php.inc');
?>
	 </TD>
	 <TD vAlign=top width="100%">
		<DIV align=center>
		<TABLE width="85%">
		  <TR>
			 <TD>

<?
echo"<table>";
echo"<tr><td width=50%>";
if ($report) {
   $result = mysql_query("SELECT data FROM parsed_scans WHERE scan_id='".$report."'");
   if ($data = mysql_fetch_row($result)) {
      echo "<pre id=dajm>";        
      echo $data[0];
	   echo "</pre><br>";
      echo "<br><font color=red>Real nw score you see in TKOC might not be up to date.</font><br><br>";
   }
}
echo"</td><td width=50%><table>";
$i=0;
$result = mysql_query("SELECT * FROM parsed_scans ORDER BY tick DESC LIMIT 25");
while($data = mysql_fetch_row($result)) {
   $i++;
   echo "
  			<tr>
						<td class='two' style='text-align: right;white-space:nowrap;' width='28' nowrap='nowrap'>$i</td>
                	<td class='two' style='text-align: left;white-space:nowrap;' width='99%' nowrap='nowrap'><a href='rank.php?action=province&amp;prov=".$data[2]."'>$data[2]</a></td>
                	<td class='two' style='text-align: right;white-space:nowrap;' width='40' nowrap='nowrap'><a href='?report=".$data[0]."'>tick $data[1]</a></td>
        </tr>
        "; 
}
echo"</table></td></tr></table>";
?>			
				
			 </TD>
		  </TR>
		</TABLE>
		</DIV>
	 <BR><BR><BR>
	 </TD>
  </TR>
</TABLE>

</BODY>
</HTML>