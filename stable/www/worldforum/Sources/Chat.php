<?php
if (!defined('SMF'))
die('Hacking attempt...');

function Chat() {
	global $context, $settings, $user_info, $txt, $modSettings, $smcFunc;
	$context['page_title'] = $modSettings['irc_page_title'];
      $context['linktree'][] = array(
         'url' => $scripturl . '?action=chat',
         'name' => $modSettings['irc_page_title'],
      );
	if (empty($context['smileys']))
	{
		$context['smileys'] = array(
			'postform' => array(),
			'popup' => array(),
		);
		if (empty($modSettings['smiley_enable']) && $user_info['smiley_set'] != 'none')
			$context['smileys']['postform'][] = array(
				'smileys' => array(
					array('code' => ':)', 'filename' => 'smiley.gif', 'description' => $txt['icon_smiley']),
					array('code' => ';)', 'filename' => 'wink.gif', 'description' => $txt['icon_wink']),
					array('code' => ':D', 'filename' => 'cheesy.gif', 'description' => $txt['icon_cheesy']),
					array('code' => ';D', 'filename' => 'grin.gif', 'description' => $txt['icon_grin']),
					array('code' => '>:(', 'filename' => 'angry.gif', 'description' => $txt['icon_angry']),
					array('code' => ':(', 'filename' => 'sad.gif', 'description' => $txt['icon_sad']),
					array('code' => ':o', 'filename' => 'shocked.gif', 'description' => $txt['icon_shocked']),
					array('code' => '8)', 'filename' => 'cool.gif', 'description' => $txt['icon_cool']),
					array('code' => '???', 'filename' => 'huh.gif', 'description' => $txt['icon_huh']),
					array('code' => '::)', 'filename' => 'rolleyes.gif', 'description' => $txt['icon_rolleyes']),
					array('code' => ':P', 'filename' => 'tongue.gif', 'description' => $txt['icon_tongue']),
					array('code' => ':-[', 'filename' => 'embarrassed.gif', 'description' => $txt['icon_embarrassed']),
					array('code' => ':-X', 'filename' => 'lipsrsealed.gif', 'description' => $txt['icon_lips']),
					array('code' => ':-\\', 'filename' => 'undecided.gif', 'description' => $txt['icon_undecided']),
					array('code' => ':-*', 'filename' => 'kiss.gif', 'description' => $txt['icon_kiss']),
					array('code' => ':\'(', 'filename' => 'cry.gif', 'description' => $txt['icon_cry'])
				),
				'last' => true,
			);
		elseif ($user_info['smiley_set'] != 'none')
		{
			if (($temp = cache_get_data('posting_smileys', 480)) == null)
			{
				$request = $smcFunc['db_query']('', '
					SELECT code, filename, description, smiley_row, hidden
					FROM {db_prefix}smileys
					WHERE hidden IN (0, 2)
					ORDER BY smiley_row, smiley_order',
					array(
					)
				);
				while ($row = $smcFunc['db_fetch_assoc']($request))
				{
					$row['filename'] = htmlspecialchars($row['filename']);
					$row['description'] = htmlspecialchars($row['description']);

					$context['smileys'][empty($row['hidden']) ? 'postform' : 'popup'][$row['smiley_row']]['smileys'][] = $row;
				}
				$smcFunc['db_free_result']($request);

				cache_put_data('posting_smileys', $context['smileys'], 480);
			}
			else
				$context['smileys'] = $temp;
		}

		// Clean house... add slashes to the code for javascript.
		foreach (array_keys($context['smileys']) as $location)
		{
			foreach ($context['smileys'][$location] as $j => $row)
			{
				$n = count($context['smileys'][$location][$j]['smileys']);
				for ($i = 0; $i < $n; $i++)
				{
					$context['smileys'][$location][$j]['smileys'][$i]['code'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['code']);
					$context['smileys'][$location][$j]['smileys'][$i]['js_description'] = addslashes($context['smileys'][$location][$j]['smileys'][$i]['description']);
				}

				$context['smileys'][$location][$j]['smileys'][$n - 1]['last'] = true;
			}
			if (!empty($context['smileys'][$location]))
				$context['smileys'][$location][count($context['smileys'][$location]) - 1]['last'] = true;
		}
	}
	loadTemplate('Chat');
}

?>