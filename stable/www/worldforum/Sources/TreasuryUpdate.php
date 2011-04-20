<?php
/*******************************************************************************
* This program is free software; you can redistribute it and/or modify         *
* it under the terms of the GNU General Public License as published by         *
* the Free Software Foundation; either version 2 of the License.               *
* $Source: /0cvs/TreasurySMF/TreasuryUpdate.php,v $                            *
* $Revision: 1.16 $                                                            *
* $Date: 2010/01/25 04:18:19 $                                                 *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz                     *
*******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

function treasuryGroupCheck()
{
	global $db_prefix, $smcFunc;
	$request1 = $smcFunc['db_query']('','
		SELECT user_id, group_id 
		FROM {db_prefix}treas_subscribers
		WHERE group_end <= {int:groupend} 
		',
		array(
			'groupend' => gmmktime(),
		)
	);

	while ($request1 && $row = $smcFunc['db_fetch_row']($request1)) {
		$request2 = $smcFunc['db_query']('',"
			SELECT additional_groups 
			FROM {db_prefix}members
			WHERE id_member = '$row[0]' 
			LIMIT 1
			",
			array()
		);
		list($additional_groups) = $smcFunc['db_fetch_row']($request2);
		$smcFunc['db_free_result']($request2);
		$add_groups = implode(',', array_diff(explode(',', $additional_groups), array($row[1])));
		$result3 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}members 
			SET additional_groups = {string:addgroups} 
			WHERE id_member = {int:mid}
			', 
			array(
				'mid' => $row[0],
				'addgroups' => $add_groups,
			)
		);
		$result4 = $smcFunc['db_query']('', '
			DELETE FROM {db_prefix}treas_subscribers 
			WHERE user_id = {int:uid}
				AND group_id = {int:gid}
			', 
			array(
				'uid' => $row[0],
				'gid' => $row[1],
			)
		);
	}

	//Now we set a new checkpoint in Settings
	$request5 = $smcFunc['db_query']('','
		SELECT user_id, group_id 
		FROM {db_prefix}treas_subscribers
		ORDER BY group_end ASC 
		LIMIT 1
		',
		array()
	);
	if ($smcFunc['db_affected_rows']() == 1) {
		list($group_end) = $smcFunc['db_fetch_row']($request5);
		$result6 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}settings 
			SET value = {int:gend} 
			WHERE variable = {string:gcheck}
			', 
			array(
				'gend' => $group_end,
				'gcheck' => 'treasury_groupcheck',
			)
		);
	} else {
		$result6 = $smcFunc['db_query']('', '
			UPDATE {db_prefix}settings 
			SET value = 0 
			WHERE variable = {string:gcheck}
			', 
			array(
				'gcheck' => 'treasury_groupcheck',
			)
		);
	}
	$smcFunc['db_free_result']($request5);
}