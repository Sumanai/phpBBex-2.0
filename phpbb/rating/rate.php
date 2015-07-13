<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\rating;

class rate
{
	/**
	* Auth object
	*
	* @var \phpbb\auth\auth
	*/
	protected $auth;

	/**
	* The config.
	*
	* @var \phpbb\config\config
	*/
	protected $config;

	/**
	* Database connection.
	*
	* @var \phpbb\db\driver\driver_interface
	*/
	protected $db;

	/**
	* Constructor
	*
	* @param	\phpbb\auth\auth					$auth		Auth object
	* @param	\phpbb\config\config				$config		The config
	* @param	\phpbb\db\driver\driver_interface	$db			Database connection
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
	}

	public function rate_post($post_id, $user_id, $rate, $token)
	{
		try
		{
			if (!check_link_hash($token, 'ajax')) throw new \phpbb\extension\exception('User\'s token is invalid');
			if (!$post_id) throw new \phpbb\extension\exception('post_id is required');

			// Get current user rate
			$sql = 'SELECT *
				FROM ' . POST_RATES_TABLE . '
				WHERE user_id = ' . $user_id . '
					AND post_id = ' . $post_id;
			$result = $this->db->sql_query($sql);
			$user_rate = $this->db->sql_fetchrow($result);
			if (!$user_rate) $user_rate = array('rate' => 0, 'rate_time' => 0);

			// Get post
			$sql = 'SELECT p.*, t.topic_first_post_id
				FROM ' . POSTS_TABLE . ' p
				LEFT JOIN ' . TOPICS_TABLE . ' t ON t.topic_id = p.topic_id
				WHERE p.post_id = ' . $post_id;
			$result = $this->db->sql_query($sql);
			$post = $this->db->sql_fetchrow($result);
			if (!$post) throw new \phpbb\extension\exception('post not exists');
			$rate_time = ($post['topic_first_post_id'] != $post['post_id'] || !isset($this->config['rate_topic_time']) || $this->config['rate_topic_time'] == -1) ? $this->config['rate_time'] : $this->config['rate_topic_time'];

			$can = false;
			switch ($rate)
			{
				case 'minus':
					$can = $this->config['rate_enabled'] && ($user_id != ANONYMOUS) && ($user_id != $post['poster_id']) && (empty($this->config['rate_only_topics']) || $post['topic_first_post_id'] == $post['post_id']) && ($rate_time > 0 ? $rate_time + $post['post_time'] > time() : true) && ($user_rate['rate'] >= 0) && ($user_rate['rate'] != 0 && $this->config['rate_change_time'] > 0 ? $this->config['rate_change_time'] + $user_rate['rate_time'] > time() : true) && ($this->config['rate_no_negative'] ? $user_rate['rate'] != 0 : true) && $this->auth->acl_get('u_canminus');
					if ($can) $user_rate['rate']--;
					if ($user_rate['rate'] < -1) $user_rate['rate'] = -1;
				break;
				case 'plus':
					$can = $this->config['rate_enabled'] && ($user_id != ANONYMOUS) && ($user_id != $post['poster_id']) && (empty($this->config['rate_only_topics']) || $post['topic_first_post_id'] == $post['post_id']) && ($rate_time > 0 ? $rate_time + $post['post_time'] > time() : true) && ($user_rate['rate'] <= 0) && ($user_rate['rate'] != 0 && $this->config['rate_change_time'] > 0 ? $this->config['rate_change_time'] + $user_rate['rate_time'] > time() : true) && ($this->config['rate_no_positive'] ? $user_rate['rate'] != 0 : true) && $this->auth->acl_get('u_canplus');
					if ($can) $user_rate['rate']++;
					if ($user_rate['rate'] > 1) $user_rate['rate'] = 1;
				break;
			}

			if ($can)
			{
				if ($user_rate['rate'] == 0)
				{
					$user_rate['rate_time'] = 0;
					$sql = 'DELETE
						FROM ' . POST_RATES_TABLE . '
						WHERE user_id = ' . $user_id . '
							AND post_id = ' . $post_id;
				}
				else
				{
					$user_rate['rate_time'] = time();
					$sql = 'REPLACE 
						INTO ' . POST_RATES_TABLE . '
						SET rate = ' . $user_rate['rate'] . ',
							rate_time = ' . time() . ',
							user_id = ' . $user_id . ',
							post_id = ' . $post_id;
				}
				$this->db->sql_query($sql);
			}

			// Update post rating
			$sql = 'SELECT rate, COUNT(*) as count
				FROM ' . POST_RATES_TABLE . '
				WHERE post_id = ' . $post_id . '
				GROUP BY rate';
			$result = $this->db->sql_query($sql);

			$post_rating_negative = 0;
			$post_rating_positive = 0;
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['rate'] < 0)
				{
					$post_rating_negative += abs($row['rate'] * $row['count']);
				}
				else
				{
					$post_rating_positive += abs($row['rate'] * $row['count']);
				}
			}

			$sql = 'UPDATE ' . POSTS_TABLE . '
				SET post_rating_positive = ' . $post_rating_positive . ',
					post_rating_negative = ' . $post_rating_negative . '
				WHERE post_id = ' . $post_id;
			$this->db->sql_query($sql);

			// Update poster rating
			$sql = 'SELECT rate, COUNT(*) as count
				FROM ' . POST_RATES_TABLE . ' r
				LEFT JOIN ' . POSTS_TABLE . ' p ON r.post_id = p.post_id
				WHERE p.poster_id = ' . $post['poster_id'] . '
				GROUP BY rate';
			$result = $this->db->sql_query($sql);

			$poster_rating_negative = 0;
			$poster_rating_positive = 0;
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['rate'] < 0)
				{
					$poster_rating_negative += abs($row['rate'] * $row['count']);
				}
				else
				{
					$poster_rating_positive += abs($row['rate'] * $row['count']);
				}
			}

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_rating_positive = ' . $poster_rating_positive . ',
					user_rating_negative = ' . $poster_rating_negative . '
				WHERE user_id = ' . $post['poster_id'];
			$this->db->sql_query($sql);
			
			// Update rater info
			$sql = 'SELECT rate, COUNT(*) as count
				FROM ' . POST_RATES_TABLE . '
				WHERE user_id = ' . $user_id . '
				GROUP BY rate';
			$result = $this->db->sql_query($sql);

			$user_rated_negative = 0;
			$user_rated_positive = 0;
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['rate'] < 0)
				{
					$user_rated_negative += abs($row['rate'] * $row['count']);
				}
				else
				{
					$user_rated_positive += abs($row['rate'] * $row['count']);
				}
			}

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET user_rated_positive = ' . $user_rated_positive . ',
					user_rated_negative = ' . $user_rated_negative . '
				WHERE user_id = ' . $user_id;
			$this->db->sql_query($sql);

			$result = array(
				'status'				=> 'ok',
				'user_can_minus'		=> $this->config['rate_enabled'] && ($user_id != ANONYMOUS) && ($user_id != $post['poster_id']) && (empty($this->config['rate_only_topics']) || $post['topic_first_post_id'] == $post['post_id']) && ($rate_time > 0 ? $rate_time + $post['post_time'] > time() : true) && ($user_rate['rate'] >= 0) && ($user_rate['rate'] != 0 && $this->config['rate_change_time'] > 0 ? $this->config['rate_change_time'] + $user_rate['rate_time'] > time() : true) && ($this->config['rate_no_negative'] ? $user_rate['rate'] != 0 : true) && $this->auth->acl_get('u_canminus'),
				'user_can_plus'			=> $this->config['rate_enabled'] && ($user_id != ANONYMOUS) && ($user_id != $post['poster_id']) && (empty($this->config['rate_only_topics']) || $post['topic_first_post_id'] == $post['post_id']) && ($rate_time > 0 ? $rate_time + $post['post_time'] > time() : true) && ($user_rate['rate'] <= 0) && ($user_rate['rate'] != 0 && $this->config['rate_change_time'] > 0 ? $this->config['rate_change_time'] + $user_rate['rate_time'] > time() : true) && ($this->config['rate_no_positive'] ? $user_rate['rate'] != 0 : true) && $this->auth->acl_get('u_canplus'),
				'user_rate'				=> $user_rate['rate'],
				'post_rating'			=> ($this->config['rate_no_positive'] ? 0 : $post_rating_positive) - ($this->config['rate_no_negative'] ? 0 : $post_rating_negative),
				'post_rating_negative'	=> $post_rating_negative,
				'post_rating_positive'	=> $post_rating_positive,
				'poster_id'				=> $post['poster_id'],
				'poster_rating'			=> ($this->config['rate_no_positive'] ? 0 : $poster_rating_positive) - ($this->config['rate_no_negative'] ? 0 : $poster_rating_negative),
				'poster_rating_negative'=> $poster_rating_negative,
				'poster_rating_positive'=> $poster_rating_positive,
				'user_id'				=> $user_id,
				'user_rated'			=> ($this->config['rate_no_positive'] ? 0 : $user_rated_positive) - ($this->config['rate_no_negative'] ? 0 : $user_rated_negative),
				'user_rated_negative'	=> $user_rated_negative,
				'user_rated_positive'	=> $user_rated_positive,
			);

			return $result;
		}
		catch (\phpbb\extension\exception $e)
		{
			return array('error' => $e->getMessage(), 'code' => $e->getCode());
		}
	}

	/**
	* Synchronization ratings
	*/
	public function resync_rates()
	{
		// Remove rates for nonexistent posts
		$sql = 'DELETE
			FROM r USING ' . POST_RATES_TABLE . ' r
			LEFT JOIN ' . POSTS_TABLE . ' p ON r.post_id = p.post_id
			WHERE p.post_id IS NULL';
		$this->db->sql_query($sql);

		// Remove rates from nonexistent users
		$sql = 'DELETE
			FROM r USING ' . POST_RATES_TABLE . ' r
			LEFT JOIN ' . USERS_TABLE . ' u ON r.user_id = u.user_id
			WHERE u.user_id IS NULL';
		$this->db->sql_query($sql);

		// Clear rating fields
		$this->db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_rated_negative = 0, user_rated_positive = 0, user_rating_negative = 0, user_rating_positive = 0');
		$this->db->sql_query('UPDATE ' . POSTS_TABLE . ' SET post_rating_negative = 0, post_rating_positive = 0');

		// Update user_rated_negative
		$sql = 'SELECT user_id, ABS(SUM(rate)) as rates
			FROM ' . POST_RATES_TABLE . '
			WHERE rate < 0
			GROUP BY user_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rated_negative = {$row['rates']} WHERE user_id = {$row['user_id']}");
		}
		$this->db->sql_freeresult($result);

		// Update user_rated_positive
		$sql = 'SELECT user_id, ABS(SUM(rate)) as rates
			FROM ' . POST_RATES_TABLE . '
			WHERE rate > 0
			GROUP BY user_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rated_positive = {$row['rates']} WHERE user_id = {$row['user_id']}");
		}
		$this->db->sql_freeresult($result);

		// Update user_rating_negative
		$sql = 'SELECT p.poster_id, ABS(SUM(r.rate)) AS rates
			FROM ' . POST_RATES_TABLE . ' r
			INNER JOIN ' . POSTS_TABLE . ' p ON r.post_id = p.post_id
			WHERE r.rate < 0
			GROUP BY p.poster_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rating_negative = {$row['rates']} WHERE user_id = {$row['poster_id']}");
		}
		$this->db->sql_freeresult($result);

		// Update user_rating_positive
		$sql = 'SELECT p.poster_id, ABS(SUM(r.rate)) AS rates
			FROM ' . POST_RATES_TABLE . ' r
			INNER JOIN ' . POSTS_TABLE . ' p ON r.post_id = p.post_id
			WHERE r.rate > 0
			GROUP BY p.poster_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rating_positive = {$row['rates']} WHERE user_id = {$row['poster_id']}");
		}
		$this->db->sql_freeresult($result);

		// Update post_rating_negative
		$sql = 'SELECT post_id, ABS(SUM(rate)) AS rates
			FROM ' . POST_RATES_TABLE . '
			WHERE rate < 0
			GROUP BY post_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . POSTS_TABLE . " SET post_rating_negative = {$row['rates']} WHERE post_id = {$row['post_id']}");
		}
		$this->db->sql_freeresult($result);

		// Update post_rating_positive
		$sql = 'SELECT post_id, ABS(SUM(rate)) AS rates
			FROM ' . POST_RATES_TABLE . '
			WHERE rate > 0
			GROUP BY post_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . POSTS_TABLE . " SET post_rating_positive = {$row['rates']} WHERE post_id = {$row['post_id']}");
		}
		$this->db->sql_freeresult($result);
	}

	public function remove_rates_batch($type, $id, $negative = true, $positive = true, $from_time = false, $to_time = false)
	{
		if (!$negative && !$positive) return;

		$sql = 'SELECT r.*, p.poster_id
			FROM ' . POST_RATES_TABLE . ' r
			LEFT JOIN ' . POSTS_TABLE . ' p ON r.post_id = p.post_id';

		switch ($type)
		{
			case 'user':
				$sql .= ' WHERE r.user_id = ' . $id;
			break;

			case 'post':
				$sql .= ' WHERE r.post_id = ' . $id;
			break;

			default:
				return;
			break;
		}

		if (!($negative && $positive))
		{
			if ($negative)
			{
				$sql .= ' AND r.rate < 0';
			}
			else
			{
				$sql .= ' AND r.rate > 0';
			}
		}

		$sql .= ($from_time ? ' AND r.rate_time >= ' . intval($from_time) : '');
		$sql .= ($to_time ? ' AND r.rate_time <= ' . intval($to_time) : '');

		$result = $this->db->sql_query($sql);

		while ($rate_row = $this->db->sql_fetchrow($result))
		{
			$this->remove_rate_row($rate_row);
		}

		$this->db->sql_freeresult($result);
	}

	private function remove_rate_row($rate_row)
	{
		if ($rate_row['rate'] < 0)
		{
			$rate = abs($rate_row['rate']);
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rated_negative = user_rated_negative - {$rate} WHERE user_id = {$rate_row['user_id']}");
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rating_negative = user_rating_negative - {$rate} WHERE user_id = {$rate_row['poster_id']}");
			$this->db->sql_query('UPDATE ' . POSTS_TABLE . " SET post_rating_negative = post_rating_negative - {$rate} WHERE post_id = {$rate_row['post_id']}");
		}
		else
		{
			$rate = abs($rate_row['rate']);
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rated_positive = user_rated_positive - {$rate} WHERE user_id = {$rate_row['user_id']}");
			$this->db->sql_query('UPDATE ' . USERS_TABLE . " SET user_rating_positive = user_rating_positive - {$rate} WHERE user_id = {$rate_row['poster_id']}");
			$this->db->sql_query('UPDATE ' . POSTS_TABLE . " SET post_rating_positive = post_rating_positive - {$rate} WHERE post_id = {$rate_row['post_id']}");
		}

		$sql = 'DELETE
			FROM ' . POST_RATES_TABLE . "
			WHERE user_id = {$rate_row['user_id']} AND post_id = {$rate_row['post_id']}";
		$result = $this->db->sql_query($sql);
	}
}
