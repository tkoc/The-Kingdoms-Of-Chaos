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
<!-- Menu -->

<table style="MARGIN-LEFT: 10px;" cellSpacing=0 cellPadding=0 width=120 align=left bgColor=#000000 border=0>
	<tr>
		<td width="100%"><table class="navlink" cellSpacing="1" cellPadding="1" width="100%" border="0">
				<FORM action='rank.php' method='get' encType='multipart/form-data'>
					<input type='hidden' name='action' value='kingdom'>
					<tr>
						<td class="menuheader" width="100%"> Menu: </td>
					</tr>
					<tr>
						<td class="menuentry"><a href="http://stats.tkoc.net/index.php">Home</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a href="http://www.tkoc.net/scripts/showProvince.php">Return to Game</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a href="http://www.tkoc.net/worldforum">WorldForum</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a href="http://www.tkoc.net/worldforum/index.php?action=chat">IRC Chat</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a href="http://www.tkoc.net/worldforum/index.php?action=paypal">Donate</a> </td>
					</tr>
					<tr>
						<td class="menuheader" width="100%">Rankings: </td>
					</tr>
					<tr>
						<td class="menuentry" width="100%"><a
                  href="rank.php?action=province">Province</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=kingdom">Kingdom</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=orc">Orcs</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=human">Humans</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=elf">Elfs</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=dwarf">Dwarfs</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=undead">Undead</a> </td>
					</tr>
					<tr>
						<td class="menuentry"><a
                  href="rank.php?action=giant">Giant</a> </td>
					</tr>
					<tr>
						<td class="menuheader" width="100%">Search: </td>
					</tr>
					<tr>
						<td
                class="menuentry"><a
                  href="rank.php?action=kingdom&amp;kd=1">KD
							Browser</a> </td>
					</tr>
					<tr>
						<td class="menuheader" width="100%"> Quick Lookup: </td>
					</tr>
					<tr>
						<td class="menuentry"><INPUT class=coord maxLength=3 size=1 name=kd>
							<INPUT type=submit value="->">
						</td>
					</tr>
				</FORM>
			</table></td>
	</tr>
</table>
<!-- End Menu -->
