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

class v153 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.5.3', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex1x\v140',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'poll_votes' => array(
					'vote_time'		=> array('VCHAR:40', '', 'after' => 'vote_user_ip'),
				),
			),
			'change_columns'	=> array(
				$this->table_prefix . 'user_browser_ids' => array(
					'created'		=> array('TIMESTAMP', 0),
					'last_visit'	=> array('TIMESTAMP', 0),
					'visits'		=> array('TIMESTAMP', 0),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			// New phpBBex options
			array('config.add', array('search_highlight_keywords', 0)),

			// Style options
			array('config.add', array('style_max_width', 1280)),

			// Reset some other options to phpBBex defaults
			array('config.update', array('edit_time', 60)),
			array('config.update', array('feed_item_statistics', 0)),
			array('config.update', array('form_token_lifetime', 43200)),
			array('config.update', array('board_hide_emails', 0)),
			array('custom', array(array($this, 'update_all_users_options'))),

			// New phpBBex options
			array('config.add', array('external_links_newwindow', 0)),
			array('config.add', array('external_links_newwindow_exclude', '')),

			// Remove obsolete options
			array('config.remove', array('style_google_analytics_id')),
			array('config.remove', array('style_show_liveinternet_counter')),

			// phpBBex version
			array('config.update', array('phpbbex_version', '1.5.3')),
		);
	}

	//	Reset allow view email options for all users
	public function update_all_users_options()
	{
		$sql = 'UPDATE ' . $this->table_prefix . 'users
			SET user_allow_viewemail = 0';
		$this->sql_query($sql);
	}
}
