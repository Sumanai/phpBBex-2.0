<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\db\migration\data\vphpbbex1x;

class v140 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.4.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v30x\release_3_0_13');
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'user_confirm_keys' => array(
					'COLUMNS' => array(
						'confirm_key'	=> array('VCHAR:10', ''),
						'user_id'		=> array('UINT', 0),
						'confirm_time'	=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'confirm_key',
					'KEYS' => array(
						'user_id' => array('INDEX', 'user_id'),
					),
				),
				$this->table_prefix . 'user_browser_ids' => array(
					'COLUMNS' => array(
						'browser_id'	=> array('CHAR:32', ''),
						'user_id'		=> array('UINT', 0),
						'created'		=> array('TIMESTAMP', 0),
						'last_visit'	=> array('TIMESTAMP', 0),
						'visits'		=> array('TIMESTAMP', 0),
						'agent'			=> array('VCHAR:150', ''),
						'last_ip'		=> array('VCHAR:40', ''),
					),
					'PRIMARY_KEY' => array('browser_id', 'user_id'),
				),
			),

			'add_columns' => array(
				$this->table_prefix . 'bbcodes' => array(
					'bbcode_order' => array('TINT:4', 0, 'after' => 'bbcode_id'),
				),
				$this->table_prefix . 'posts' => array(
					'poster_browser_id'	=> array('CHAR:32', '', 'after' => 'poster_ip'),
					'post_merged'		=> array('TIMESTAMP', 0, 'after' => 'post_time'),
				),
				$this->table_prefix . 'topics' => array(
					'poll_show_voters'		=> array('BOOL', 0, 'after' => 'poll_vote_change'),
					'topic_first_post_show'	=> array('BOOL', 0, 'after' => 'poll_show_voters'),
				),
				$this->table_prefix . 'users' => array(
					'user_browser'			=> array('VCHAR:150', '', 'after' => 'user_ip'),
					'user_gender'			=> array('TINT:1', 0, 'after' => 'user_birthday'),
					'user_topics_per_page'	=> array('UINT:8', 0, 'after' => 'user_topic_sortby_dir'),
					'user_posts_per_page'	=> array('UINT:8', 0, 'after' => 'user_post_sortby_dir'),
				),
			),

			'change_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'user_options'		=> array('UINT:11', 233343),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			// New phpBBex options
			array('config.add', array('active_topics_on_index', 5)),
			array('config.add', array('active_topics_days', 30)),
			array('config.add', array('active_users_days', 90)),
			array('config.add', array('announce_index', 1)),
			array('config.add', array('allow_quick_full_quote', 0)),
			array('config.add', array('allow_quick_post', 0)),
			array('config.add', array('allow_quick_post_options', 0)),
			array('config.add', array('allow_quick_reply_options', 20)),
			array('config.add', array('auto_guest_lang', 0)),
			array('config.add', array('default_search_titleonly', 0)),
			array('config.add', array('load_online_bots', 0)),
			array('config.add', array('login_via_email_enable', 1)),
			array('config.add', array('max_post_imgs', 0)),
			array('config.add', array('max_sig_imgs', 0)),
			array('config.add', array('max_sig_lines', 4)),
			array('config.add', array('merge_interval', 18)),
			array('config.add', array('override_user_lang', 0)),
			array('config.add', array('override_user_dateformat', 0)),
			array('config.add', array('override_user_timezone', 0)),
			array('config.add', array('site_keywords', '')),
			array('config.add', array('social_media_cover_url', '')),

			// Style options
			array('config.add', array('style_mp_on_left', 0)),
			array('config.add', array('style_mp_show_topic_poster', 1)),
			array('config.add', array('style_mp_show_gender', 1)),
			array('config.add', array('style_mp_show_age', 1)),
			array('config.add', array('style_mp_show_warnings', 1)),
			array('config.add', array('style_mp_show_posts', 0)),
			array('config.add', array('style_mp_show_joined', 0)),
			array('config.add', array('style_mp_show_with_us', 1)),
			array('config.add', array('style_mp_show_buttons', 1)),
			array('config.add', array('style_show_feeds_in_forumlist', 0)),
			array('config.add', array('style_show_sitename_in_headerbar', 1)),
			array('config.add', array('style_show_social_buttons', 1)),

			// Replace default phpBB config
			array('config.update', array('allow_quick_reply', 2)),
			array('config.update', array('default_dateformat', '|d.m.Y|{, H:i}')),
			array('config.update', array('max_sig_font_size', 100)),
			array('config.update', array('posts_per_page', 20)),
			array('config.update', array('topics_per_page', 50)),

			// Remove obsolete options
			array('config.remove', array('copyright_notice_html')),

			// New phpBBex modules
			array('module.add', array(
				'acp',
				'ACP_BOARD_CONFIGURATION',
				array(
					'module_basename'	=> 'acp_board',
					'modes'				=> array('style'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_MESSAGES',
				array(
					'module_basename'	=> 'acp_quick_reply',
					'modes'				=> array('quick_reply'),
				),
			)),

			// New phpBBex ACL rights
			array('permission.add', array('u_ignoreedittime', true)),
			array('permission.add', array('u_ignorefpedittime', true)),

			array('custom', array(array($this, 'update_all_users_options'))),
			array('custom', array(array($this, 'new_bbcodes'))),

			// phpBBex version
			array('config.add', array('phpbbex_version', '1.4.0')),
		);
	}

	//	Reset options for all users (new dateformat, enable quick reply, etc)
	public function update_all_users_options()
	{
		$sql = 'UPDATE ' . $this->table_prefix . 'users
			SET user_options = 233343, user_dateformat = "|d.m.Y|{, H:i}"';
		$this->sql_query($sql);

		// Other options for robots
		$sql = 'UPDATE ' . $this->table_prefix . 'users
			SET user_dateformat = "d.m.Y{, H:i}"
			WHERE group_id = 6';
		$this->sql_query($sql);
	}

	// Resolve conflicts with the new system bbcodes
	public function new_bbcodes()
	{
		$sql = 'DELETE FROM ' . $this->table_prefix . 'bbcodes WHERE bbcode_tag IN ("s", "tt", "spoiler", "spoiler=")';
		$this->sql_query($sql);

		$this->resynchronize_bbcode_order();
	}

	/**
	* Resynchronize the Custom BBCodes order field
	*
	* Based on Custom BBCode Sorting MOD by RMcGirr83
	*
	* @return null
	* @access public
	*/
	public function resynchronize_bbcode_order()
	{
		// By default, check that order is valid and fix it if necessary
		$sql = 'SELECT bbcode_id, bbcode_order
			FROM ' . $this->table_prefix . 'bbcodes
			ORDER BY bbcode_order, bbcode_id';
		$result = $this->db->sql_query($sql);

		if ($row = $this->db->sql_fetchrow($result))
		{
			$order = 0;
			do
			{
				// pre-increment $order
				++$order;

				if ($row['bbcode_order'] != $order)
				{
					$sql = 'UPDATE ' . $this->table_prefix . "bbcodes
						SET bbcode_order = $order
						WHERE bbcode_id = {$row['bbcode_id']}";
					$this->db->sql_query($sql);
				}
			}
			while ($row = $this->db->sql_fetchrow($result));
		}

		$this->db->sql_freeresult($result);
	}
}
