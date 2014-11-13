-- General scheme updates
CREATE TABLE phpbb_user_confirm_keys (
	confirm_key varchar(10) NOT NULL,
	user_id mediumint(8) UNSIGNED NOT NULL,
	confirm_time int(11) UNSIGNED NOT NULL,
	PRIMARY KEY  (confirm_key),
	KEY user_id (user_id)
) CHARACTER SET `utf8` COLLATE `utf8_bin`;


ALTER TABLE phpbb_bbcodes
	ADD COLUMN bbcode_order smallint(4) DEFAULT '0' NOT NULL AFTER bbcode_id;

-- New phpBBex options
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_post_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_imgs', '0');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('max_sig_lines', '4');
REPLACE INTO phpbb_config (config_name, config_value) VALUES ('site_keywords', '');

-- Update YandexBot UA and remove Aport [Bot]
UPDATE phpbb_bots SET bot_agent = 'YandexBot/' WHERE bot_agent = 'Yandex/';
DELETE FROM phpbb_users WHERE username='Aport [Bot]';
DELETE FROM phpbb_bots WHERE bot_name='Aport [Bot]';

 -- phpBBex extension
REPLACE INTO phpbb_ext (ext_name, ext_active, ext_state) VALUES ('phpBBex/phpBBext', 1, 'b:0;');
