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
error_reporting (E_NONE);
session_start();

require("private/konf.nohack.php");

if (isset($_GET['action'])) 
	$action = $_GET['action'];

if (isset($_GET['kd'])) 
	$kd = $_GET['kd'];
	
if (isset($_GET['prov'])) 
	$prov = $_GET['prov'];
	
if (isset($_GET['orderby'])) 
	$orderby = $_GET['orderby'];
	
if (isset($_GET['sort'])) 
	$sort = $_GET['sort'];



if (!$orderby) {
	$orderby = 'score';
}

if (!$sort) {
	$sort = 'DESC';
}

if ($prov) {
	$prov=str_replace("'", "&#39;", $prov);	
}




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
<TITLE>The Kingdom of Chaos statistics</TITLE>
<LINK href="style.css" type="text/css" rel="stylesheet">
</HEAD>
<BODY>
<?

include_once('topstat.php.inc');

?>
<BR>
<BR>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<TD vAlign=top><?

include_once('meny.php.inc')

?>
		</TD>
		<TD vAlign=top width="100%"><DIV align=center>
				<TABLE width="85%">
					<TR>
						<TD><TABLE style="MARGIN-BOTTOM: 5px" cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD width="100%" align=center><?

if ($action=='kingdom') {
	$result = mysql_query("SELECT * FROM kingdoms WHERE kingdomId='$kd'");
	
	$data2 = mysql_fetch_array($result);
	
	mysql_free_result($result);
	
	if($data2) {
		
		$result = mysql_query("SELECT * FROM kingdomstat WHERE kingdomId='$kd' ORDER BY time DESC LIMIT 1");
		
		$data3 = mysql_fetch_array($result);
		
		mysql_free_result($result);
		
		
		
		$result = mysql_query("SELECT * FROM kingdomstat WHERE kingdomId='$kd' ORDER BY time DESC LIMIT 1,2");
		
		$data4 = mysql_fetch_array($result);
		
		mysql_free_result($result);
		
		
		
		$ratio = ($data3[3]/$data3[2]);
		
		$ratio = number_format( $ratio, 2);
		
		$acresp = 0;
		
		$scorep= 0;
		
		if($data4[2]) {
		
			$acresp = (($data3[2] - $data4[2]) / $data4[2]) * 100;
		
			$acresp = number_format($acresp, 2);
		
			$acresneg = substr($acresp, 0, 1);
		
		}
		
		if($data4[3]) {
		
			$scorep = (($data3[3] - $data4[3]) / $data4[3]) * 100;
		
			$scorep = number_format($scorep, 2);
		
			$scoreneg = substr($scorep, 0, 1);
		
		}
		
		$prevkd = $kd - 1;
		
		if ($prevkd == '0') $prevkd = $kingcount;
		
		$nextkd = $kd + 1;
		
		if ($nextkd > $kingcount) $nextkd = '1';
		
		?>
											<FORM action=rank.php method=post 
		
					encType=multipart/form-data>
												<input type=hidden name=action value='kingdom'>
												<input type=hidden id="kingdom" name=kd value=''>
												<TABLE>
												<TR>
													<TD><TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 
		
					align=center border=0>
														<TR>
															<TD class=listingheader style="TEXT-ALIGN: center" width="100%" 
		
						colSpan=9><INPUT type=submit OnClick="javascript:document.getElementById('kingdom').value = '<?=$prevkd?>'" value="<-- Prev" name=prev>
																Kingdom info [
																<?=$data2[1]?>
																(# <A class=table 
		
						  href="rank.php?action=kingdom&amp;kd=<?=$data2[0]?>">
																<?=$data2[0]?>
																</A>)]
																<INPUT type=submit OnClick="javascript:document.getElementById('kingdom').value = '<?=$nextkd?>'" value="Next -->" name=next>
															</TD>
														</TR>
											</FORM>
									<TR>
										<TD class=main style="TEXT-ALIGN: right" width="3%">#S </TD>
										<TD class=main style="TEXT-ALIGN: right" width="3%">KD </TD>
										<TD class=main style="TEXT-ALIGN: center" width="38%">Name </TD>
										<TD class=main style="TEXT-ALIGN: center" width="7%">Acres </TD>
										<TD class=main style="TEXT-ALIGN: center" width="10%">Score </TD>
										<TD class=main style="TEXT-ALIGN: center" width="3%">Provs </TD>
										<TD class=main style="TEXT-ALIGN: center" width="3%">Ratio </TD>
										<TD class=main style="TEXT-ALIGN: center" width="6%">ADev </TD>
										<TD class=main style="TEXT-ALIGN: center" width="6%">SDev </TD>
									</TR>
									<TR>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$data3[4]?>
											<?
		
								if ($data3[4] == $data4[4]) {
		
					   echo "<IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9>";
		
				 }
		
				 else if ($data4[4] < $data3[4]) {
		
					   echo "<IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>";
		
				 }
		
				 else {
		
					   echo "<IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9>";      
		
				 }
		
						?>
										</TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$kd?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="28%"><?=$data2[1]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="7%"><?=$data3[2]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="10%"><?=$data3[3]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$data2[2]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$ratio?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="6%"><?
		
					if ($acresneg == '-') {
		
						$acresp = substr($acresp,1);
		
									echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";
		
					}
		
					else if ($acresp == '0' || !$acresp) {
		
						$acresp = 0;
		
									echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		
		
					}
		
					else {
		
									echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";
		
					}                 
		
					?>
										</TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="6%"><?
		
											if ($scoreneg == '-') {
		
						$scorep = substr($scorep,1);                   
		
								   echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";
		
								}
		
								else if ($scorep == '0' || !$scorep) {
		
									$scorep = 0;
		
								   echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";
		
								
		
								}
		
								else {
		
								   echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";
		
								
		
								}
		
								?>
										</TD>
									</TR>
								</TABLE>
								<BR>
								<BR>
								<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 
		
					align=center border=0>
									<TR>
										<TD class=listingheader style="TEXT-ALIGN: center" 
		
						width="3%"></TD>
										<TD class=listingheader style="TEXT-ALIGN: center" width="50%" 
		
						colSpan=7></TD>
										<TD class=listingheader style="TEXT-ALIGN: center" width="12%" 
		
						colSpan=2>Growth </TD>
									</TR>
									<TR>
										<TD class=main style="TEXT-ALIGN: right" width="3%"># </TD>
										<TD class=main style="TEXT-ALIGN: right" width="3%">Rank </TD>
										<TD class=main style="TEXT-ALIGN: right" width="3%">KD </TD>
										<TD class=main style="TEXT-ALIGN: center" width="16%">Province Name </TD>
										<TD class=main style="TEXT-ALIGN: center" width="7%">Race </TD>
										<TD class=main style="TEXT-ALIGN: center" width="7%">Acres </TD>
										<TD class=main style="TEXT-ALIGN: center" width="10%">Score </TD>
										<TD class=main style="TEXT-ALIGN: center" width="3%">Ratio </TD>
										<TD class=main style="TEXT-ALIGN: center" width="6%">ADev </TD>
										<TD class=main style="TEXT-ALIGN: center" width="6%">SDev </TD>
									</TR>
									<!-- Begin Provinces in KD -->
									<?
		
		$i=0;
		
		$result = mysql_query("SELECT * FROM provinces WHERE pking='$kd' ORDER BY pname ASC");
		
		
		
		while ($data = mysql_fetch_array($result)) {
		
		$i++;
		
		$result2 = mysql_query("SELECT * FROM statistics WHERE pname='$data[1]' AND tick='$tid[0]'");
		
		if ($rader = mysql_num_rows($result2)) {
		
		mysql_free_result($result2);
		
		
		
		$result2 = mysql_query("SELECT * FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT 1");
		
		$data5 = mysql_fetch_array($result2);
		
		mysql_free_result($result2);
		
		
		
		$result2 = mysql_query("SELECT * FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT 1,2");
		
		$data6 = mysql_fetch_array($result2);
		
		mysql_free_result($result2);
		
		?>
									<TR>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><I>
											<?=$i?>
											</I></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$data5[4]?>
											<?
		
				if ($data5[4] == $data6[4]) {
		
					   echo "         
		
								<IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9> 
		
					   ";
		
				 }
		
				 else if ($data6[4] < $data5[4]) {
		
					   echo "
		
								<IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>
		
					   ";
		
				 }
		
				 else {
		
					   echo " 
		
								<IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9> 
		
					   ";      
		
				 }
		
				 
		
				$acresp = 0;
		
			$scorep = 0;
		
				if($data6[2]) {
		
			$acresp = (($data5[2] - $data6[2]) / $data6[2]) * 100;
		
			$acresp = number_format($acresp, 2);
		
			$acresneg = substr($acresp, 0, 1);
		
			}
		
				if($data6[3]) {
		
			$scorep = (($data5[3] - $data6[3]) / $data6[3]) * 100;
		
			$scorep = number_format($scorep, 2);
		
			$scoreneg = substr($scorep, 0, 1);
		
			$ratio = ($data5[3]/$data5[2]);
		
			$ratio = number_format( $ratio, 2);
		
			}
		
				?>
										</TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><A class=table 
		
						  href="rank.php?action=kingdom&amp;kd=<?=$kd?>">
											<?=$kd?>
											</A></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="16%"><A class=table href="rank.php?action=province&amp;prov=<?=$data[0]?>">
											<?=$data[1]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="7%"><?=$data[2]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="7%"><?=$data5[2]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="10%"><?=$data5[3]?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 
		
						noWrap width="3%"><?=$ratio?></TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="6%"><?
		
					if ($acresneg == '-') {
		
						$acresp = substr($acresp,1);
		
									echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";
		
					}
		
					else if ($acresp == '0' || !$acresp) {
		
						$acresp = 0;
		
									echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		
		
					}
		
					else {
		
									echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";
		
					}
		
					?>
										</TD>
										<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 
		
						noWrap width="6%"><?
		
					if ($scoreneg == '-') {
		
						$scorep = substr($scorep,1);
		
									echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";
		
					}
		
					else if ($scorep == '0' || !$scorep) {
		
						$scorep = 0;
		
									echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";		
		
					}
		
					else {
		
									echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";
		
					}
		
					?>
										</TD>
									</TR>
									<?
		
		}
		
		else {
		
		delete_prov($data[0]);
		
		}
		
		}
		
		mysql_free_result($result);
		
		?>
									<!-- End Provinces in KD -->
								</TABLE>
								<BR>
								<BR>
								<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 
		
					align=center border=0>
									<TBODY>
										<TR>
											<TD class=listingheader style="TEXT-ALIGN: center" 
		
						  width="100%">Graphical History for
												<?=$data2[1]?></TD>
										</TR>
										<TR>
											<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: left" 
		
						noWrap width="100%" ?><IMG alt=graph 
		
						  src="graph.php?action=kingdom&offset=24&ival=5&type=acres&pid=<?=$kd?>"><IMG alt=graph 
		
						  src="graph.php?action=kingdom&offset=24&ival=5&type=score&pid=<?=$kd?>"> </TD>
										</TR>
										<TR>
											<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: left" 
		
						noWrap width="100%" ?><IMG alt=graph src="graph.php?action=kingdom&ival=24&type=score&pid=<?=$kd?>&what=all"><br>
												<IMG alt=graph src="graph.php?action=kingdom&ival=24&type=acres&pid=<?=$kd?>&what=all"> </TD>
										</TR>
									</TBODY>
								</TABLE>
								<BR>
								<BR>
								<?
		
		echo "</td></tr></table>";
		
	} 
	/*else {
		echo "Non existing or a not-yet-updated kingdom!";
	}*/
}

?>
							<?

if ($action=='province' && $prov) {



$result = mysql_query("SELECT * FROM provinces WHERE id='$prov'");

$data = mysql_fetch_array($result);

mysql_free_result($result);



$result = mysql_query("SELECT * FROM kingdoms WHERE kingdomId='$data[3]'");

$data2 = mysql_fetch_array($result);

mysql_free_result($result);



$result = mysql_query("SELECT * FROM kingdomstat WHERE kingdomId='$data[3]' ORDER BY time DESC LIMIT 1");

$data3 = mysql_fetch_array($result);

mysql_free_result($result);



$result = mysql_query("SELECT * FROM kingdomstat WHERE kingdomId='$data[3]' ORDER BY time DESC LIMIT 1,2");

$data4 = mysql_fetch_array($result);

mysql_free_result($result);



$result = mysql_query("SELECT * FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT 1");

$data5 = mysql_fetch_array($result);

mysql_free_result($result);



$result = mysql_query("SELECT * FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT 1,2");

$data6 = mysql_fetch_array($result);

mysql_free_result($result);



$ratio = ($data3[3]/$data3[2]);

$ratio = number_format( $ratio, 2);

$acresp = 0;

$scorep= 0;

if($data4[1]) {

	$acresp = (($data3[2] - $data4[2]) / $data4[2]) * 100;

	$acresp = number_format($acresp, 2);

	$acresneg = substr($acresp, 0, 1);

}

if($data4[2]) {

	$scorep = (($data3[3] - $data4[3]) / $data4[3]) * 100;

	$scorep = number_format($scorep, 2);

	$scoreneg = substr($scorep, 0, 1);

}

?>
							<TABLE>
								<TR>
									<TD><TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 

            align=center border=0>
											<TR>
												<TD class=listingheader style="TEXT-ALIGN: center" width="100%" 

                colSpan=9>Kingdom info [
													<?=$data2[1]?>
													(# <A class=table 

                  href="rank.php?action=kingdom&amp;kd=<?=$data[3]?>">
													<?=$data[3]?>
													</A>)] </TD>
											</TR>
											<TR>
												<TD class=main style="TEXT-ALIGN: right" width="3%">#S </TD>
												<TD class=main style="TEXT-ALIGN: right" width="3%">KD </TD>
												<TD class=main style="TEXT-ALIGN: center" width="38%">Name </TD>
												<TD class=main style="TEXT-ALIGN: center" width="7%">Acres </TD>
												<TD class=main style="TEXT-ALIGN: center" width="10%">Score </TD>
												<TD class=main style="TEXT-ALIGN: center" width="3%">Provs </TD>
												<TD class=main style="TEXT-ALIGN: center" width="3%">Ratio </TD>
												<TD class=main style="TEXT-ALIGN: center" width="6%">ADev </TD>
												<TD class=main style="TEXT-ALIGN: center" width="6%">SDev </TD>
											</TR>
											<TR>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="3%"><?=$data3[4]?>
													<?

                        if ($data3[4] == $data4[4]) {

               echo "<IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9>";

         }

         else if ($data4[4] < $data3[4]) {

               echo "<IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>";

         }

         else {

               echo "<IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9>";      

         }

                ?>
												</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="3%"><A class=table 

                  href="rank.php?action=kingdom&amp;kd=<?=$data[3]?>">
													<?=$data[3]?>
													</A></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="28%"><?=$data2[1]?></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="7%"><?=$data3[2]?></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="10%"><?=$data3[3]?></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="3%"><?=$data2[2]?></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: right" 

                noWrap width="3%"><?=$ratio?></TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="6%"><?

			if ($acresneg == '-') {

				$acresp = substr($acresp,1);

                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";

			}

			else if ($acresp == '0' || !$acresp) {

				$acresp = 0;

                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		

			}

			else {

                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";

			}                 

			?>
												</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="6%"><?

                         			if ($scoreneg == '-') {

				$scorep = substr($scorep,1);                   

 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";

                        }

                        else if ($scorep == '0' || !$scorep) {

                        	$scorep = 0;

 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";

                        

                        }

                        else {

 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";

                        

                        }

                        ?>
												</TD>
											</TR>
										</TABLE>
										<BR>
										<BR>
										<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=400 

            align=center border=0>
											<TR>
												<TD class=listingheader style="TEXT-ALIGN: center" width="100%" 

                colSpan=2>Province info </TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Province 
													
													Name</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data[1]?></TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Race </TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data[2]?></TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Kingdom</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><A class=table 

                  href="rank.php?action=kingdom&amp;kd=<?=$data[3]?>">
													<?=$data[3]?>
													</A></TD>
											</TR>
											<TR>
												<TD class=one style="TEXT-ALIGN: center" width="50%">Acres</TD>
												<TD class=one style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data5[2]?></TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Score</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data5[3]?></TD>
											</TR>
											<TR>
												<?

              if($data5[3]){ 

              	$ratio = ($data5[3]/$data5[2]);

              	$ratio = number_format($ratio, 2);

              }

              else {

              	$ratio = 0;

              }

              ?>
												<TD class=one style="TEXT-ALIGN: center" width="50%">Ratio</TD>
												<TD class=one style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$ratio?></TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Rank</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data5[4]?>
													<?

                        if ($data5[4] == $data6[4]) {

               echo "<IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9>";

         }

         else if ($data6[4] < $data5[4]) {

               echo "<IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>";

         }

         else {

               echo "<IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9>";      

         }

                ?>
												</TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Kingdom 
													
													Rank</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?=$data3[4]?>
													<?

                        if ($data3[4] == $data4[4]) {

               echo "<IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9>";

         }

         else if ($data4[4] < $data3[4]) {

               echo "<IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>";

         }

         else {

               echo "<IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9>";      

         }

         $acresp = 0;

$scorep= 0;

		if($data6[2]) {

	$acresp = (($data5[2] - $data6[2]) / $data6[2]) * 100;

	$acresp = number_format($acresp, 2);

	$acresneg = substr($acresp, 0, 1);

	}

		if($data6[3]) {

	$scorep = (($data5[3] - $data6[3]) / $data6[3]) * 100;

	$scorep = number_format($scorep, 2);

	$scoreneg = substr($scorep, 0, 1);

	}         

         

                ?>
												</TD>
											</TR>
											<TR>
												<TD class=two style="TEXT-ALIGN: center" width="50%">Acres 
													
													Growth</TD>
												<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?

			if ($acresneg == '-') {

				$acresp = substr($acresp,1);

                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";

			}

			else if ($acresp == '0' || !$acresp) {

				$acresp = 0;

                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		

			}

			else {

                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";

			}



?>
												</TD>
											</TR>
											<TR>
												<TD class=one style="TEXT-ALIGN: center" width="50%">Score 
													
													Growth </TD>
												<TD class=one style="WHITE-SPACE: nowrap; TEXT-ALIGN: center" 

                noWrap width="50%"><?

			if ($scoreneg == '-') {       

				$scorep = substr($scorep,1);               

 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";

                        }

                        else if ($scorep == '0' || !$scorep) {

                        	$scorep = 0;

 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";

                        

                        }

                        else {

 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";

                        

                        }

?>
												</TD>
											</TR>
											<?

if($valid==strrev(soundex(name).md5($username))) {

   echo "

              <TR>

                <TD class=one style=\"TEXT-ALIGN: center\" width=\"50%\">Reports </TD>

                <TD class=one style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" 

                noWrap width=\"50%\">

                ";

$result = mysql_query("SELECT * FROM parsed_scans WHERE name='$data[0]' ORDER BY tick DESC LIMIT 1");

$data7 = mysql_fetch_array($result);

mysql_free_result($result);     

if($data7[0]) {

   echo "<a href=\"scans.php?report=".$data7[0]."\">tick ".$data7[1]."</a>";

}

else {

   echo "None";

}          

                echo"

                  </TD></TR>

                  ";

               }

                  

?>
											</TBODY>
											
										</TABLE>
										<BR>
										<BR>
										<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 

            align=center border=0>
											<TBODY>
												<TR>
													<TD class=listingheader style="TEXT-ALIGN: center" width="100%" 

                colSpan=10>Last 10 days acres</TD>
												</TR>
												<TR>
													<?              

        

        echo "      

                <TD class=main style=\"TEXT-ALIGN: right\" width=\"3%\">NOW</TD>

        ";

        

for($i=1;$i<10;$i++) {

$limitn = (24 * $i);

$plimit = $limitn + 1;

$result = mysql_query("SELECT tick FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT $limitn,$plimit");

$data8 = mysql_fetch_array($result);

mysql_free_result($result);



if (!$data8[0]) $data8[0]='N/A';



echo "



                <TD class=main style=\"TEXT-ALIGN: right\" width=\"3%\">$data8[0]</TD>

";	

}

?>
												</TR>
												<TR>
													<!-- 10 Last days acres -->
													<?

$plimit=1;

for($i=0;$i<10;$i++) {

$limitn = (24 * $i);

$plimit = $limitn + 1;

$result = mysql_query("SELECT acres FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT $limitn,$plimit");

$data7 = mysql_fetch_array($result);

mysql_free_result($result);



if (!$data7[0]) $data7[0]='N/A';

echo "

                <TD class=highlight style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=\"3%\">

                $data7[0]

                </TD>

";





}



?>
													<!-- 10 Last days acres -->
												</TR>
										</TABLE>
										<BR>
										<BR>
										<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 

            align=center border=0>
											<TBODY>
												<TR>
													<TD class=listingheader style="TEXT-ALIGN: center" width="100%" 

                colSpan=10>Last 10 days score</TD>
												</TR>
												<TR>
													<?              

        

        echo "      

                <TD class=main style=\"TEXT-ALIGN: right\" width=\"3%\">NOW</TD>

        ";

        

for($i=1;$i<10;$i++) {

$limitn = (24 * $i);

$plimit = $limitn + 1;

$result = mysql_query("SELECT tick FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT $limitn,$plimit");

$data8 = mysql_fetch_array($result);

mysql_free_result($result);



if (!$data8[0]) $data8[0]='N/A';



echo "



                <TD class=main style=\"TEXT-ALIGN: right\" width=\"3%\">$data8[0]</TD>

";	

}

?>
												</TR>
												<TR>
													<!-- 10 Last days acres -->
													<?

$plimit=1;

for($i=0;$i<10;$i++) {

$limitn = (24 * $i);

$plimit = $limitn + 1;

$result = mysql_query("SELECT score FROM statistics WHERE pname='$data[1]' ORDER BY tick DESC LIMIT $limitn,$plimit");

$data7 = mysql_fetch_array($result);

mysql_free_result($result);



if (!$data7[0]) $data7[0]='N/A';

echo "

                <TD class=highlight style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=\"3%\">

                $data7[0]

                </TD>

";





}



?>
													<!-- 10 Last days acres -->
												</TR>
										</TABLE>
										<BR>
										<BR>
										<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width=800 

            align=center border=0>
											<TBODY>
												<TR>
													<TD class=listingheader style="TEXT-ALIGN: center" 

                  width="100%">Graphical History for
														<?=$data[1]?></TD>
												</TR>
												<TR>
													<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: left" 

                noWrap width="100%" ?><IMG alt=graph 

                  src="graph.php?type=acres&pid=<?=$data[1]?>"><IMG alt=graph 

                  src="graph.php?type=score&pid=<?=$data[1]?>"> </TD>
												</TR>
											<TD class=two style="WHITE-SPACE: nowrap; TEXT-ALIGN: left" 

                noWrap width="100%" ?><IMG alt=graph src="graph.php?ival=24&type=score&what=all&pid=<?=$data[1]?>"><br>
													<IMG alt=graph src="graph.php?ival=24&type=acres&what=all&pid=<?=$data[1]?>"> </TD>
											</TR>
											</TBODY>
											
										</TABLE>
										<BR>
										<BR></TD>
								</TR>
								</TBODY>
								
							</TABLE>
							<?

}

?>
							<?

if ($action=='kingdom' && !$kd) {

?>
							<!-- Kingdoms -->
							<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="50%" border=0>
								<TR>
									<TD class=listingheader style="TEXT-ALIGN: center" width="100%" colSpan=9> Top 50 Kingdoms (
										<?=$kingcount?>
										in total) (BEST <a href="rank.php?action=kingdom&orderby=<?=$orderby?>&sort=DESC">FIRST</a>/<a href="rank.php?action=kingdom&orderby=<?=$orderby?>&sort=ASC">LAST</a>) </TD>
								</TR>
								<TR>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=25> R </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20> KD </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\"> Name </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20> Provs </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40><a href="rank.php?action=kingdom&orderby=acres&sort=<?=$sort?>">Acre</a> </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55><a href="rank.php?action=kingdom&orderby=score&sort=<?=$sort?>">Score</a> </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20> Ratio </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> ADev </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> SDev </TD>
								</TR>
								<!-- Begin Kingdom Ranks -->
								<?



$result = mysql_query("SELECT * FROM kingdomstat WHERE time='$tid[0]' ORDER BY $orderby $sort LIMIT 50");

$number = 1;

$rows = mysql_num_rows($result);

while ( $data = mysql_fetch_array($result) ) {



	$result2 = mysql_query("SELECT lastp,acres,score FROM kingdomstat WHERE kingdomId='$data[0]' ORDER BY time DESC LIMIT 1,1");

	$data2 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	

	echo "

                    <TR>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>

                        $number

        ";

        

        if ($data[4] == $data2[0]) {

               echo "         

                        <IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9> 

               ";

         }

         else if ($data[4] < $data2[0]) {

               echo "

                        <IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>

               ";

         }

         else {

               echo " 

                        <IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9> 

               ";      

         }

	$result2 = mysql_query("SELECT * FROM kingdoms WHERE kingdomId='$data[0]'");

	$data3 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	$acresp = 0;

$scorep= 0;

		if($data2[1]) {

	$acresp = (($data[2] - $data2[1]) / $data2[1]) * 100;

	$acresp = number_format($acresp, 2);

	$acresneg = substr($acresp, 0, 1);

	}

		if($data2[2]) {

	$scorep = (($data[3] - $data2[2]) / $data2[2]) * 100;

	$scorep = number_format($scorep, 2);

	$scoreneg = substr($scorep, 0, 1);

	}

	

	$ratio = ($data[3]/$data[2]);

	$ratio = number_format( $ratio, 2);

	

         echo "

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>

                        <A class=table href=\"rank.php?action=kingdom&amp;kd=$data[0]\">$data[0]</A>

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">

                        $data3[1]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20>

                        $data3[2]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>

                        $data[2]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>

                        $data[3]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20>

                        $ratio

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

	";

			if ($acresneg == '-') {

				$acresp = substr($acresp,1);

                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";

			}

			else if ($acresp == '0' || !$acresp) {

				$acresp = 0;

                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		

			}

			else {

                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";

			}

			echo "

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

                      ";

			if ($scoreneg == '-') {

				$scorep = substr($scorep,1);                   

 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";

                        }

                        else if ($scorep == '0' || !$scorep) {

                        	$scorep = 0;

 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";

                        

                        }

                        else {

 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";

                        

                        }

	echo "

                      </TD>

                    </TR>



	";

	$number++;

	

}



mysql_free_result($result);



?>
								<!-- End Kingdom Ranks -->
							</TABLE>
							<!-- End Kingdoms -->
							<?

}

?>
							<?

if ($action=='province' && !$prov) {

?>
							<!-- Provinces -->
							<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="50%" align=center border=0>
								<TR>
									<TD class=listingheader style="TEXT-ALIGN: center" width="100%" colSpan=11> Top 50 Provinces (
										<?=$provcount?>
										in total) (BEST <a href="rank.php?action=province&orderby=<?=$orderby?>&sort=DESC">FIRST</a>/<a href="rank.php?action=province&orderby=<?=$orderby?>&sort=ASC">LAST</a>) </TD>
								</TR>
								<TR>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=25> R </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20> KD </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\"> Name </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20> Race </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40><a href="rank.php?action=province&orderby=acres&sort=<?=$sort?>">Acre</a> </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55><a href="rank.php?action=province&orderby=score&sort=<?=$sort?>">Score</a> </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20> Ratio </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> ADev </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> SDev </TD>
								</TR>
								<!-- Provinces Rank -->
								<?

$result = mysql_query("SELECT * FROM statistics WHERE tick='$tid[0]' ORDER BY $orderby $sort LIMIT 50");

$number = 1;

$rows = mysql_num_rows($result);

while ( $data = mysql_fetch_array($result) ) {



	$result2 = mysql_query("SELECT lastp,acres,score FROM statistics WHERE pname='$data[0]' ORDER BY tick DESC LIMIT 1,1");

	$data2 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	

	echo "

                    <TR>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>

                        $number

        ";

        

        if ($data[4] == $data2[0]) {

               echo "         

                        <IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9> 

               ";

         }

         else if ($data[4] < $data2[0]) {

               echo "

                        <IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>

               ";

         }

         else {

               echo " 

                        <IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9> 

               ";      

         }

	$result2 = mysql_query("SELECT * FROM provinces WHERE pname='$data[0]'");

	$data3 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	$acresp = 0;

	$scorep = 0;

		if($data2[1]) {

	$acresp = (($data[2] - $data2[1]) / $data2[1]) * 100;

	$acresp = number_format($acresp, 2);

	$acresneg = substr($acresp, 0, 1);

	}

		if($data2[2]) {

	$scorep = (($data[3] - $data2[2]) / $data2[2]) * 100;

	$scorep = number_format($scorep, 2);

	$scoreneg = substr($scorep, 0, 1);

	}

	

	$ratio = ($data[3]/$data[2]);

	$ratio = number_format( $ratio, 2);

		

         echo "

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>

                        <A class=table href=\"rank.php?action=kingdom&amp;kd=$data3[3]\">$data3[3]</A>

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">

                        <A class=table href=\"rank.php?action=province&amp;prov=$data3[0]\">$data3[1]</A>

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>

                        $data3[2]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>

                        $data[2]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>

                        $data[3]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20>

                        $ratio

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

	";

			if ($acresneg == '-') {

				$acresp = substr($acresp,1);

                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";

			}

			else if ($acresp == '0' || !$acresp) {

				$acresp = 0;

                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		

			}

			else {

                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";

			}

			echo "

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

                      ";

			if ($scoreneg == '-') {       

				$scorep = substr($scorep,1);               

 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";

                        }

                        else if ($scorep == '0'  || !$scorep) {

                        	$scorep = 0;

 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";

                        

                        }

                        else {

 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";

                        

                        }

	echo "

                      </TD>

                    </TR>



	";

	$number++;

	

}



mysql_free_result($result);



?>
								<!-- End Provinces Rank -->
							</TABLE>
							<!-- End Provinces -->
							<?

}

if (($action=='orc') || ($action=='elf') || ($action=='human') || ($action=='dwarf') || ($action=='undead') || ($action=='giant')) { 

?>
							<!-- Orc -->
							<?

$result = mysql_query("SELECT * FROM provinces WHERE prace='$action'");

$rows = mysql_num_rows($result);

mysql_free_result($result);

?>
							<TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="50%" align=center border=0>
								<TR>
									<TD class=listingheader style="TEXT-ALIGN: center" width="100%" colSpan=8> Top 50
										<?=ucfirst($action)?>
										provinces (
										<?=$rows?>
										in total) (BEST <a href="rank.php?action=<?=$action?>&orderby=<?=$orderby?>&sort=DESC">FIRST</a>/<a href="rank.php?action=<?=$action?>&orderby=<?=$orderby?>&sort=ASC">LAST</a>) </TD>
								</TR>
								<TR>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=25> RR </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=25> TR </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20> KD </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\"> Name </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40><a href="rank.php?action=<?=$action?>&orderby=acres&sort=<?=$sort?>">Acre </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55><a href="rank.php?action=<?=$action?>&orderby=score&sort=<?=$sort?>">Score </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20> Ratio </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> ADev </TD>
									<TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43> SDev </TD>
								</TR>
								<!-- Orc Ranks -->
								<?



$result = mysql_query("SELECT * FROM statistics,provinces WHERE statistics.tick='$tid[0]' AND statistics.pname=provinces.pname AND provinces.prace='$action' ORDER BY $orderby $sort LIMIT 50");

$number = 1;



while ( $data = mysql_fetch_array($result) ) {



	$result2 = mysql_query("SELECT lastp,acres,score FROM statistics WHERE pname='$data[0]' ORDER BY tick DESC LIMIT 1,1");

	$data2 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	

	echo "

                    <TR>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=25>

                        $number

                      </TD>       

        ";

        

	$result2 = mysql_query("SELECT * FROM provinces WHERE pname='$data[0]'");

	$data3 = mysql_fetch_array($result2);

	mysql_free_result($result2);

	$acresp = 0;

	$scorep= 0;

	if ($data2[1]) {

	$acresp = (($data[2] - $data2[1]) / $data2[1]) * 100;

	$acresp = number_format($acresp, 2);

	$acresneg = substr($acresp, 0, 1);

	}

	if($data2[2]) {

	$scorep = (($data[3] - $data2[2]) / $data2[2]) * 100;

	$scorep = number_format($scorep, 2);

	$scoreneg = substr($scorep, 0, 1);

	}



	$ratio = ($data[3]/$data[2]);

	$ratio = number_format( $ratio, 2);

		

         echo "

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=25>

                        $data[4]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>

                        <A class=table href=\"rank.php?action=kingdom&amp;kd=$data3[3]\">$data3[3]</A>

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">

                        <A class=table href=\"rank.php?action=province&amp;prov=$data3[0]\">$data3[1]</a>

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>

                        $data[2]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>

                        $data[3]

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20>

                        $ratio

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

	";

			if ($acresneg == '-') {

				$acresp = substr($acresp,1);

                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";

			}

			else if ($acresp == '0'  || !$acresp) {

				$acresp = 0;

                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		

			}

			else {

                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";

			}

			echo "

                      </TD>

                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>

                      ";

			if ($scoreneg == '-') {

				$scorep = substr($scorep,1);                   

 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";

                        }

                        else if ($scorep == '0'  || !$scorep) {

                        	$scorep = 0;

 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";

                        

                        }

                        else {

 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";

                        

                        }

	echo "

                      </TD>

                    </TR>



	";

	$number++;

	

}



mysql_free_result($result);



?>
								<!-- End Orc Ranks -->
							</TABLE>
							<!-- End Orc -->
							<?

}

?>
						</TD>
					</TR>
					<TR>
						<TD>&nbsp;<BR>
							<BR>
						</TD>
					</TR>
				</TABLE>
			</DIV>
			<BR>
			<BR>
			<BR>
		</TD>
	</TR>
</TABLE>
</BODY>
</HTML>
<?

function delete_prov($province) {

	$result = mysql_query("DELETE FROM provinces WHERE pname='$province'");

}

?>