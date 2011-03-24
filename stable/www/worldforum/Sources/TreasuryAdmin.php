<?php
/*******************************************************************************
* Originally NukeTreasury - Financial management for PHP-Nuke                  *
* Copyright (c) 2004 by Dave Lawrence AKA Thrash  thrash@fragnastika.com       *
*                                                                              *
* This program is free software; you can redistribute it and/or modify         *
* it under the terms of the GNU General Public License as published by         *
* the Free Software Foundation; either version 2 of the License.               *
* $Source: /0cvs/TreasurySMF/TreasuryAdmin.php,v $                             *
* $Revision: 1.35 $                                                            *
* $Date: 2010/01/25 04:18:18 $                                                 *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz                     *
*******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function treasuryAdmin()
{
    global $smcFunc, $scripturl, $context, $txt;
	$context['treas_link'] = '?action=admin;area=treasury';
	$context['treas_smf'] = 2;

	loadLanguage('Treasury');
	loadTemplate('TreasuryAdmin');

	//Treasury actions
	$subActions = array(
		'readme' => array('readMe', 'admin_treasury'),
		'registry' => array('treasuryRegister', 'admin_treasury'),
		'transregadd' => array('transactionRegAdd', 'admin_treasury'),
		'transregdel' => array('transactionRegDel', 'admin_treasury'),
		'transregedit' => array('transactionRegEdit', 'admin_treasury'),
		'translog' => array('transactionLog', 'admin_treasury'),
		'translogdel' => array('transactionLogdelete', 'admin_treasury'),
		'finregadd' => array('financialRegAdd', 'admin_treasury'),
		'finregedit' => array('financialRegEdit', 'admin_treasury'),
		'finregdel' => array('financialRegDel', 'admin_treasury'),
		'config' => array('config', 'admin_treasury'),
		'configpaypal' => array('configPaypal', 'admin_treasury'),
		'configblock' => array('configBlock', 'admin_treasury'),
		'configevents' => array('configEvents', 'admin_treasury'),
		'configupdate' => array('configUpdate', 'admin_treasury'),
		'eventsedit' => array('eventsEdit', 'admin_treasury'),
		'eventsadd' => array('eventsAdd', 'admin_treasury'),
		'eventsdel' => array('eventsDelete', 'admin_treasury'),
		'ipnrec' => array('ipnRec', 'admin_treasury'),
		'ipnrecupdate' => array('ipnrecUpdate', 'admin_treasury'),
		'donations' => array('treasuryDonations', 'admin_treasury'),
		'donortotals' => array('treasuryTotals', 'admin_treasury'),
		'treashelp' => array('treasuryHelp', 'admin_treasury'),
	);


	// Default the sub-action to 'readme'.
	$_GET['sa'] = isset($_POST['sa']) ? $_POST['sa'] : (isset($_GET['sa']) && isset($subActions[$_GET['sa']]) ? $_GET['sa'] : 'readme');
	$context['sub_action'] = $_GET['sa'];

	// Make sure you can do this.
	isAllowedTo($subActions[$_GET['sa']][1]);

	// Tabs for browsing the different treasury functions.
	$context[$context['admin_menu_name']]['tab_data'] = array(
		'title' => $txt['treasury_admin'],
		'help' => '',
		'description' => $txt['treasury_admin'],
		'tabs' => array(
			'readme' => array(
				'title' => $txt['treas_read_me'],
				'description' => $txt['treas_read_me_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=readme',
			),
			'registry' => array(
				'title' => $txt['treas_fin_register'],
				'description' => $txt['treas_fin_register_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=registry',
			),
			'donations' => array(
				'title' => $txt['treas_donations'],
				'description' => $txt['treas_donations_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=donations',
			),
			'donortotals' => array(
				'title' => $txt['treas_donor_totals'],
				'description' => $txt['treas_donor_totals_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=donortotals',
			),
			'config' => array(
				'title' => $txt['treas_main_config'],
				'description' => $txt['treas_main_config_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=config',
			),
			'configpaypal' => array(
				'title' => $txt['treas_paypal_config'],
				'description' => $txt['treas_paypal_config_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=configpaypal',
			),
			'configblock' => array(
				'title' => $txt['treas_block_config'],
				'description' => $txt['treas_block_config_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=configblock',
			),
			'configevents' => array(
				'title' => $txt['treas_events'],
				'description' => $txt['treas_event_config'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=configevents',
			),
			'translog' => array(
				'title' => $txt['treas_transaction_log'],
				'description' => $txt['treas_transaction_log_descr'],
				'href' => $scripturl . '?action=admin;area=treasury;sa=translog',
				'is_last' => true,
			),
		),
	);

	//Try to activate a tab.
	if (isset($context['admin_tabs']['tabs'][$context['sub_action']]))
		$context['admin_tabs']['tabs'][$context['sub_action']]['is_selected'] = true;

	//Otherwise it's going to be the browse anyway...
	else
		$context['admin_tabs']['tabs']['readme']['is_selected'] = true;

	// Call the right function for this sub-acton.
	$subActions[$_GET['sa']][0]();
}

function parsedate($value) {
	// Courtesy of ScottB on php.net
    $reformatted = preg_replace("/^\s*([0-9]{1,2})[\/\. -]+([0-9]{1,2})[\/\. -]+([0-9]{1,4})/", "\\2/\\1/\\3", $value);
    return strtotime($reformatted);
}

function treasdate() {
	global $tr_config;
	if ($tr_config['date_format'] == 0) { $treas_date = '%Y-%m-%d %H:%M:%S'; }
	elseif ($tr_config['date_format'] == 1) { $treas_date = '%Y/%m/%d %H:%M:%S'; }
	elseif ($tr_config['date_format'] == 2) { $treas_date = '%d-%m-%Y %H:%M:%S'; }
	elseif ($tr_config['date_format'] == 3) { $treas_date = '%d/%m/%Y %H:%M:%S'; }
	return $treas_date;
}

function treasuryRegister()
{
    global $smcFunc, $scripturl, $context, $txt, $num_ipn, $ipn_tot, $total;
	global $pageNum_Recordset1, $totalRows_Recordset1, $totalPages_Recordset1;
	$context['sub_template'] = 'treasuryregister';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$cfgtgt = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}treas_targets',
		array(
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
		$tr_targets[$row['name']][$row['subtype']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgtgt);

	// Register paging
	$maxRows_Recordset1 = 10;
	$pageNum_Recordset1 = isset($_POST['pageNum_Recordset1']) ? intval($_POST['pageNum_Recordset1']) : '0';
	$startRow_Recordset1 = $pageNum_Recordset1 * $maxRows_Recordset1;
	$query_Recordset1 = "
		SELECT id, date AS fdate, num, name, descr, amount 
		FROM {db_prefix}treas_registry 
		ORDER BY date DESC";
	$query_limit_Recordset1 = $query_Recordset1 . ' LIMIT ' . $startRow_Recordset1 . ', ' . $maxRows_Recordset1;
	$Recordset1 = $smcFunc['db_query']('', $query_limit_Recordset1, array());
	if (isset($_POST['totalRows_Recordset1']))
	{
		$totalRows_Recordset1 = $_POST['totalRows_Recordset1'];
	} else {
		$all_Recordset1 = $smcFunc['db_query']('', $query_Recordset1, array());
		$totalRows_Recordset1 = $smcFunc['db_affected_rows']();
	}
	$totalPages_Recordset1 = ceil($totalRows_Recordset1/$maxRows_Recordset1)-1;

	while ($row = $smcFunc['db_fetch_assoc']($Recordset1))
		$context['treas_registry'][] = array(
			'id' => $row['id'],
			'fdate' => $row['fdate'],
			'num' => $row['num'],
			'name' => $row['name'],
			'descr' => $row['descr'],
			'amount' => $row['amount'],
		);
	$smcFunc['db_free_result']($Recordset1);

	// Collect IPN reconcile data
	// First, get the date of the last time we reconciled
	$Recordset2 = $smcFunc['db_query']('', "
		SELECT date AS recdate 
		FROM {db_prefix}treas_registry 
		WHERE name = 'PayPal IPN' 
		ORDER BY date DESC 
		LIMIT 1",
		array()
	);
	list($recdate) = $smcFunc['db_fetch_row']($Recordset2);
	$smcFunc['db_free_result']($Recordset2);

	// Get the date of the last donation
	$Recordset3 = $smcFunc['db_query']('', "
		SELECT payment_date AS curdate 
		FROM {db_prefix}treas_donations 
		WHERE (payment_status = 'Completed' OR payment_status = 'Refunded') 
			AND ( txn_type = 'send_money' OR txn_type = 'web_accept' ) 
		ORDER BY payment_date DESC 
		LIMIT 1",
		array()
	);
	list($curdate) = $smcFunc['db_fetch_row']($Recordset3);
	$smcFunc['db_free_result']($Recordset3);

	// Collect the IPN transactions between recdate and curdate
	$Recordset4 = $smcFunc['db_query']('', "
		SELECT SUM( settle_amount ) AS ipn_tot, COUNT( * ) AS num_ipn 
		FROM {db_prefix}treas_donations 
		WHERE (payment_status = 'Completed' OR payment_status = 'Refunded') 
			AND ( payment_date > '$recdate' AND payment_date <= '$curdate' )
		",
		array()
	);
	list($ipn_tot, $num_ipn) = $smcFunc['db_fetch_row']($Recordset4);
	$smcFunc['db_free_result']($Recordset4);

	// Get the register balance
	$Recordset5 = $smcFunc['db_query']('', "
		SELECT SUM(amount) AS total 
		FROM {db_prefix}treas_registry", 
		array()
	);
	list($total) = $smcFunc['db_fetch_row']($Recordset5);
	$smcFunc['db_free_result']($Recordset5);

}

function treasuryDonations()
{
    global $smcFunc, $scripturl, $txt, $context, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'treasury_donations';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	// Donation paging
	$maxRows = 10;
	$start = isset($_POST['start']) ? intval($_POST['start']) : (isset($_GET['start']) ? intval($_GET['start']) : 0);
	$sort_order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] :'');
	$sort_order = (isset($sort_order) && $sort_order == 'ASC') ? 'ASC' : 'DESC';
	$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : 'lastdonated');
	switch ($mode) {
		case 'lastdonated':
			$order_by = "payment_date $sort_order LIMIT $start, ".$maxRows;
			break;
		case 'username':
			$order_by = "custom $sort_order LIMIT $start, ".$maxRows;
			break;
		case 'donation':
			$order_by = "ROUND(mc_gross,0) $sort_order LIMIT $start, ".$maxRows;
			break;
	}
	$query_Recordset1 = "
		SELECT id, txn_id, custom, option_seleczion1, payment_status, mc_gross, mc_fee, payment_date, mc_currency, settle_amount, exchange_rate, group_id 
		FROM {db_prefix}treas_donations";
	$query_limit_Recordset1 = $query_Recordset1.' ORDER by '.$order_by;

	$Recordset1 = $smcFunc['db_query']('', $query_limit_Recordset1, array());
	$all_Recordset1 = $smcFunc['db_query']('', $query_Recordset1, array());
	$totalRows = $smcFunc['db_affected_rows']();

	while ($row = $smcFunc['db_fetch_assoc']($Recordset1))
		$context['treas_donations'][] = array(
			'id' => $row['id'],
			'txn_id' => $row['txn_id'],
			'custom' => $row['custom'],
			'option_seleczion1' => $row['option_seleczion1'],
			'payment_status' => $row['payment_status'],
			'mc_gross' => $row['mc_gross'],
			'mc_fee' => $row['mc_fee'],
			'payment_date' => $row['payment_date'],
			'mc_currency' => $row['mc_currency'],
			'settle_amount' => $row['settle_amount'],
			'exchange_rate' => $row['exchange_rate'],
			'eid' => $row['group_id'],
		);
	$smcFunc['db_free_result']($Recordset1);

}

function treasuryTotals() {
    global $smcFunc, $scripturl, $txt, $context, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows, $search_time, $search_event;
	global $num, $total, $fees, $net, $settled, $periods, $periode;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'treasury_totals';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	// Set the event and get the events.
	$eventid = $smcFunc['db_query']('', '
		SELECT eid, title
		FROM {db_prefix}treas_events',
		array(
		)
	);
	$context['eventid'] = array();
	while ($eventid && $row = $smcFunc['db_fetch_assoc']($eventid))
		$context['treas_eventid'][$row['eid']] = $row['title'];
	$smcFunc['db_free_result']($eventid);

	// Totals paging
	$maxRows = 10;
	$search_event = isset($_POST['search_event']) ? intval($_POST['search_event']) : (isset($_GET['search_event']) ? intval($_GET['search_event']) : '0');
	$search_time = isset($_POST['search_time']) ? intval($_POST['search_time']) : (isset($_GET['search_time']) ? intval($_GET['search_time']) : '0');
	$searchtime = $search_time == '0' ? '0' : gmmktime() - ($search_time * 86400);
	$start = isset($_POST['start']) ? intval($_POST['start']) : (isset($_GET['start']) ? intval($_GET['start']) : 0);
	$sort_order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] :'');
	$sort_order = (isset($sort_order) && $sort_order == 'ASC') ? 'ASC' : 'DESC';
	$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : 'donation');
	switch ($mode) {
		case 'lastdonated':
			$order_by = "lastdate $sort_order LIMIT $start, " . $maxRows;
			break;
		case 'username':
			$order_by = "custom $sort_order LIMIT $start, " . $maxRows;
			break;
		case 'donation':
			$order_by = "total $sort_order LIMIT $start, " . $maxRows;
			break;
	}
	$periods = isset($_POST['periods']) ? strtotime($_POST['periods']) : '';
	$periode = isset($_POST['periode']) ? strtotime($_POST['periode']) : '';

	$search_query = !empty($search_event) ? "group_id = $search_event" : (($periods == '' || $periode == '') ? "payment_date > $searchtime" : "payment_date > $periods AND payment_date < $periode");
	$query_Recordset1 = "SELECT user_id, custom, SUM(ROUND(mc_gross,2)) AS total, SUM(ROUND(mc_fee,2)) AS fee, SUM(ROUND(mc_gross,2))-SUM(ROUND(mc_fee,2)) AS net, MAX(payment_date) AS lastdate, mc_currency, SUM(ROUND(settle_amount,2)) AS settle 
	FROM {db_prefix}treas_donations 
	WHERE $search_query 
		AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
	GROUP BY user_id";
	$query_limit_Recordset1 = $query_Recordset1 . ' ORDER BY ' . $order_by;

	$Recordset1 = $smcFunc['db_query']('', $query_limit_Recordset1, array());
	$all_Recordset1 = $smcFunc['db_query']('', $query_Recordset1, array());
	$totalRows = $smcFunc['db_affected_rows']();

	while ($row = $smcFunc['db_fetch_assoc']($Recordset1))
		$context['donor_totals'][] = array(
			'custom' => $row['custom'],
			'mc_gross' => $row['total'],
			'mc_fee' => $row['fee'],
			'mc_net' => $row['net'],
			'lastdate' => $row['lastdate'],
			'mc_currency' => $row['mc_currency'],
			'settle_amount' => $row['settle'],
		);
	$smcFunc['db_free_result']($Recordset1);

	$query_Recordset2 = "SELECT COUNT(*) AS number, SUM(ROUND(mc_gross,2)) AS total, SUM(ROUND(mc_fee,2)) AS fee, SUM(ROUND(mc_gross,2))-SUM(ROUND(mc_fee,2)) AS net, SUM(ROUND(settle_amount,2)) AS settle 
	FROM {db_prefix}treas_donations 
	WHERE $search_query
		AND (payment_status = 'Completed' OR payment_status = 'Refunded')";
	$Recordset2 = $smcFunc['db_query']('', $query_Recordset2, array());
	list($num, $total, $fees, $net, $settled) = $smcFunc['db_fetch_row']($Recordset2);
	$smcFunc['db_free_result']($Recordset2);
}

function transactionRegAdd()
{
    global $smcFunc, $txt, $tr_config;
	isAllowedTo('admin_treasury');
	checkSession();

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);
	$cfgtgt = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}treas_targets',
		array(
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
		$tr_targets[$row['name']][$row['subtype']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgtgt);

	$currency_symbol = $tr_targets['currency'][$_POST['Mc_currency']];
	$bizness = $tr_config['receiver_email'];
	$itemname = $tr_config['pp_itemname'];
	$itemnum = $tr_config['pp_item_num'];

	if ($_POST['Payment_date'] == '')
	{
		fatal_error('The Date field cannot be blank');
	}
	elseif (strtotime($_POST['Payment_date']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['Custom']) == 0)
	{
		fatal_error('The Name field cannot be blank');
	}
	elseif (!is_numeric($_POST['Mc_gross']))
	{
		fatal_error('Invalid Gross field');
	}
	elseif (!is_numeric($_POST['Mc_fee']))
	{
		fatal_error('Invalid Fee field');
	}
	elseif ($_POST['Exchange_rate'] == ''){
		fatal_error('Exchange rate field cannot be blank');
	}
	elseif (!is_numeric($_POST['Exchange_rate']))
	{
		fatal_error('Invalid Exchange Rate field');
	}
	else
	{
		$custom = htmlentities($_POST['Custom'], ENT_QUOTES);
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}members
			WHERE (real_name = {string:custom} OR member_name = {string:custom}) 
			LIMIT 1', 
			array(
				'custom' => $custom,
			)
		);
		list ($user_id) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		$exchange_rate= ($_POST['Exchange_rate'] == 0) ? '1.00' : $_POST['Exchange_rate'];
		$nTime = parsedate($_POST['Payment_date']);
		$insert_Transaction = "
			INSERT INTO {db_prefix}treas_donations 
			(id, user_id, business, item_name, item_number, txn_id, custom, option_seleczion1, payment_status, mc_currency, currency_symbol, mc_gross, mc_fee, settle_amount, exchange_rate, txn_type, payment_date, group_id) 
			VALUES 
			(DEFAULT, '$user_id', '$bizness', '$itemname', '$itemnum', '$_POST[Txn_id]', '$custom', '$_POST[Option_seleczion1]', '$_POST[Payment_status]', '$_POST[Mc_currency]', '$currency_symbol', '$_POST[Mc_gross]', '$_POST[Mc_fee]', '$_POST[Settle_amount]', '$exchange_rate', 'web_accept', '$nTime', '$_POST[Eid]')";
		$rvalue = $smcFunc['db_query']('', $insert_Transaction, array());

		donorGroup ($user_id, $_POST['Custom'], $nTime, $_POST['Option_seleczion1']);
		redirectexit('action=admin;area=treasury;sa=donations');
	}
}

function transactionRegDel()
{
    global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession('get');

	$start = (isset($_GET['start']) && intval($_GET['start'])) ? $_GET['start'] : 0;
	$sort_order = isset($_GET['order']) ? $_GET['order'] : '';
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'lastdonated';
	if ( !(is_numeric($_GET['id']) && $_GET['id']>0))
	{
		fatal_error('Invalid record id specified, operation aborted');
	} else {
		$del_Transaction = "
			DELETE 
			FROM {db_prefix}treas_donations 
			WHERE id='$_GET[id]' 
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $del_Transaction, array());
		redirectexit('action=admin;area=treasury;sa=donations;mode='.$mode.';start='.$start.';order='.$sort_order);
	}
}

function transactionRegEdit()
{
    global $smcFunc, $txt, $scripturl;
	isAllowedTo('admin_treasury');
	checkSession();

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
	$currency_symbol = $tr_targets['currency'][$_POST['Mc_currency']];

	$start = isset($_POST['start']) ? intval($_POST['start']) : (isset($_GET['start']) ? intval($_GET['start']) : 0);
	$sort_order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] :'');
	$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : 'lastdonated');
	if ($_POST['Payment_date'] == '')
	{
		fatal_error('The Date field cannot be blank');
	}
	elseif (strtotime($_POST['Payment_date']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['Custom']) == 0)
	{
		fatal_error('The Name field cannot be blank');
	}
	elseif (!is_numeric($_POST['Mc_gross']))
	{
		fatal_error('Invalid Gross field');
	}
	elseif (!is_numeric($_POST['Mc_fee']))
	{
		fatal_error('Invalid Fee field');
	}
	elseif ($_POST['Exchange_rate'] == '')
	{
		fatal_error('Exchange rate field cannot be blank');
	}
	elseif (!is_numeric($_POST['Exchange_rate']))
	{
		fatal_error('Invalid Exchange Rate field');
	}
	else
	{
		$custom = htmlentities($_POST['Custom'], ENT_QUOTES);
		$request = $smcFunc['db_query']('', '
			SELECT id_member
			FROM {db_prefix}members
			WHERE (real_name = {string:custom} OR member_name = {string:custom}) 
			LIMIT 1', 
			array(
				'custom' => $custom,
			)
		);
		list ($user_id) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		$nTime = parsedate($_POST['Payment_date']);
		$insert_Transaction = "
			UPDATE {db_prefix}treas_donations 
			SET user_id = '$user_id', payment_date='$nTime', txn_id='$_POST[Txn_id]', custom='$custom', option_seleczion1='$_POST[Option_seleczion1]', payment_status='$_POST[Payment_status]', mc_currency='$_POST[Mc_currency]', currency_symbol='$currency_symbol', mc_gross='$_POST[Mc_gross]', mc_fee='$_POST[Mc_fee]', settle_amount='$_POST[Settle_amount]', exchange_rate = '$_POST[Exchange_rate]', txn_type='web_accept', group_id='$_POST[Eid]' 
			WHERE id='$_POST[id]' 
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $insert_Transaction, array());

		donorGroup ($user_id, $_POST['Custom'], $nTime, $_POST['Option_seleczion1']);
		redirectexit('action=admin;area=treasury;sa=donations;mode='.$mode.';start='.$start.';order='.$sort_order);
	}
}

function financialRegAdd()
{
    global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession();

	if ($_POST['Date'] == '')
	{
		fatal_error('The Date field cannot be blank');
	}
	elseif (strtotime($_POST['Date']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['Name']) == 0)
	{
		fatal_error('The Name field cannot be blank');
	}
	elseif (!is_numeric($_POST['Amount']))
	{
		fatal_error('Invalid Amount field');
	}
	else
	{
		$nTime = parsedate($_POST['Date']);
		$insert_Recordset = "
			INSERT INTO {db_prefix}treas_registry 
			(id, date, num, name, descr, amount) 
			VALUES 
			(DEFAULT, '$nTime', '$_POST[Num]', '$_POST[Name]', '$_POST[Descr]', '$_POST[Amount]') ";
		$rvalue = $smcFunc['db_query']('', $insert_Recordset, array());
		redirectexit('action=admin;area=treasury;sa=registry');
	}
}

function financialRegDel()
{
    global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession('get');

	if ( !(is_numeric($_GET['id']) && $_GET['id'] > 0))
	{
		fatal_error('Invalid record id specified, operation aborted');
	}
	else
	{
		$del_Recordset = "
			DELETE 
			FROM {db_prefix}treas_registry 
			WHERE id='$_GET[id]' 
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $del_Recordset, array());
		redirectexit('action=admin;area=treasury;sa=registry');
	}
}

function financialRegEdit()
{
    global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession();

	if ($_POST['Date'] == '')
	{
		fatal_error('The Date field cannot be blank');
	}
	elseif (strtotime($_POST['Date']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['Name']) == 0)
	{
		fatal_error('The Name field cannot be blank');
	}
	elseif (!is_numeric($_POST['Amount']))
	{
		fatal_error('Invalid Amount field, do not use any characters other than -.0123456789');
	}
	else
	{
		$nTime = parsedate($_POST['Date']);
		$insert_Recordset = "
			UPDATE {db_prefix}treas_registry 
			SET date='$nTime', num='$_POST[Num]', name='$_POST[Name]', descr='$_POST[Descr]', amount='$_POST[Amount]' 
			WHERE id='$_POST[id]' 
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $insert_Recordset, array());
		redirectexit('action=admin;area=treasury;sa=registry');
	}
}

function selectYN($nm, $val)
{
	global $tr_config, $tr_targets;
	echo '<select size="1" name="', $nm, '">';
	if ($val)
	{
		echo '<option selected="selected" value="1">Yes</option>'
		. '<option value="0">No</option>';
	} else {
		echo '<option value="1">Yes</option>'
		. '<option selected="selected" value="0">No</option>';
	}
	echo '</select>';
}

function showYNBox($name, $desc, $tdWidth, $inpSize, $useHelp)
{
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

    echo '<tr class="windowbg">'
    .'<td style="font-size:10px;">', $desc, '</td>'
    .'<td align="left">';
	echo '<select size="1" name="var_', $name, '">';
	if ( $tr_config[$name] )
	{
		echo '<option selected="selected" value="1">Yes</option>'
		. '<option value="0">No</option>';
	} else {
		echo '<option value="1">Yes</option>'
		. '<option selected="selected" value="0">No</option>';
	}
	echo '</select></td>
	<td align="center">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $name. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>
	</tr>';
}

function showTextBox($name, $desc, $tdWidth, $inpSize, $useHelp)
{
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

    echo '<tr class="windowbg">'
    .'<td style="width:', $tdWidth, 'px; font-size:10px;">', $desc, '</td>'
    .'<td align="left">'
	.'<input size="', $inpSize, '" name="var_', $name, '" type="text" value="', $tr_config[$name], '" /></td>'
	.'<td align="center" style="width:16px;">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $name. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>'
	.'</tr>';
}

function showTextArea($name, $desc, $tcols, $trows, $useHelp)
{
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

    echo '<tr class="windowbg">'
    .'<td style="font-size:11px;">', $desc, '</td>'
    .'<td align="left">'
	.'<textarea name="var_'.$name.'" cols="'.$tcols.'" rows="'.$trows.'">'.$tr_config[$name].'</textarea>'
	.'<td align="center">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $name. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>'
	.'</tr>';
}

function showImgXYBox($xnm, $ynm, $desc, $inpSize, $useHelp)
{
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

	echo '<tr class="windowbg">'
	.'<td style="font-size:10px;">', $desc, '</td><td align="left" style="white-space:nowrap;">';
	echo 'Width '
	."<input size=\"$inpSize\" name=\"var_$xnm\" type=\"text\" value=\"$tr_config[$xnm]\" />";
	echo ' Height '
	."<input size=\"$inpSize\" name=\"var_$ynm\" type=\"text\" value=\"$tr_config[$ynm]\" />";
	echo '</td>
	<td align="center">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $xnm. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>
	</tr>';
}

function selectBox($name, $desc, $default, $options, $useHelp) {
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

    echo '<tr class="windowbg">'
    .'<td style="font-size:10px;">', $desc, '</td>'
    .'<td align="left">';
	$select = '<select name="var_' . $name . '" id="var_' . $name . '">';
	foreach($options as $value => $opname) {
		$select .= "<option value=\"$value\"" . (($value == $default)?' selected="selected"':'') . ">$opname</option>\n";
	}
	echo $select, '</select></td>
	<td align="center">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $name. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>
	</tr>';
}

function selectOption($name, $desc, $default, $options, $useHelp) {
	global $smcFunc, $tr_config, $tr_targets, $scripturl, $settings, $txt;
	isAllowedTo('admin_treasury');

    echo '<tr class="windowbg">'
    .'<td style="font-size:10px;">', $desc, '</td>'
    .'<td align="left">';
	$select = '<select name="var_' . $name . '" id="var_' . $name . '">';
	foreach($options as $opname) {
		$select .= "<option " . (($opname == $default)?' selected="selected"':'') . ">$opname</option>\n";
	}
	echo $select, '</select></td>
	<td align="center">', ($useHelp ? '<a href="' . $scripturl . '?action=admin;area=treasury;sa=treashelp;help=treas_'. $name. '" onclick="return reqWin(this.href);" class="help"><img src="'. $settings['images_url']. '/helptopics.gif" alt="'. $txt['help']. '" align="top" /></a>' : ''), '</td>
	</tr>';
}

function config()
{
    global $smcFunc, $tr_config, $tr_targets, $scripturl, $context;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'config';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$cfgtgt = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}treas_targets',
		array(
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
		$tr_targets[$row['name']][$row['subtype']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgtgt);

	// Get the groups.
	$request = $smcFunc['db_query']('', '
		SELECT id_group, group_name
		FROM {db_prefix}membergroups
		WHERE id_group > {int:min_group}
			AND min_posts = {int:min_posts}',
		array(
			'min_group' => 3,
			'min_posts' => -1,
		)
	);
	$context['groups'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$context['groups'][$row['id_group']] = $row['group_name'];
	$smcFunc['db_free_result']($request);
}

function configPaypal()
{
    global $smcFunc, $scripturl, $txt, $context, $tr_config, $tr_targets;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'config_paypal';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$cfgtgt = $smcFunc['db_query']('', '
		SELECT *
		FROM {db_prefix}treas_targets
		WHERE name={string:curr}',
		array(
			'curr' => 'currency',
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
		$tr_targets['currency'][$row['subtype']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgtgt);
}

function configBlock()
{
    global $smcFunc, $tr_config, $tr_targets, $dbg_lvl, $scripturl, $txt, $context;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'config_block';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$cfgtgt = $smcFunc['db_query']('', "
		SELECT *
		FROM {db_prefix}treas_targets
		WHERE name='goal'",
		array(
		)
	);
	$tr_targets = array();
	while ( $cfgtgt && $row = $smcFunc['db_fetch_assoc']($cfgtgt)) {
		$tr_targets['goal'][$row['subtype']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgtgt);
}

function configEvents()
{
    global $smcFunc, $tr_config, $context;
	global $op, $eid, $start, $sort_order, $maxRows, $mode, $totalRows;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'config_events';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$eid = isset($_POST['eid']) ? $_POST['eid'] : (isset($_GET['eid']) ? $_GET['eid'] : '');
	// Events paging
	$maxRows = 10;
	$start = isset($_POST['start']) ? intval($_POST['start']) : (isset($_GET['start']) ? intval($_GET['start']) : 0);
	$sort_order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] :'');
	$sort_order = (isset($sort_order) && $sort_order == 'ASC') ? 'ASC' : 'DESC';
	$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : 'lastevent');
	switch ($mode) {
		case 'lastevent':
			$order_by = "eid $sort_order LIMIT $start, ".$maxRows;
			break;
		case 'target':
			$order_by = "target $sort_order LIMIT $start, ".$maxRows;
			break;
		case 'actual':
			$order_by = "actual $sort_order LIMIT $start, ".$maxRows;
			break;
	}
	$query_Recordset = "
		SELECT * 
		FROM {db_prefix}treas_events";
	$query_limit_Recordset = $query_Recordset.' ORDER BY '.$order_by;

	$Recordset = $smcFunc['db_query']('', $query_limit_Recordset, array());
	$all_Recordset = $smcFunc['db_query']('', $query_Recordset, array());
	$totalRows = $smcFunc['db_num_rows']($all_Recordset);

	// Set the event and get the events.
	$eventid = $smcFunc['db_query']('', "
		SELECT eid, title
		FROM {db_prefix}treas_events", array());
	$context['eventid'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($eventid))
		$context['eventid'][$row['eid']] = $row['title'];
	$smcFunc['db_free_result']($eventid);

	$events = $smcFunc['db_query']('', "
		SELECT * 
		FROM {db_prefix}treas_events
		ORDER BY eid DESC", array());
	while ($row = $smcFunc['db_fetch_assoc']($Recordset))
		$context['treas_events'][] = array(
			'eid' => $row['eid'],
			'date_start' => $row['date_start'],
			'date_end' => $row['date_end'],
			'title' => $row['title'],
			'description' => $row['description'],
			'target' => $row['target'],
			'actual' => $row['actual'],
		);
	$smcFunc['db_free_result']($Recordset);

	if (!empty($eid)) {
		$query2 = "
			SELECT * 
			FROM {db_prefix}treas_events
			WHERE eid='$eid'
			LIMIT 1";
		$result2 = $smcFunc['db_query']('', $query2, array());
		while ($row = $smcFunc['db_fetch_assoc']($result2))
			$context['treas_event'][] = array(
			'eid' => $row['eid'],
			'date_start' => $row['date_start'],
			'date_end' => $row['date_end'],
			'title' => $row['title'],
			'description' => $row['description'],
			'target' => $row['target'],
			'actual' => $row['actual'],
			);
		$smcFunc['db_free_result']($result2);
	}
}

function updateTarget($nm, $sub, $val)
{
	global $ilog, $smcFunc;
	isAllowedTo('admin_treasury');

	$rvalue = $smcFunc['db_query']('', '
		UPDATE {db_prefix}treas_targets 
		SET value={string:valu} 
		WHERE name={string:nam} 
			AND subtype={string:subt}', 
		array(
			'valu' => $val,
			'nam' => $nm,
			'subt' => $sub,
		)
	);
}

function updateConfig($nm, $val)
{
	global $ilog, $smcFunc;
	isAllowedTo('admin_treasury');

	$rvalue = $smcFunc['db_query']('', '
		UPDATE {db_prefix}treas_config 
		SET value={string:valu} 
		WHERE name={string:nam}', 
		array(
			'valu' => $val,
			'nam' => $nm,
		)
	);
}

function configUpdate()
{
	global $ipnppd, $txt;
	isAllowedTo('admin_treasury');
	checkSession();

	echo '<div style="text-align:center;" class="titlebg"><b>', $txt['treas_config_error'], '</b></div>';
	echo '<br /><p style="color:#0000FF;"><b>If you see this screen then an SQL error was encountered</b></br>'
	. 'You should see a message in <span style="color:#FF0000;">RED</span> below indicating what the error is</p><br /><br />';

	$ERR = 1;
	$ilog = '';
	$ilog .= '<br />';
	foreach( $_POST as $option => $value )
	{
		// Look for form variables
		if ( preg_match('/var_/', $option))
		{
			$varnm = preg_replace('/var_/', '', $option);
			// Check for subtype field
			if ( preg_match('/-(.*)/', $varnm, $subtype) ) {
				echo "<br />subtype = $subtype[1] <br />";
				$temp = $varnm;
				$varnm = preg_replace('/-.*/', '', $temp);
				echo "$varnm $subtype[1] => $value<br />";
				$ERR &= UpdateTarget($varnm, $subtype[1], $value);
			} else {
				echo "$varnm  => $value<br />";
				$ERR &= UpdateConfig($varnm, $value);
			}
		}
	}
	$configger = $_POST['configger'];
	// If there were no errors
	if( $ERR == 0 )
		redirectexit($configger);
}

function breakFix($str) {
	$str = preg_replace(array('/\r\n/', '/\n/'), '<br />', $str);
  return $str;
}

function eventsEdit()
{
	global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession();

	$start = (isset($_GET['start']) && intval($_GET['start'])) ? $_GET['start'] : 0;
	$sort_order = isset($_GET['order']) ? $_GET['order'] : '';
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'lastevent';
	if ($_POST['date_start'] == '')
	{
		fatal_error('The Start Date field cannot be blank');
	}
	elseif (strtotime($_POST['date_start']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['title']) == 0)
	{
		fatal_error('The Title field cannot be blank');
	}
	elseif (strlen($_POST['description']) == 0)
	{
		fatal_error('The Description field cannot be blank');
	}
	elseif (!is_numeric($_POST['target']))
	{
		fatal_error('Invalid Target field');
	}
	elseif (!is_numeric($_POST['actual']))
	{
		fatal_error('Invalid Actual field');
	}
	else
	{
		$sTime = strtotime($_POST['date_start']);
		$eTime = empty($_POST['date_end']) ? '0' : strtotime($_POST['date_end']);
		$insert_Recordset = "
			UPDATE {db_prefix}treas_events 
			SET date_start='$sTime', date_end='$eTime', title='" . addslashes($_POST['title']) . "', description='" . addslashes(breakFix($_POST['description'])) . "', target='$_POST[target]', actual='$_POST[actual]'
			WHERE eid='$_POST[eid]'
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $insert_Recordset, array());

		redirectexit('action=admin;area=treasury;sa=configevents;mode=' . $mode . ';start=' . $start . ';order=' . $sort_order);
	}
}

function eventsAdd()
{
	global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession();

	if ($_POST['date_start'] == '')
	{
		fatal_error('The Start Date field cannot be blank');
	}
	elseif (strtotime($_POST['date_start']) == -1)
	{
		fatal_error('Invalid Date format');
	}
	elseif (strlen($_POST['title']) == 0)
	{
		fatal_error('The Title field cannot be blank');
	}
	elseif (strlen($_POST['description']) == 0)
	{
		fatal_error('The Description field cannot be blank');
	}
	elseif (!is_numeric($_POST['target']))
	{
		fatal_error('Invalid Target field');
	}
	else
	{
		$sTime = strtotime($_POST['date_start']);
		$eTime = empty($_POST['date_end']) ? '0' : strtotime($_POST['date_end']);
		$insert_Recordset = "
			INSERT INTO {db_prefix}treas_events 
			(eid, date_start, date_end, title, description, target, actual) 
			VALUES 
			(DEFAULT, '$sTime', '$eTime', '" . addslashes($_POST['title']) . "', '" . addslashes(breakFix($_POST['description'])) . "', '$_POST[target]', '0')";
		$rvalue = $smcFunc['db_query']('', $insert_Recordset, array());

		redirectexit('action=admin;area=treasury;sa=configevents');
	}
}

function eventsDelete()
{
    global $smcFunc, $txt;
	isAllowedTo('admin_treasury');
	checkSession('get');

	$start = (isset($_GET['start']) && intval($_GET['start'])) ? $_GET['start'] : 0;
	$sort_order = isset($_GET['order']) ? $_GET['order'] : '';
	$mode = isset($_GET['mode']) ? $_GET['mode'] : 'lastevent';
	if ( !(is_numeric($_GET['id']) && $_GET['id'] > 0) )
	{
		fatal_error('Invalid record eid specified, operation aborted');
	} else {
		$del_Transaction = "
			DELETE 
			FROM {db_prefix}treas_events 
			WHERE eid='$_GET[id]' 
			LIMIT 1";
		$rvalue = $smcFunc['db_query']('', $del_Transaction, array());
		redirectexit('action=admin;area=treasury;sa=configevents;mode=' . $mode . ';start=' . $start . ';order=' . $sort_order);
	}
}

function transactionLog()
{
	global $smcFunc, $context, $txt, $tr_config;
	global $start, $sort_order, $maxRows, $mode, $totalRows;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'trans_log';

	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	// Transaction log paging
	$maxRows = 5;
	$start = isset($_POST['start']) ? intval($_POST['start']) : (isset($_GET['start']) ? intval($_GET['start']) : 0);
	$sort_order = isset($_POST['order']) ? $_POST['order'] : (isset($_GET['order']) ? $_GET['order'] :'');
	$sort_order = (isset($sort_order) && $sort_order == 'ASC') ? 'ASC' : 'DESC';
	$order_by = "id $sort_order LIMIT $start, ".$maxRows;
	$query_Recordset1 = "
		SELECT * 
		FROM {db_prefix}log_treasurey";
	$query_limit_Recordset1 = $query_Recordset1.' ORDER by '.$order_by;

	$Recordset1 = $smcFunc['db_query']('', $query_limit_Recordset1, array());
	$all_Recordset1 = $smcFunc['db_query']('', $query_Recordset1, array());
	$totalRows = $smcFunc['db_affected_rows']();

	while ($row = $smcFunc['db_fetch_assoc']($Recordset1))
		$context['trans_log'][] = array(
			'id' => $row['id'],
			'log_date' => $row['log_date'],
			'payment_date' => $row['payment_date'],
			'logentry' => $row['logentry'],
		);
	$smcFunc['db_free_result']($Recordset1);

}

function transactionLogdelete() {
    global $smcFunc;

	$start = (isset($_GET['start']) && intval($_GET['start'])) ? $_GET['start'] : 0;
	$sort_order = isset($_GET['order']) ? $_GET['order'] : '';
	if ( !(is_numeric($_GET['id']) && $_GET['id'] > 0))
	{
		fatal_error('Invalid record id specified, operation aborted');
	}
	else
	{
		$del_Transaction = $smcFunc['db_query']('', "
			DELETE FROM {db_prefix}log_treasurey 
			WHERE id={int:lid} 
			LIMIT 1",
			array(
				'lid' => $_GET['id'],
			)
		);
		redirectexit('action=admin;area=treasury;sa=translog;start='.$start.';order='.$sort_order);
	}
}

function ipnRec()
{
	global $smcFunc, $context, $txt, $curdate, $ipn_total, $numrecs;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'ipn_reconcile';

	$query_Recordset1 = "
		SELECT date as recdate 
		FROM {db_prefix}treas_registry 
		WHERE name = 'PayPal IPN' 
		ORDER BY date DESC 
		LIMIT 1 ";
	$Recordset1 = $smcFunc['db_query']('', $query_Recordset1, array());
	list($recdate) = $smcFunc['db_fetch_row']($Recordset1);
	$smcFunc['db_free_result']($Recordset1);

	$query_Recordset2 = "
		SELECT payment_date as curdate 
		FROM {db_prefix}treas_donations 
		WHERE (payment_status = 'Completed' OR payment_status = 'Refunded') 
			AND ( txn_type = 'send_money' OR txn_type = 'web_accept' ) 
		ORDER BY payment_date DESC 
		LIMIT 1 ";
	$Recordset2 = $smcFunc['db_query']('', $query_Recordset2, array());
	list($curdate) = $smcFunc['db_fetch_row']($Recordset2);
	$smcFunc['db_free_result']($Recordset2);

    $query_Recordset3 = "
		SELECT SUM( settle_amount ) AS ipn_total, COUNT( * ) AS numrecs 
    	FROM {db_prefix}treas_donations 
    	WHERE ( payment_date > '$recdate' AND payment_date <= '$curdate' ) 
			AND (payment_status = 'Completed' OR payment_status = 'Refunded') 
			AND ( txn_type = 'send_money' OR txn_type = 'web_accept' ) ";
	$Recordset3 = $smcFunc['db_query']('', $query_Recordset3, array());
	list($ipn_total, $numrecs) = $smcFunc['db_fetch_row']($Recordset3);
	$smcFunc['db_free_result']($Recordset3);
}

function ipnrecUpdate()
{
	global $smcFunc, $curdate, $ipn_total, $numrecs, $rval;

	$insert_set = "
	INSERT INTO {db_prefix}treas_registry 
	(id, date, num, name, descr, amount) 
	VALUES 
	(DEFAULT, '$curdate', '$numrecs', 'PayPal IPN', 'Auto-Reconcile', '$ipn_total') ";
	$rval = $smcFunc['db_query']('', $insert_set, array());
	return $rval;
}

function readMe()
{
	global $context, $txt;
	isAllowedTo('admin_treasury');
	$context['sub_template'] = 'read_me';
}

function paypalUpdate ($insertSQL)
{
	global $smcFunc, $ResultSQL;
	$ResultSQL = $smcFunc['db_query']('', $insertSQL, array());
	return $ResultSQL;
}

function donorGroup ($user_id, $custom, $payment_date, $option_seleczion1)
{
	global $smcFunc;
	$cfgset = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_config',
		array(
		)
	);
	$tr_config = array();
	while ( $cfgset && $row = $smcFunc['db_fetch_assoc']($cfgset)) {
		$tr_config[$row['name']] = $row['value'];
	}
	$smcFunc['db_free_result']($cfgset);

	$group_end = !$tr_config['duration'] ? 1 : (($tr_config['duration'] <3) ? $tr_config['duration']*3  :  12);
	$group_end = $group_end * 30.5 * 24 * 3600 + $payment_date;
	$group_id = $tr_config['group_id'];

	// Check the group exists.
	$isgroup = $smcFunc['db_query']('', '
		SELECT id_group 
		FROM {db_prefix}membergroups
		WHERE id_group = {int:group_id}
		LIMIT 1',
		array(
			'group_id' => $group_id,
		)
	);
	// Check the user is not in a group.
	$requesta = $smcFunc['db_query']('', '
		SELECT additional_groups
		FROM {db_prefix}members
		WHERE FIND_IN_SET({int:donor_group}, additional_groups)
			AND id_member = {int:user_id}
		LIMIT 1',
		array(
			'donor_group' => $group_id,
			'user_id' => $user_id,
		)
	);
	$ingroupa = $smcFunc['db_num_rows']($requesta);

	$grouplog = '';
	// Do we want anonymous users added to groups?
	if ($tr_config['group_anonymous'] == 0  && $option_seleczion1 == 'No') {
		$grouplog .= $custom.' not added to Donor Group - anonymous disallowed.';
	}
	elseif (empty($isgroup)) {
		$grouplog .= 'Donor group does not exist.<br />';
	}
	elseif ($ingroupa) {
		$grouplog .= $custom . ' is already a Donor group member';
		// If using duration limited
		if ($tr_config['group_duration']) {
			$request2 = $smcFunc['db_query']('', '
				SELECT id 
				FROM {db_prefix}treas_subscribers 
				WHERE user_id = {int:userid} 
					AND group_id = {int:groupid} 
				LIMIT 1',
				array(
					'groupid' => $group_id,
					'userid' => $user_id,
				)
			);
			if ($smcFunc['db_num_rows']($request2) == 1) {
				$request3 = $smcFunc['db_query']('', '
					UPDATE {db_prefix}treas_subscribers 
					SET group_end = {int:groupend} 
					WHERE user_id = {int:userid} 
						AND group_id = {int:groupid} 
					LIMIT 1',
					array(
						'groupend' => $group_end,
						'groupid' => $group_id,
						'userid' => $user_id,
					)
				);
				$grouplog .= ' - Group membership extended to ' . timeformat($group_end, treasdate());
			} else {
				$request3 = $smcFunc['db_query']('', '
					INSERT INTO {db_prefix}treas_subscribers 
					(id, user_id, group_id, group_end) 
					VALUES
					(DEFAULT, {int:userid}, {int:groupid}, {int:groupend})', 
					array(
						'groupid' => $group_id,
						'groupend' => $group_end,
						'userid' => $user_id,
					)
				);
				$grouplog .= ' - added to Group until ' . timeformat($group_end, treasdate());
			}
		} else {
			$grouplog .= ' - Group membership unlimited.';
		}
	}
	else {
		$request2 = $smcFunc['db_query']('', '
			SELECT additional_groups
			FROM {db_prefix}members
			WHERE id_member = {int:user_id}
			LIMIT 1',
			array(
				'user_id' => $user_id,
			)
		);
		list($additional_groups) = $smcFunc['db_fetch_row']($request2);
		$add_group = empty($additional_groups) ? $group_id : $additional_groups . ',' . $group_id;
		$smcFunc['db_free_result']($request2);

	# Add the additional group
		$request3 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}members 
			SET additional_groups = {string:add_group} 
			WHERE id_member = {int:user_id}', 
			array(
				'add_group' => $add_group,
				'user_id' => $user_id,
			)
		);
		$grouplog .= $custom.' added to Donor additional group';

		if ($tr_config['group_duration']) {
			// Add to subscribers list
			$request4 = $smcFunc['db_query']('', '
				INSERT INTO {db_prefix}treas_subscribers 
				(id, user_id, group_id, group_end) 
				VALUES 
				(DEFAULT, {int:userid}, {int:groupid}, {int:groupend})', 
				array(
					'groupid' => $group_id,
					'groupend' => $group_end,
					'userid' => $user_id,
				)
			);
			$grouplog .= ' - added to Donor group until ' . timeformat($group_end, treasdate());
		} else {
			$grouplog .= ' - Group membership unlimited.';
		}
	}

	//Now we set a new checkpoint in Settings
	$request5 = $smcFunc['db_query']('', '
		SELECT group_end 
		FROM {db_prefix}treas_subscribers 
		ORDER BY group_end ASC 
		LIMIT 1
	', array());
	if ($smcFunc['db_num_rows']($request5) == 1 && $tr_config['group_duration']) {
		list($group_end) = $smcFunc['db_fetch_row']($request5);
		$smcFunc['db_free_result']($request5);
		$request6 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}settings
			SET value = {int:groupend} 
			WHERE variable = {string:groupcheck}',
			array(
				'groupend' => $group_end,
				'groupcheck' => 'treasury_groupcheck',
			)
		);
		$grouplog .= '<br />Group duration check updated.';
	} else {
		$request6 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}settings
			SET value = 0 
			WHERE variable = {string:groupcheck}',
			array(
				'groupcheck' => 'treasury_groupcheck',
			)
		);
		$grouplog .= '<br />Group duration check now empty.';
	}

	return $grouplog;
}

function treasuryHelp() {
	global $txt, $helptxt, $context, $scripturl;
	isAllowedTo('admin_treasury');
	loadLanguage('TreasuryHelp');

	// hmmm, don't want to affect core, so borrow SMF code - thank you :)
	$context['page_title'] = 'Treasury ' . $txt['help'];
	// Don't show any template layers, just the popup sub template.
	$context['template_layers'] = array();
	$context['sub_template'] = 'treasuryhelp';

	// What help string should be used?
	if (isset($helptxt[$_GET['help']]))
		$context['help_text'] = &$helptxt[$_GET['help']];
	elseif (isset($txt[$_GET['help']]))
		$context['help_text'] = &$txt[$_GET['help']];
	else
		$context['help_text'] = $_GET['help'];

	// Does this text contain a link that we should fill in?
	if (preg_match('~%([0-9]+\$)?s\?~', $context['help_text'], $match))
	{
		$context['help_text'] = sprintf($context['help_text'], $scripturl, $context['session_id']);
	}
}

function treasuryPages($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = TRUE)
{
	global $scripturl, $txt;

	$total_pages = ceil($num_items/$per_page);
	if ($total_pages == 1) { return ''; }
	$on_page = floor($start_item / $per_page) + 1;
	$page_string = '';
	if ( $total_pages > 10 ) {
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
		for($i = 1; $i < $init_page_max + 1; $i++) {
			$page_string .= ( $i == $on_page ) ? '<b>'.$i.'</b>' : '<a href="'.$scripturl . ($base_url.';start='.( ( $i - 1 ) * $per_page ) ).'">'.$i.'</a>';
			if ( $i <  $init_page_max ) { $page_string .= ', '; }
		}
		if ( $total_pages > 3 ) {
			if ( $on_page > 1  && $on_page < $total_pages ) {
				$page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';
				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;
				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++) {
					$page_string .= ($i == $on_page) ? '<b>'.$i.'</b>' : '<a href="'.$scripturl . ($base_url.';start='.( ( $i - 1 ) * $per_page ) ).'">'.$i.'</a>';
					if ( $i <  $init_page_max + 1 ) { $page_string .= ', '; }
				}
				$page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
			} else {
				$page_string .= ' ... ';
			}
			for($i = $total_pages - 2; $i < $total_pages + 1; $i++) {
				$page_string .= ( $i == $on_page ) ? '<b>'.$i.'</b>'  : '<a href="'.$scripturl . ($base_url.';start='.( ( $i - 1 ) * $per_page ) ).'">'.$i.'</a>';
				if( $i <  $total_pages ) { $page_string .= ", "; }
			}
		}
	} else {
		for($i = 1; $i < $total_pages + 1; $i++) {
			$page_string .= ( $i == $on_page ) ? '<b>'.$i.'</b>' : '<a href="'.$scripturl . ($base_url.';start='.( ( $i - 1 ) * $per_page ) ).'">'.$i.'</a>';
			if ( $i <  $total_pages ) { $page_string .= ', '; }
		}
	}
	if ( $add_prevnext_text ) {
		if ( $on_page > 1 ) {
			$page_string = ' <a href="'.$scripturl . ($base_url.';start='.( ( $on_page - 2 ) * $per_page ) ).'">' . $txt['treas_previous'] . '</a>&nbsp;&nbsp;'.$page_string;
		}
		if ( $on_page < $total_pages ) {
			$page_string .= '&nbsp;&nbsp;<a href="'.$scripturl . ($base_url.';start='.( $on_page * $per_page ) ).'">' . $txt['treas_next'] . '</a>';
		}
	}
	$page_string = $txt['treas_goto_page'] . ': '.$page_string;
	return $page_string;
}
?>