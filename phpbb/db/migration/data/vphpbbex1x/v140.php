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
		return array(
			'\phpbb\db\migration\data\v30x\release_3_0_11',
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'post_rates' => array(
					'COLUMNS'		=> array(
						'user_id'		=> array('UINT', 0),
						'post_id'		=> array('UINT', 0),
						'rate'			=> array('TINT:4', 0),
						'rate_time'		=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'user_id,post_id',
					'KEYS' => array(
						'post_id' => array('INDEX', 'post_id'),
						'user_id' => array('INDEX', 'user_id'),
					),
				),
				$this->table_prefix . 'user_confirm_keys' => array(
					'COLUMNS'		=> array(
						'confirm_key'	=> array('VCHAR:10', ''),
						'user_id'		=> array('UINT', 0),
						'confirm_time'	=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY'	=> 'confirm_key',
					'KEYS'			=> array(
						'user_id' => array('INDEX', 'user_id'),
					),
				),
				$this->table_prefix . 'user_browser_ids' => array(
					'COLUMNS'		=> array(
						'browser_id'	=> array('CHAR:32', ''),
						'user_id'		=> array('UINT', 0),
						'created'		=> array('TIMESTAMP', 0),
						'last_visit'	=> array('TIMESTAMP', 0),
						'visits'		=> array('TIMESTAMP', 0),
						'agent'			=> array('VCHAR:150', ''),
						'last_ip'		=> array('VCHAR:40', ''),
					),
					'PRIMARY_KEY'	=> array('browser_id', 'user_id'),
				),
			),

			'add_columns' => array(
				$this->table_prefix . 'bbcodes' => array(
					'bbcode_order'			=> array('TINT:4', 0, 'after' => 'bbcode_id'),
				),
				$this->table_prefix . 'posts' => array(
					'poster_browser_id'		=> array('CHAR:32', '', 'after' => 'poster_ip'),
					'post_merged'			=> array('TIMESTAMP', 0, 'after' => 'post_time'),
					'post_rating_positive'	=> array('UINT:8', 0, 'after' => 'post_reported'),
					'post_rating_negative'	=> array('UINT:8', 0, 'after' => 'post_rating_positive'),
				),
				$this->table_prefix . 'topics' => array(
					'poll_show_voters'		=> array('BOOL', 0, 'after' => 'poll_vote_change'),
					'topic_first_post_show'	=> array('BOOL', 0, 'after' => 'poll_show_voters'),
				),
				$this->table_prefix . 'users' => array(
					'user_browser'			=> array('VCHAR:150', '', 'after' => 'user_ip'),
					'user_gender'			=> array('TINT:1', 0, 'after' => 'user_birthday'),
					'user_rating_positive'	=> array('UINT:8', 0, 'after' => 'user_last_search'),
					'user_rating_negative'	=> array('UINT:8', 0, 'after' => 'user_rating_positive'),
					'user_rated_positive'	=> array('UINT:8', 0, 'after' => 'user_rating_negative'),
					'user_rated_negative'	=> array('UINT:8', 0, 'after' => 'user_rated_positive'),
					'user_topics_per_page'	=> array('UINT:8', 0, 'after' => 'user_topic_sortby_dir'),
					'user_posts_per_page'	=> array('UINT:8', 0, 'after' => 'user_post_sortby_dir'),
				),
				$this->table_prefix . 'warnings' => array(
					'warning_active'		=> array('TINT:1', 1, 'after' => 'warning_id'),
					'issuer_id'				=> array('UINT:8', 0, 'after' => 'warning_active'),
					'warning_days'			=> array('TIMESTAMP', 0, 'after' => 'warning_time'),
					'warning_type'			=> array('ENUM:remark,warning,ban', 'warning', 'after' => 'warning_days'),
					'warning_text'			=> array('TEXT', NULL, 'after' => 'warning_type'),
				),
			),

			'change_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'user_options'		=> array('UINT:11', 233343),
				),
			),

			'add_index'		=> array(
				$this->table_prefix . 'warnings'		=> array(
					'warning_active'	=> array('warning_active'),
					'issuer_id'			=> array('issuer_id'),
					'user_id'			=> array('user_id'),
					'post_id'			=> array('post_id'),
				),
			),

			'add_unique_index'	=> array(
				$this->table_prefix . 'extensions'		=> array(
					'extension'			=> array('extension'),
				),
			),
		);
	}

	public function update_data()
	{
		global $user;

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
			array('config.add', array('no_sid', 0)),
			array('config.add', array('override_user_lang', 0)),
			array('config.add', array('override_user_dateformat', 0)),
			array('config.add', array('override_user_timezone', 0)),
			array('config.add', array('rate_enabled', '1')),
			array('config.add', array('rate_only_topics', '0')),
			array('config.add', array('rate_time', 2592000)), // 3600*24*30
			array('config.add', array('rate_topic_time', -1)),
			array('config.add', array('rate_change_time', 300)), // 60*5
			array('config.add', array('rate_no_negative', '0')),
			array('config.add', array('rate_no_positive', '0')),
			array('config.add', array('site_keywords', '')),
			array('config.add', array('social_media_cover_url', '')),
			array('config.add', array('warning_post_default', $user->lang['WARNING_POST_DEFAULT'])),

			// Style options
			array('config.add', array('style_ml_show_row_numbers', 1)),
			array('config.add', array('style_ml_show_gender', 1)),
			array('config.add', array('style_ml_show_rank', 1)),
			array('config.add', array('style_ml_show_rating', '1')),
			array('config.add', array('style_ml_show_rating_detailed', '0')),
			array('config.add', array('style_ml_show_rated', '0')),
			array('config.add', array('style_ml_show_rated_detailed', '0')),
			array('config.add', array('style_ml_show_posts', 1)),
			array('config.add', array('style_ml_show_joined', 1)),
			array('config.add', array('style_ml_show_last_active', 1)),
			array('config.add', array('style_mp_on_left', 0)),
			array('config.add', array('style_mp_show_topic_poster', 0)),
			array('config.add', array('style_mp_show_gender', 1)),
			array('config.add', array('style_mp_show_age', 1)),
			array('config.add', array('style_mp_show_warnings', 1)),
			array('config.add', array('style_mp_show_rating', '1')),
			array('config.add', array('style_mp_show_rating_detailed', '0')),
			array('config.add', array('style_mp_show_rated', '0')),
			array('config.add', array('style_mp_show_rated_detailed', '0')),
			array('config.add', array('style_mp_show_posts', 0)),
			array('config.add', array('style_mp_show_joined', 0)),
			array('config.add', array('style_mp_show_with_us', 1)),
			array('config.add', array('style_mp_show_buttons', 1)),
			array('config.add', array('style_p_show_rating', '1')),
			array('config.add', array('style_p_show_rating_detailed', '1')),
			array('config.add', array('style_p_show_rated', '0')),
			array('config.add', array('style_p_show_rated_detailed', '0')),
			array('config.add', array('style_show_feeds_in_forumlist', 0)),
			array('config.add', array('style_show_sitename_in_headerbar', 1)),
			array('config.add', array('style_show_social_buttons', 1)),

			// Reset avatar options to phpBBex defaults
			array('config.update', array('allow_avatar', 1)),
			array('config.update', array('allow_avatar_upload', 1)),
			array('config.update', array('allow_avatar_remote_upload', 1)),
			array('config.update', array('avatar_filesize', 10240)),
			array('config.update', array('avatar_max_height', 100)),
			array('config.update', array('avatar_max_width', 100)),
			array('config.update', array('avatar_min_height', 64)),
			array('config.update', array('avatar_min_width', 64)),

			// Reset signature options to phpBBex defaults (Disable BBCodes, max 200 characters)
			array('config.update', array('allow_sig_bbcode', 0)),
			array('config.update', array('allow_sig_img', 0)),
			array('config.update', array('allow_sig_links', 0)),
			array('config.update', array('allow_sig_smilies', 0)),
			array('config.update', array('max_sig_chars', 200)),

			// Reset attachments options to phpBBex defaults
			array('config.update', array('allow_pm_attach', 1)),
			array('config.update', array('max_attachments', 30)),
			array('config.update', array('max_filesize', 524288)),
			array('config.update', array('max_filesize_pm', 262144)),
			array('config.update', array('img_create_thumbnail', 1)),

			// Reset some other options to phpBBex defaults
//			array('config.update', array('require_activation', '1')),
			array('config.update', array('default_dateformat', '|d.m.Y|{, H:i}')),
			array('config.update', array('delete_time', 15)),
			array('config.update', array('feed_enable', 1)),
			array('config.update', array('feed_overall', 0)),
			array('config.update', array('load_moderators', 0)),
			array('config.update', array('load_tplcompile', 1)),
			array('config.update', array('max_poll_options', 25)),
			array('config.update', array('max_post_smilies', 20)),
			array('config.update', array('max_post_urls', 20)),
			array('config.update', array('max_quote_depth', 2)),
			array('config.update', array('pm_max_msgs', 1000)),
			array('config.update', array('hot_threshold', 100)),
			array('config.update', array('posts_per_page', 20)),
			array('config.update', array('topics_per_page', 50)),

			// Replace default phpBB config
			array('config.update', array('allow_quick_reply', 2)),

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
			array('module.add', array(
				'mcp',
				'MCP_WARN',
				array(
					'module_basename'	=> 'mcp_warn',
					'modes'				=> array('warn_edit'),
				),
			)),

			array('module.remove', array(
				'acp',
				'ACP_AUTOMATION',
				array(
					'module_basename'	=> 'acp_update',
					'module_langname'	=> 'ACP_VERSION_CHECK',
					'module_mode'		=> 'version_check',
					'module_auth'		=> 'acl_a_board',
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
