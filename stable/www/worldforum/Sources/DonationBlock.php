<?php
/*************************************************************************
* This program is free software; you can redistribute it and/or modify   *
* it under the terms of the GNU General Public License as published by   *
* the Free Software Foundation; either version 2 of the License.         *
*                                                                        *
* $Source: /0cvs/TreasurySMF/DonationBlock.php,v $                       *
* $Revision: 1.11 $                                                       *
* $Date: 2010/01/25 04:18:18 $                                           *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz               *
*************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

global $db_prefix, $txt, $context, $boarddir, $scripturl, $smcFunc, $settings;
loadLanguage('Treasury');

$cfgset = $smcFunc['db_query']('', 'SELECT * 
	FROM {db_prefix}treas_config',
	array(
	)
);
$tr_config = array();
while ($cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
	$tr_config[$row['name']] = $row['value'];
}
$smcFunc['db_free_result']($cfgset);

$cfgtgt = $smcFunc['db_query']('', 'SELECT *
	FROM {db_prefix}treas_targets',
	array(
	)
);
$tr_targets = array();
while ($cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
	$tr_targets[$row['name']][$row['subtype']] = $row['value'];
}
$smcFunc['db_free_result']($cfgtgt);

if ($tr_config['event_active'])
{
	$result = $smcFunc['db_query']('', "
		SELECT * FROM {db_prefix}treas_events 
		WHERE eid = '$tr_config[event_active]' 
		LIMIT 1
	", array());
	while ($row = $smcFunc['db_fetch_assoc']($result))
		$context['info_event'][] = array(
			'eid' => $row['eid'],
			'title' => stripslashes($row['title']),
			'description' => stripslashes($row['description']),
			'date_start' => $row['date_start'],
			'date_end' => $row['date_end'],
			'target' => $row['target'],
			'actual' => $row['actual'],
		);
	$smcFunc['db_free_result']($result);
}

$month = date('n');
$year = date('Y');
$duration = $tr_config['duration'];
$tr_period = array();
$qstart = $month >= 10 ? 10 : ($month >= 7 ? 7 : ($month >= 4 ? 4 : 1));
$hstart = $month >= 7 ? 7 : 1;
$startb = !$duration ? $month : ($duration == 1 ? $qstart : ($duration == 2 ? $hstart : 1));
$tr_period[0] = gmmktime(0, 0, 0, $startb, 1, $year, 0);
$endb = !$duration ? 1 : (($duration <3) ? $duration*3  :  12); 
$tr_period[1] = gmmktime(23, 59, 59, $startb+$endb, 0, $year, 0);
$tr_period[2] = $duration == 0 ? timeformat(mktime(0, 0, 0, $month, 1, 0), '%B') : ($duration == 1 ? $txt['treas_quarterly'] : ($duration == 2 ? $txt['treas_half_yearly'] : $txt['treas_year'].' '.date('Y')));
$tr_period[3] = 0;
if (!$duration) {
	$tr_period[3] = $tr_targets['goal'][$month];
} else {
	for ($i=$startb; $i<=($startb+$endb-1); $i++) {
		$tr_period[3] += $tr_targets['goal'][$i];
	}
}

setlocale(LC_TIME, $txt['lang_locale']);
	$lc_time = substr($txt['lang_locale'], 0, 5);
	$smcFunc['db_query']('', "SET lc_time_names = '$lc_time'", array());
$where = $tr_config['event_active'] ? "group_id = '$tr_config[event_active]'" : "( payment_date >= $tr_period[0] ) AND ( payment_date <= $tr_period[1] )";
$query_Recordset2 = "
	SELECT business, COUNT( settle_amount ) AS count, SUM( mc_gross * exchange_rate ) AS receipts, SUM( settle_amount ) AS net,  FROM_UNIXTIME( $tr_period[1] , ' %b %e' ) AS due_by 
	FROM {$db_prefix}treas_donations 
	WHERE $where 
		AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
	GROUP BY business";
$Recordset2 = $smcFunc['db_query']('', $query_Recordset2,
	array(
	)
);
$row_Recordset2 = $smcFunc['db_fetch_assoc']($Recordset2);
$smcFunc['db_free_result']($Recordset2);

// If there are not records, then get "null" data
if ( !$row_Recordset2 )
{
	$row_Recordset2['due_by'] = date('M', $tr_period[1]).' '.date('d', $tr_period[1]);
	$row_Recordset2['count'] = $row_Recordset2['receipts'] = $row_Recordset2['net'] = 0;
}

// Get currency symbol
$currency_symbol = $tr_targets['currency'][$tr_config['pp_currency']];

if ($tr_config['event_active'] && isset($context['info_event']))
{
	foreach ($context['info_event'] as $event_info)
	{
		$row_Recordset2['due_by'] = !empty($event_info['date_end']) ? timeformat($event_info['date_end'], '%M %d') : $txt['treas_event_open'];
		$tr_period[2] = $txt['treas_event_campaign'];
		$tr_period[3] = $event_info['target'];
		$tr_config['dm_title'] = $event_info['title'];
	}
}

$dm_button_dims = is_numeric($tr_config['dm_img_width']) ? 'width:'.$tr_config['dm_img_width'].'px;' : '';
$dm_button_dims .= is_numeric($tr_config['dm_img_height']) ? 'height:'.$tr_config['dm_img_height'].'px;' : '';
echo '<div style="text-align:center;text-decoration:blink;">', $tr_config['dm_title'], '<br /></div>
	<div style="text-align:center;">
	', ($tr_config['dm_comments'] ? '<br />'.$tr_config['dm_comments'].'<br />' : ''), '
	<a href="', $scripturl, '?action=treasury">
	<img src="', $settings['default_images_url'], '/', $tr_config['dm_button'], '" style="margin:5px 0px 0px 0px; border:0;', $dm_button_dims, '" alt="Donate with PayPal!"  /></a>
	</div>';

if ($tr_config['dm_show_targets'] || $tr_config['dm_show_meter']) {

	$dm_left = sprintf('%.02f', $tr_period[3] - ($tr_config['don_show_gross'] ? $row_Recordset2['receipts'] : $row_Recordset2['net']));
	$pp_fees = sprintf('%.02f', $row_Recordset2['receipts'] - $row_Recordset2['net']);
	$donatometer = ($tr_period[3] > 0) ? round((100 * ($tr_config['don_show_gross'] ? $row_Recordset2['receipts'] : $row_Recordset2['net']) / $tr_period[3]), 0) : '0';

	$donormeter = '<div style="width:100%; height:12px; background-color:#FFFFFF; border:1px solid green;">'.($donatometer < 15 ? '<div style="width:'.$donatometer.'%; height:10px; margin:1px; background-color:green;"></div><div style="font-size:8px; margin-top:-11px; text-indent:'.$donatometer.'%; color:green;">&nbsp;'.$donatometer.'%</div>' : ($donatometer > 99 ? '<div style="width:98%; height:10px; margin:1px; background-color:blue;"><span style="font-size:8px; float:right; color:#FFFFFF;">'.$donatometer.'%&nbsp;</span></div>' : '<div style="width:'.$donatometer.'%; height:10px; margin:1px; background-color:green;"><span style="font-size:8px; float:right; color:#FFFFFF;">'.$donatometer.'%&nbsp;</span></div>')).'</div>';

	echo '<div style="width:145px;margin:auto;">';
	if ($tr_config['dm_show_targets']) {
		echo '<span style="width:95px;font-size:10px;float:left;">', $tr_period[2], ' ', $txt['treas_goal'], ':</span>
	    <span style="float:right;font-size:10px;">', $currency_symbol.sprintf('%.02f', $tr_period[3]), '</span><br />
	    <span style="width:95px;font-size:10px;float:left;">', $txt['treas_due_date'], ':</span>
	    <span style="float:right;font-size:10px;">', $row_Recordset2['due_by'], '</span><br />
	    <span style="width:95px;font-size:10px;float:left;">', $txt['treas_total_receipts'], ':</span>
	    <span style="float:right;font-size:10px;">', $currency_symbol.sprintf('%.02f', $row_Recordset2['receipts']), '</span><br />';
		if ($tr_config['don_show_gross'] == 0) {
			echo '<span style="width:95px;font-size:10px;float:left;">PayPal Fees:</span>
		    <span style="float:right;font-size:10px;">', $currency_symbol.$pp_fees, '</span><br />
		    <span style="width:95px;font-size:10px;float:left;">', $txt['treas_net_balance'], ':</span>
		    <span style="float:right;font-size:10px;">', $currency_symbol.sprintf('%.02f', $row_Recordset2['net']), '</span><br />';
		}
		echo '<span style="width:95px;font-size:10px;float:left;">', (($dm_left >= 0 ) ? $txt['treas_below_goal'] : $txt['treas_above_goal']), ':</span>
	    <span style="float:right;font-size:10px;">', $currency_symbol.sprintf('%.02f', abs($dm_left)), '</span><br />
		<span style="width:95px;font-size:10px;float:left;">', $txt['treas_site_currency'], ':</span>
		<span style="float:right;font-size:10px;">', $tr_config['pp_currency'], '</span><br />';
	}
	// Do we want the donormeter displayed?
	echo ($tr_config['dm_show_meter'] ? $donormeter : '');
	echo '</div>';
}

// Do we want to display the donors? 
if (is_numeric($tr_config['dm_num_don']) && $tr_config['dm_num_don'] >= 0) {
	// Get the list of donors
	$query_Recordset4 = "
		SELECT user_id, custom AS name, option_seleczion1 AS showname, mc_currency AS currency, currency_symbol AS symbol, settle_amount AS settled, FROM_UNIXTIME( payment_date, '%b-%e' ) AS date, SUM(ROUND(mc_gross,2)) AS amt 
		FROM {db_prefix}treas_donations
		WHERE $where 
			AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
		GROUP BY txn_id 
		ORDER BY payment_date DESC";
	$Recordset4 = $smcFunc['db_query']('', $query_Recordset4,
		array(
		)
	);
	$numset3 = $smcFunc['db_num_rows']($Recordset4);
	if ($numset3) {
		echo '<div style="width:100%; text-align:center; font-size:11px;"><b><u>', $tr_period[2], ' ', $txt['treas_donations'], '</u></b></div>';

		// List all the donors
		echo '<div style="width:100%">';
		$i = 0;
		while ( ($row_Recordset4 = $smcFunc['db_fetch_assoc']($Recordset4)) && ($i != $tr_config['dm_num_don']) ) {
			// Refunded transactions will show up with $0 amount
			if( $row_Recordset4['amt'] > '0' ) {
				// Observe the user's wish regarding revealing their name
				if ( strcmp($row_Recordset4['showname'], 'Yes') == 0) {
					$dname = (strlen($row_Recordset4['name']) > $tr_config['dm_name_length']) ? substr($row_Recordset4['name'],0,$tr_config['dm_name_length']).'...' : $row_Recordset4['name'];
				} else {
					$dname = $txt['treas_anonymous'];
				}
				$dname = (($row_Recordset4['name'] == $context['user']['name'] || allowedTo('admin_treasury')) && $row_Recordset4['user_id'] > 0 ) ? '<a href="'.$scripturl.'?action=profile;u='.$row_Recordset4['user_id'].';sa=showDonations" style="text-decoration:underline;">'.$dname.'</a>' : $dname;

				if ( !$tr_config['dm_show_amt'] && !$tr_config['dm_show_date'] ) {
					echo '<div>';
					echo  $dname;
					echo '</div>';
				} else {
					echo '<div>';
					if ( $tr_config['dm_show_date'] ) {
						echo '<span style="font-size:xx-small; float:left;">';
						echo $row_Recordset4['date'];
						echo '&nbsp;</span>';
					} else {
						echo '';
					}
					echo '<span style="font-size:xx-small; float:left;">';
					echo $dname;
					echo '</span>';
					if ( $tr_config['dm_show_amt'] ) {
						echo '<span style="font-size:xx-small; float:right;">';
						echo ' ', $row_Recordset4['currency'].$row_Recordset4['amt'];
						echo '</span>';
					} else {
						echo '';
					}
					echo '<br /></div>';
				}
			}
			$i++;
		}
		$smcFunc['db_free_result']($Recordset4);
		echo '</div>';
	}
}
?>