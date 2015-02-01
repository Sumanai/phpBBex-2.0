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
			'\phpbb\db\migration\data\vphpbbex1x\v160',
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('min_post_font_size', 85)),
			array('config.add', array('min_sig_font_size', 100)),

			// phpBBex version
			array('config.update', array('phpbbex_version', '1.7.0')),
		);
	}
}
