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

class forum_image_opacity extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex2x\v203',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'forums' => array(
					'forum_icon_replace'	=> array('BOOL', 0, 'after' => 'forum_image'),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('style_forum_image_opacity', '0.6')),
		);
	}
}
