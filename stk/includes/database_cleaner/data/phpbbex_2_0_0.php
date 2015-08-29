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

/**
* phpBBex 2.0.0 data file
*/
class datafile_phpbbex_2_0_0
{
	/**
	* @var Array The bots
	*/
	var $bots = array(
		'Ahrefs [Bot]'				=> array('AhrefsBot/', ''),
		'MailRu [Bot]'				=> array('Mail.Ru/', ''),
		'Rambler [Bot]'				=> array('StackRambler/', ''),
		'WebAlta [Bot]'				=> array('WebAlta Crawler/', ''),
		'Yandex [Addurl]'			=> array('YandexAddurl/', ''),
		'Yandex [Blogs]'			=> array('YandexBlogs/', ''),
		'Yandex [Bot]'				=> array('YandexBot/', ''),
		'Yandex [Catalog]'			=> array('YandexCatalog/', ''),
		'Yandex [Direct]'			=> array('YandexDirect/', ''),
		'Yandex [Images]'			=> array('YandexImages/', ''),
		'Yandex [Media]'			=> array('YandexMedia/', ''),
		'Yandex [Metrika]'			=> array('YandexMetrika/', ''),
		'Yandex [News]'				=> array('YandexNews/', ''),
		'Yandex [Video]'			=> array('YandexVideo/', ''),
	);

	/**
	* @var Array phpBBex 2.0.0 config data
	*/
	var $config = array(
		// 1.4.0
		'active_topics_days'					=> array('config_value' => '30', 'is_dynamic' => '0'),
		'active_topics_on_index'				=> array('config_value' => '5', 'is_dynamic' => '0'),
		'active_users_days'						=> array('config_value' => '90', 'is_dynamic' => '0'),
		'allow_quick_full_quote'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'allow_quick_post'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'allow_quick_post_options'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'allow_quick_reply_options'				=> array('config_value' => '20', 'is_dynamic' => '0'),
		'announce_index'						=> array('config_value' => '1', 'is_dynamic' => '0'),
		'auto_guest_lang'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'default_search_titleonly'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'load_online_bots'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'login_via_email_enable'				=> array('config_value' => '1', 'is_dynamic' => '0'),
		'max_post_imgs'							=> array('config_value' => '0', 'is_dynamic' => '0'),
		'max_sig_imgs'							=> array('config_value' => '0', 'is_dynamic' => '0'),
		'max_sig_lines'							=> array('config_value' => '4', 'is_dynamic' => '0'),
		'merge_interval'						=> array('config_value' => '18', 'is_dynamic' => '0'),
		'no_sid'								=> array('config_value' => '0', 'is_dynamic' => '0'),
		'override_user_dateformat'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'override_user_lang'					=> array('config_value' => '0', 'is_dynamic' => '0'),
		'override_user_timezone'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'rate_change_time'						=> array('config_value' => 300, 'is_dynamic' => '0'), // 60*5
		'rate_enabled'							=> array('config_value' => '1', 'is_dynamic' => '0'),
		'rate_no_negative'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'rate_no_positive'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'rate_only_topics'						=> array('config_value' => 2592000, 'is_dynamic' => '0'), // 3600*24*30
		'rate_time'								=> array('config_value' => '1', 'is_dynamic' => '0'),
		'rate_topic_time'						=> array('config_value' => '-1', 'is_dynamic' => '0'),
		'site_keywords'							=> array('config_value' => '', 'is_dynamic' => '0'),
		'social_media_cover_url'				=> array('config_value' => '', 'is_dynamic' => '0'),
		'warning_post_default'					=> array('config_value' => '', 'is_dynamic' => '0'),

		'style_ml_show_gender'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_joined'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_last_active'				=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_posts'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_rank'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_rated'					=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_ml_show_rated_detailed'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_ml_show_rating'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_ml_show_rating_detailed'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_ml_show_row_numbers'				=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_on_left'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_age'						=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_buttons'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_gender'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_joined'					=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_posts'					=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_rated'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_rated'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_rated'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_rated'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_rated_detailed'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_rating'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_rating_detailed'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_topic_poster'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_mp_show_warnings'				=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_mp_show_with_us'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_p_show_rated'					=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_p_show_rated_detailed'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_p_show_rating'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_p_show_rating_detailed'			=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_show_feeds_in_forumlist'			=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_show_sitename_in_headerbar'		=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_show_social_buttons'				=> array('config_value' => '1', 'is_dynamic' => '0'),

		// 1.5.3
		'search_highlight_keywords'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'style_max_width'						=> array('config_value' => '1280', 'is_dynamic' => '0'),
		'external_links_newwindow'				=> array('config_value' => '0', 'is_dynamic' => '0'),
		'external_links_newwindow_exclude'		=> array('config_value' => '', 'is_dynamic' => '0'),

		// 1.6.0
		'max_spoiler_depth'						=> array('config_value' => '2', 'is_dynamic' => '0'),
		'style_back_to_top'						=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_new_year'						=> array('config_value' => '-1', 'is_dynamic' => '0'),
		'style_rounded_corners'					=> array('config_value' => '1', 'is_dynamic' => '0'),
		'style_vt_show_post_numbers'			=> array('config_value' => '0', 'is_dynamic' => '0'),

		// 1.7.0
		'min_post_font_size'					=> array('config_value' => '85', 'is_dynamic' => '0'),
		'min_sig_font_size'						=> array('config_value' => '100', 'is_dynamic' => '0'),

		// 1.8.0
		'display_raters'						=> array('config_value' => '0', 'is_dynamic' => '0'),
		'keep_admin_logs_days'					=> array('config_value' => '365', 'is_dynamic' => '0'),
		'keep_mod_logs_days'					=> array('config_value' => '365', 'is_dynamic' => '0'),
		'keep_critical_logs_days'				=> array('config_value' => '365', 'is_dynamic' => '0'),
		'keep_user_logs_days'					=> array('config_value' => '365', 'is_dynamic' => '0'),
		'keep_register_logs_days'				=> array('config_value' => '30', 'is_dynamic' => '0'),

		// 2.0.0
		'load_jquery_v2_url'					=> array('config_value' => '//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js', 'is_dynamic' => '0'),

		// Version
		'phpbbex_version'						=> array('config_value' => '2.0.0', 'is_dynamic' => '0'),

	);

	/**
	* @var Array Config entries that were removed by the phpBBex 2.0.0 update
	*/
	var $removed_config = array(
		// No config entries removed phpBB -> phpBBex 2.0.0
	);

	/**
	* @var Array All default permission settings
	*/
	var $acl_options = array(
		// 1.4.0
		'u_ignoreedittime'		=> array('is_global' => '1', 'is_local' => '0', 'founder_only' => '0'),
		'u_ignorefpedittime'	=> array('is_global' => '1', 'is_local' => '0', 'founder_only' => '0'),
		// 1.6.0
		'u_canminus'			=> array('is_global' => '1', 'is_local' => '0', 'founder_only' => '0'),
		'u_canplus'				=> array('is_global' => '1', 'is_local' => '0', 'founder_only' => '0'),
	);

	/**
	* @var Array All default roles
	*/
	var $acl_roles = array(
		// Replace default sorting for forum roles
		'ROLE_FORUM_FULL'			=> array('ROLE_DESCRIPTION_FORUM_FULL', 'f_', 10),
		'ROLE_FORUM_STANDARD'		=> array('ROLE_DESCRIPTION_FORUM_STANDARD', 'f_', 6),
		'ROLE_FORUM_BOT'			=> array('ROLE_DESCRIPTION_FORUM_BOT', 'f_', 15),
		'ROLE_FORUM_ONQUEUE'		=> array('ROLE_DESCRIPTION_FORUM_ONQUEUE', 'f_', 9),
		'ROLE_FORUM_POLLS'			=> array('ROLE_DESCRIPTION_FORUM_POLLS', 'f_', 7),
		'ROLE_FORUM_NEW_MEMBER'		=> array('ROLE_DESCRIPTION_FORUM_NEW_MEMBER', 'f_', 14),

		// New user roles
		'ROLE_USER_GUEST'			=> array('ROLE_DESCRIPTION_USER_GUEST', 'u_', 7),
		'ROLE_USER_BOT'				=> array('ROLE_DESCRIPTION_USER_BOT', 'u_', 8),

		// New forum roles
		'ROLE_FORUM_NOTOPIC'		=> array('ROLE_DESCRIPTION_FORUM_NOTOPIC', 'f_', 5),
		'ROLE_FORUM_GUEST'			=> array('ROLE_DESCRIPTION_FORUM_GUEST', 'f_', 11),
		'ROLE_FORUM_GUEST_PREMOD'	=> array('ROLE_DESCRIPTION_FORUM_GUEST_PREMOD', 'f_', 13),
		'ROLE_FORUM_POLLS_LOCK'		=> array('ROLE_DESCRIPTION_FORUM_POLLS_LOCK', 'f_', 8),
		'ROLE_FORUM_GUEST_TOPIC'	=> array('ROLE_DESCRIPTION_FORUM_GUEST_TOPIC', 'f_', 12),
	);

	/**
	* @var Array All default role data
	*/
	var $acl_role_data = array(
		// New user roles
		'ROLE_USER_GUEST'			=> array(
			'OPTION_LIKE'	=> "'u_%'",
			'OPTION_IN'		=> array('u_', 'u_download', 'u_search', 'u_viewprofile'),
			'SETTING'		=> '1',
		),
		'ROLE_USER_BOT'				=> array(
			'OPTION_LIKE'	=> "'u_%'",
			'OPTION_IN'		=> array('u_', 'u_download'),
			'SETTING'		=> '1',
		),

		// New forum roles
		'ROLE_FORUM_NOTOPIC'		=> array(
			'OPTION_LIKE'	=> "'f_%'",
			'OPTION_IN'		=> array('f_announce', 'f_flash', 'f_ignoreflood', 'f_poll', 'f_sticky', 'f_user_lock', 'f_post'),
			'NEGATE'		=> true,
			'SETTING'		=> '1',
		),
		'ROLE_FORUM_GUEST'			=> array(
			'OPTION_LIKE'	=> "'f_%'",
			'OPTION_IN'		=> array('f_', 'f_download', 'f_list', 'f_read', 'f_print', 'f_reply', 'f_bbcode', 'f_img', 'f_smilies', 'f_search', 'f_noapprove'),
			'SETTING'		=> '1',
		),
		'ROLE_FORUM_GUEST_TOPIC'	=> array(
			'OPTION_LIKE'	=> "'f_%'",
			'OPTION_IN'		=> array('f_', 'f_download', 'f_list', 'f_read', 'f_print', 'f_reply', 'f_bbcode', 'f_img', 'f_smilies', 'f_search', 'f_post', 'f_noapprove'),
			'SETTING'		=> '1',
		),
		'ROLE_FORUM_GUEST_PREMOD'	=> array(
			'OPTION_LIKE'	=> "'f_%'",
			'OPTION_IN'		=> array('f_', 'f_download', 'f_list', 'f_read', 'f_print', 'f_reply', 'f_bbcode', 'f_img', 'f_smilies', 'f_search', 'f_post'),
			'SETTING'		=> '1',
		),
		'ROLE_FORUM_POLLS_LOCK'		=> array(
			'OPTION_LIKE'	=> "'f_%'",
			'OPTION_IN'		=> array('f_announce', 'f_flash', 'f_ignoreflood', 'f_sticky'),
			'NEGATE'		=> true,
			'SETTING'		=> '1',
		),
	);

	/**
	* @var Array All default extension groups
	*/
	var $extension_groups = array(
		'IMAGES'				=> array(1, 1, 1, '', 0, '', 1),
		'ARCHIVES'				=> array(0, 1, 1, '', 0, '', 1),
		'PLAIN_TEXT'			=> array(0, 1, 1, '', 0, '', 1),
		'DOCUMENTS'				=> array(0, 1, 1, '', 0, '', 1),
		'AUDIO'					=> array(3, 1, 1, '', 0, '', 1),
		'VIDEO'					=> array(2, 1, 1, '', 0, '', 1),
		'FLASH_FILES'			=> array(5, 1, 1, '', 0, '', 1),
		'DOWNLOADABLE_FILES'	=> array(0, 1, 1, '', 0, '', 1),

		// Remove obsolete extension groups
		'REMOVE_REAL_MEDIA'			=> '',
		'REMOVE_WINDOWS_MEDIA'		=> '',
		'REMOVE_QUICKTIME_MEDIA'	=> '',
	);

	/**
	* @var Array All default extensions
	*/
	var $extensions = array(
		'PLAIN_TEXT'			=> array(
			'diff',
			'sql',
		),

		'VIDEO'					=> array(
			'm4v',
			'mp4',
			'webm',
		),

		'AUDIO'					=> array(
			'm4a',
			'mp3',
			'oga',
			'ogg',
		),

		'FLASH_FILES'			=> array(
			'REMOVE_swf',
		),

		'DOWNLOADABLE_FILES'	=> array(
			'avi',
			'mka',
			'mkv',
			'mpeg',
			'ogv',
			'opus',
			'ram',
			'rm',
			'swf',
			'webp',
			'wma',
			'wmv',
			'3g2',
			'3gp',
			'mov',
			'qt',
			// mp3 moved in AUDIO
			'REMOVE_mp3',
			// ogg moved in AUDIO
			'REMOVE_ogg',
			'REMOVE_ogm',
		),

		// Remove obsolete extension groups
		'REMOVE_REAL_MEDIA'			=> '',
		'REMOVE_WINDOWS_MEDIA'		=> '',
		'REMOVE_QUICKTIME_MEDIA'	=> '',
	);

	/**
	* Define the module structure so that we can populate the database without
	* needing to hard-code module_id values
	*/
	var $module_categories = array(
		// No Module categories changes phpBB -> phpBBex 2.0.0
	);

	var $module_extras = array(
		// No Module extra changes phpBB -> phpBBex 2.0.0
	);

	var $module_categories_basenames = array(
		// No Categories basenames changes phpBB -> phpBBex 2.0.0
	);

	/**
	* @var Array All default groups
	*/
	var $groups = array(
		// No Group changes phpBB -> phpBBex 2.0.0
	);
	/**
	* @var Array All default report reasons
	*/
	var $report_reasons = array(
		// No reason changes phpBB -> phpBBex 2.0.0
	);

	var $acp_modules = array(
		'acp'		=> array(
				'ACP_CAT_GENERAL'	=> array(
					'ACP_BOARD_CONFIGURATION'	=> array(
						'ACP_STYLE_SETTINGS',
						'ACP_TOP_LINKS',
					),
				),
				'ACP_CAT_POSTING'	=> array(
					'ACP_MESSAGES'				=> array(
						'ACP_QUICK_REPLY',
					),
				),
				'ACP_CAT_MAINTENANCE'	=> array(
					'ACP_FORUM_LOGS'		=> array(
						'ACP_LOGGING_SETTINGS',
						'ACP_REGISTER_LOGS',
					),
				),
		),
		'mcp'	=> array(
			'MCP_WARN'		=> array(
				'MCP_WARN_EDIT',
			),
		),
	);

	/**
	* Define the basic structure
	* The format:
	*		array('{TABLE_NAME}' => {TABLE_DATA})
	*		{TABLE_DATA}:
	*			COLUMNS = array({column_name} = array({column_type}, {default}, {auto_increment}))
	*			PRIMARY_KEY = {column_name(s)}
	*			KEYS = array({key_name} = array({key_type}, {column_name(s)})),
	*
	*	Column Types:
	*	INT:x		=> SIGNED int(x)
	*	BINT		=> BIGINT
	*	UINT		=> mediumint(8) UNSIGNED
	*	UINT:x		=> int(x) UNSIGNED
	*	TINT:x		=> tinyint(x)
	*	USINT		=> smallint(4) UNSIGNED (for _order columns)
	*	BOOL		=> tinyint(1) UNSIGNED
	*	VCHAR		=> varchar(255)
	*	CHAR:x		=> char(x)
	*	XSTEXT_UNI	=> text for storing 100 characters (topic_title for example)
	*	STEXT_UNI	=> text for storing 255 characters (normal input field with a max of 255 single-byte chars) - same as VCHAR_UNI
	*	TEXT_UNI	=> text for storing 3000 characters (short text, descriptions, comments, etc.)
	*	MTEXT_UNI	=> mediumtext (post text, large text)
	*	VCHAR:x		=> varchar(x)
	*	TIMESTAMP	=> int(11) UNSIGNED
	*	DECIMAL		=> decimal number (5,2)
	*	DECIMAL:	=> decimal number (x,2)
	*	PDECIMAL	=> precision decimal number (6,3)
	*	PDECIMAL:	=> precision decimal number (x,3)
	*	VCHAR_UNI	=> varchar(255) BINARY
	*	VCHAR_CI	=> varchar_ci for postgresql, others VCHAR
	*	ENUM:		=> ENUM for mysql, others varchar(255)
	*/
	function get_schema_struct(&$schema_data)
	{
		// New phpBBex tables
		$schema_data['phpbb_post_rates'] = array(
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
		);

		$schema_data['phpbb_user_confirm_keys'] = array(
			'COLUMNS'		=> array(
				'confirm_key'	=> array('VCHAR:10', ''),
				'user_id'		=> array('UINT', 0),
				'confirm_time'	=> array('TIMESTAMP', 0),
			),
			'PRIMARY_KEY'	=> 'confirm_key',
			'KEYS'			=> array(
				'user_id' => array('INDEX', 'user_id'),
			),
		);

		$schema_data['phpbb_user_browser_ids'] = array(
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
		);

		// Modified phpBBex tables
		// 1.4.0
		$schema_data['phpbb_bbcodes']['COLUMNS'] += array(
			'bbcode_order'			=> array('TINT:4', 0, 'after' => 'bbcode_id'),
		);

		$schema_data['phpbb_extensions']['KEYS'] = array(
			'extension'				=> array('UNIQUE', 'extension'),
		);

		$schema_data['phpbb_posts']['COLUMNS'] += array(
			'poster_browser_id'		=> array('CHAR:32', '', 'after' => 'poster_ip'),
			'post_merged'			=> array('TIMESTAMP', 0, 'after' => 'post_time'),
			'post_rating_positive'	=> array('UINT:8', 0, 'after' => 'post_reported'),
			'post_rating_negative'	=> array('UINT:8', 0, 'after' => 'post_rating_positive'),
		);

		$schema_data['phpbb_topics']['COLUMNS'] += array(
			'poll_show_voters'		=> array('BOOL', 0, 'after' => 'poll_vote_change'),
			'topic_first_post_show'	=> array('BOOL', 0, 'after' => 'poll_show_voters'),
		);

		$schema_data['phpbb_users']['COLUMNS'] += array(
			'user_browser'			=> array('VCHAR:150', '', 'after' => 'user_ip'),
			'user_gender'			=> array('TINT:1', 0, 'after' => 'user_birthday'),
			'user_rating_positive'	=> array('UINT:8', 0, 'after' => 'user_last_search'),
			'user_rating_negative'	=> array('UINT:8', 0, 'after' => 'user_rating_positive'),
			'user_rated_positive'	=> array('UINT:8', 0, 'after' => 'user_rating_negative'),
			'user_rated_negative'	=> array('UINT:8', 0, 'after' => 'user_rated_positive'),
			'user_topics_per_page'	=> array('UINT:8', 0, 'after' => 'user_topic_sortby_dir'),
			'user_posts_per_page'	=> array('UINT:8', 0, 'after' => 'user_post_sortby_dir'),
		);

		$schema_data['phpbb_users']['COLUMNS']['user_options'] = array('UINT:11', 233343);

		$schema_data['phpbb_warnings']['COLUMNS'] += array(
			'warning_active'		=> array('TINT:1', 1, 'after' => 'warning_id'),
			'issuer_id'				=> array('UINT:8', 0, 'after' => 'warning_active'),
			'warning_days'			=> array('TIMESTAMP', 0, 'after' => 'warning_time'),
			'warning_type'			=> array('ENUM:remark,warning,ban', 'warning', 'after' => 'warning_days'),
			'warning_text'			=> array('TEXT', NULL, 'after' => 'warning_type'),
		);

		$schema_data['phpbb_warnings']['KEYS'] = array(
			'warning_active'	=> array('INDEX', 'warning_active'),
			'issuer_id'			=> array('INDEX', 'issuer_id'),
			'user_id'			=> array('INDEX', 'user_id'),
			'post_id'			=> array('INDEX', 'post_id'),
		);

		// 1.5.3
		$schema_data['phpbb_poll_votes']['COLUMNS'] += array(
			'vote_time' => array('VCHAR:40', '', 'after' => 'vote_user_ip'),
		);

		// 1.6.0
		$schema_data['phpbb_forums']['COLUMNS'] += array(
			'forum_topic_show_days'		=> array('TINT:4', 0, 'after' => 'forum_rules_uid'),
			'forum_topic_sortby_type'	=> array('VCHAR:1', 't', 'after' => 'forum_rules_uid'),
			'forum_topic_sortby_dir'	=> array('VCHAR:1', 'd', 'after' => 'forum_rules_uid'),
		);

		$schema_data['phpbb_topics']['COLUMNS'] += array(
			'topic_priority'		=> array('UINT', 0, 'after' => 'topic_type'),
		);

		$schema_data['phpbb_topics']['KEYS'] += array(
			'topic_priority'		=> array('INDEX', 'topic_priority'),
		);

		// 2.0.0
		$schema_data['phpbb_config_text']['COLUMNS'] += array(
			'is_dynamic'	=> array('BOOL', 1, 'after' => 'config_value'),
		);

		$schema_data['phpbb_profile_fields']['COLUMNS'] += array(
			'field_input_maxlen'	=> array('VCHAR', '', 'after' => 'field_maxlen'),
			'field_regexp'			=> array('VCHAR:255', '', 'after' => 'field_validation'),
			'field_search_on_ml'	=> array('BOOL', 0, 'after' => 'field_show_profile'),
		);

		$schema_data['phpbb_styles']['COLUMNS'] += array(
			'phpbbex_compatible'	=> array('BOOL', 0, 'after' => 'bbcode_bitfield'),
		);

		$schema_data['phpbb_users']['COLUMNS'] += array(
			'user_last_ip'	=> array('VCHAR:40', '', 'after' => 'user_ip'),
			'mp_on_left'	=> array('TINT:1', 0, 'after' => 'user_posts_per_page'),
		);
	}
}
