<?php
/*************************************************************************
* Originally NukeTreasury - Financial management for PHP-Nuke            *
* Copyright (c) 2004 by Dave Lawrence AKA Thrash  thrash@fragnastika.com *
*                                                                        *
* This program is free software; you can redistribute it and/or modify   *
* it under the terms of the GNU General Public License as published by   *
* the Free Software Foundation; either version 2 of the License.         *
* $Source: /0cvs/TreasurySMF/ipntreas.php,v $                            *
* $Revision: 1.31 $                                                      *
* $Date: 2010/01/25 04:18:20 $                                           *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz               *
*************************************************************************/
if (!file_exists(dirname(__FILE__) . '/SSI.php'))
	die('Cannot find SSI.php');
require_once(dirname(__FILE__) . '/SSI.php');
require_once($sourcedir . '/Subs-Post.php');
require_once($sourcedir . '/TreasuryAdmin.php');
loadLanguage('Treasury');

global $db_prefix, $smcFunc, $scripturl, $txt, $context, $user_info, $ResultSQL;
//Setup some globals needed in the template
global $first_name, $last_name, $custom, $option_seleczion1, $item_name, $payment_amount, $payment_currency, $total;

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

$ERR = 0;
$log = '';
$loglvl = $tr_config['ipn_dbg_lvl'];
define('_ERR', 1);
define('_INF', 2);

$dbg = (isset($_GET['dbg'])) ? 1 : 0;

if ($dbg)
{
	dprt('Debug mode activated<br />', _INF);
	echo 'SMF2 Treasury mod<br /><br />PayPal Instant Payment Notification script<br /><br />See below for status:<br />';
	echo '----------------------------------------------------------------<br />';
	$receiver_email = $tr_config['receiver_email'];
}

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// Is cURL available?
if ($tr_config['use_curl'] && function_exists('curl_init') && $curly = curl_init('http://www.' . ($tr_config['pp_sandbox'] ? 'sandbox.' : '') .  'paypal.com/cgi-bin/webscr'))
{
	dprt('Opening connection via curl and validating request with PayPal...<br />', _INF);
	curl_setopt($curly, CURLOPT_POST, true);
	curl_setopt($curly, CURLOPT_POSTFIELDSIZE, 0);
	curl_setopt($curly, CURLOPT_POSTFIELDS, $req);
	curl_setopt($curly, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curly, CURLOPT_TIMEOUT, 4);
	curl_setopt($curly, CURLOPT_FAILONERROR, true);
	curl_setopt($curly, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curly, CURLOPT_FRESH_CONNECT, true);
	$ErrorNum = curl_errno($curly);
	$ErrorText = curl_error($curly);
	if ($ErrorNum)
		dprt("Curl error number, $ErrorNum - text, $ErrorText<br />", _INF);
	else
		dprt('OK!<br />', _INF);
	$res = curl_exec($curly);
	curl_close($curly);
}
else
{
	// we're stuck with HTTP to post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

	dprt('Curl isn\'t available.<br />Opening connection via http and validating request with PayPal...<br />', _INF);

	$fp = fsockopen ('www.' . ($tr_config['pp_sandbox'] ? 'sandbox.' : '') . 'paypal.com', 80, $errno, $errstr, 30);

	if (!$fp)
	{
		// HTTP ERROR
		dprt('FAILED to connect to PayPal<br />', _ERR);
		die();
	}

	dprt('OK!<br />', _INF);

	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, 'VERIFIED') == 0)
			break;
	}
	fclose ($fp);
}

if (!$dbg) {
	if (strcmp ($res, 'VERIFIED') == 0)
	{
		// okay, PayPal has told us we have a valid IPN here
		dprt('PayPal Verified<br />', _INF);
		$verified = 1;
	}
	elseif (strcmp ($res, 'INVALID') == 0)
	{
		// log for manual investigation
		dprt('Invalid IPN transaction, this is an abnormal condition<br />', _ERR);
		foreach ($_POST as $key => $val) {
			dprt("$key => $val", $_ERR);
		}
	}
}

// assign posted variables to local variables
$business = addslashes($_POST['business']);
$quantity = intval($_POST['quantity']);
$item_name = addslashes($_POST['item_name']);
$item_number = addslashes($_POST['item_number']);
$payment_date = addslashes($_POST['payment_date']);
$payer_status = addslashes($_POST['payer_status']);
$payment_status = addslashes($_POST['payment_status']);
$payment_amount = addslashes($_POST['mc_gross']);
$payment_fee = addslashes($_POST['mc_fee']);
$payment_currency = addslashes($_POST['mc_currency']);
$txn_id = addslashes($_POST['txn_id']);
$txn_type = addslashes($_POST['txn_type']);
$receiver_email = addslashes($_POST['receiver_email']);
$payer_email = addslashes($_POST['payer_email']);
$first_name = addslashes($_POST['first_name']);
$last_name = addslashes($_POST['last_name']);
$address_street = addslashes($_POST['address_street']);
$address_city = addslashes($_POST['address_city']);
$address_state = addslashes($_POST['address_state']);
$address_zip = intval($_POST['address_zip']);
$address_country = addslashes($_POST['address_country']);
$invoice = addslashes($_POST['invoice']);
$custom = htmlentities($_POST['custom'], ENT_QUOTES);
$option_seleczion2 = htmlentities($_POST['option_selection2'], ENT_QUOTES);
$memo = addslashes($_POST['memo']);
$tax = addslashes($_POST['tax']);
$option_name1 = addslashes($_POST['option_name1']);
$option_seleczion1 = addslashes($_POST['option_selection1']);
$option_name2 = addslashes($_POST['option_name2']);
$address_status = addslashes($_POST['address_status']);
$pending_reason = addslashes($_POST['pending_reason']);
$payment_type = addslashes($_POST['payment_type']);

if ($payment_currency == $tr_config['pp_currency']  && $payment_status != 'Pending')
{
	$settle_amount = $payment_amount - $payment_fee;
	$exchange_rate = '1.00';
}
else
{
	$settle_amount = addslashes($_POST['settle_amount']);
	$exchange_rate = addslashes($_POST['exchange_rate']);
}

// Perform PayPal email account verification
if ( !$dbg && strcasecmp( $_POST['business'], $tr_config['receiver_email']) != 0)
{
	dprt("Incorrect receiver email: $_POST[business] vs $tr_config[receiver_email], aborting<br />", _ERR) ;
	$ERR = 1;
}

$insertSQL = '';
// Look for duplicate txn_id's
if ( $txn_id )
{
	$Recordset1 = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_donations 
			WHERE txn_id = {string:tax_id}',
		array(
			'tax_id' => $txn_id,
		)
	);
	$row_Recordset1 = $smcFunc['db_fetch_assoc']($Recordset1);
	$NumDups = $smcFunc['db_affected_rows']();
	$smcFunc['db_free_result']($Recordset1);
}

if (!$dbg && !$ERR && $verified == 1) {

	$request = $smcFunc['db_query']('', '
		SELECT id_member 
		FROM {db_prefix}members 
			WHERE member_name = {string:custom} OR real_name = {string:custom} 
			LIMIT 1',
		array(
			'custom' => $custom,
		)
	);
	if ($smcFunc['db_affected_rows']() == 0)
		dprt("No Member ID - $id_member<br />", _INF);
	list ($id_member) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	// Check for a reversal or a refund
	if ( $payment_status == 'Refunded' || $payment_status == 'Reversed' )
	{
		// Verify the reversal
		dprt('Transaction is a Refund<br />', _INF);
		if ( $_POST['parent_txn_id'] )
		{
			$Recordset1 = $smcFunc['db_query']('', '
				SELECT * 
				FROM {db_prefix}treas_donations 
					WHERE txn_id = {string:parent_tax_id}',
				array(
					'parent_tax_id' => $_POST['parent_txn_id'],
				)
			);
			$row_Recordset1 = $smcFunc['db_fetch_assoc']($Recordset1);
			$NumTrans = $smcFunc['db_affected_rows']();
			$smcFunc['db_free_result']($Recordset1);
		}

		if ( $NumTrans == 0 )
		{
			// This is an error.  A reversal implies a pre-existing completed transaction
			dprt('IPN Error: Received refund but missing prior completed transaction<br />', _ERR);
			foreach( $_POST as $key => $val ) {
				dprt("$key => $val", $_ERR);
			}
		}
		elseif ( $NumTrans != 1 )
		{
			dprt('IPN Error: Received refund but multiple prior txn_id\'s encountered, aborting<br />', _ERR);
			foreach( $_POST as $key => $val ) {
				dprt("$key => $val", $_ERR);
			}
		}
		else
		{
			$payment_date = strtotime($payment_date);
			$insertSQL = "
				INSERT INTO {db_prefix}treas_donations 
				(id, txn_id, business, item_name, item_number, quantity, invoice, custom, memo, tax, option_name1, option_seleczion1, option_name2, option_seleczion2, payment_status, payment_date, txn_type, mc_gross, mc_fee, mc_currency, settle_amount, exchange_rate, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, address_status, payer_email, payer_status, user_id, currency_symbol, group_id) 
				VALUES 
				(DEFAULT, '$_POST[parent_txn_id]', '$business', '$row_Recordset1[item_name]', '$item_number', '$row_Recordset1[quantity]', '$invoice', '$row_Recordset1[custom]', '$memo', '$row_Recordset1[tax]', '$row_Recordset1[option_name1]', '$row_Recordset1[option_seleczion1]', '$row_Recordset1[option_name2]', '$row_Recordset1[option_seleczion2]', '$payment_status', '$payment_date', '$row_Recordset1[txn_type]', '$payment_amount', '$payment_fee', '$payment_currency', '$settle_amount', '$row_Recordset1[exchange_rate]', '$first_name', '$last_name', '$address_street', '$address_city', '$address_state', '$address_zip', '$address_country', '$address_status', '$payer_email', '$row_Recordset1[payer_status]', '$row_Recordset1[user_id]', '$row_Recordset1[currency_symbol]', '$row_Recordset1[group_id]')";
			// We're cleared to add this record
			dprt($insertSQL.'<br />', _INF);
			paypalUpdate($insertSQL);
			dprt('SQL result = ' . $ResultSQL . '<br />', _INF);
			$body = $row_Recordset1['currency_symbol'].(-$payment_amount) . " refunded to " . $row_Recordset1['custom'] . "\n\n";
			$body .= "Member Link:\n" . $scripturl . '?action=profile;u=' . $id_member . ';sa=showDonations';
			$body2 = 'Thank you '. $row_Recordset1['custom'] . "\n\n";
			$body2 .= 'We confirm refunding your donation of ' . $row_Recordset1['currency_symbol'].(-$payment_amount) . "\n";
			$body2 .= "Details Here:\n" . $scripturl . '?action=profile;u=' . $id_member . ';sa=showDonations';
			emailadmin('Treasury Refund', $body);
			emailuser('Treasury Refund', $body2, $payer_email);
		}
	}
	// Look for abnormal payment
	elseif ( $payment_status == 'Completed' || $payment_status == 'Pending' && $txn_type == 'web_accept' || $txn_type == 'send_money' )
	{
		dprt('Normal transaction<br />', _INF);
		if ($pending_reason)
			dprt('Pending '.$pending_reason.'<br />', _INF);

		if ( $lp )
			fputs($lp, $payer_email . ' ' . $payment_status . ' ' . $_POST['payment_date'] . "\n");

		// Check for a duplicate txn_id - if echeck, then we have a confirmed payment
		if ($NumDups != 0  && $payment_type == 'echeck')
		{
			$updateSQL = $smcFunc['db_query']('', '
				UPDATE {db_prefix}treas_donations 
				SET payment_status = {string:paystat}, mc_fee = {string:payfee}, settle_amount = {string:settle}, exchange_rate = {string:exchange} 
				WHERE txn_id = {string:tax_id}',
				array(
					'paystat' => $payment_status,
					'payfee' => $payment_fee,
					'settle' => $settle_amount,
					'exchange' => $exchange_rate,
					'tax_id' => $txn_id,
				)
			);
			dprt($updateSQL.'<br />', _INF);
			paypalUpdate($updateSQL);
			dprt('SQL echeck cleared = ' . $ResultSQL . '<br />', _INF);
			if ($tr_config['group_use'] == 1 && $tr_config['group_id'] > 1) {
				dprt(donorGroup($id_member, $custom, $payment_date, $option_seleczion1) . '<br />', _INF);
			} else {
				dprt('Donor groups not activated.<br />', _INF);
			}
		}
		elseif ($NumDups != 0)
		// Oh well, no echeck, let's get out of here - suspicious.
		{
			dprt('Valid IPN, but DUPLICATE txn_id! aborting<br />', _ERR);
			foreach( $_POST as $key => $val )	{
				dprt("$key => $val", $_ERR);
			}
		}
		else
		{
			// Money, money, money - let's record it and say thanks
			$currency_symbol = $tr_targets['currency'][$payment_currency];
			$payment_date = strtotime($payment_date);
			if ($tr_config['event_active'])
			{
				$event_id = $tr_config['event_active'];
				$update_Actual = $smcFunc['db_query']('', '
					UPDATE {db_prefix}treas_events 
					SET actual = actual + {string:settle}
					WHERE eid = {int:eventid}
					LIMIT 1',
					array(
						'eventid' => $event_id,
						'settle' => $settle_amount,
					)
				);
			}
			else
			{
				$event_id = 0;
			}
			$insertSQL = "
				INSERT INTO {db_prefix}treas_donations 
				(id, txn_id, business, item_name, item_number, quantity, invoice, custom, memo, tax, option_name1, option_seleczion1, option_name2, option_seleczion2, payment_status, payment_date, txn_type, mc_gross, mc_fee, mc_currency, settle_amount, exchange_rate, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, address_status, payer_email, payer_status, user_id, currency_symbol) 
				VALUES 
				(DEFAULT, '$txn_id', '$business', '$item_name', '$item_number', '$quantity', '$invoice', '$custom', '$memo', '$tax', '$option_name1', '$option_seleczion1', '$option_name2', '$option_seleczion2', '$payment_status', '$payment_date', '$txn_type', '$payment_amount', '$payment_fee', '$payment_currency', '$settle_amount', '$exchange_rate', '$first_name', '$last_name', '$address_street', '$address_city', '$address_state', '$address_zip', '$address_country', '$address_status', '$payer_email', '$payer_status', '$id_member', '$currency_symbol')";
			// We're cleared to add this record
			dprt($insertSQL.'<br />', _INF);
			paypalUpdate($insertSQL);
			dprt('SQL result = ' . $ResultSQL . '<br />', _INF);
			$body = $currency_symbol.$payment_amount . ' received from ' . $custom . "\n\n";
			$body .= "Member Link:\n" . $scripturl . '?action=profile;u=' . $id_member . ';sa=showDonations';
			$body2 = 'Thank you '. $custom . "\n\n";
			$body2 .= 'We are pleased to confirm receipt of your generous donation of ' . $currency_symbol.$payment_amount . "\n";
			$body2 .= "Details Here:\n" . $scripturl . '?action=profile;u=' . $id_member . ';sa=showDonations';
			emailadmin('Treasury Donation', $body);
			emailuser('Treasury Donation', $body2, $payer_email);

			if ($payment_status == 'Pending') {
				dprt('Payment pending - no actions on Donor group.<br />', _INF);
			} elseif ($tr_config['group_use'] == 1 && $tr_config['group_id'] > 1) {
				dprt(donorGroup($id_member, $custom, $payment_date, $option_seleczion1) . '<br />', _INF);
			} else {
				dprt('Donor groups not activated or group not selected.<br />', _INF);
			}
		}
	}
		else
	{ // We're not interested in this transaction, so we're done
		dprt('Valid IPN, but not interested in this transaction<br />', _ERR);
		foreach( $_POST as $key => $val ) {
			dprt("$key => $val", $_ERR);
		}
	}
}

if ($dbg)
{
	dprt('Selecting database......<br />', _INF);
	dprt('Executing test query....<br />',_INF);
	$Result1 = $smcFunc['db_query']('', '
		SELECT * 
		FROM {db_prefix}treas_donations 
		LIMIT 10',
		array(
		)
	);
	if ($Result1) {
		dprt('PASSED!<br />', _INF);
	} else {
		dprt('FAILED!<br />', _INF);
	}

	dprt('PayPal Receiver Email: <b>' . $tr_config['receiver_email'] . '</b><br />', _INF);
	echo '<span style="color:red;"><b>Is this really your PayPal Email address?</b></span><br />';
}

if ($log)
{
	dprt('Logging events....<br />', _INF);
	// Insert the log entry
	$pay_date = isset($_POST['payment_date']) ? strtotime($_POST['payment_date']) : 0;
	$Result1 = $smcFunc['db_query']('', '
		INSERT INTO {db_prefix}log_treasurey 
		(id, log_date, payment_date, logentry) 
		VALUES 
		(DEFAULT, {int:gm_time}, {int:pay_date}, {string:log_me})', 
		array(
			'gm_time' => gmmktime(),
			'pay_date' => $pay_date,
			'log_me' => $log,
		)
	);
	dprt($Result1.' event inserted into the log.<br />', _INF);
	// Clear out old log entries
	$Result1 = $smcFunc['db_query']('', '
		SELECT id AS lowid 
		FROM {db_prefix}log_treasurey 
			ORDER BY id DESC 
			LIMIT {int:ipn_entries}',
		array(
			'ipn_entries' => $tr_config['ipn_log_entries'],
		)
	);
	while ($recordSet = $smcFunc['db_fetch_assoc']($Result1)) {
		$lowid = $recordSet['lowid'];
	}
	$Result1 = $smcFunc['db_query']('', '
		DELETE 
		FROM {db_prefix}log_treasurey 
			WHERE id < {int:low_id}',
		array(
			'low_id' => $lowid,
		)
	);
}

if ($lp) fputs($lp,"Exiting\n");
if ($lp) fclose ($lp);

if ($dbg)
{
	echo '----------------------------------------------------------------<br />';
	echo 'If you don\'t see any error messages, you should be good to go!<br />';
}

function dprt($str, $clvl)
{
	global $dbg, $ipnppd, $lp, $log, $loglvl;
	if( $lp ) fputs($lp, $str . "\n");
	if( $dbg ) echo $str . '<br />';
	if( $clvl <= $loglvl )
		$log .= $str . "\n";
}

function emailadmin($subject, $body)
{
	global $mbname, $webmaster_email;
	sendmail($webmaster_email, $mbname . ' ' . $subject, $body);
}

function emailuser($subject, $body2, $payer_email)
{
	global $mbname;
	sendmail($payer_email, $mbname . ' ' . $subject, $body2);
}
?>