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

class add_phpbbex_style extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\v31x\style_update',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns'		=> array(
				$this->table_prefix . 'styles'	=> array(
					'phpbbex_compatible'	=> array('BOOL', 0, 'after' => 'bbcode_bitfield'),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'add_phpbbex_style'))),
		);
	}

	public function add_phpbbex_style()
	{
		$sql = 'SELECT style_id
				FROM ' . $this->table_prefix . "styles
				WHERE style_name = 'prosilverEx'";
		$result = $this->db->sql_query($sql);
		$style_ex = (int) $this->db->sql_fetchfield('style_id');
		$this->db->sql_freeresult($result);

		if (!$style_ex)
		{
			$sql = 'SELECT style_id
				FROM ' . $this->table_prefix . 'styles
				WHERE style_name = "prosilver"';
			$result = $this->db->sql_query($sql);
			$prosilver_id = (int) $this->db->sql_fetchfield('style_id');
			$this->db->sql_freeresult($result);

			$cfg = parse_cfg_file($this->phpbb_root_path . 'styles/prosilver_ex/style.cfg');

			$style = array(
				'style_name'		=> $cfg['name'],
				'style_copyright'	=> $cfg['copyright'],
				'style_active'		=> 1,
				'style_path'		=> 'prosilver_ex',
				'bbcode_bitfield'	=> $cfg['template_bitfield'],
				'phpbbex_compatible'	=> 1,
				'style_parent_id'	=> $prosilver_id,
				'style_parent_tree'	=> 'prosilver',
			);

			$sql = 'INSERT INTO ' . $this->table_prefix . 'styles
				' . $this->db->sql_build_array('INSERT', $style);
			$this->db->sql_query($sql);

			$style_id = $this->db->sql_nextid();

			// Set phpBBex default style
			$this->config->set('default_style', $style_id);

			// Set users to phpBBex default style
			$sql = 'UPDATE ' . $this->table_prefix . 'users
				SET user_style = ' . $style_id;
			$this->db->sql_query($sql);

			// Disabling prosilver style
			$sql = 'UPDATE ' . $this->table_prefix . 'styles
				SET style_active = 0
				WHERE style_id = ' . $prosilver_id;
			$this->db->sql_query($sql);
		}
	}
}
