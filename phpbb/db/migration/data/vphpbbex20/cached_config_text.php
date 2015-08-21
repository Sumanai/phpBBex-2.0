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

class cached_config_text extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'config_text', 'is_dynamic');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v310\gold',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'config_text' => array(
					'is_dynamic'	=> array('BOOL', 1, 'after' => 'config_value'),
				),
			),

			'add_index'		=> array(
				$this->table_prefix . 'config_text'		=> array(
					'is_dynamic'	=> array('is_dynamic'),
				),
			),
		);
	}
}
