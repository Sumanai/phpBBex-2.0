<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\db\migration\data\vphpbbex20;

class v200 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '2.0.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v310\gold',
			'\phpbb\db\migration\data\vphpbbex1x\v191',
			'\phpbb\db\migration\data\vphpbbex20\add_phpbbex_style',
			'\phpbb\db\migration\data\vphpbbex20\cached_config_text',
			'\phpbb\db\migration\data\vphpbbex20\update_profile_field',
			'\phpbb\db\migration\data\vphpbbex20\profilefield_vk',
			'\phpbb\db\migration\data\vphpbbex20\search_profilefield_on_ml',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'users' => array(
					'user_last_ip'	=> array('VCHAR:40', '', 'after' => 'user_ip'),
					'mp_on_left'	=> array('TINT:1', 0, 'after' => 'user_posts_per_page'),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'config_text_configuration'))),
			array('custom', array(array($this, 'phpbbex_ext'))),

			// Removing outdated options from 1.x versions
			array('config.remove', array('copyright_notice')),
			array('config.remove', array('outlinks')),
			array('config.remove', array('style_counter_html_1')),
			array('config.remove', array('style_counter_html_2')),
			array('config.remove', array('style_counter_html_3')),
			array('config.remove', array('style_counter_html_4')),
			array('config.remove', array('style_counter_html_5')),
			array('config.remove', array('style_min_width')),

			// Renaming outlinks in toplinks
			array('module.remove', array(
				'acp',
				'ACP_BOARD_CONFIGURATION',
				array(
					'module_basename'	=> 'acp_outlinks',
					'module_langname'	=> 'ACP_OUTLINKS',
					'module_mode'		=> 'outlinks',
					'module_auth'		=> 'acl_a_board',
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_BOARD_CONFIGURATION',
				array(
					'module_basename'	=> 'acp_top_links',
					'modes'				=> array('toplinks'),
				),
			)),

			// Reinstalling Automation module in order to position it at the end.
			array('module.remove', array(
				'acp',
				'ACP_CAT_SYSTEM',
				'ACP_AUTOMATION',
			)),
			array('module.add', array(
				'acp',
				'ACP_CAT_SYSTEM',
				'ACP_AUTOMATION',
			)),

			array('module.add', array(
				'acp',
				'ACP_AUTOMATION',
				array(
					'module_basename'	=> 'acp_update',
					'module_langname'	=> 'ACP_VERSION_CHECK',
					'module_mode'		=> 'version_check',
					'module_auth'		=> 'acl_a_board',
				),
			)),

			array('config.update', array('load_jquery_url', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js')),
			array('config.add', array('load_jquery_v2_url', '//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js')),

			// phpBBex version
			array('config.update', array('phpbbex_version', '2.0.0')),
		);
	}

	public function config_text_configuration()
	{
		$text_config = new \phpbb\config\db_text($this->db, $this->table_prefix . 'config_text');

		$style_counters_html = $this->config['style_counter_html_1'] . $this->config['style_counter_html_2'] . $this->config['style_counter_html_3'] . $this->config['style_counter_html_4'] . $this->config['style_counter_html_5'];

		$text_config->set_array(array(
			'copyright_notice'		=> $this->config['copyright_notice'],
			'toplinks'				=> $this->config['outlinks'],
			'style_counters_html'	=> $style_counters_html,
		), true);
	}

	// phpBBex compatibility extension
	public function phpbbex_ext()
	{
		$sql = 'REPLACE INTO ' . $this->table_prefix . "ext (ext_name, ext_active, ext_state) VALUES ('phpBBex/phpBBext', 1, 'b:0;')";
		$this->sql_query($sql);
	}
}
