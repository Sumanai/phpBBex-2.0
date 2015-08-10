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

class v170 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.7.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v30x\release_3_0_12',
			'\phpbb\db\migration\data\vphpbbex1x\v160',
		);
	}

	public function update_data()
	{
		return array(
			// Reset some other options to phpBBex defaults
			array('config.update', array('max_post_font_size', 200)),
			array('config.update', array('max_sig_font_size', 100)),

			array('config.add', array('min_post_font_size', 85)),
			array('config.add', array('min_sig_font_size', 100)),

			array('custom', array(array($this, 'extensions'))),

			// phpBBex version
			array('config.update', array('phpbbex_version', '1.7.0')),
		);
	}

	public function extensions()
	{
		$sql = 'UPDATE ' . $this->table_prefix . "extension_groups SET group_name = 'AUDIO' WHERE cat_id = 3";
		$this->sql_query($sql);

		$sql = 'UPDATE ' . $this->table_prefix . "extension_groups SET group_name = 'VIDEO' WHERE cat_id = 2";
		$this->sql_query($sql);

		$sql = 'UPDATE ' . $this->table_prefix . "extension_groups SET cat_id = 0 WHERE cat_id = 6";
		$this->sql_query($sql);
	}
}
