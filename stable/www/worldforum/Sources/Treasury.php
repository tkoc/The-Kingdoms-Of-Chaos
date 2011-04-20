<?php
/*************************************************************************
* Originally NukeTreasury - Financial management for PHP-Nuke            *
* Copyright (c) 2004 by Dave Lawrence AKA Thrash  thrash@fragnastika.com *
*                                                                        *
* This program is free software; you can redistribute it and/or modify   *
* it under the terms of the GNU General Public License as published by   *
* the Free Software Foundation; either version 2 of the License.         *
* $Source: /0cvs/TreasurySMF/Treasury.php,v $                            *
* $Revision: 1.28 $                                                      *
* $Date: 2010/01/25 04:18:18 $                                           *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz               *
*************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function treasuryMain()
{

	// Load the main treasury language and template
	if (loadlanguage('Treasury') == false)
		loadLanguage('Treasury','english');
	loadtemplate('Treasury');

	// Treasury actions
	$subActions = array(
		'paypal' => 'paypal_return',
	);


	// Follow the sa or just go to main links index.
	if (!empty($_GET['sa']) && isset($_GET['sa']))
		$subActions[$_GET['sa']]();
	else
		view();

}

function view()
{
	global $smcFunc, $txt, $context, $mbname, $id_member;
	global $tr_config, $tr_targets, $period, $is_donor, $net_registry, $row_Recordset3;
	//Check if the current user can view treasury
	isAllowedTo('view_treasury');
	//Load the main index treasury template
	$context['sub_template']  = 'main';
	//Set the page title
	$context['page_title'] = $mbname . ' - Treasury';

	$cfgset = $smcFunc['db_query']('', 'SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$cfgtgt = $smcFunc['db_query']('', 'SELECT *
		FROM {db_prefix}treas_targets',
		array(
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
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

	$context['don_text'] = $tr_config['don_text'];

	setlocale(LC_TIME, $txt['lang_locale']);
	$period = treasDates(date('n'), date('Y'), $tr_config['duration']);
	$lc_time = substr($txt['lang_locale'], 0, 5);
	$smcFunc['db_query']('', "SET lc_time_names = '$lc_time'", array());
	$where = $tr_config['event_active'] ? "group_id = '$tr_config[event_active]'" : "( payment_date >= $period[0] ) AND ( payment_date <= $period[1] )";
	$query_Recordset1 = "
		SELECT user_id, custom AS name, option_seleczion1 as showname, mc_currency AS currency, currency_symbol AS symbol, settle_amount AS settled, FROM_UNIXTIME( payment_date, '%b-%e' ) AS date, SUM(ROUND(mc_gross,2)) AS amt 
		FROM {db_prefix}treas_donations
		WHERE $where 
			AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
		GROUP BY txn_id 
		ORDER BY payment_date DESC
		LIMIT $tr_config[don_num_don]";
	$Recordset1 = $smcFunc['db_query']('', $query_Recordset1,
		array(
		)
	);
	$totalRows_Recordset1 = $smcFunc['db_num_rows']($Recordset1);

	while ($row = $smcFunc['db_fetch_assoc']($Recordset1))
		$context['rows_donators'][] = array(
			'user_id' => $row['user_id'],
			'name' => $row['name'],
			'showname' => $row['showname'],
			'currency' => $row['currency'],
			'symbol' => $row['symbol'],
			'settled' => $row['settled'],
			'date' => $row['date'],
			'amt' => $row['amt'],
		);
	$smcFunc['db_free_result']($Recordset1);
	$query_Recordset3 = "
		SELECT business, COUNT( settle_amount ) AS count, SUM( mc_gross * exchange_rate ) AS receipts, SUM( settle_amount ) AS net,  FROM_UNIXTIME( $period[1] , ' %b %e' ) AS due_by 
		FROM {db_prefix}treas_donations 
		WHERE $where 
			AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
		GROUP BY business";

	// Get the donation totals
	$Recordset3 = $smcFunc['db_query']('', $query_Recordset3,
		array(
		)
	);
	$row_Recordset3 = $smcFunc['db_fetch_assoc']($Recordset3);
	$smcFunc['db_free_result']($Recordset3);
	// If there are not records, then get "null" data
	if ( !$row_Recordset3 )
	{
		$row_Recordset3['due_by'] = date('M', $period[1]).' '.date('d', $period[1]);
		$row_Recordset3['count'] = $row_Recordset3['receipts'] = $row_Recordset3['net'] = 0;
	}

	if ($tr_config['show_registry'])
	{
		$userdonor = $smcFunc['db_query']('', '
			SELECT user_id FROM {db_prefix}treas_donations
			WHERE user_id = {int:userid}', 
			array(
				'userid' => $context['user']['id'],
			)
		);
		$is_donor = $smcFunc['db_num_rows']($userdonor);
		if ($is_donor >= 1)
		{
			$registry_rows = $smcFunc['db_query']('', '
				SELECT SUM(amount) as total, SUM(num) as number, name  FROM {db_prefix}treas_registry
				GROUP BY name
			', array());
			while ($row = $smcFunc['db_fetch_assoc']($registry_rows))
				$context['show_registry'][] = array(
					'total' => $row['total'],
					'number' => $row['number'],
					'name' => $row['name'],
				);
			$smcFunc['db_free_result']($registry_rows);
			$registry_net = $smcFunc['db_query']('', '
				SELECT SUM(amount) as net FROM {db_prefix}treas_registry
			', array());
			list($net_registry) = $smcFunc['db_fetch_row']($registry_net);
			$smcFunc['db_free_result']($registry_net);
		}
	}
}

function paypal_return()
{
	global $db_prefix, $smcFunc, $scripturl, $txt, $context, $user_info;
	global $first_name, $last_name, $custom, $option_seleczion1, $item_name, $payment_amount, $payment_currency, $total;
	// Check if the current user can view treasury
	isAllowedTo('view_treasury');
	$context['sub_template'] = 'paypal_return';

	$txn_id = $_GET['id'];
	$total = array();
	$result = $smcFunc['db_query']('', '
		SELECT mc_gross, mc_currency 
		FROM {db_prefix}treas_donations 
		WHERE user_id={int:userid}',
		array(
			'userid' => $user_info['id'],
		)
	);
	while ($row = $smcFunc['db_fetch_row']($result)) {
		$ammount = intval(str_replace('.', '', $row[0]));
		if (isset($total[$row[1]]))
		{
			$total[$row[1]] += $ammount;
		} else {
			$total[$row[1]] = $ammount;
		}
	}
	$smcFunc['db_free_result']($result);

	$result = $smcFunc['db_query']('', '
		SELECT first_name, last_name, custom, option_seleczion1, item_name, mc_gross, mc_currency 
		FROM {db_prefix}treas_donations 
		WHERE txn_id={string:txnid}',
		array(
			'txnid' => $txn_id,
		)
	);
	list ($first_name, $last_name, $custom, $option_seleczion1, $item_name, $payment_amount, $payment_currency) = $smcFunc['db_fetch_row']($result);
	$smcFunc['db_free_result']($result);
}

function treasDates($month, $year, $duration) {
	global $tr_targets, $txt;
	$period = array();
	$qstart = $month >= 10 ? 10 : ($month >= 7 ? 7 : ($month >= 4 ? 4 : 1));
	$hstart = $month >= 7 ? 7 : 1;
	$start = !$duration ? $month : ($duration == 1 ? $qstart : ($duration == 2 ? $hstart : 1));
	$period[0] = gmmktime(0, 0, 0, $start, 1, $year, 0);
	$end = !$duration ? 1 : (($duration <3) ? $duration*3  :  12); 
	$period[1] = gmmktime(23, 59, 59, $start+$end, 0, $year, 0);
	$period[2] = $duration == 0 ? timeformat(mktime(0, 0, 0, $month, 1, 0), '%B') : ($duration == 1 ? $txt['treas_quarterly'] : ($duration == 2 ? $txt['treas_half_yearly'] : $txt['treas_year'].' '.date('Y')));
	$period[3] = 0;
	if (!$duration) { $period[3] = $tr_targets['goal'][$month]; } else {
		for ($i=$start; $i<=($start+$end-1); $i++) {
			$period[3] += $tr_targets['goal'][$i];
		}
	}
	return $period;
}
?>