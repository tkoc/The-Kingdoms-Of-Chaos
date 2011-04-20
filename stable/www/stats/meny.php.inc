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
<?php
include_once ("./menu.inc.php");
if (false) {
?>


<!-- Menu -->
      <TABLE style="MARGIN-LEFT: 10px" cellSpacing=0 cellPadding=0 width=120 align=left bgColor=#000000 border=0>
        <TR>
          <TD width="100%">
            <FORM action=rank.php method=get encType=multipart/form-data>
            <input type=hidden name='action' value='kingdom'>
            <TABLE class=navlink cellSpacing=1 cellPadding=1 width="100%" border=0>
              <TR>
                <TD class=menuheader width="100%">
                  <a href="http://phantasyrpg.com/main.php?view=145971"><img src="Battle_KnightvTroll.gif" border=0></a>
                </TD>
              </TR>
              <TR>
                <TD class=menuheader width="100%">
                  Menu:
                </TD>
              </TR>
              <TR>
                <TD class=menuentry onmousedown="LmDown(this, 'menuentry_hover')" onmouseover="LmOver(this, 'menuentry_hover')">
                  <A href="http://stats.tkoc.net/index.php">Home</A>
                </TD>
              </TR>
			  <TR>
                <TD class=menuentry onmousedown="LmDown(this, 'menuentry_hover')" onmouseover="LmOver(this, 'menuentry_hover')">
                  <A href="http://www.tkoc.net/scripts/showProvince.php">Return to Game</A>
                </TD>
              </TR>
			  <TR>
                <TD class=menuentry onmousedown="LmDown(this, 'menuentry_hover')" onmouseover="LmOver(this, 'menuentry_hover')">
                  <A href="http://www.tkoc.net/worldforum">WorldForum</A>
                </TD>
              </TR>
			  <TR>
                <TD class=menuentry onmousedown="LmDown(this, 'menuentry_hover')" onmouseover="LmOver(this, 'menuentry_hover')">
                  <A href="http://www.tkoc.net/worldforum/index.php?action=chat">IRC Chat</A>
                </TD>
              </TR>
			  <TR>
                <TD class=menuentry onmousedown="LmDown(this, 'menuentry_hover')" onmouseover="LmOver(this, 'menuentry_hover')">
                  <A href="http://www.tkoc.net/worldforum/index.php?action=paypal">Donate</A>
                </TD>
              </TR>
              <TR>
                <TD class=menuheader width="100%">Rankings: </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=province">Province</A>
                </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=kingdom">Kingdom</A>
                </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=orc">Orcs</A>
                </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=human">Humans</A>
                </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=elf">Elfs</A>
                </TD></TR>
                              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=dwarf">Dwarfs</A>
                </TD></TR>
                <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=undead">Undead</A>
                </TD></TR>
			<TR>
                <TD class=menuheader width="100%">Search: </TD></TR>
              <TR>
                <TD
                class=menuentry onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="rank.php?action=kingdom&amp;kd=1">KD
                  Browser</A> </TD>
				</TR>
              <TR>
                <TD class=menuheader width="100%">
                  Quick Lookup:
                </TD>
              </TR>
              <TR>
                <TD class=menuentry onmouseover="LmOver(this, 'menuentry_hover')" onmouseout="LmOut(this, 'menuentry')" width="100%">
                  <INPUT class=coord maxLength=3 size=1 name=kd>
                  <INPUT type=submit value="->">
                </TD>
              </TR>
            </FORM>
              <!--<TR>
                <TD class=menuheader width="100%">Tools: </TD></TR>
              <TR>
                <TD
                class=menuentry onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="parse.php">Scan Parser</A> </TD></TR>-->
<?
if($valid==strrev(soundex(name).md5($username))) {
   echo "
              /*<TR>
                <TD
                class=menuentry onmousedown=\"LmDown(this, 'menuentry_hover')\"
                onmouseover=\"LmOver(this, 'menuentry_hover')\"
                onmouseout=\"LmOut(this, 'menuentry')\" width=\"100%\"><A
                  href=\"targets.php\">Thief targets</A> </TD></TR>
              <TR>
                <TD
                class=menuentry onmousedown=\"LmDown(this, 'menuentry_hover')\"
                onmouseover=\"LmOver(this, 'menuentry_hover')\"
                onmouseout=\"LmOut(this, 'menuentry')\" width=\"100%\"><A
                  href=\"scans.php\">Last SoPs</A> </TD></TR>  
                <TD
                class=menuentry onmousedown=\"LmDown(this, 'menuentry_hover')\"
                onmouseover=\"LmOver(this, 'menuentry_hover')\"
                onmouseout=\"LmOut(this, 'menuentry')\" width=\"100%\"><A
                  href=\"logginn.php?cmd=logout\">Log Out</A> </TD></TR>*/                     
   ";
}
?>
              
              <!--<TR>
                <TD class=menuheader width="100%">Links: </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="http://www.tkoc.net/">TKOC Home</A> </TD></TR>
                  <?
if($valid!=strrev(soundex(name).md5($username))) {
   echo "
              <TR>
                <TD class=menuheader width=\"100%\">Login: </TD></TR>

                <TR>
 <TD class=menuentry
                onmousedown=\"LmDown(this, 'menuentry_hover')\"
                onmouseover=\"LmOver(this, 'menuentry_hover')\"
                onmouseout=\"LmOut(this, 'menuentry')\" width=\"100%\">
  <form method=POST ACTION='logginn.php'>
  <input type=hidden name=cmd value=login>
   <table ALIGN=CENTER>
      <tr>
         <td>
 <font size=\"-3\">Username:</font><br>
 <input type=text size=10 name=username tabindex=1 style='background-color: #000000; color: #FFEECC; font-family: Tahoma; font-weight:
bold; border-color: #5B5A56'>
                 </td>
          </tr>
          <tr>
                 <td>
                <font size=\"-3\">Password:</font><br>
                <input type=password size=10 name=password tabindex=2 style='background-color: #000000; color: #FFEECC; font-family: Tahoma;
font-weight: bold; border-color: #5B5A56'>
                </td>
        </tr>
        <tr>
        <td align='center'>
                <input type=submit name=submit value='Login' tabindex=3 style='background-color: #000000; color: #FFEECC; font-family: Tahoma;
font-weight: bold; border-color: #5B5A56'>
                </td>
        </tr>
   </table>
</font>
            </FORM>

</TD></TR>";
}
?>

              <TR>
                <TD class=menuheader width="100%">Login TKOC: </TD></TR>

                <TR>
 <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%">
  <form method=POST ACTION='http://www.tkoc.net/scripts/login.php' target="_new">
   <table ALIGN=CENTER>
      <tr>
         <td>
 <font size="-3">Username:</font><br>
 <input type=text size=10 name=username tabindex=4 style='background-color: #000000; color: #FFEECC; font-family: Tahoma; font-weight:
bold; border-color: #5B5A56'>
                 </td>
          </tr>
          <tr>
                 <td>
                <font size="-3">Password:</font><br>
                <input type=password size=10 name=password tabindex=5 style='background-color: #000000; color: #FFEECC; font-family: Tahoma;
font-weight: bold; border-color: #5B5A56'>
                </td>
        </tr>
        <tr>
        <td align='center'>
                <input type=submit name=submit value='Login' tabindex=6 style='background-color: #000000; color: #FFEECC; font-family: Tahoma;
font-weight: bold; border-color: #5B5A56'>
                </td>
        </tr>
   </table>
</font>
            </FORM>

</TD></TR>
             <TR>
                <TD class=menuheader width="100%">Contact: </TD></TR>
              <TR>
                <TD class=menuentry
                onmousedown="LmDown(this, 'menuentry_hover')"
                onmouseover="LmOver(this, 'menuentry_hover')"
                onmouseout="LmOut(this, 'menuentry')" width="100%"><A
                  href="mailto:admin@tkoc.net">Admin</A> </TD></TR>
--> 
            </TABLE>
          </TD>
        </TR>
      </TABLE>
<!-- End Menu -->
<?php
}
?>
