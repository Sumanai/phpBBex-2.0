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

class v180 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.8.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex1x\v170',
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('display_raters', '0')),
			array('config.add', array('keep_admin_logs_days', 365)),
			array('config.add', array('keep_mod_logs_days', 365)),
			array('config.add', array('keep_critical_logs_days', 365)),
			array('config.add', array('keep_user_logs_days', 365)),
			array('config.add', array('keep_register_logs_days', 30)),

			// New phpBBex modules
			array('module.add', array(
				'acp',
				'ACP_FORUM_LOGS',
				array(
					'module_basename'	=> 'acp_board',
					'modes'				=> array('logs'),
				),
			)),

			// phpBBex version
			array('config.update', array('phpbbex_version', '1.8.0')),
		);
	}
}
