<?php

	/**
	 * Common helper functions
	 */

	/**
	 * Run the I18n translation function
	 *
	 * @param string $text the text to translate
	 * @param array $replacements[optional] replacements
	 *
	 * @return string
	 */
	function __($text, $replacements = array())
	{
		return TBGContext::getI18n()->__($text, $replacements);
	}

	/**
	 * Truncate a string, and optionally add padding dots
	 * 
	 * @param string $text
	 * @param integer $length
	 * @param boolean $add_dots[optional] defaults to true
	 * 
	 * @return string The truncated string
	 */
	function tbg_truncateText($text, $length, $add_dots = true)
	{
		if (strlen($text) > $length)
		{
			$string = wordwrap($text, $length - 3);
			$text = substr($string, 0, strpos($string, "\n"));
			if ($add_dots) $text .= '...';
		}
		return $text;
	}

	/**
	 * Returns a random number
	 * 
	 * @return integer
	 */
	function tbg_printRandomNumber()
	{
		$randomNumber = "";

		for($cc = 1; $cc <= 6; $cc++)
		{
			$rndNo = mt_rand(0,9);
			$randomNumber .= $rndNo;
		}

		return $randomNumber;
	}

	/**
	 * Returns a formatted string of the given timestamp
	 *
	 * @param integer $tstamp the timestamp to format
	 * @param integer $format[optional] the format
	 * @param integer $skiptimestamp
	 */
	function tbg_formatTime($tstamp, $format = 0, $skiptimestamp = 0)
	{
		if (TBGSettings::getUserTimezone() !== null && $skiptimestamp == 0)
		{
			$tstamp += -(TBGSettings::getGMToffset() * 60 * 60);
			$tstamp += (TBGSettings::getUserTimezone() * 60 * 60);
		}
		switch ($format)
		{
			case 1:
				return strftime("%H:%M - %a %b %d, %Y", $tstamp);
			case 2:
				return strftime("%H:%M - %a %d.m, %Y", $tstamp);
			case 3:
				return strftime("%a %b %d %H:%M", $tstamp);
			case 4:
				return strftime("%b %d %H:%M", $tstamp);
			case 5:
				return strftime("%B %d, %Y", $tstamp);
			case 6:
				return strftime("%B %d, %Y (%H:%M)", $tstamp);
			case 7:
				return strftime("%A %d %B, %Y (%H:%M)", $tstamp);
			case 8:
				return strftime("%b %d, %Y %H:%M", $tstamp);
			case 9:
				return strftime("%b %d, %Y (%H:%M)", $tstamp);
			case 10:
				return strftime("%b %d, %Y - %H:%M", $tstamp);
			case 11:
				return strftime("%b %d, %Y (%H:%M)", $tstamp);
			case 12:
				$tstring = '';
				if (date('dmY', $tstamp) == date('dmY'))
				{
					$tstring .= __('Today') . ', ';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
				{
					$tstring .= __('Yesterday') . ', ';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
				{
					$tstring .= __('Tomorrow') . ', ';
				}
				else
				{
					$tstring .= strftime("%b %d, ", $tstamp);
				}
				$tstring .= strftime('%H:%M', $tstamp);
				return $tstring;
			case 13:
				$tstring = '';
				if (date('dmY', $tstamp) == date('dmY'))
				{
					//$tstring .= __('Today') . ', ';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
				{
					$tstring .= __('Yesterday') . ', ';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
				{
					$tstring .= __('Tomorrow') . ', ';
				}
				else
				{
					$tstring .= strftime("%b %d, ", $tstamp);
				}
				$tstring .= strftime('%H:%M', $tstamp);
				return $tstring;
			case 14:
				$tstring = '';
				if (date('dmY', $tstamp) == date('dmY'))
				{
					$tstring .= __('Today');
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
				{
					$tstring .= __('Yesterday');
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
				{
					$tstring .= __('Tomorrow');
				}
				else
				{
					$tstring .= strftime("%b %d", $tstamp);
				}
				return $tstring;
			case 15:
				$tstring = strftime('%B', $tstamp);
				return $tstring;
			case 16:
				$tstring = strftime('%b %d', $tstamp);
				return $tstring;
			case 17:
				$tstring = strftime('%a', $tstamp);
				return $tstring;
			case 18:
				$old = date_default_timezone_get();
				date_default_timezone_set('UTC');
				$date = date('G\h i\m', $tstamp);
				date_default_timezone_set($old);
				return $date;
			case 19:
				$tstring = strftime('%H:%M', $tstamp);
				return $tstring;
				break;
			case 20:
				$tstring = '';
				if (date('dmY', $tstamp) == date('dmY'))
				{
					$tstring .= __('Today') . ' (' . strftime('%H:%M', $tstamp) . ')';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
				{
					$tstring .= __('Yesterday') . ' (' . strftime('%H:%M', $tstamp) . ')';
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
				{
					$tstring .= __('Tomorrow') . ' (' . strftime('%H:%M', $tstamp) . ')';
				}
				else
				{
					$tstring .= strftime("%b %d, %Y", $tstamp);
				}
				return $tstring;
			case 21:
				$time = strftime("%a, %d %b %Y %H:%M:%S GMT", $tstamp);
				if (TBGContext::getUser()->getTimezone() > 0) $time .= '+';
				if (TBGContext::getUser()->getTimezone() < 0) $time .= '-';
				if (TBGContext::getUser()->getTimezone() != 0) $time .= TBGContext::getUser()->getTimezone();
				return $time;
				break;
			case 22:
				return strftime("%b %d, %Y", $tstamp);
			case 23:
				$tstring = '';
				if (date('dmY', $tstamp) == date('dmY'))
				{
					$tstring .= __('Today');
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') - 1))))
				{
					$tstring .= __('Yesterday');
				}
				elseif (date('dmY', $tstamp) == date('dmY', mktime(0, 0, 0, date('m'), (date('d') + 1))))
				{
					$tstring .= __('Tomorrow');
				}
				else
				{
					$tstring .= strftime("%b %d, %Y", $tstamp);
				}
				return $tstring;
			default:
				return $tstamp;
		}
	}

	function tbg_parse_text($text, $toc = false, $article_id = null, $options = array())
	{
		// Perform wiki parsing
		$wiki_parser = new TBGTextParser($text, $toc, 'article_' . $article_id);
		foreach ($options as $option => $value)
		{
			$wiki_parser->setOption($option, $value);
		}
		$text = $wiki_parser->getParsedText();

		return $text;
	}

