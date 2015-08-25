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

class v201 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '2.0.1', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex20\v200',
		);
	}

	public function update_data()
	{
		return array(
			// Removing outdated options from 1.x versions
			array('config.remove', array('style_ml_show_from')),
			array('config.remove', array('style_ml_show_website')),
			array('config.remove', array('style_mp_show_from')),

			// New phpBBex options
			array('config.add', array('active_topics_on_index_exclude', 0)),

			// phpBBex version
			array('config.update', array('phpbbex_version', '2.0.1')),
		);
	}
}
