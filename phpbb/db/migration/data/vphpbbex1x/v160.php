<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\db\migration\data\vphpbbex1x;

class v160 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['phpbbex_version']) && version_compare($this->config['phpbbex_version'], '1.6.0', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\phpbb\db\migration\data\vphpbbex1x\v153',
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'forums' => array(
					'forum_topic_show_days'		=> array('TINT:4', 0, 'after' => 'forum_rules_uid'),
					'forum_topic_sortby_type'	=> array('VCHAR:1', 't', 'after' => 'forum_rules_uid'),
					'forum_topic_sortby_dir'	=> array('VCHAR:1', 'd', 'after' => 'forum_rules_uid'),
				),
				$this->table_prefix . 'topics' => array(
					'topic_priority'		=> array('UINT', 0, 'after' => 'topic_type'),
				),
			),
			'add_index'		=> array(
				$this->table_prefix . 'topics'		=> array(
					'topic_priority'		=> array('topic_priority'),
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			// New phpBBex options
			array('config.add', array('max_spoiler_depth', 2)),

			// Style options
			array('config.add', array('style_back_to_top', 1)),
			array('config.add', array('style_new_year', '-1')),
			array('config.add', array('style_rounded_corners', 1)),
			array('config.add', array('style_vt_show_post_numbers', 0)),

			// New phpBBex ACL rights
			array('permission.add', array('u_canplus', true)),
			array('permission.add', array('u_canminus', true)),

			// Set permissions
			array('permission.permission_set', array('ROLE_USER_FULL', array('u_canplus', 'u_canminus'))),
			array('permission.permission_set', array('ROLE_USER_STANDARD', array('u_canplus', 'u_canminus'))),
			array('permission.permission_set', array('ROLE_USER_LIMITED', array('u_canplus', 'u_canminus'))),
			array('permission.permission_set', array('ROLE_USER_NOPM', array('u_canplus', 'u_canminus'))),
			array('permission.permission_set', array('ROLE_USER_NOAVATAR', array('u_canplus', 'u_canminus'))),

			array('custom', array(array($this, 'converting_merging_data'))),

			// phpBBex version
			array('config.update', array('phpbbex_version', '1.6.0')),
		);
	}

	// Converting old merging data to new storing format
	public function converting_merging_data()
	{
		if (!$this->db_tools->sql_column_exists($this->table_prefix . 'posts', 'post_created'))
		{
			$sql = 'UPDATE ' . $this->table_prefix . 'posts
				SET post_merged = post_time, post_time=post_created WHERE post_created != 0 AND post_merged = 0';
			$this->sql_query($sql);

			$sql = 'ALTER TABLE ' . $this->table_prefix . 'posts
				DROP COLUMN post_created';
			$this->sql_query($sql);
		}
	}
}
