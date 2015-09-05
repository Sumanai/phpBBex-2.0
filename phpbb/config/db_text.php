<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace phpbb\config;

/**
* Manages configuration options with an arbitrary length value stored in a TEXT
* column. In constrast to class \phpbb\config\db, values are never cached and
* prefetched, but every get operation sends a query to the database.
*/
class db_text
{
	/**
	* Database connection
	* @var \phpbb\db\driver\driver_interface
	*/
	protected $db;

	/**
	* Name of the database table used.
	* @var string
	*/
	protected $table;

	/**
	* A cache instance or null
	* @var \phpbb\cache\driver\driver_interface
	*/
	protected $cache;

	/**
	* Cached text configuration
	* @var array
	*/
	protected $cache_config_text;

	/**
	* @param \phpbb\db\driver\driver_interface $db        Database connection
	* @param string          $table     Table name
	* @param \phpbb\cache\driver\driver_interface $cache  A cache instance or null
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $table, \phpbb\cache\driver\driver_interface $cache = null)
	{
		$this->db = $db;
		$this->table = $this->db->sql_escape($table);
		$this->cache = $cache;

		$this->cache_config_text = ($this->cache) ? $this->cache->get('config_text') : array();

		if ($this->cache_config_text === false)
		{
			$this->cache_config_text = array();
		}
	}

	/**
	* Sets the configuration option with the name $key to $value.
	*
	* @param string $key       The configuration option's name
	* @param string $value     New configuration value
	*
	* @return null
	*/
	public function set($key, $value, $use_cache = false)
	{
		$this->set_array(array($key => $value), $use_cache);
	}

	/**
	* Gets the configuration value for the name $key.
	*
	* @param string $key       The configuration option's name
	*
	* @return string|null      String result on success
	*                          null if there is no such option
	*/
	public function get($key)
	{
		$map = $this->get_array(array($key));

		return isset($map[$key]) ? $map[$key] : null;
	}

	/**
	* Removes the configuration option with the name $key.
	*
	* @param string $key       The configuration option's name
	*
	* @return null
	*/
	public function delete($key, $use_cache = false)
	{
		$this->delete_array(array($key), $use_cache);
	}

	/**
	* Mass set configuration options: Receives an associative array,
	* treats array keys as configuration option names and associated
	* array values as their configuration option values.
	*
	* @param array $map        Map from configuration names to values
	*
	* @return null
	*/
	public function set_array(array $map, $use_cache = false)
	{
		$this->db->sql_transaction('begin');

		foreach ($map as $key => $value)
		{
			$sql = 'UPDATE ' . $this->table . "
				SET config_value = '" . $this->db->sql_escape($value) . "'
				WHERE config_name = '" . $this->db->sql_escape($key) . "'";
			$this->db->sql_query($sql);

			if (!$this->db->sql_affectedrows())
			{
				$sql_array = array(
					'config_name'	=> (string) $key,
					'config_value'	=> (string) $value,
				);

				if ($use_cache)
				{
					$sql_array['is_dynamic'] = 0;
				}

				$sql = 'INSERT INTO ' . $this->table . ' ' . $this->db->sql_build_array('INSERT', $sql_array);
				$this->db->sql_query($sql);
			}
		}

		$this->db->sql_transaction('commit');

		if ($use_cache && $this->cache)
		{
			$this->cache->destroy('config_text');
		}
	}

	/**
	* Mass get configuration options: Receives a set of configuration
	* option names and returns the result as a key => value map where
	* array keys are configuration option names and array values are
	* associated config option values.
	*
	* @param array $keys       Set of configuration option names
	*
	* @return array            Map from configuration names to values
	*/
	public function get_array(array $keys)
	{
		$map = array();

		// Get cached values
		foreach ($keys as $key => $config_name)
		{
			if (isset($this->cache_config_text[$config_name]))
			{
				$map[$config_name] = $this->cache_config_text[$config_name];
				unset($keys[$key]);
			}
		}

		if (empty($keys))
		{
			// All values cached
			return $map;
		}

		$sql = 'SELECT *
			FROM ' . $this->table . '
			WHERE ' . $this->db->sql_in_set('config_name', $keys, false, true);
		$result = $this->db->sql_query($sql);

		$update_cache = false;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$map[$row['config_name']] = $row['config_value'];

			if (!$row['is_dynamic'] && $this->cache)
			{
				$update_cache = true;
				$this->cache_config_text[$row['config_name']] = $row['config_value'];
			}
		}
		$this->db->sql_freeresult($result);

		// Set cached values
		if ($update_cache)
		{
			$this->cache->put('config_text', $this->cache_config_text);
		}

		return $map;
	}

	/**
	* Mass delete configuration options.
	*
	* @param array $keys       Set of configuration option names
	*
	* @return null
	*/
	public function delete_array(array $keys, $use_cache = false)
	{
		$sql = 'DELETE
			FROM ' . $this->table . '
			WHERE ' . $this->db->sql_in_set('config_name', $keys, false, true);
		$this->db->sql_query($sql);

		if ($use_cache && $this->cache)
		{
			$this->cache->destroy('config_text');
		}
	}
}
