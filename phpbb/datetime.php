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
* phpBB custom extensions to the PHP DateTime class
* This handles the relative formats phpBB employs
*/
class datetime extends \DateTime
{
	/**
	* String used to wrap the date segment which should be replaced by today/tomorrow/yesterday
	*/
	const RELATIVE_WRAPPER = '|';

	/**
	* @var user User who is the context for this DateTime instance
	*/
	protected $user;

	/**
	* @var array Date formats are preprocessed by phpBB, to save constant recalculation they are cached.
	*/
	static protected $format_cache = array();

	/**
	* Constructs a new instance of \phpbb\datetime, expanded to include an argument to inject
	* the user context and modify the timezone to the users selected timezone if one is not set.
	*
	* @param user $user object for context.
	* @param string $time String in a format accepted by strtotime().
	* @param \DateTimeZone $timezone Time zone of the time.
	*/
	public function __construct($user, $time = 'now', \DateTimeZone $timezone = null)
	{
		$this->user	= $user;
		$timezone	= $timezone ?: $this->user->timezone;

		parent::__construct($time, $timezone);
	}

	/**
	* Formats the current date time into the specified format
	*
	* @param string $format Optional format to use for output, defaults to users chosen format
	* @param boolean $force_absolute Force output of a non relative date
	* @param boolean $notime If true, the date format will be short
	* @return string Formatted date time
	*/
	public function format($format = '', $force_absolute = false, $notime = false)
	{
		$format		= $format ? $format : $this->user->date_format;
		$format		= self::format_cache($format, $this->user, $notime);
		$relative	= ($format['is_short'] && !$force_absolute);
		$now		= new self($this->user, 'now', $this->user->timezone);

		$timestamp	= $this->getTimestamp();
		$now_ts		= $now->getTimeStamp();

		$delta		= $now_ts - $timestamp;

		if ($relative)
		{
			/*
			* Check the delta is less than or equal to 1 hour
			* and the delta not more than a minute in the past
			* and the delta is either greater than -5 seconds or timestamp
			* and current time are of the same minute (they must be in the same hour already)
			* finally check that relative dates are supported by the language pack
			*/
			if ($delta <= 3600 && $delta > -60 &&
				($delta >= -5 || (($now_ts / 60) % 60) == (($timestamp / 60) % 60))
				&& isset($this->user->lang['datetime']['AGO']))
			{
				return $this->user->lang(array('datetime', 'AGO'), max(0, (int) floor($delta / 60)));
			}
			else
			{
				$midnight = clone $now;
				$midnight->setTime(0, 0, 0);

				$midnight	= $midnight->getTimestamp();

				if ($timestamp <= $midnight + 2 * 86400)
				{
					$day = false;

					if ($timestamp > $midnight + 86400)
					{
						$day = 'TOMORROW';
					}
					else if ($timestamp > $midnight)
					{
						$day = 'TODAY';
					}
					else if ($timestamp > $midnight - 86400)
					{
						$day = 'YESTERDAY';
					}

					if ($day !== false)
					{
						// Format using the short formatting and finally swap out the relative token placeholder with the correct value
						return str_replace(self::RELATIVE_WRAPPER . self::RELATIVE_WRAPPER, $this->user->lang['datetime'][$day], strtr(parent::format($format['format_short']), $format['lang']));
					}
				}
			}
		}

		return strtr(parent::format($format['format_long']), $format['lang']);
	}

	/**
	* Magic method to convert DateTime object to string
	*
	* @return string Formatted date time, according to the users default settings.
	*/
	public function __toString()
	{
		return $this->format();
	}

	/**
	* Pre-processes the specified date format
	*
	* @param string $format Output format
	* @param user $user User object to use for localisation
	* @param boolean $notime If true, the date format will be short
	* @return array Processed date format
	*/
	static protected function format_cache($format, $user, $notime)
	{
		$lang = $user->lang_name;

		static $format_cached = array();

		if (!isset($format_cached[$format]))
		{
			$format_cached[$format] = array(
				'full'		=> str_replace(array('{', '}'), '', $format),
				'notime'	=> str_replace(array('{', '}'), '', preg_replace('#{.*?}#i', '', $format)),
			);
		}
		$format = $format_cached[$format][$notime?'notime':'full'];

		if (!isset(self::$format_cache[$lang]))
		{
			self::$format_cache[$lang] = array();
		}

		if (!isset(self::$format_cache[$lang][$format]))
		{
			// Is the user requesting a friendly date format (i.e. 'Today 12:42')?
			self::$format_cache[$lang][$format] = array(
				'is_short'		=> strpos($format, self::RELATIVE_WRAPPER) !== false,
				'format_short'	=> substr($format, 0, strpos($format, self::RELATIVE_WRAPPER)) . self::RELATIVE_WRAPPER . self::RELATIVE_WRAPPER . substr(strrchr($format, self::RELATIVE_WRAPPER), 1),
				'format_long'	=> str_replace(self::RELATIVE_WRAPPER, '', $format),
				'lang'			=> array_filter($user->lang['datetime'], 'is_string'),
			);

			// Short representation of month in format? Some languages use different terms for the long and short format of May
			if ((strpos($format, '\M') === false && strpos($format, 'M') !== false) || (strpos($format, '\r') === false && strpos($format, 'r') !== false))
			{
				self::$format_cache[$lang][$format]['lang']['May'] = $user->lang['datetime']['May_short'];
			}
		}

		return self::$format_cache[$lang][$format];
	}

	/**
	* This and the following functions is based on Delta_Russian class created by Dmitry Koterov, http://forum.dklab.ru
	* Makes the spellable phrase.
	*
	* @return string Formatted date time delta
	*/
	static public function get_verbal($first_time, $last_time, $accuracy = false, $max_parts = false, $keep_zeros = false)
	{
		global $user;

		if ($first_time - $last_time === 0)
		{
			return $user->lang('D_SECONDS', 0);
		}

		// Solve data delta
		$delta = self::getdelta($first_time, $last_time);
		if (!$delta)
		{
			return false;
		}

		// Make spellable phrase.
		$parts = array();
		$parts_count = 0;
		foreach (array_reverse($delta) as $measure => $value) 
		{
			if ($max_parts && $max_parts <= $parts_count)
			{
				break;
			}
			if (!$value && (!$keep_zeros || !$parts_count)) 
			{
				if ($measure !== $accuracy)
				{
					if ($parts_count) $parts_count++;
					continue;
				}
				else if (count($parts))
				{
					break;
				}
			}
			$parts_count++;
			$parts[] = $user->lang('D_' . strtoupper($measure), $value);
			if ($measure === $accuracy)
			{
				break;
			}
		}
		return join(' ', $parts);
	}

	/**
	* Returns the associative array with date deltas.
	*
	* @param integer $first First date
	* @param integer $last Last date
	* @return array Processed date deltas
	*/
	static private function getdelta($first, $last)
	{
		if ($last < $first) return false;

		// Solve H:M:S part.
		$hms = ($last - $first) % (3600 * 24);
		$delta['seconds'] = $hms % 60;
		$delta['minutes'] = floor($hms/60) % 60;
		$delta['hours']   = floor($hms/3600) % 60;

		// Now work only with date, delta time = 0.
		$last -= $hms;
		$f = getdate($first);
		$l = getdate($last); // the same daytime as $first!

		$dYear = $dMon = $dDay = 0;

		// Delta day. Is negative, month overlapping.
		$dDay += $l['mday'] - $f['mday'];
		if ($dDay < 0) 
		{
			$monlen = self::monthlength(date("Y", $first), date("m", $first));
			$dDay += $monlen;
			$dMon--;
		}
		$delta['mday'] = $dDay;

		// Delta month. If negative, year overlapping.
		$dMon += $l['mon'] - $f['mon'];
		if ($dMon < 0) 
		{
			$dMon += 12;
			$dYear --;
		}
		$delta['mon'] = $dMon;

		// Delta year.
		$dYear += $l['year'] - $f['year'];
		$delta['year'] = $dYear;
		
		return $delta;
	}

	/**
	* Returns the length (in days) of the specified month.
	*
	* @param integer $year Year
	* @param integer $mon Month
	* @return integer Length of the month
	*/
	static private function monthlength($year, $mon)
	{
		$l = 28;
		while (checkdate($mon, $l+1, $year))
		{
			$l++;
		}
		return $l;
	}
}
