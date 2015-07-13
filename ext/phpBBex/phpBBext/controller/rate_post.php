<?php
/**
*
* This file is part of the phpBBex.
*
* @copyright (c) phpBBex <http://phpbbex.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpBBex\phpBBext\controller;

use Symfony\Component\HttpFoundation\Response;

class rate_post
{
	/**
	* phpBB user
	*
	* @var \phpbb\user
	*/
	protected $user;

	/**
	* Rate post
	*
	* @var \phpbb\rating
	*/
	protected $rate;

	/**
	* Constructor
	*
	* @param	\phpbb\user			$user
	* @param	\phpbb\rating\rate	$rate
	*/
	public function __construct(\phpbb\user $user, \phpbb\rating\rate $rate)
	{
		$this->user = $user;
		$this->rate = $rate;
	}

	public function main($action, $post, $token)
	{
		$response = $this->rate->rate_post($post, $this->user->data['user_id'], $action, $token);
		$json_response = new \phpbb\json_response;
		$json_response->set_expire(false);
		$json_response->send($response);
	}
}
