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
require("private/konf.nohack.php");
include ("jpgraph.php");
include ("jpgraph_line.php");
include ("jpgraph_scatter.php");
include ("jpgraph_regstat.php");

if (isset($_GET['pid'])) 
	$pid = $_GET['pid'];

if (isset($_GET['offset'])) 
	$offset = $_GET['offset'];
	
if (isset($_GET['ival'])) 
	$ival = $_GET['ival'];
	
if (isset($_GET['type'])) 
	$type = $_GET['type'];
	
if (isset($_GET['action'])) 
	$action = $_GET['action'];

if (isset($_GET['type'])) 
	$type = $_GET['type'];
	
if (isset($_GET['what'])) 
	$what = $_GET['what'];


if(!$pid) $pid = '1';
if(!$offset) $offset = '24';
if(!$ival) $ival = '5';
if(!$type) $type = 'score';
if(!$action) $action = 'province';
if ($pid) {
	$pid=str_replace("'", "&#39;", $pid);	
}

if($action == 'province') {
	if($type == 'score') {
		if($what == 'all') {
			$result = mysql_query("SELECT score FROM statistics WHERE pname='$pid'");
			$antall = mysql_num_rows($result);
			$result = mysql_query("SELECT score,tick FROM statistics WHERE pname='$pid' ORDER BY tick ASC");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(800,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Total Score History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
		else {
			$result = mysql_query("SELECT score FROM statistics WHERE pname='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			
			
			$result = mysql_query("SELECT score,tick FROM statistics WHERE pname='$pid' ORDER BY tick ASC LIMIT $antall,$offset");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(400,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Score History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
	}
	else if ($type == 'acres') {
		if ($what == 'all') {
			$result = mysql_query("SELECT acres FROM statistics WHERE pname='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT acres,tick FROM statistics WHERE pname='$pid' ORDER BY tick ASC");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(800,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Total Acres History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
		else {
			$result = mysql_query("SELECT acres FROM statistics WHERE pname='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT acres,tick FROM statistics WHERE pname='$pid' ORDER BY tick ASC LIMIT $antall,$offset");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(400,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Acres History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
	}
}

else if($action == 'kingdom') {
	if($type == 'score') {
		if($what=='all') {
			$result = mysql_query("SELECT score FROM kingdomstat WHERE kingdomId='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT score,time FROM kingdomstat WHERE kingdomId='$pid' ORDER BY time ASC");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(800,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Total Score History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
		else {
			$result = mysql_query("SELECT score FROM kingdomstat WHERE kingdomId='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT score,time FROM kingdomstat WHERE kingdomId='$pid' ORDER BY time ASC LIMIT $antall,$offset");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(400,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Score History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
	}
	else if ($type == 'acres') {
		if($what=='all') {
			$result = mysql_query("SELECT acres FROM kingdomstat WHERE kingdomId='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT acres,time FROM kingdomstat WHERE kingdomId='$pid' ORDER BY time ASC");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(800,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Total Acres History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}
		else {
			$result = mysql_query("SELECT acres FROM kingdomstat WHERE kingdomId='$pid'");
			$antall = mysql_num_rows($result);
			$antall -= $offset;
			$result = mysql_query("SELECT acres,time FROM kingdomstat WHERE kingdomId='$pid' ORDER BY time ASC LIMIT $antall,$offset");
			
			$i = 0;
			$sammenx[0] = 0;
			$sammeny[0] = 0;
			while ($data = mysql_fetch_array($result)) {
				$sammeny[$i] = $data[0];	
				$sammenx[$i] = $data[1];
				$i++;
				
			}
			mysql_free_result($result);
			// Setup the graph
			$graph = new Graph(400,250);
			$graph->SetScale('textint');
			$graph->SetFrame(true, "#384c6a");
			$graph->SetMargin(60,30,30,40);
			$graph->SetMarginColor('#384c6a');
			$graph->title->Set('Acres History');
			
			
			$graph->yaxis->HideZeroLabel();
			$graph->ygrid->SetFill(true,'#EFEFEF','#BBCCFF');
			$graph->ygrid->Show(true,false);
			$graph->xgrid->Show(true,false);
			#$graph->xaxis->SetLabelAngle(90);
			$graph->xaxis->SetTickLabels($sammenx);
			#$graph->xaxis->SetTextLabelInterval(13);
			$graph->xaxis->SetTextTickInterval($ival); 
			
			$graph->yaxis->SetColor('black','white'); 
			$graph->xaxis->SetColor('black','white'); 
			$graph->title->SetColor('white');
			$graph->xaxis->SetLabelFormat('%0.1f%%'); 
			
			// Create the first line
			$p1 = new LinePlot($sammeny);
			$p1->SetColor("blue");
			$graph->Add($p1);
			
			// Output line
			$graph->Stroke();
		}	
	}
}
?>
