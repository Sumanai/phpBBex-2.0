<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* @package module_install
*/
class acp_top_links_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_top_links',
			'title'		=> 'ACP_TOP_LINKS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'toplinks'		=> array('title' => 'ACP_TOP_LINKS', 'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
