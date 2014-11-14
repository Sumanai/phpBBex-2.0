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

ALTER TABLE phpbb_poll_votes
	ADD COLUMN vote_time int(11) UNSIGNED DEFAULT '0' NOT NULL AFTER vote_user_id;

ALTER TABLE phpbb_posts
	ADD COLUMN poster_browser_id char(32) DEFAULT '' NOT NULL AFTER poster_ip;

ALTER TABLE phpbb_topics
	ADD COLUMN poll_show_voters tinyint(1) UNSIGNED DEFAULT '0' NOT NULL AFTER poll_vote_change;

ALTER TABLE phpbb_users
	ADD COLUMN user_last_ip varchar(40) DEFAULT '' NOT NULL AFTER user_ip,
	ADD COLUMN user_browser varchar(150) DEFAULT '' NOT NULL AFTER user_last_ip;

-- New phpBBex options
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_post_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_lines', '4');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('min_post_font_size', '85');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_post_font_size', '200');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('min_sig_font_size', '100');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_font_size', '100');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('site_keywords', '');

-- Update YandexBot UA and remove Aport [Bot]
UPDATE phpbb_bots SET bot_agent = 'YandexBot/' WHERE bot_agent = 'Yandex/';
DELETE FROM phpbb_users WHERE username='Aport [Bot]';
DELETE FROM phpbb_bots WHERE bot_name='Aport [Bot]';

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
