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

class profilefield_vk extends \phpbb\db\migration\profilefield_base_migration
{
	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex20\update_profile_field',
		);
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'create_custom_field'))),
		);
	}

	protected $profilefield_name = 'phpbb_vk';

	protected $profilefield_database_type = array('VCHAR', '');

	protected $profilefield_data = array(
		'field_name'			=> 'phpbb_vk',
		'field_type'			=> 'profilefields.type.string',
		'field_ident'			=> 'phpbb_vk',
		'field_length'			=> '40',
		'field_minlen'			=> '1',
		'field_maxlen'			=> '30',
		'field_input_maxlen'	=> '50',
		'field_novalue'			=> '',
		'field_default_value'	=> '',
		'field_validation'		=> '[a-zA-Z][\w\.,\-]+',
		'field_regexp'			=> '(?:https?:\/\/)?(?:vk\.com|vkontakte\.ru)/([a-zA-Z][\w\.,\-]+)',
		'field_required'		=> 0,
		'field_show_novalue'	=> 0,
		'field_show_on_reg'		=> 0,
		'field_show_on_pm'		=> 1,
		'field_show_on_vt'		=> 1,
		'field_show_profile'	=> 1,
		'field_hide'			=> 0,
		'field_no_view'			=> 0,
		'field_active'			=> 1,
		'field_is_contact'		=> 1,
		'field_contact_desc'	=> 'VIEW_VK_PROFILE',
		'field_contact_url'		=> 'https://vk.com/%s',
	);
}
