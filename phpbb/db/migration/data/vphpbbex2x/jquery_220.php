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

class jquery_220 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex2x\v201',
		);
	}

	public function update_data()
	{
		return array(
			// Update jQuery
			array('config.update', array('load_jquery_url', '//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js')),
			array('config.update', array('load_jquery_v2_url', '//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js')),
		);
	}
}
