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

class update_profile_field extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v312');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'profile_fields' => array(
					'field_input_maxlen'	=> array('VCHAR', '', 'after' => 'field_maxlen'),
					'field_regexp'			=> array('VCHAR:255', '', 'after' => 'field_validation'),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'update_profile_fields'))),
		);
	}

	public function update_profile_fields()
	{
		$this->update_field('phpbb_facebook', 40, 70, '(?:https?:\/\/)?facebook\.com\/([\w.]+)\/?');
		$this->update_field('phpbb_twitter', 40, 36, '(?:https?:\/\/)?twitter\.com\/([\w]+)\/?');
		$this->update_field('phpbb_googleplus', 40, 255, '(?:https?:\/\/)?plus\.google\.com\/(?:\+)?([a-zA-Z0-9]+)\/?');
		$this->update_field('phpbb_youtube', 40, 85, '(?:https?:\/\/)?youtube\.com\/user\/([a-zA-Z][\w\.,\-]+)\/?');
	}

	public function update_field($field_name, $field_length, $field_input_maxlen, $field_regexp)
	{
		$sql_ary = array(
			'field_length'			=> $field_length,
			'field_input_maxlen'	=> $field_input_maxlen,
			'field_regexp'			=> $field_regexp,
		);

		$sql = 'UPDATE ' . $this->table_prefix . 'profile_fields
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . "
			WHERE field_name = '" . $this->db->sql_escape($field_name) . "'";
		$this->sql_query($sql);
	}
}
