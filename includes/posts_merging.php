<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

$post_need_approval = ((!$auth->acl_get('f_noapprove', $data['forum_id']) && empty($data['force_approved_state'])) || (isset($data['force_approved_state']) && !$data['force_approved_state']));
if (($mode != 'reply' && $mode != 'quote') || $post_need_approval) return;

// Get merging data
$sql_array = array(
	'SELECT'	=> 'f.enable_indexing, f.forum_id, p.bbcode_bitfield, p.bbcode_uid, p.enable_bbcode, 
		p.enable_magic_url, p.enable_smilies, p.poster_id, p.post_attachment, p.poster_browser_id,
		p.post_edit_locked, p.post_id, p.post_subject, p.post_text, p.post_time, t.topic_attachment,
		t.topic_last_post_time',
	'FROM'		=> array(FORUMS_TABLE => 'f', POSTS_TABLE => 'p', TOPICS_TABLE => 't'),
	'WHERE'		=> "p.post_id = t.topic_last_post_id
		AND t.topic_id = $topic_id
		AND p.post_visibility = " . ITEM_APPROVED . "
		AND (f.forum_id = t.forum_id 
			OR f.forum_id = $forum_id)",
);

$sql = $db->sql_build_query('SELECT', $sql_array);

$result = $db->sql_query($sql);
$merge_post_data = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$merge_post_id = $merge_post_data['post_id'];

if (!$merge_post_id)
{
	$user->add_lang('posting');
	trigger_error('NO_POST');
}

// Should we do merging?
$do_merge = ($merge_post_data['poster_id'] == $user->data['user_id']) && !$merge_post_data['post_edit_locked'];
$do_merge = $do_merge && ((int) $config['max_post_chars'] == 0 || (utf8_strlen($merge_post_data['post_text']) + utf8_strlen($addon_for_merge) + 200) <= (int) $config['max_post_chars']);

if ($user->data['is_registered'])
{
	$do_merge = $do_merge && request_var('do_merge', false);
}
else
{
	$do_merge = $do_merge && ($current_time - $merge_post_data['topic_last_post_time']) < intval($config['merge_interval']) * 3600;
	$do_merge = $do_merge && request_var($config['cookie_name'] . '_bid', '', false, true) == $merge_post_data['poster_browser_id'];
}

if (!$do_merge) return;

// Don't let user to violate attachments limit by posts merging
$sql = 'SELECT attach_id, COUNT(*) as num_attach
	FROM ' . ATTACHMENTS_TABLE . "
	WHERE post_msg_id = $merge_post_id
		AND in_message = 0";
$result = $db->sql_query($sql);
$num_old_attach = (int) $db->sql_fetchfield('num_attach');

$num_new_attach = count($data['attachment_data']);
$total_attach_count = $num_old_attach + $num_new_attach;
$merge_post_data['post_attachment'] = ($total_attach_count) ? 1 : 0;
$do_merge = $do_merge && ($total_attach_count <= $config['max_attachments']);

if (!$do_merge) return;

// Do merging
$message_parser = new \parse_message();

$message_parser->message = &$merge_post_data['post_text'];
unset($merge_post_data['post_text']);

// Decode text for update properly
$message_parser->decode_message($merge_post_data['bbcode_uid']);
$merge_post_data['post_text'] = html_entity_decode($message_parser->message, ENT_COMPAT, 'UTF-8');
unset($message_parser);

$username = (!$user->data['is_registered'] && $post_data['username']) ? $post_data['username'] : (($user->data['user_id'] != ANONYMOUS) ? $user->data['username'] : '');

// Handle with inline attachments
if ($num_new_attach)
{
	$merge_post_data['post_text'] = preg_replace_callback('#\[attachment=([0-9]+)\](.*?)\[\/attachment\]#', function($matches) use ($num_new_attach) {
		return '[attachment=' . ($matches[1] + $num_new_attach) . ']' . $matches[2] . '[/attachment]';
	}, $merge_post_data['post_text']);
}

// Make sure the message is safe
$type_cast_helper = new \phpbb\request\type_cast_helper();
$type_cast_helper->recursive_set_var($merge_post_data['post_text'], '', true);

// Merge posts
$subject = $post_data['post_subject'];
$separator = "\n\n[upd=" . $current_time . ']' . $subject . "[/upd]\n";
$merge_post_data['post_text'] = $merge_post_data['post_text'] . $separator . $addon_for_merge;

// Prepare post for submit
$options = '';
generate_text_for_storage($merge_post_data['post_text'], $merge_post_data['bbcode_uid'], $merge_post_data['bbcode_bitfield'], $options, $merge_post_data['enable_bbcode'], $merge_post_data['enable_magic_url'], $merge_post_data['enable_smilies']);

$poster_id = (int) $merge_post_data['poster_id'];

// Prepare post data for update
$sql_data[POSTS_TABLE]['sql'] = array(
	'bbcode_uid'		=> $merge_post_data['bbcode_uid'],
	'bbcode_bitfield'	=> $merge_post_data['bbcode_bitfield'],
	'post_text'			=> $merge_post_data['post_text'],
	'post_checksum'		=> md5($merge_post_data['post_text']),
	'post_merged'		=> $current_time,
	'post_attachment'	=> (!empty($data['attachment_data'])) ? 1 : ($merge_post_data['post_attachment'] ? 1 : 0),
);

$sql_data[TOPICS_TABLE]['sql'] = array(
	'topic_last_post_id'		=> $merge_post_id,
	'topic_last_poster_id'		=> $poster_id,
	'topic_last_poster_name'	=> $username,
	'topic_last_poster_colour'	=> ($user->data['user_id'] != ANONYMOUS) ? $user->data['user_colour'] : '',
	'topic_last_post_subject'	=> utf8_normalize_nfc($merge_post_data['post_subject'] ? $merge_post_data['post_subject'] : $data['topic_title']),
	'topic_last_post_time'		=> $current_time,
	'topic_attachment'			=> (!empty($data['attachment_data']) || (isset($merge_post_data['topic_attachment']) && $merge_post_data['topic_attachment'])) ? 1 : 0,
);

$sql_data[FORUMS_TABLE]['sql'] = array(
	'forum_last_post_id'		=> $merge_post_id,
	'forum_last_post_subject'	=> utf8_normalize_nfc($merge_post_data['post_subject'] ? $merge_post_data['post_subject'] : $data['topic_title']),
	'forum_last_post_time'		=> $current_time,
	'forum_last_poster_id'		=> $poster_id,
	'forum_last_poster_name'	=> $username,
	'forum_last_poster_colour'	=> ($user->data['user_id'] != ANONYMOUS) ? $user->data['user_colour'] : '',
);

// Update post information - submit merged post
$sql = 'UPDATE ' . POSTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data[POSTS_TABLE]['sql']) . " WHERE post_id = $merge_post_id";
$db->sql_query($sql);

$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data[TOPICS_TABLE]['sql']) . " WHERE topic_id = $topic_id";
$db->sql_query($sql);

$sql = 'UPDATE ' . FORUMS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data[FORUMS_TABLE]['sql']) . " WHERE forum_id = $forum_id";
$db->sql_query($sql);

// Submit Attachments
if (!empty($data['attachment_data']))
{
	$space_taken = $files_added = 0;
	$orphan_rows = array();

	foreach ($data['attachment_data'] as $pos => $attach_row)
	{
		$orphan_rows[(int) $attach_row['attach_id']] = array();
	}

	if (count($orphan_rows))
	{
		$sql = 'SELECT attach_id, filesize, physical_filename
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE ' . $db->sql_in_set('attach_id', array_keys($orphan_rows)) . '
				AND is_orphan = 1
				AND poster_id = ' . $user->data['user_id'];
		$result = $db->sql_query($sql);

		$orphan_rows = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$orphan_rows[$row['attach_id']] = $row;
		}
		$db->sql_freeresult($result);
	}

	foreach ($data['attachment_data'] as $pos => $attach_row)
	{
		if ($attach_row['is_orphan'] && !in_array($attach_row['attach_id'], array_keys($orphan_rows)))
		{
			continue;
		}

		if (!$attach_row['is_orphan'])
		{
			// update entry in db if attachment already stored in db and filespace
			$sql = 'UPDATE ' . ATTACHMENTS_TABLE . "
				SET attach_comment = '" . $db->sql_escape($attach_row['attach_comment']) . "'
				WHERE attach_id = " . (int) $attach_row['attach_id'] . '
					AND is_orphan = 0';
			$db->sql_query($sql);
		}
		else
		{
			// insert attachment into db
			if (!@file_exists($phpbb_root_path . $config['upload_path'] . '/' . basename($orphan_rows[$attach_row['attach_id']]['physical_filename'])))
			{
				continue;
			}

			$space_taken += $orphan_rows[$attach_row['attach_id']]['filesize'];
			$files_added++;

			$attach_sql = array(
				'post_msg_id'		=> $merge_post_id,
				'topic_id'			=> $topic_id,
				'is_orphan'			=> 0,
				'poster_id'			=> $poster_id,
				'attach_comment'	=> $attach_row['attach_comment'],
			);

			$sql = 'UPDATE ' . ATTACHMENTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $attach_sql) . '
				WHERE attach_id = ' . $attach_row['attach_id'] . '
					AND is_orphan = 1
					AND poster_id = ' . $user->data['user_id'];
			$db->sql_query($sql);
		}
	}

	if ($space_taken && $files_added)
	{
		set_config('upload_dir_size', $config['upload_dir_size'] + $space_taken, true);
		set_config('num_files', $config['num_files'] + $files_added, true);
	}
}

// Index message contents
if ($merge_post_data['enable_indexing'])
{
	// Select the search method and do some additional checks to ensure it can actually be utilised
	$search_type = $config['search_type'];

	if (!class_exists($search_type))
	{
		trigger_error('NO_SUCH_SEARCH_MODULE');
	}

	$error = false;
	$search = new $search_type($error, $phpbb_root_path, $phpEx, $auth, $config, $db, $user, $phpbb_dispatcher);

	if ($error)
	{
		trigger_error($error);
	}

	$search->index('edit', $merge_post_id, $merge_post_data['post_text'], $subject, $poster_id, $forum_id);
}

// Mark the post and the topic read
markread('post', $forum_id, $topic_id, $current_time);
markread('topic', $forum_id, $topic_id, $current_time);

// Handle read tracking
if ($config['load_db_lastread'] && $user->data['is_registered'])
{
	$sql = 'SELECT mark_time
		FROM ' . FORUMS_TRACK_TABLE . '
		WHERE user_id = ' . (int) $user->data['user_id'] . '
			AND forum_id = ' . $data['forum_id'];
	$result = $db->sql_query($sql);
	$f_mark_time = (int) $db->sql_fetchfield('mark_time');
	$db->sql_freeresult($result);
}
else if ($config['load_anon_lastread'] || $user->data['is_registered'])
{
	$f_mark_time = false;
}

if (($config['load_db_lastread'] && $user->data['is_registered']) || $config['load_anon_lastread'] || $user->data['is_registered'])
{
	// Update forum info
	$sql = 'SELECT forum_last_post_time
		FROM ' . FORUMS_TABLE . '
		WHERE forum_id = ' . $data['forum_id'];
	$result = $db->sql_query($sql);
	$forum_last_post_time = (int) $db->sql_fetchfield('forum_last_post_time');
	$db->sql_freeresult($result);

	update_forum_tracking_info($forum_id, $forum_last_post_time, $f_mark_time, false);
}

// Send Notifications
$notification_data = array_merge($data, array(
	'post_id'		=> (int) $merge_post_data['post_id'],
	'topic_title'		=> (isset($data['topic_title'])) ? $data['topic_title'] : $subject,
	'post_username'		=> $username,
	'poster_id'			=> (int) $data['poster_id'],
	'post_text'			=> $merge_post_data['post_text'],
	'post_time'			=> $merge_post_data['post_time'],
	'post_subject'		=> $subject,
));

$phpbb_notifications = $phpbb_container->get('notification_manager');

$phpbb_notifications->add_notifications(array(
	'notification.type.quote',
	'notification.type.bookmark',
	'notification.type.post',
), $notification_data);

//Generate redirection URL and redirecting
$params = $add_anchor = '';
$params .= '&amp;t=' . $topic_id;
$params .= '&amp;p=' . $merge_post_id;
$add_anchor = '#p' . $merge_post_id;
$url = "{$phpbb_root_path}viewtopic.$phpEx";
$url = append_sid($url, 'f=' . $forum_id . $params) . $add_anchor;

/**
* For compatibility with third-party extensions.
* Modify the data for post submitting
*
* @event rxu.postsmerging.posts_merging_end
* @var	string	mode				Variable containing posting mode value
* @var	string	subject				Variable containing post subject value
* @var	string	username			Variable containing post author name
* @var	int		topic_type			Variable containing topic type value
* @var	array	poll				Array with the poll data for the post
* @var	array	data				Array with the data for the post
* @var	bool	update_message		Flag indicating if the post will be updated
* @var	bool	update_search_index	Flag indicating if the search index will be updated
* @var	string	url			The "Return to topic" URL
* @since 2.0.0
*/
$vars = array(
	'mode',
	'subject',
	'username',
	'topic_type',
	'poll',
	'data',
	'update_message',
	'update_search_index',
	'url',
);
extract($phpbb_dispatcher->trigger_event('rxu.postsmerging.posts_merging_end', compact($vars)));

redirect($url);
