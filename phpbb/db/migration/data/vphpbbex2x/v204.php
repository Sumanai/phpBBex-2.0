<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\db\migration\data\vphpbbex2x;

class v204 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '2.0.4', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v31x\v319',
			'\phpbb\db\migration\data\vphpbbex2x\v203',
			'\phpbb\db\migration\data\vphpbbex2x\jquery_222',
			'\phpbb\db\migration\data\vphpbbex2x\forum_image_opacity',
			'\phpbb\db\migration\data\vphpbbex2x\update_announce_priority_type',
		);
	}

	public function update_data()
	{
		return array(
			// phpBBex version
			array('config.update', array('phpbbex_version', '2.0.4')),
		);
	}
}
