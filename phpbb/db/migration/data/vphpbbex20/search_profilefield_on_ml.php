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

class search_profilefield_on_ml extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'profile_fields', 'field_search_on_ml');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex20\profilefield_vk',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns'		=> array(
				$this->table_prefix . 'profile_fields'	=> array(
					'field_search_on_ml'	=> array('BOOL', 0, 'after' => 'field_show_profile'),
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
		$field_show_on_ml = array(phpbb_facebook, phpbb_twitter, phpbb_skype, phpbb_youtube, phpbb_googleplus);
		$sql = 'UPDATE ' . $this->table_prefix . 'profile_fields
			SET field_show_on_ml = 0
			WHERE ' . $this->db->sql_in_set('field_name', $field_show_on_ml);
		$this->db->sql_query($sql);

		$field_search_on_ml = array(phpbb_location, phpbb_website, phpbb_facebook, phpbb_twitter, phpbb_skype, phpbb_youtube, phpbb_googleplus, phpbb_vk);
		$sql = 'UPDATE ' . $this->table_prefix . 'profile_fields
			SET field_search_on_ml = 1
			WHERE ' . $this->db->sql_in_set('field_name', $field_search_on_ml);
		$this->db->sql_query($sql);
	}
}
