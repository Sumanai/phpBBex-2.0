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
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* phpBBex 2.0.2 data file
*/
class datafile_phpbbex_2_0_2
{
	/**
	* @var Array The bots
	*/
	var $bots = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array phpBBex 2.0.2 config data
	*/
	var $config = array(
		'active_topics_on_index_exclude'		=> array('config_value' => '0', 'is_dynamic' => '0'),

		// Version
		'phpbbex_version'						=> array('config_value' => '2.0.2', 'is_dynamic' => '0'),

	);

	/**
	* @var Array Config entries that were removed by the phpBBex 2.0.1 update
	*/
	var $removed_config = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default permission settings
	*/
	var $acl_options = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default roles
	*/
	var $acl_roles = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default role data
	*/
	var $acl_role_data = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default extension groups
	*/
	var $extension_groups = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default extensions
	*/
	var $extensions = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* Define the module structure so that we can populate the database without
	* needing to hard-code module_id values
	*/
	var $module_categories = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	var $module_extras = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	var $module_categories_basenames = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* @var Array All default groups
	*/
	var $groups = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);
	/**
	* @var Array All default report reasons
	*/
	var $report_reasons = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	var $acp_modules = array(
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	);

	/**
	* Define the basic structure
	* The format:
	*		array('{TABLE_NAME}' => {TABLE_DATA})
	*		{TABLE_DATA}:
	*			COLUMNS = array({column_name} = array({column_type}, {default}, {auto_increment}))
	*			PRIMARY_KEY = {column_name(s)}
	*			KEYS = array({key_name} = array({key_type}, {column_name(s)})),
	*
	*	Column Types:
	*	INT:x		=> SIGNED int(x)
	*	BINT		=> BIGINT
	*	UINT		=> mediumint(8) UNSIGNED
	*	UINT:x		=> int(x) UNSIGNED
	*	TINT:x		=> tinyint(x)
	*	USINT		=> smallint(4) UNSIGNED (for _order columns)
	*	BOOL		=> tinyint(1) UNSIGNED
	*	VCHAR		=> varchar(255)
	*	CHAR:x		=> char(x)
	*	XSTEXT_UNI	=> text for storing 100 characters (topic_title for example)
	*	STEXT_UNI	=> text for storing 255 characters (normal input field with a max of 255 single-byte chars) - same as VCHAR_UNI
	*	TEXT_UNI	=> text for storing 3000 characters (short text, descriptions, comments, etc.)
	*	MTEXT_UNI	=> mediumtext (post text, large text)
	*	VCHAR:x		=> varchar(x)
	*	TIMESTAMP	=> int(11) UNSIGNED
	*	DECIMAL		=> decimal number (5,2)
	*	DECIMAL:	=> decimal number (x,2)
	*	PDECIMAL	=> precision decimal number (6,3)
	*	PDECIMAL:	=> precision decimal number (x,3)
	*	VCHAR_UNI	=> varchar(255) BINARY
	*	VCHAR_CI	=> varchar_ci for postgresql, others VCHAR
	*	ENUM:		=> ENUM for mysql, others varchar(255)
	*/
	function get_schema_struct(&$schema_data)
	{
		// No changes phpBBex 2.0.1 -> phpBBex 2.0.2
	}
}
