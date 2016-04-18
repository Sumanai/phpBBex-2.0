<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb;

class display_topics
{
	/**
	* Auth object
	*
	* @var \phpbb\auth\auth
	*/
	protected $auth;

	/**
	* The config
	*
	* @var \phpbb\config\config
	*/
	protected $config;

	/**
	* Current board configuration
	*
	* @var \phpbb\cache\service
	*/
	protected $cache;

	/**
	* Get visibility function
	*
	* @var \phpbb\content_visibility
	*/
	protected $content_visibility;

	/**
	* Database connection
	*
	* @var \phpbb\db\driver\driver_interface
	*/
	protected $db;

	/**
	* Event Dispatcher object
	*
	* @var \phpbb\event\dispatcher_interface
	*/
	protected $dispatcher;

	/**
	* Pagination
	*
	* @var \phpbb\pagination
	*/
	protected $pagination;

	/**
	* DI container
	*
	* @var \Symfony\Component\DependencyInjection\ContainerInterface
	*/
	protected $phpbb_container;

	/**
	* Template object
	*
	* @var \phpbb\template\template
	*/
	protected $template;

	/**
	* User object
	*
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* phpBB root path
	*
	* @var string
	*/
	protected $root_path;

	/**
	* PHP file extension
	*
	* @var string
	*/
	protected $phpEx;

	/**
	* Constructor
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\pagination $pagination,
		\Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$root_path,
		$phpEx
	) {
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->pagination = $pagination;
		$this->phpbb_container = $phpbb_container;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->phpEx = $phpEx;
	}

	/**
	* Display announcements
	*/
	public function announcements($tpl_loopname = 'announcetopic')
	{
		$forum_ary = $this->auth->acl_getf('f_read', true);
		$forum_ary = array_unique(array_keys($forum_ary));

		if (!sizeof($forum_ary))
		{
			return;
		}

		$sql_where = $this->db->sql_in_set('t.forum_id', $forum_ary) . ' AND (';

		if ($this->config['global_announce_on_index'])
		{
			$sql_where .= 't.topic_type = ' . POST_GLOBAL;
		}

		if ($this->config['global_announce_on_index'] && $this->config['simple_announce_on_index'])
		{
			$sql_where .= ' OR ';
		}

		if ($this->config['simple_announce_on_index'])
		{
			$sql_where .= 't.topic_type = ' . POST_ANNOUNCE;
		}

		$sql_where .= ')';

		$sql_order = 't.topic_priority DESC, t.topic_time DESC';

		$this->display_topic_rows($tpl_loopname, $sql_where, $sql_order);
	}

	public function active($tpl_loopname = 'activetopic')
	{
		// Get the allowed forums
		$forum_ary = array();
		$forum_read_ary = $this->auth->acl_getf('f_read');
		foreach ($forum_read_ary as $forum_id => $allowed)
		{
			if ($allowed['f_read'])
			{
				$forum_ary[] = (int) $forum_id;
			}
		}
		$forum_ids = array_unique($forum_ary);

		if (!sizeof($forum_ids))
		{
			// No forums with f_read
			return;
		}

		$sql = 'SELECT forum_id
			FROM ' . FORUMS_TABLE . '
			WHERE ' . $this->db->sql_in_set('forum_id', $forum_ids) . '
				AND forum_flags & ' . FORUM_FLAG_ACTIVE_TOPICS;
		$result = $this->db->sql_query($sql);

		$forum_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$forum_ids[] = $row['forum_id'];
		}
		$this->db->sql_freeresult($result);

		// No forums with recent topics enabled
		if (!sizeof($forum_ids))
		{
			return;
		}

		$sql_where = 't.topic_status <> ' . ITEM_MOVED . '
			AND ' . $this->content_visibility->get_forums_visibility_sql('topic', $forum_ids, 't.');

		if ($this->config['global_announce_on_index'])
		{
			$sql_where .= ' AND t.topic_type <> ' . POST_GLOBAL;
		}

		if ($this->config['simple_announce_on_index'])
		{
			$sql_where .= ' AND t.topic_type <> ' . POST_ANNOUNCE;
		}

		if ($this->config['active_topics_on_index_exclude'])
		{
			$excluded_topics = explode(',', str_replace(' ', '', $this->config['active_topics_on_index_exclude']));
			$sql_where .= ' AND ' . $this->db->sql_in_set('t.topic_id', $excluded_topics, true);
		}

		$sql_order = 't.topic_last_post_time DESC';

		$this->display_topic_rows($tpl_loopname, $sql_where, $sql_order, $this->config['active_topics_on_index']);
	}

	/**
	* Display topic rows
	*/
	private function display_topic_rows($tpl_loopname, $sql_where, $sql_order, $total_limit = 0)
	{
		$sql_array = array(
			'SELECT'    => 't.forum_id, t.topic_id, t.topic_type, t.icon_id, tt.mark_time, ft.mark_time as f_mark_time',
			'FROM'      => array(TOPICS_TABLE => 't'),
			'LEFT_JOIN' => array(
				array(
					'FROM'  => array(TOPICS_TRACK_TABLE => 'tt'),
					'ON'    => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $this->user->data['user_id'],
				),
				array(
					'FROM'  => array(FORUMS_TRACK_TABLE => 'ft'),
					'ON'    => 'ft.forum_id = t.forum_id AND ft.user_id = ' . $this->user->data['user_id'],
				),
			),
			'WHERE'     => $sql_where,
			'ORDER_BY'  => $sql_order,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		if ($total_limit)
		{
			$result = $this->db->sql_query_limit($sql, $total_limit);
		}
		else
		{
			$result = $this->db->sql_query($sql);
		}

		$forums = $topic_list = array();
		$obtain_icons = false;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$topic_list[] = $row['topic_id'];

			$rowset[$row['topic_id']] = $row;
			if (!isset($forums[$row['forum_id']]) && $this->user->data['is_registered'] && $this->config['load_db_lastread'])
			{
				$forums[$row['forum_id']]['mark_time'] = $row['f_mark_time'];
			}
			$forums[$row['forum_id']]['topic_list'][] = $row['topic_id'];
			$forums[$row['forum_id']]['rowset'][$row['topic_id']] = & $rowset[$row['topic_id']];

			if ($row['icon_id'] && $this->auth->acl_get('f_icons', $row['forum_id']))
			{
				$obtain_icons = true;
			}
		}

		// No topics to display
		if (empty($topic_list))
		{
			return;
		}

		// Grab icons
		if ($obtain_icons)
		{
			$icons = $this->cache->obtain_icons();
		}
		else
		{
			$icons = array();
		}

		// Borrowed from search.php
		foreach ($forums as $forum_id => $forum)
		{
			if ($this->user->data['is_registered'] && $this->config['load_db_lastread'])
			{
				$topic_tracking_info[$forum_id] = get_topic_tracking($forum_id, $forum['topic_list'], $forum['rowset'], array($forum_id => $forum['mark_time']), ($forum_id) ? false : $forum['topic_list']);
			}
			else if ($this->config['load_anon_lastread'] || $this->user->data['is_registered'])
			{
				$this->request = $this->phpbb_container->get('request');
				$tracking_topics = $this->request->variable($this->config['cookie_name'] . '_track', '', true, \phpbb\request\request_interface::COOKIE);
				$tracking_topics = ($tracking_topics) ? tracking_unserialize($tracking_topics) : array();

				$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $forum['topic_list'], ($forum_id) ? false : $forum['topic_list']);

				if (!$this->user->data['is_registered'])
				{
					$this->user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int)(base_convert($tracking_topics['l'], 36, 10) + $this->config['board_startdate']) : 0;
				}
			}
		}

		// Now only pull the data of the requested topics
		$sql_array = array(
			'SELECT'    => 't.*, tp.topic_posted, f.forum_name, f.enable_icons',
			'FROM'      => array(TOPICS_TABLE => 't'),
			'LEFT_JOIN' => array(
				array(
					'FROM'  => array(TOPICS_POSTED_TABLE => 'tp'),
					'ON'    => 't.topic_id = tp.topic_id AND tp.user_id = ' . $this->user->data['user_id'],
				),
				array(
					'FROM'  => array(FORUMS_TABLE => 'f'),
					'ON'    => 'f.forum_id = t.forum_id',
				),
			),
			'WHERE'     => $this->db->sql_in_set('t.topic_id', $topic_list),
			'ORDER_BY'  => $sql_order,
		);

		/**
		* For compatibility with third-party extensions
		* Event to modify the SQL query before the topics data is retrieved
		*
		* @event paybas.recenttopics.sql_pull_topics_data
		* @var    array    sql_array        The SQL array
		* @since 2.0.0
		*/
		$vars = array('sql_array');
		extract($this->dispatcher->trigger_event('paybas.recenttopics.sql_pull_topics_data', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		$rowset = $topic_icons = array();

		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[] = $row;
		}
		$this->db->sql_freeresult($result);

		// No topics returned by the DB
		if (!sizeof($rowset))
		{
			return;
		}

		/**
		* For compatibility with third-party extensions
		* Event to modify the topics list data before we start the display loop
		*
		* @event paybas.recenttopics.modify_topics_list
		* @var    array    topic_list        Array of all the topic IDs
		* @var    array    rowset            The full topics list array
		* @since 2.0.1
		*/
		$vars = array('topic_list', 'rowset');
		extract($this->dispatcher->trigger_event('paybas.recenttopics.modify_topics_list', compact($vars)));

		foreach ($rowset as $row)
		{
			$topic_id = $row['topic_id'];
			$forum_id = $row['forum_id'];

			$replies = $this->content_visibility->get_count('topic_posts', $row, $forum_id) - 1;
			$unread_topic = (isset($topic_tracking_info[$forum_id][$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$topic_id]) ? true : false;

			$folder_img = $folder_alt = $topic_type = '';
			topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

			// Generate all the URIs ...
			$view_topic_url_params = 'f=' . $row['forum_id'] . '&amp;t=' . $topic_id;
			$view_topic_url = append_sid("{$this->root_path}viewtopic.$this->phpEx", $view_topic_url_params);
			$view_forum_url = append_sid("{$this->root_path}viewforum.$this->phpEx", 'f=' . $forum_id);

			$topic_unapproved = (($row['topic_visibility'] == ITEM_UNAPPROVED || $row['topic_visibility'] == ITEM_REAPPROVE) && $this->auth->acl_get('m_approve', $row['forum_id']));
			$posts_unapproved = ($row['topic_visibility'] == ITEM_APPROVED && $row['topic_posts_unapproved'] && $this->auth->acl_get('m_approve', $row['forum_id']));
			$topic_deleted = $row['topic_visibility'] == ITEM_DELETED;

			$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$this->root_path}mcp.$this->phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $this->user->session_id) : '';
			$u_mcp_queue = (!$u_mcp_queue && $topic_deleted) ? append_sid("{$this->root_path}mcp.$this->phpEx", 'i=queue&amp;mode=deleted_topics&amp;t=' . $topic_id, true, $this->user->session_id) : $u_mcp_queue;

			$tpl_ary = array(
				'FORUM_ID'					=> $forum_id,
				'TOPIC_ID'					=> $topic_id,
				'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'FIRST_POST_TIME'			=> $this->user->format_date($row['topic_time']),
				'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
				'LAST_POST_TIME'			=> $this->user->format_date($row['topic_last_post_time']),
				'LAST_VIEW_TIME'			=> $this->user->format_date($row['topic_last_view_time']),
				'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

				'REPLIES'					=> $replies,
				'VIEWS'						=> $row['topic_views'],
				'TOPIC_TITLE'				=> censor_text($row['topic_title']),
				'TOPIC_TYPE'				=> $topic_type,
				'FORUM_NAME'				=> $row['forum_name'],

				'TOPIC_IMG_STYLE'		=> $folder_img,
				'TOPIC_FOLDER_IMG'		=> $this->user->img($folder_img, $folder_alt),
				'TOPIC_FOLDER_IMG_ALT'	=> $this->user->lang[$folder_alt],

				'TOPIC_ICON_IMG'		=> ($row['enable_icons'] && !empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
				'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $this->user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',
				'REPORTED_IMG'			=> ($row['topic_reported'] && $this->auth->acl_get('m_report', $forum_id)) ? $this->user->img('icon_topic_reported', 'TOPIC_REPORTED') : '',

				'S_TOPIC_TYPE'			=> $row['topic_type'],
				'S_UNREAD_TOPIC'		=> $unread_topic,
				'S_TOPIC_REPORTED'		=> ($row['topic_reported'] && $this->auth->acl_get('m_report', $forum_id)) ? true : false,
				'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
				'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
				'S_TOPIC_DELETED'		=> $topic_deleted,
				'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,
				'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
				'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
				'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
				'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
				'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,

				'U_NEWEST_POST'			=> append_sid("{$this->root_path}viewtopic.$this->phpEx", $view_topic_url_params . '&amp;view=unread') . '#unread',
				'U_LAST_POST'			=> append_sid("{$this->root_path}viewtopic.$this->phpEx", $view_topic_url_params . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
				'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'U_VIEW_TOPIC'			=> $view_topic_url,
				'U_VIEW_FORUM'			=> $view_forum_url,
				'U_MCP_REPORT'			=> append_sid("{$this->root_path}mcp.$this->phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $this->user->session_id),
				'U_MCP_QUEUE'			=> $u_mcp_queue,
			);

			/**
			* For compatibility with third-party extensions
			* Modify the topic data before it is assigned to the template
			*
			* @event paybas.recenttopics.modify_tpl_ary
			* @var    array    row            Array with topic data
			* @var    array    tpl_ary        Template block array with topic data
			* @since 2.0.0
			*/
			$vars = array('row', 'tpl_ary');
			extract($this->dispatcher->trigger_event('paybas.recenttopics.modify_tpl_ary', compact($vars)));

			$this->template->assign_block_vars($tpl_loopname, $tpl_ary);

			$this->pagination->generate_template_pagination($view_topic_url, $tpl_loopname . '.pagination', 'start', $replies + 1, $this->config['posts_per_page'], 1, true, true);
		}
	}
}
