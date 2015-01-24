-- General scheme updates
CREATE TABLE phpbb_user_confirm_keys (
	confirm_key varchar(10) NOT NULL,
	user_id mediumint(8) UNSIGNED NOT NULL,
	confirm_time int(11) UNSIGNED NOT NULL,
	PRIMARY KEY  (confirm_key),
	KEY user_id (user_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;

CREATE TABLE phpbb_user_browser_ids (
	browser_id char(32) DEFAULT '' NOT NULL,
	user_id mediumint(8) UNSIGNED NOT NULL,
	created int(11) UNSIGNED DEFAULT '0' NOT NULL,
	last_visit int(11) UNSIGNED DEFAULT '0' NOT NULL,
	visits int(11) UNSIGNED DEFAULT '0' NOT NULL,
	agent varchar(150) DEFAULT '' NOT NULL,
	last_ip varchar(40) DEFAULT '' NOT NULL,
	PRIMARY KEY (browser_id,user_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;

ALTER TABLE phpbb_bbcodes
	ADD COLUMN bbcode_order smallint(4) DEFAULT '0' NOT NULL AFTER bbcode_id;

ALTER TABLE phpbb_forums
	ADD COLUMN forum_topic_show_days smallint(4) UNSIGNED DEFAULT '0' NOT NULL AFTER forum_rules_uid,
	ADD COLUMN forum_topic_sortby_type varchar(1) DEFAULT '' NOT NULL AFTER forum_topic_show_days,
	ADD COLUMN forum_topic_sortby_dir varchar(1) DEFAULT '' NOT NULL AFTER forum_topic_sortby_type;

ALTER TABLE phpbb_poll_votes
	ADD COLUMN vote_time int(11) UNSIGNED DEFAULT '0' NOT NULL AFTER vote_user_id;

ALTER TABLE phpbb_posts
	ADD COLUMN poster_browser_id char(32) DEFAULT '' NOT NULL AFTER poster_ip,
	ADD COLUMN post_merged int(11) UNSIGNED DEFAULT '0' NOT NULL AFTER post_time;

-- Only for converting old merging data to new storing format
-- ALTER TABLE phpbb_posts ADD COLUMN post_merged int(11) UNSIGNED DEFAULT '0' NOT NULL AFTER post_time;
-- UPDATE phpbb_posts SET post_merged = post_time, post_time=post_created WHERE post_created != 0 AND post_merged = 0;
-- ALTER TABLE phpbb_posts DROP COLUMN post_created;

ALTER TABLE phpbb_topics
	ADD COLUMN poll_show_voters tinyint(1) UNSIGNED DEFAULT '0' NOT NULL AFTER poll_vote_change,
	ADD COLUMN topic_first_post_show tinyint(1) UNSIGNED DEFAULT '0' NOT NULL AFTER poll_show_voters;

ALTER TABLE phpbb_topics
	ADD COLUMN topic_priority mediumint(8) DEFAULT '0' NOT NULL AFTER topic_type,
	ADD INDEX topic_priority (topic_priority);

ALTER TABLE phpbb_users
	ADD COLUMN user_last_ip varchar(40) DEFAULT '' NOT NULL AFTER user_ip,
	ADD COLUMN user_browser varchar(150) DEFAULT '' NOT NULL AFTER user_last_ip,
	ADD COLUMN user_gender tinyint(1) UNSIGNED DEFAULT '0' NOT NULL AFTER user_birthday,
	ADD COLUMN user_topics_per_page mediumint(8) UNSIGNED DEFAULT '0' NOT NULL AFTER user_topic_sortby_dir,
	ADD COLUMN user_posts_per_page mediumint(8) UNSIGNED DEFAULT '0' NOT NULL AFTER user_post_sortby_dir,
	ADD COLUMN mp_on_left tinyint(1) UNSIGNED DEFAULT '0' NOT NULL AFTER user_post_sortby_dir;

-- New phpBBex options
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('active_topics_on_index', '5');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('announce_index', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('allow_quick_full_quote', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('allow_quick_post', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('allow_quick_post_options', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('allow_quick_reply_options', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('default_search_titleonly', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('keep_admin_logs_days', '365');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('keep_mod_logs_days', '365');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('keep_critical_logs_days', '365');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('keep_user_logs_days', '365');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('keep_register_logs_days', '30');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('login_via_email_enable', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_post_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_lines', '4');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_spoiler_depth', '2');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('merge_interval', '18');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('min_post_font_size', '85');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('min_sig_font_size', '100');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('override_user_lang', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('override_user_dateformat', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('override_user_timezone', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('search_highlight_keywords', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('site_keywords', '');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('social_media_cover_url', '');

REPLACE INTO phpbb_config_text (config_name, config_value, is_dynamic) VALUES ('copyright_notice', '', '0');
REPLACE INTO phpbb_config_text (config_name, config_value, is_dynamic) VALUES ('toplinks', '', '0');
REPLACE INTO phpbb_config_text (config_name, config_value, is_dynamic) VALUES ('style_counters_html', '', '0');

-- Style options
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_back_to_top', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_max_width', '1240');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_on_left', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_topic_poster', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_gender', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_age', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_warnings', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_posts', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_joined', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_with_us', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_mp_show_buttons', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_new_year', '-1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_rounded_corners', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_show_feeds_in_forumlist', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_show_sitename_in_headerbar', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_show_social_buttons', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('style_vt_show_post_numbers', '0');

-- Replace default phpBB config
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('allow_quick_reply', '2');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_font_size', '100');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('board_hide_emails', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('posts_per_page', '20');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('topics_per_page', '50');
UPDATE phpbb_users SET user_allow_viewemail = 0;

-- New phpBBex ACL rights
REPLACE INTO phpbb_acl_options (auth_option, is_global) VALUES ('u_ignoreedittime', 1);
REPLACE INTO phpbb_acl_options (auth_option, is_global) VALUES ('u_ignorefpedittime', 1);

-- Reset options for all users (new dateformat, enable quick reply, etc)
UPDATE phpbb_users SET user_options = 233343, user_dateformat = '|d.m.Y|{, H:i}';

-- Other options for robots
UPDATE phpbb_users SET user_dateformat = 'd.m.Y{, H:i}' WHERE group_id = 6;

-- Resolve conflicts with the new system bbcodes
DELETE FROM phpbb_bbcodes WHERE bbcode_tag IN ('s', 'tt', 'spoiler', 'spoiler=');
SELECT (@new_bbcode_id:=GREATEST(MAX(bbcode_id)+1, 17)) FROM phpbb_bbcodes;
UPDATE phpbb_bbcodes SET bbcode_id=@new_bbcode_id WHERE bbcode_id = 13;
SELECT (@new_bbcode_id:=GREATEST(MAX(bbcode_id)+1, 17)) FROM phpbb_bbcodes;
UPDATE phpbb_bbcodes SET bbcode_id=@new_bbcode_id WHERE bbcode_id = 14;
SELECT (@new_bbcode_id:=GREATEST(MAX(bbcode_id)+1, 17)) FROM phpbb_bbcodes;
UPDATE phpbb_bbcodes SET bbcode_id=@new_bbcode_id WHERE bbcode_id = 15;

-- Update YandexBot UA and remove Aport [Bot]
UPDATE phpbb_bots SET bot_agent = 'YandexBot/' WHERE bot_agent = 'Yandex/';
DELETE FROM phpbb_users WHERE username='Aport [Bot]';
DELETE FROM phpbb_bots WHERE bot_name='Aport [Bot]';

-- Reset some other options to phpBBex defaults
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('default_dateformat', '|d.m.Y|{, H:i}');

-- Reset CAPTCHA options
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_plugin', 'phpbb_captcha_nogd');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_foreground_noise', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_x_grid', '25');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_y_grid', '25');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_wave', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_3d_noise', '1');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('captcha_gd_fonts', '1');

 -- phpBBex extension
REPLACE INTO phpbb_ext (ext_name, ext_active, ext_state) VALUES ('phpBBex/phpBBext', 1, 'b:0;');

-- Removing outdated options from 1.x versions
DELETE FROM phpbb_config WHERE config_name='copyright_notice';
DELETE FROM phpbb_config WHERE config_name='style_counters_html_1';
DELETE FROM phpbb_config WHERE config_name='style_counters_html_2';
DELETE FROM phpbb_config WHERE config_name='style_counters_html_3';
DELETE FROM phpbb_config WHERE config_name='style_counters_html_4';
DELETE FROM phpbb_config WHERE config_name='style_counters_html_5';
DELETE FROM phpbb_config WHERE config_name='style_min_width';
