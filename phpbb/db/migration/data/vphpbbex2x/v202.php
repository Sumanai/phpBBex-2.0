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

class v202 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '2.0.2', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v31x\v317pl1',
			'\phpbb\db\migration\data\vphpbbex2x\v201',
			'\phpbb\db\migration\data\vphpbbex2x\jquery_220',
		);
	}

	public function update_data()
	{
		return array(
			// phpBBex version
			array('config.update', array('phpbbex_version', '2.0.2')),
		);
	}
}
