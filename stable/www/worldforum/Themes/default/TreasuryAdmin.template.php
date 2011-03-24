<?php
/*******************************************************************************
* Originally NukeTreasury - Financial management for PHP-Nuke                  *
* Copyright (c) 2004 by Dave Lawrence AKA Thrash  thrash@fragnastika.com       *
*                                                                              *
* This program is free software; you can redistribute it and/or modify         *
* it under the terms of the GNU General Public License as published by         *
* the Free Software Foundation; either version 2 of the License.               *
* $Source: /0cvs/TreasurySMF/TreasuryAdmin.template.php,v $                    *
* $Revision: 1.37 $                                                            *
* $Date: 2010/01/25 04:18:19 $                                                 *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz                     *
*******************************************************************************/

function template_treasuryregister()
{
	global $scripturl, $txt, $context, $settings, $tr_config, $tr_targets, $num_ipn, $ipn_tot, $total;
	global $pageNum_Recordset1, $totalRows_Recordset1, $totalPages_Recordset1;

	// Output the page
	echo '<form action="', $scripturl.$context['treas_link'], ';sa=ipnrec" method="post">';
    echo '<table class="tborder" width="100%">';
    echo '<tr class="catbg2"><td align="center">';
	echo 'Number of new IPN records: ', $num_ipn, ' - ', $txt['treas_totalling'], ' ', $tr_targets['currency'][$tr_config['pp_currency']].sprintf('%0.2f',  $ipn_tot);
	echo '</td></tr>';
    echo '<tr class="windowbg"><td align="center">';
	echo '<input type="submit" value="PayPal IPN reconcile" onclick="return confirm(\'This action will total up all recent PayPal IPN' . '\n' . 'transactions and post them here in the register.' . '\n\n' . 'Are you sure you want to do this now?\')" />';
	echo '<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	echo '</td></tr></table>';
	echo '</form>';

	echo '<br /><table style="margin:auto;"><tr>';
	if ( $pageNum_Recordset1 > 0 )
	{
		echo '<td><form action="', $scripturl.$context['treas_link'], ';sa=registry" method="post" style="margin:0;">'
		.'<input type="hidden" name="pageNum_Recordset1" value="0" />'
		.'<input type="hidden" name="totalRows_Recordset1" value="', $totalRows_Recordset1, '" />'
		.'<input type="submit" name="navig" value="|&laquo;" title="Current" /></form></td>';
		echo '<td><form action="', $scripturl.$context['treas_link'], ';sa=registry" method="post" style="margin:0;">'
		.'<input type="hidden" name="pageNum_Recordset1" value="', max(0, $pageNum_Recordset1 - 1), '" />'
		.'<input type="hidden" name="totalRows_Recordset1" value="', $totalRows_Recordset1, '" />'
		.'<input type="hidden" name="sc" value="', $context['session_id'], '" />'
		.'<input type="submit" name="navig" value="&laquo;" title="Next newest" /></form></td>';
	} else {
		echo '<td colspan="2"></td>';
	}
	if ( $pageNum_Recordset1 < $totalPages_Recordset1 )
	{
		echo '<td><form action="', $scripturl.$context['treas_link'], ';sa=registry" method="post" style="margin:0;">'
		.'<input type="hidden" name="pageNum_Recordset1" value="', min($totalPages_Recordset1, $pageNum_Recordset1 + 1), '" />'
		.'<input type="hidden" name="totalRows_Recordset1" value="', $totalRows_Recordset1, '" />'
		.'<input type="submit" name="navig" value="&raquo;" title="Next Oldest" /></form></td>';
		echo '<td><form action="', $scripturl.$context['treas_link'], ';sa=registry" method="post" style="margin:0;">'
		.'<input type="hidden" name="pageNum_Recordset1" value="', $totalPages_Recordset1, '" />'
		.'<input type="hidden" name="totalRows_Recordset1" value="', $totalRows_Recordset1, '" />'
		.'<input type="hidden" name="sc" value="', $context['session_id'], '" />'
		.'<input type="submit" name="navig" value="&raquo;|" title="Oldest" /></form></td>';
	} else {
		echo '<td colspan="2"></td>';
	}
	echo '</tr></table><br />';

    echo '<table class="tborder" width="100%" style="margin:auto;"><tr>'
    .'<td class="titlebg2" align="center">&nbsp;</td>'
    .'<td class="titlebg2" align="center">Date</td>'
    .'<td class="titlebg2" align="center">Num</td>'
    .'<td class="titlebg2" align="center">Name</td>'
    .'<td class="titlebg2" align="center">Description</td>'
    .'<td class="titlebg2" align="center">Amount</td></tr>';

	if (isset($context['treas_registry']))
	{
		foreach ($context['treas_registry'] as $register_treas)
		{
			$register_treas['fdate'] = timeformat($register_treas['fdate'], treasdate());
		    echo '<tr class="windowbg">'
		    .'<td align="center">'
			  ."<a href=\"javascript: void 0\" onclick=\""
			    ."document.recedit.id.value = '$register_treas[id]'; "
			    ."document.recedit.Date.value = '$register_treas[fdate]'; "
			    ."document.recedit.Num.value = '$register_treas[num]'; "
			    ."document.recedit.Name.value = '$register_treas[name]'; "
				."document.recedit.Descr.value = '$register_treas[descr]'; "
			 	."document.recedit.Amount.value = '$register_treas[amount]'; "
			 	."document.recedit.Submit.value = 'Modify'; "
			 	."document.recedit.sa.value = 'finregedit'; "
			  ."return false;\">"
	  		.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/edit.png" alt="Edit" /></a>&nbsp;'
			.'<a href="', $scripturl.$context['treas_link'], ';sa=finregdel;id=', $register_treas['id'], ';sesc=', $context['session_id'], '">'
			.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/drop.png" onclick="return confirm(\'Are you sure you want to delete this record?\n\nAre you sure you want to do this now?\')" alt="Delete" />'
			.'</a></td>'
			."<td align=\"left\">$register_treas[fdate]</td>"
	        ."<td align=\"left\" width=\"8\">$register_treas[num]</td>"
	        ."<td align=\"left\">$register_treas[name]</td>"
	        ."<td align=\"left\">$register_treas[descr]</td>"
	        .'<td align="right"><span ';
			$amt =  sprintf('%10.2f', $register_treas['amount']);
			if ( $amt < 0 )
				echo 'style="color:#FF0000;"';
			echo ">", $tr_targets['currency'][$tr_config['pp_currency']].$amt, "</span></td></tr>";
		}
	}
    echo '</table><table width="100%"><tr><td align="right"><b>Net Balance&nbsp;&nbsp;&nbsp;';
	echo $tr_targets['currency'][$tr_config['pp_currency']].sprintf('%0.2f', $total), '&nbsp;</b></td>';
    echo '</tr></table>';
    echo '<form name="recedit" action="', $scripturl.$context['treas_link'], '" method="post">'
	.'<table><tr>'
    .'<td class="titlebg2" align="center">Date</td>'
    .'<td class="titlebg2" align="center">Num</td>'
    .'<td class="titlebg2" align="center">Name</td>'
    .'<td class="titlebg2" align="center">Description</td>'
    .'<td class="titlebg2" align="center">Amount</td></tr><tr>'
	.'<td align="left" style="width:8px;"><input name="id" type="hidden" />'
	.'<input name="Date" type="text" size="22" /></td>'
	.'<td align="left" style="width:8px;"><input name="Num" type="text" size="6" /></td>'
	.'<td align="left"><input name="Name" type="text" size="15" /></td>'
	.'<td align="left"><input name="Descr" type="text" size="30" /></td>'
	.'<td align="right"><input name="Amount" type="text" size="8" /></td></tr>';
	echo '<tr><td align="right" colspan="5">'
	.'<input type="hidden" name="sc" value="', $context['session_id'], '" />'
	.'<input type="hidden" name="sa" value="finregadd" />'
	."<input name=\"\" type=\"reset\" value=\"Reset\" onclick=\""
	."document.recedit.Submit.value = 'Add'; "
	."document.recedit.sa.value = 'finregadd'; "
	."return true;\" />&nbsp;"
	.'<input name="Submit" type="submit" value="', $txt['treas_add'], '" />';
	echo '</td></tr></table></form>';
	echo '<div><b>Note date format -</b> ', timeformat(gmmktime(), treasdate()), '</div>';
    echo '<br />';
}

function template_treasury_donations()
{
	global $scripturl, $txt, $context, $settings, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows;

	// Donation paging
	$pagination = treasuryPages($context['treas_link'] . ';sa=donations;mode=' . $mode . ';order=' . $sort_order, $totalRows, $maxRows, $start) . '&nbsp;';

	echo '<form method="post" action="', $scripturl.$context['treas_link'], ';sa=donations">
	<table width="100%" cellspacing="2" cellpadding="2" style="border:1px solid;" class="windowbg">
	<tr>
	<td align="left">', sprintf($txt['treas_page_of'], ( floor( $start / $maxRows ) + 1 ), ceil( $totalRows / $maxRows )), '</td>
	  <td align="right" style="white-space:nowrap;">', $txt['treas_select_sort'], ':&nbsp;';
	$options = array('lastdonated' => $txt['treas_sort_lastdate'], 'username' => $txt['treas_sort_username'], 'donation' => $txt['treas_sort_donations']);
	$select = '<select name="mode" id="mode">';
	foreach($options as $opname => $opvalue) {
		$select .= '<option value="' . $opname . '" ' . (($opname == $mode) ? 'selected="selected"' : '') . '>' . $opvalue . '</option>' . "\n";
	}
	$select .= '</select>';
	echo $select;
	echo '&nbsp;&nbsp;', $txt['treas_select_order'], '&nbsp;';
	$options2 = array('DESC' => $txt['treas_sort_desc'], 'ASC' => $txt['treas_sort_asc']);
	$select2 = '<select name="order" id="order">';
	foreach($options2 as $opname2 => $opvalue2) {
		$select2 .= '<option value="' . $opname2 . '" ' . (($opname2 == $sort_order) ? 'selected="selected"' : '') . '>' . $opvalue2 . '</option>' . "\n";
	}
	$select2 .= '</select>';
	echo $select2;
	echo '	  </td>
	</tr>
	<tr>
	<td align="left">', $pagination, '</td>
	<td align="right">
		<input type="hidden" name="start" value="', $start, '" />
		<input type="submit" name="submit" value="', $txt['treas_sort'], '" />
	</td></tr>
	</table></form>';

    echo '<table width="100%" style="margin:auto;" class="tborder"><tr>'
    .'<td class="titlebg2" align="center"><b>&nbsp;</b></td>'
    .'<td class="titlebg2" align="center">Tax ID</td>'
    .'<td class="titlebg2" align="center">Name</td>'
    .'<td class="titlebg2" align="center">Show</td>'
    .'<td class="titlebg2" align="center">Status</td>'
    .'<td class="titlebg2" align="center">Curr.</td>'
    .'<td class="titlebg2" align="center">Gross</td>'
    .'<td class="titlebg2" align="center">Fee</td>'
    .'<td class="titlebg2" align="center">Settle</td>'
    .'<td class="titlebg2" align="center">Date</td>'
    .'<td class="titlebg2" align="center">Rate</td>'
    .'<td class="titlebg2" align="center">Event</td>'
	.'</tr>';

	if (isset($context['treas_donations']))
	{
		foreach ($context['treas_donations'] as $donations_treas)
		{
			$donations_treas['payment_date'] = timeformat($donations_treas['payment_date'], treasdate());
			echo '<tr class="windowbg">'
			.'<td align="center">'
			  ."<a href=\"javascript: void 0\" onclick=\""
				."document.transedit.id.value = '$donations_treas[id]'; "
				."document.transedit.Txn_id.value = '$donations_treas[txn_id]'; "
				."document.transedit.Custom.value = '".htmlentities($donations_treas['custom'], ENT_QUOTES)."'; "
				."document.transedit.Option_seleczion1.value = '$donations_treas[option_seleczion1]'; "
				."document.transedit.Payment_status.value = '$donations_treas[payment_status]'; "
			 	."document.transedit.Mc_currency.value = '$donations_treas[mc_currency]'; "
			 	."document.transedit.Mc_gross.value = '$donations_treas[mc_gross]'; "
			 	."document.transedit.Mc_fee.value = '$donations_treas[mc_fee]'; "
			 	."document.transedit.Settle_amount.value = '$donations_treas[settle_amount]'; "
			 	."document.transedit.Payment_date.value = '$donations_treas[payment_date]'; "
			 	."document.transedit.Exchange_rate.value = '$donations_treas[exchange_rate]'; "
			 	."document.transedit.Eid.value = '$donations_treas[eid]'; "
			 	."document.transedit.Submit.value = 'Modify'; "
			 	."document.transedit.sa.value = 'transregedit'; "
			."return false;\">"
	  		.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/treasury_edit.png" alt="Edit" /></a><br />'
			.'<a href="', $scripturl.$context['treas_link'], ';sa=transregdel;id=', $donations_treas['id'], ';start=', $start, ';order=', $sort_order, ';mode=', $mode,';sesc=', $context['session_id'],  '">'
			.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/treasury_drop.png" onclick="return confirm(\'Are you sure you want to delete this record?\n\nAre you sure you want to do this now?\')" alt="Delete" />'
			.'</a></td>'
			."<td align=\"left\" class=\"smalltext\">$donations_treas[txn_id]</td>"
	        ."<td align=\"left\" class=\"smalltext\">$donations_treas[custom]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[option_seleczion1]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[payment_status]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[mc_currency]</td>"
	        ."<td align=\"right\" class=\"smalltext\">$donations_treas[mc_gross]</td>"
	        ."<td align=\"right\" class=\"smalltext\">$donations_treas[mc_fee]</td>"
	        ."<td align=\"right\" class=\"smalltext\">$donations_treas[settle_amount]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[payment_date]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[exchange_rate]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$donations_treas[eid]</td>"
			."</tr>";
		}
	}

    echo '</table><br />';
    echo '<form name="transedit" action="', $scripturl.$context['treas_link'], '" method="post">'
		.'<table><tr>'
	    .'<td class="titlebg2" align="center">Tax ID</td>'
	    .'<td class="titlebg2" align="center">Name</td>'
	    .'<td class="titlebg2" align="center">Show</td>'
	    .'<td class="titlebg2" align="center">Status</td>'
	    .'<td class="titlebg2" align="center">Curr.</td>'
	    .'<td class="titlebg2" align="center">Gross</td>'
	    .'<td class="titlebg2" align="center">Fee</td>'
	    .'<td class="titlebg2" align="center">Settle</td>'
	    .'<td class="titlebg2" align="center">Date</td>'
	    .'<td class="titlebg2" align="center">Rate</td>'
	    .'<td class="titlebg2" align="center">Event</td>'
		.'</tr><tr>'
        .'<td align="left" style="width:8px;"><input name="id" type="hidden" />'
        .'<input name="Txn_id" type="text" size="20" class="smalltext" /></td>'
        .'<td align="left"><input name="Custom" type="text" size="13" class="smalltext" /></td>'
        .'<td align="center"><input name="Option_seleczion1" type="text" size="4" class="smalltext" /></td>'
        .'<td align="center"><input name="Payment_status" type="text" size="10" class="smalltext" /></td>'
        .'<td align="center"><input name="Mc_currency" type="text" size="6" class="smalltext" /></td>'
        .'<td align="right"><input name="Mc_gross" type="text" size="6" class="smalltext" /></td>'
        .'<td align="right"><input name="Mc_fee" type="text" size="6" class="smalltext" /></td>'
        .'<td align="right"><input name="Settle_amount" type="text" size="6" class="smalltext" /></td>'
        .'<td align="center"><input name="Payment_date" type="text" size="22" class="smalltext" /></td>'
        .'<td align="center"><input name="Exchange_rate" type="text" size="6" class="smalltext" /></td>'
        .'<td align="center"><input name="Eid" type="text" size="3" class="smalltext" /></td>'
		.'</tr>';
	echo '<tr><td align="center" colspan="9"><br />'
		.'<input type="hidden" name="mode" value="', $mode, '" />'
		.'<input type="hidden" name="start" value="', $start, '" />'
		.'<input type="hidden" name="sort_order" value="', $sort_order, '" />'
		.'<input type="hidden" name="sc" value="', $context['session_id'], '" />'
		.'<input type="hidden" name="sa" value="transregadd" />'
	    ."<input name=\"\" type=\"reset\" value=\"Reset\" onclick=\""
		."document.transedit.Submit.value = 'Add'; "
		."document.transedit.sa.value = 'transregadd'; "
	    ."return true;\" />&nbsp;"
		.'<input name="Submit" type="submit" value="', $txt['treas_add'], '" />';
	echo '</td></tr></table></form>';
	echo '<br /><table width="100%" style="border:1px solid;" class="windowbg">
	<tr><td><b>Tax ID:</b></td><td> the receipt from paypal, something like ZYXGTY1234567890 (must be UNIQUE for every entry)</td></tr>
<tr><td><b>Name:</b></td><td> the Username who donated (must be genuine for details to show in Profile)</td></tr>
<tr><td><b>Show:</b></td><td> <b>Yes</b>, if they wanted their name displayed, otherwise <b>No</b>.</td></tr>
<tr><td><b>Status:</b></td><td> <b>Completed</b> for display,  <b>Refunded</b> if refund, <b>Pending</b> if waiting on echeck clearance.</td></tr>
<tr><td><b>Curr. :</b></td><td> currency of donation - must match one of USD CAD AUD YEN EUR GBP</td></tr>
<tr><td><b>Gross:</b></td><td> the donation amount, in that currency.</td></tr>
<tr><td><b>Fee:</b></td><td> the paypal fee for that donation, in that currency.</td></tr>
<tr><td><b>Settle:</b></td><td> the Gross less Fee, <b>converted</b> to your site (primary) currency.</td></tr>
<tr><td><b>Date:</b></td><td> the format ', timeformat(gmmktime(), treasdate()), ' - <b>must</b> match this format.</td></tr>
<tr><td><b>Rate:</b></td><td> the exchange rate (1.00 if donation currency same as site currency)</td></tr>
<tr><td><b>Event:</b></td><td> the ID value for any events you may have used, default 0)</td></tr>
</table>';
}

function template_treasury_totals() {
	global $scripturl, $boardurl, $txt, $context, $settings, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows, $search_time, $search_event;
	global $num, $total, $fees, $net, $settled, $periods, $periode;

	$pagination = treasuryPages($context['treas_link'] . ';sa=donortotals;search_time=' . $search_time . ';search_event=' . $search_event . ';mode=' . $mode . ';order=' . $sort_order, $totalRows, $maxRows, $start) . '&nbsp;';
	echo '<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/ts_picker.js">
	//Script by Denis Gritcyuk: tspicker@yahoo.com
	//Submitted to JavaScript Kit (http://javascriptkit.com)
	//Visit http://javascriptkit.com for this script
	</script>';
	echo '<form name="treas" method="post" action="', $scripturl.$context['treas_link'], ';sa=donortotals">
	<table width="100%" cellspacing="2" cellpadding="2" style="border:1px solid;" class="windowbg">
	<tr>
	<td align="left">', sprintf($txt['treas_page_of'], ( floor( $start / $maxRows ) + 1 ), ceil( $totalRows / $maxRows )), '</td>
	<td align="right" style="white-space:nowrap; font-size:10px;">', $txt['treas_select_sort'], ':&nbsp;';
	$options = array('lastdonated' => $txt['treas_sort_lastdate'], 'username' => $txt['treas_sort_username'], 'donation' => $txt['treas_sort_donations']);
	$select = '<select name="mode" id="mode">';
	foreach($options as $opname => $opvalue) {
		$select .= '<option value="' . $opname . '" ' . (($opname == $mode) ? 'selected="selected"' : '') . '>' . $opvalue . '</option>' . "\n";
	}
	$select .= '</select>';
	echo $select;

	echo '&nbsp;', $txt['treas_select_order'], ':&nbsp;';
	$options3 = array('DESC' => $txt['treas_sort_desc'], 'ASC' => $txt['treas_sort_asc']);
	$select3 = '<select name="order" id="order">';
	foreach($options3 as $opname3 => $opvalue3) {
		$select3 .= '<option value="' . $opname3 . '" ' . (($opname3 == $sort_order) ? 'selected="selected"' : '') . '>' . $opvalue3 . '</option>' . "\n";
	}
	$select3 .= '</select>';
	echo $select3;

	echo '&nbsp;', $txt['treas_select_time'], ':&nbsp;';
	$options2 = array(0 => $txt['treas_all_donations'], 1 => $txt['treas_last_day'], 7 => $txt['treas_last_week'], 
	14 => $txt['treas_last_fortnight'], 30 => $txt['treas_last_month'], 91 => $txt['treas_last_quarter'], 
	182 => $txt['treas_last_half'], 365 => $txt['treas_last_year'], 730 => $txt['treas_last_2years']);
	$select2 = '<select name="search_time" id="search_time">';
	foreach($options2 as $opname2 => $opvalue2) {
		$select2 .= '<option value="' . $opname2 . '" ' . (($opname2 == $search_time) ? 'selected="selected"' : '') . '>' . $opvalue2 . '</option>' . "\n";
	}
	$select2 .= '</select>';
	echo $select2;

	echo '&nbsp;', $txt['treas_event'], ':&nbsp;';
	$select4 = '<select name="search_event" id="search_event">';
	$select4 .= '<option value="0">No Event</option>';
	if (!empty($context['treas_eventid'])) {
		foreach($context['treas_eventid'] AS $eid => $etitle) {
			$select4 .= '<option value="' . $eid . '" ' . (($eid == $search_event) ? 'selected="selected"' : '') . '>' . $etitle . '</option>' . "\n";
		}
	}
	$select4 .= '</select>';
	echo $select4;

	echo '&nbsp;</td>
	</tr>
	<tr class="windowbg"><td colspan="2"><span style="float:left;">', $pagination, '</span>';
	echo '<span style="float:right;">
	<b>&raquo;</b> From <input type="text" name="periods" value="', ($periods >0 ? strftime('%Y-%m-%d', $periods) : ''), '" size="10" /> <a href="javascript:show_calendar(\'document.treas.periods\', document.treas.periods.value);" title="Choose Start Date"><img src="', $settings['default_images_url'], '/cal.gif" style="margin-bottom:-2px; width:16px; height:15px;" alt="" /></a>&nbsp;to&nbsp;<input type="text" name="periode" value="', ($periode > 0 ? strftime('%Y-%m-%d', $periode) : ''), '" size="10" /> <a href="javascript:show_calendar(\'document.treas.periode\', document.treas.periode.value);" title="Choose End Date"><img src="', $settings['default_images_url'], '/cal.gif" style="margin-bottom:-2px; width:16px; height:15px;" alt="" /></a> <a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_choose_period" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', ($context['treas_smf'] == 1 ? $txt['119'] : $txt['help']), '" align="top" /></a> <b>&laquo;</b>&nbsp;&nbsp;&nbsp;&nbsp; 
	<input type="hidden" name="sc" value="', $context['session_id'], '" />
	<input type="hidden" name="start" value="', $start, '" />
	<input type="submit" name="submit" value="', $txt['treas_change'], '" style="margin:0;" />&nbsp;</span>
	</td></tr>
	</table></form>';

    echo '<table width="100%" class="tborder"><tr class="titlebg2">'
    .'<td align="left"><b>', $txt['treas_name'], '</b></td>'
    .'<td align="center"><b>', $txt['treas_currency'], '</b></td>'
    .'<td align="right"><b>', $txt['treas_gross'], '</b></td>'
    .'<td align="right"><b>', $txt['treas_fees'], '</b></td>'
    .'<td align="right"><b>', $txt['treas_net'], '</b></td>'
    .'<td align="right"><b>', $txt['treas_settle'], '</b></td>'
    .'<td align="center"><b>', $txt['treas_last_date'], '</b></td>'
	.'</tr>';
	if (isset($context['donor_totals']))
	{
		foreach ($context['donor_totals'] as $totals_donor)
		{
			$totals_donor['lastdate'] = timeformat($totals_donor['lastdate'], treasdate());
	    	echo '<tr class="windowbg2">'
	        .'<td align="left">', $totals_donor['custom'], '</td>'
	        .'<td align="center">', $totals_donor['mc_currency'], '</td>'
	        .'<td align="right">', $totals_donor['mc_gross'], '</td>'
	        .'<td align="right">', $totals_donor['mc_fee'], '</td>'
	        .'<td align="right">', $totals_donor['mc_net'], '</td>'
	        .'<td align="right">', $totals_donor['settle_amount'], '</td>'
	        .'<td align="center">', $totals_donor['lastdate'], '</td>'
			.'</tr>';
		}
	}
	echo '</table><br />';

	$period = '';
	foreach($options2 as $opname2 => $opvalue2) {
		$period .= ($opname2 == $search_time) ? $opvalue2 : '';
	}
    echo '<table class="tborder" style="margin:auto; width:50%;">'
	.'<tr class="titlebg2"><td colspan="6" align="center"><b>', $txt['treas_summary_of'], ' ', ($search_event ? $txt['treas_all_donations'] : $period), '</b></td></tr>'
    .'<tr class="windowbg">'
	.'<td><b>', $txt['treas_type'], '</b></td><td align="right"><b>', $txt['treas_num'], '</b></td><td align="right"><b>', $txt['treas_total'], ' ', $tr_config['pp_currency'], '</b></td>'
	.'<td align="right"><b>', $txt['treas_gross'], '</b></td><td align="right"><b>', $txt['treas_fees'], '</b></td><td align="right"><b>', $txt['treas_net'], '</b></td>'
	.'</tr>'
	.'<tr>'
	.'<td class="windowbg"><b>', $txt['treas_amount'], '</b></td><td align="right" class="windowbg2">', $num, '</td><td align="right" class="windowbg2">', $settled, '</td>'
	.'<td align="right" class="windowbg2">', $total, '</td><td align="right" class="windowbg2">', $fees, '</td><td align="right" class="windowbg2">', $net, '</td>'
	.'</tr>'
	.'</table>';

}

function template_config()
{
	global $db_prefix, $context, $settings, $options, $scripturl, $txt, $tr_config, $tr_targets;

	echo '<form name="tr_configs" action="', $scripturl.$context['treas_link'], ';sa=configupdate" method="post">';
    echo '<table class="tborder" width="100%" cellspacing="0" cellpadding="0">';
	echo '<tr><td width="50%"><table width="100%">';

	$date_types = array('0' => 'yyyy-mm-dd hh:mm:ss', '1' => 'yyyy/mm/dd hh:mm:ss', 
	'2' => 'dd-mm-yyyy hh:mm:ss', '3' => 'dd/mm/yyyy hh:mm:ss');
	selectBox('date_format', '<b>' . $txt['treas_date_format'] . '</b>', $tr_config['date_format'], $date_types, '1');
	ShowYNBox('dm_show_targets', '<b>' . $txt['treas_display_goals'] . '</b>', '', '', '1');
	ShowYNBox('dm_show_meter', '<b>' . $txt['treas_display_meter'] . '</b>', '', '', '1');
	showYNBox('don_show_gross', '<b>' . $txt['treas_show_gross'] . '</b>', '', '', '1');
	showYNBox('don_show_date', '<b>' . $txt['treas_reveal_dates'] . '</b>', '', '', '1');
	showYNBox('don_show_amt', '<b>' . $txt['treas_reveal_amounts'] . '</b>', '', '', '1');
//	ShowYNBox('don_show_info_center', '<b>' . $txt['treas_show_info'] . '</b>', '', '4', '1');
	showYNBox('don_show_button_top', '<b>' . $txt['treas_top_button_show'] . '</b>', '', '', '1');
	showTextBox('don_button_top', '<b>' . $txt['treas_top_button'] . '</b>', '', '25', '1');
	showImgXYBox('don_top_img_width', 'don_top_img_height', '<b>' . $txt['treas_image_dims']. '</b>', '2', '1');

	echo '</table></td><td width="50%"><table width="100%">';
	$time_durations = array('0' => $txt['treas_monthly'], '1' => $txt['treas_quarterly'], '2' => $txt['treas_half_yearly'], '3' => $txt['treas_yearly']);
	selectBox('duration', '<b>' . $txt['treas_duration'] . '</b>', $tr_config['duration'], $time_durations, '1');
	ShowYNBox('group_use', '<b>' . $txt['treas_group_use'] . '</b>', '', '', '1');
	$donor_groups = array();
	$donor_groups[0] = 'None Selected';
	foreach($context['groups'] AS $gid => $gname) {
		$donor_groups[$gid] = $gname;
	}
	selectBox('group_id', '<b>' . $txt['treas_group_select'] . '</b>', $tr_config['group_id'], $donor_groups, '1');
	showYNBox('group_duration', '<b>' . $txt['treas_group_duration'] . '</b>', '', '', '1');
	showYNBox('group_anonymous', '<b>' . $txt['treas_group_anonymous'] . '</b>', '', '', '1');
//	showTextBox('group_minimum', '<b>' . $txt['treas_group_min'] . '</b>', '', '10', '1');
	showYNBox('show_registry', '<b>' . $txt['treas_show_registry'] . '</b>', '', '', '1');
	ShowTextBox('don_num_don', '<b>' . $txt['treas_num_donors'] . '</b>', '', '4', '1');
	showTextBox('don_button_submit', '<b>'.$txt['treas_submit_button'].'</b>', '', '25', '1');
	showImgXYBox('don_sub_img_width', 'don_sub_img_height', '<b>' . $txt['treas_image_dims'] . '</b>', '2', '1');

	echo '</table></td></tr></table>';
    echo '<table class="tborder" width="100%">';
	showTextBox('don_name_prompt', '<b>' . $txt['treas_username_prompt'] . '</b>', '', '60', '1');
	showTextBox('don_name_yes', '<b>' . $txt['treas_username_yes'] . '</b>', '', '60', '1');
	showTextBox('don_name_no', '<b>' . $txt['treas_username_no'] . '</b>', '', '60', '1');
	if ($tr_config['event_active']) {
		echo '<tr><td colspan="3" align="center"><span style="color:red;">BEWARE: you have an active event, so the following Title, Text and Monthly Goals are inoperative!</span></td></tr>';
	}
	ShowTextBox('don_text_title', '<b>' . $txt['treas_donation_title'] . '</b>', '', '60', '1');
	showTextArea('don_text', '<b>' . $txt['treas_donation_text'] . '</b>', '50', '10', '1');
	echo '</table><br />';

	echo '<div class="catbg">', $txt['treas_donation_goals'], '</div>';
    echo '<table class="tborder" width="100%">';
	$row1 = '<tr><td class="titlebg"><a href="' . $scripturl.$context['treas_link'] . ';sa=treashelp;help=treas_goal" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . ($context['treas_smf'] == 1 ? $txt['119'] : $txt['help']) . '" align="top" /></a></td>';
	$row2 = '<tr><td class="titlebg" align="left">' . $txt['treas_goal'] . '</td>';
	foreach ($tr_targets['goal'] as $block_month => $block_goal)
	{
		$row1 .= '<td class="titlebg">' . timeformat(mktime(0, 0, 0, $block_month, 1, 0), '%b') . '</td>';
		$row2 .= "<td><input size=\"4\" name=\"var_goal-$block_month\" type=\"text\" value=\"$block_goal\" /></td>";
	}
	$row1 .= '</tr>';
	$row2 .= '</tr>';
	echo $row1, ' ', $row2;
	echo '</table><br />';

	echo '<div class="catbg">', $txt['treas_donation_amounts'], '</div>';
    echo '<table class="tborder">';
	$row1 = '<tr><td class="titlebg"><a href="' . $scripturl.$context['treas_link'] . ';sa=treashelp;help=treas_don_amount" onclick="return reqWin(this.href);" class="help"><img src="' . $settings['images_url'] . '/helptopics.gif" alt="' . ($context['treas_smf'] == 1 ? $txt['119'] : $txt['help']) . '" align="top" /></a></td>';
	$row2 = '<tr><td class="titlebg">' . $txt['treas_amount'] . '</td>';
	foreach ($tr_targets['don_amount'] AS $amountn => $amounts)
	{
		$row1 .= "<td class=\"titlebg\">$amountn</td>";
		$row2 .= "<td><input size=\"4\" name=\"var_don_amount-$amountn\" type=\"text\" value=\"$amounts\" /></td>";
	}
	$row1 .= '</tr>';
	$row2 .= '</tr>';
	echo $row1, ' ', $row2;
	echo '</table>';
    echo '<table class="tborder" width="100%">';
	echo '<tr><td width="50%"><table width="100%">';
	showTextBox('don_amt_checked', '<b>' . $txt['treas_donation_default'] . '</b>', '300', '4', '1');
	echo '</table></td><td width="50%"><table width="100%">';
	showYNBox('don_amt_other', '<b>User can specify "Other" Amount?</b>', '', '', '1');
	echo '<table></td></tr></table></table><br />';

	echo '<input type="hidden" name="configger" value="', ($context['treas_smf'] == 2 ? 'action=admin;area=treasury' : 'action=treasuryadmin'), ';sa=config" />';
	echo '<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	echo '<input type="submit" value="', $txt['treas_submit'], '" />';
	echo '</form>';
}

function template_config_paypal()
{
	global $context, $txt, $scripturl, $boardurl, $tr_config, $tr_targets;

	echo '<form action="', $scripturl.$context['treas_link'], ';sa=configupdate" method="post"><table class="tborder" cellspacing="4" width="100%">';
	showTextBox('receiver_email', '<b>' . $txt['treas_pp_email'] . '</b>', '', '50', '1');
	ShowTextBox('pp_notify_url', '<b>' . $txt['treas_pp_notify_file'] . '</b><br /><a href="' . $boardurl . '/ipntreas.php?dbg=1" target="_blank"><b><i>' . $txt['treas_pp_test_ipn'] . '</i></b></a> ipntreas.php ', '', '50', '1');
	showTextBox('pp_ty_url', '<b>' . $txt['treas_pp_return_file'] . '</b><br />index.php?action=profile ', '', '50', '1');
	showTextBox('pp_cancel_url', '<b>' . $txt['treas_pp_cancel_file'] . '</b><br />index.php?action=treasury ', '', '50', '1');
	showTextBox('pp_image_url', '<b>' . $txt['treas_pp_image'] . '</b>', '', '50', '1');
	echo '</table>';
	echo '<table class="tborder" width="100%" cellspacing="0" cellpadding="0">';
	echo '<tr><td width="50%"><table width="100%">';
	selectBox('use_curl', '<b>' . $txt['treas_use_curl'] . '</b>', $tr_config['use_curl'], array('0' => 'fsockopen', '1' => 'cURL',), '1');
	showTextBox('pp_itemname', '<b>' . $txt['treas_pp_item_name'] . '</b>', '', '15', '1');
	$currencies = array();
	foreach($tr_targets['currency'] AS $subtype => $value) {
		$currencies[] = $subtype;
	}
	selectOption('pp_currency', '<b>' . $txt['treas_pp_currency'] . '</b>', $tr_config['pp_currency'], $currencies, '1');
	showYNBox('pp_sandbox', '<b>' . $txt['treas_pp_use_sandbox'] . '</b>', '', '', '1');
	echo '</table></td><td width="50%" valign="top"><table width="100%">';
	selectBox('pp_language', '<b>' . $txt['treas_pp_language'] . '</b>', $tr_config['pp_language'], array('DE' => 'Deutsch', 'US' => 'English', 'ES' => 'Espa&ntilde;ol', 'FR' => 'Fran&ccedil;ais', 'IT' => 'Italiano',), 1);
	showTextBox('pp_item_num', '<b>' . $txt['treas_pp_item_number'] . '</b>', '', '15', '1');
	showYNBox('pp_currency2', '<b>' . $txt['treas_pp_other_currency'] . '</b>', '', '', '1');
	echo '</table></td></tr></table><br />';

	echo '<div style="text-align:center;" class="catbg">'.$txt['treas_pp_log_options'].'</div>';
	echo '<table class="tborder" width="100%" cellspacing="0" cellpadding="0">';
	echo '<tr><td width="50%"><table width="100%">';
	selectBox('ipn_dbg_lvl', '<b>' . $txt['treas_pp_log_level'] . '</b>', $tr_config['ipn_dbg_lvl'], array('0' => 'Off', '1' => 'Log errors only', '2' => 'Log everything'), '1');
	echo '</table></td><td width="50%"><table width="100%">';
	showTextBox('ipn_log_entries', '<b>' . $txt['treas_pp_log_number'] . '</b>', '', '4', '1');
	echo '</table></td></tr></table><br />';

	echo '<input type="hidden" name="configger" value="', ($context['treas_smf'] == 2 ? 'action=admin;area=treasury' : 'action=treasuryadmin'), ';sa=configpaypal" />';
	echo '<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	echo '<input type="submit" value="', $txt['treas_submit'], '" />';
	echo '</form>';
}

function template_config_block()
{
	global $context, $scripturl, $txt, $tr_config, $tr_targets;

	echo '<form name="tr_configs" action="', $scripturl.$context['treas_link'], ';sa=configupdate" method="post">';
    echo '<table class="tborder" width="100%" cellspacing="0" cellpadding="0">';
	echo '<tr><td width="50%"><table width="100%">';
	ShowTextBox('dm_name_length', '<b>' . $txt['treas_block_username'] . '</b>', '', '4', '1');
	showTextBox('dm_num_don', '<b>' . $txt['treas_block_number'] . '</b>', '', '4', '1');
	showTextBox('dm_button', '<b>' . $txt['treas_block_image'] . '</b>', '', '25', '1');
	echo '</table></td><td width="50%"><table width="100%">';
	showYNBox('dm_show_date', '<b>' . $txt['treas_block_dates'] . '</b>', '', '', '1');
	showYNBox('dm_show_amt', '<b>' . $txt['treas_block_amounts'] . '</b>', '', '', '1');
	showImgXYBox('dm_img_width', 'dm_img_height', '<b>' . $txt['treas_block_image_size'] . '</b>', '4', '1');
	echo '</table></td></tr></table>';
    echo '<table class="tborder" width="100%">';
	showTextBox('dm_title', '<b>' . $txt['treas_block_title'] . '</b>', '', '40', '1');
	showTextArea('dm_comments', '<b>' . $txt['treas_block_comment'] . '</b>', '40', '2', '1');
	echo '</table><br />';

	echo '<input type="hidden" name="configger" value="', ($context['treas_smf'] == 2 ? 'action=admin;area=treasury' : 'action=treasuryadmin'), ';sa=configblock" />';
	echo '<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	echo '<input type="submit" value="', $txt['treas_submit'], '" />';
	echo '</form>';
}

function template_config_events()
{
	global $context, $scripturl, $txt, $tr_config, $settings;
	global $start, $sort_order, $maxRows, $mode, $totalRows;
	echo '<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/ts_picker.js">
	//Script by Denis Gritcyuk: tspicker@yahoo.com
	//Submitted to JavaScript Kit (http://javascriptkit.com)
	//Visit http://javascriptkit.com for this script
	</script>';

	echo '<form name="tr_events" action="', $scripturl.$context['treas_link'], ';sa=configupdate" method="post">';
	echo '<table class="tborder" width="100%">';
	$event_id = array();
	$event_id[0] = $txt['treas_event_inactive'];
	if (isset($context['eventid']))
	{
		foreach($context['eventid'] AS $eid => $etitle) {
			$event_id[$eid] = $etitle;
		}
	}
	selectBox('event_active', '<b>' . $txt['treas_event_active'] . '</b>', $tr_config['event_active'], $event_id, '1');
	echo '<tr class="windowbg"><td colspan="3" align="center">';
	echo '<input type="hidden" name="configger" value="', ($context['treas_smf'] == 2 ? 'action=admin;area=treasury' : 'action=treasuryadmin'), ';sa=configevents" />';
	echo '<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	echo '<input type="submit" value="', $txt['treas_submit'], '" />';
	if ($tr_config['event_active']) {
		echo '<br /><span style="color:red;">BEWARE: you activated an event - this will change your Treasury display!</span>';
	}
	echo '</td></tr>';
	echo '</table><br />';
	echo '</form>';

	// Events paging
	$pagination = treasuryPages($context['treas_link'] . ';sa=configevents;mode=' . $mode . ';order=' . $sort_order, $totalRows, $maxRows, $start) . '&nbsp;';
	echo '<form method="post" action="', $scripturl.$context['treas_link'], ';sa=configevents">
	<table width="100%" cellspacing="2" cellpadding="2" style="border:1px solid;" class="windowbg">
	<tr>
	<td align="left">', sprintf($txt['treas_page_of'], ( floor( $start / $maxRows ) + 1 ), ceil( $totalRows / $maxRows )), '</td>
	  <td align="right" style="white-space:nowrap;">', $txt['treas_select_sort'], ':&nbsp;';
	$options = array('lastevent' => 'Sort Last Event', 'target' => 'Sort Target', 'actual' => 'Sort Actual');
	$select = '<select name="mode" id="mode">';
	foreach($options as $opname => $opvalue) {
		$select .= '<option value="' . $opname . '" ' . (($opname == $mode) ? 'selected="selected"' : '') . '>' . $opvalue . '</option>' . "\n";
	}
	$select .= '</select>';
	echo $select;
	echo '&nbsp;&nbsp;', $txt['treas_select_order'], '&nbsp;';
	$options2 = array('DESC' => $txt['treas_sort_desc'], 'ASC' => $txt['treas_sort_asc']);
	$select2 = '<select name="order" id="order">';
	foreach($options2 as $opname2 => $opvalue2) {
		$select2 .= '<option value="' . $opname2 . '" ' . (($opname2 == $sort_order) ? 'selected="selected"' : '') . '>' . $opvalue2 . '</option>' . "\n";
	}
	$select2 .= '</select>';
	echo $select2;
	echo '	  </td>
	</tr>
	<tr>
	<td align="left">', $pagination, '</td>
	<td align="right">
		<input type="hidden" name="start" value="', $start, '" />
		<input type="submit" name="submit" value="', $txt['treas_sort'], '" />
	</td></tr>
	</table></form>';

	$txt_help = $context['treas_smf'] == 1 ? $txt['119'] : $txt['help'];
	echo '<table width="100%" class="tborder">'
	.'<tr><td class="catbg2" align="center"><b>&nbsp;</b></td>'
	.'<td class="catbg2" width="120" align="center">', $txt['treas_event_title'], ' <a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_title" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'<td class="catbg2" align="center">', $txt['treas_event_descr'], ' <a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_descr" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'<td class="catbg2" align="center" width="70">', $txt['treas_event_target'], '<a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_target" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'<td class="catbg2" align="center" width="70">', $txt['treas_event_actual'], '<a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_actual" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'<td class="catbg2" align="center" width="90">', $txt['treas_event_start'], '<a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_start" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'<td class="catbg2" align="center" width="90">', $txt['treas_event_end'], '<a href="', $scripturl.$context['treas_link'], ';sa=treashelp;help=treas_events_end" onclick="return reqWin(this.href);" class="help"><img src="', $settings['images_url'], '/helptopics.gif" alt="', $txt_help, '" align="top" /></a></td>'
	.'</tr>';

	if (isset($context['treas_events']))
	{
		foreach ($context['treas_events'] as $events_treas)
		{
			$events_treas['date_start'] = timeformat($events_treas['date_start'], '%Y-%m-%d');
			$events_treas['date_end'] = $events_treas['date_end'] == '0' ? '' : timeformat($events_treas['date_end'], '%Y-%m-%d');
			echo '<tr class="windowbg">'
			.'<td align="center">'
			.'<a href="', $scripturl.$context['treas_link'], ';sa=configevents;op=edit;eid=', $events_treas['eid'], ';start=', $start, ';sesc=', $context['session_id'],  '"><img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/edit.png" alt="Edit" /></a><br />'
			.'<a href="', $scripturl.$context['treas_link'], ';sa=eventsdel;id=', $events_treas['eid'], ';start=', $start, ';order=', $sort_order, ';mode=', $mode,';sesc=', $context['session_id'],  '">'
			.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/drop.png" onclick="return confirm(\'Are you sure you want to delete this record?\n\nAre you sure you want to do this now?\')" alt="Delete" />'
			.'</a></td>'
	        .'<td align="left" class="smalltext">'.stripslashes($events_treas['title']).'</td>'
	        .'<td align="left" class="smalltext">'.stripslashes($events_treas['description']).'</td>'
	        ."<td align=\"center\" class=\"smalltext\">$events_treas[target]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$events_treas[actual]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$events_treas[date_start]</td>"
	        ."<td align=\"center\" class=\"smalltext\">$events_treas[date_end]</td>"
			.'</tr>';
		}
	}
	echo '</table><br />';

	if (isset($context['treas_event']))
	{
		foreach ($context['treas_event'] as $event_treas)
		{
			$eid = $event_treas['eid'];
			$title = stripslashes($event_treas['title']);
			$description = stripslashes($event_treas['description']);
			$date_start = timeformat($event_treas['date_start'], '%Y-%m-%d');
			$date_end = $event_treas['date_end'] == '0' ? '' : timeformat($event_treas['date_end'], '%Y-%m-%d');
			$target = $event_treas['target'];
			$actual = $event_treas['actual'];
		}
	} else {
		$eid = 0;
		$title = '';
		$description = '';
		$date_start = '';
		$date_end = '';
		$target = '';
		$actual = '';
	}
	echo '<form name="treas" action="', $scripturl.$context['treas_link'], '" method="post">'
		.'<table class="tborder" width="100%"><tr>'
		.'<td class="catbg2">', $txt['treas_event_titlemax'], '</td>'
		.'<td class="catbg2">', $txt['treas_event_descr'], '</td>'
		.'<td class="catbg2" align="center" width="60">', $txt['treas_event_target'], '</td>'
		.'<td class="catbg2" align="center" width="60">', $txt['treas_event_actual'], '</td>'
		.'<td class="catbg2" width="100">', $txt['treas_event_start'], '</td>'
		.'<td class="catbg2" width="100">', $txt['treas_event_end'], '</td>'
		.'</tr><tr class="windowbg">'
		.'<td valign="top">'
		.'<input name="title" type="text" size="30" maxlength="25" class="smalltext" value="', $title, '" /></td>'
		.'<td><textarea name="description" cols="43" rows="6" class="smalltext">', $description, '</textarea></td>'
		.'<td align="center" valign="top"><input name="target" type="text" size="9" class="smalltext" value="', $target, '" /></td>'
		.'<td align="center" valign="top"><input name="actual" type="text" size="9" class="smalltext" value="', $actual, '" /></td>'
		.'<td valign="top"><input name="date_start" type="text" size="11" class="smalltext" value="', $date_start, '"/><a href="javascript:show_calendar(\'document.treas.date_start\', document.treas.date_start.value);" title="Choose Start Date"><img src="', $settings['default_images_url'], '/cal.gif" style="margin-bottom:-2px; width:16px; height:15px;" alt="" /></a></td>'
		.'<td valign="top"><input name="date_end" type="text" size="11" class="smalltext" value="', $date_end, '" /><a href="javascript:show_calendar(\'document.treas.date_end\', document.treas.date_end.value);" title="Choose End Date"><img src="', $settings['default_images_url'], '/cal.gif" style="margin-bottom:-2px; width:16px; height:15px;" alt="" /></a></td>'
		.'</tr>';
	echo '<tr class="windowbg">
	<td colspan="6" align="center">
	<input type="hidden" name="sc" value="', $context['session_id'], '" />';
	if ($eid) {
		echo '
		<input type="hidden" name="sa" value="eventsedit" />
		<input type="hidden" name="mode" value="', $mode, '" />
		<input type="hidden" name="start" value="', $start, '" />
		<input type="hidden" name="eid" value="', $eid, '" />
		<input type="submit" name="update" style="width:120px;" value="Update Event" /></td>
		</tr>';
	} else {
		echo '
		<input type="hidden" name="sa" value="eventsadd" />
		<input type="submit" name="save" style="width:100px;" value="Add an Event" /></td>
		</tr>';
	}
	echo '</td></tr>';
	echo '</table></form>';
}

function template_trans_log()
{
	global $scripturl, $txt, $context, $settings, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows;

	// Transaction log paging
	$pagination = treasuryPages($context['treas_link'] . ';sa=translog;order=' . $sort_order, $totalRows, $maxRows, $start). '&nbsp;';

	echo '<form method="post" action="', $scripturl.$context['treas_link'], ';sa=translog">
	<table width="100%" cellspacing="2" cellpadding="2" style="border:1px solid;" class="windowbg">
	<tr>
	<td align="left">', sprintf($txt['treas_page_of'], ( floor( $start / $maxRows ) + 1 ), ceil( $totalRows / $maxRows )), '</td>
	  <td align="right" style="white-space:nowrap;">', $txt['treas_select_order'], '&nbsp;';
	$options = array('DESC' => $txt['treas_sort_desc'], 'ASC' => $txt['treas_sort_asc']);
	$select = '<select name="order" id="order">';
	foreach($options as $opname => $opvalue) {
		$select .= '<option value="' . $opname . '" ' . (($opname == $sort_order) ? 'selected="selected"' : '') . '>' . $opvalue . '</option>' . "\n";
	}
	$select .= '</select>';
	echo $select;
	echo '	  </td>
	</tr>
	<tr>
	<td align="left">', $pagination, '</td>
	<td align="right">
		<input type="hidden" name="start" value="', $start, '" />
		<input type="submit" name="submit" value="', $txt['treas_sort'], '" />
	</td></tr>
	</table></form>';


    echo '<table class="tborder" width="100%" style="margin:auto;"><tr>'
	.'<td class="titlebg2" align="center"><b>&nbsp;</b></td>'
    .'<td class="titlebg2" align="center">Log Date</td>'
    .'<td class="titlebg2" align="center">Payment</td>'
    .'<td class="titlebg2" align="center">Log Entry</td></tr>';
	if (!empty($context['trans_log']))
	{
		foreach ($context['trans_log'] as $log_trans)
		{
		    echo '<tr>'
			.'<td align="left"><a href="', $scripturl.$context['treas_link'], ';sa=translogdel;id=', $log_trans['id'], ';start=', $start, ';order=', $sort_order, ';sesc=', $context['session_id'],  '">'
			.'<img style="border:0; width:12px; height:13px;" src="', $settings['default_images_url'], '/treasury_drop.png" onclick="return confirm(\'Are you sure you want to delete this record?\n\nAre you sure you want to do this now?\')" alt="Delete" />'
			.'</a></td>'
			.'<td align="left">', timeformat($log_trans['log_date'], treasdate()), '</td>'
	        .'<td align="left">', ($log_trans['payment_date'] > 0) ? timeformat($log_trans['payment_date'], treasdate()) : '-', '</td>'
	        ."<td align=\"left\">$log_trans[logentry]</td></tr>";
			echo '<tr><td colspan="4"><hr /></td></tr>';
		}
	}
	else
	{
		echo '<tr><td colspan="3">No data.</td></tr>';
	}
    echo '</table>';

}

function template_ipn_reconcile()
{
	global $context, $scripturl, $txt, $curdate, $ipn_total, $numrecs, $rval;

	echo '<div class="titlebg" style="text-align:center;"><b>', $txt['treas_financial'], ' ', $txt['treas_reconciliation'], '</b>';
	echo '<br /><b>', $txt['treas_registry_updated'], '</b><br /><br />';

	if ( $numrecs == 0 )
	{
		echo 'There are no new IPN records to import!';
	} else {
		ipnrecUpdate();
		if ($rval)
		{
			echo "<b>$numrecs</b> ", $txt['treas_registry_imported'], " $", sprintf('%0.2f', $ipn_total);
		}
		else
		{
			echo "<b> ERROR : There are $numrecs records to import, but there was an<br />error encountered during db record insertion into the Financial table.<br />Insertion FAILED!";
		}
	}

	echo '<br /><br /><button type="button" onclick="self.location.href=\'', $scripturl.$context['treas_link'], ';sa=registry\';" style="background-color:#FFCC68; color:#000068; font-weight:bold;">Return to Treasury Admin</button>';
	echo '</div>';

}

function template_read_me()
{
	global $context, $sourcedir, $txt;
	require($sourcedir.'/TreasuryReadme.php');
}

function template_treasuryhelp()
{
	global $context, $settings, $options, $txt;
	// hmmm, don't want to affect core, so borrow SMF code - thank you :)
	// Since this is a popup of its own we need to start the html, etc.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
		<title>', $context['page_title'], '</title>
		<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/style.css" />
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js"></script>
		<style type="text/css">';

	// Internet Explorer 4/5 and Opera 6 just don't do font sizes properly. (they are bigger...)
	if ($context['browser']['needs_size_fix'])
		echo '
			@import(', $settings['default_theme_url'], '/css/fonts-compat.css);';

	// Just show the help text and a "close window" link.
	echo '
		</style>
	</head>
	<body style="margin: 1ex;">
		<div class="popuptext">
			', $context['help_text'], '<br />
			<br />
			<div align="center"><a href="javascript:self.close();">Close Window</a></div>
		</div>
	</body>
</html>';
}
?>