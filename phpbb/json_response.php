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

namespace phpbb;

/**
* JSON class
*/
class json_response
{
	/**
	* @var	int/bool	The term of caching
	*/
	protected $expire;

	/**
	* Constructor
	*/
	public function __construct()
	{
		$this->expire = true;
	}

	/**
	 * Send the data to the client and exit the script.
	 *
	 * @param array $data Any additional data to send.
	 * @param bool $exit Will exit the script if true.
	 */
	public function send($data, $exit = true)
	{
		header('Content-Type: application/json');

		if ($this->expire === 0 || $this->expire === false)
		{
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Expires: Sat, 24 Oct 1987 07:00:00 GMT');
		}
		elseif (is_int($this->expire))
		{
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $this->expire));
		}

		echo json_encode($data);

		if ($exit)
		{
			garbage_collection();
			exit_handler();
		}
	}

	/**
	 * Set term of caching
	 *
	 * @param	int/bool	$expire		The term of caching
	 */
	public function set_expire($expire = true)
	{
		$this->expire = $expire;
	}
}
