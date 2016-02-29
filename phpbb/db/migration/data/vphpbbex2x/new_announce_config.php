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

class new_announce_config extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex2x\v202',
		);
	}

	public function update_data()
	{
		return array(
			// Rename global annoucements comfig
			array('config.add', array('global_announce_on_index', (int) $this->config['announce_index'])),
			array('config.remove', array('announce_index')),

			// Add new annoucements comfigs
			array('config.add', array('global_announce_in_all_forums', 1)),
			array('config.add', array('simple_announce_on_index', 0)),
			array('config.add', array('through_announce', 1)),
		);
	}
}
