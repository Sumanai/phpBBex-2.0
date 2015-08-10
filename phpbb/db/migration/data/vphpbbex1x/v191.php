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

class v191 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.9.1', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v30x\release_3_0_14',
			'\phpbb\db\migration\data\vphpbbex1x\v190',
		);
	}

	public function update_data()
	{
		return array(
			// phpBBex version
			array('config.update', array('phpbbex_version', '1.9.1')),
		);
	}
}
