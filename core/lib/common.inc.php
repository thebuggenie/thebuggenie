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
	function __($text, $replacements = array(), $html_decode = false)
	{
		return TBGContext::getI18n()->__($text, $replacements, $html_decode);
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
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(1), $tstamp);
				break;
			case 2:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(2), $tstamp);
				break;
			case 3:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(3), $tstamp);
				break;
			case 4:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(4), $tstamp);
				break;
			case 5:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(5), $tstamp);
				break;
			case 6:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(6), $tstamp);
				break;
			case 7:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(7), $tstamp);
				break;
			case 8:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(8), $tstamp);
				break;
			case 9:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(9), $tstamp);
				break;
			case 10:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(10), $tstamp);
				break;
			case 11:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(9), $tstamp);
				break;
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
					$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(12) . ', ', $tstamp);
				}
				$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(14), $tstamp);
				break;
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
					$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(12) . ', ', $tstamp);
				}
				$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(14), $tstamp);
				break;
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
					$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(12), $tstamp);
				}
				break;
			case 15:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(11), $tstamp);
				break;
			case 16:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(12), $tstamp);
				break;
			case 17:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(13), $tstamp);
				break;
			case 18:
				$old = date_default_timezone_get();
				date_default_timezone_set('UTC');
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(16), $tstamp);
				date_default_timezone_set($old);
				break;
			case 19:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(14), $tstamp);
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
					$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(15), $tstamp);
				}
				break;
			case 21:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(17), $tstamp);
				if (TBGContext::getUser()->getTimezone() > 0) $tstring .= '+';
				if (TBGContext::getUser()->getTimezone() < 0) $tstring .= '-';
				if (TBGContext::getUser()->getTimezone() != 0) $tstring .= TBGContext::getUser()->getTimezone();
				break;
			case 22:
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(15), $tstamp);
				break;
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
					$tstring .= strftime(TBGContext::getI18n()->getDateTimeFormat(15), $tstamp);
				}
				break;
			default:
				return $tstamp;
		}
		return htmlentities($tstring);
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

	/**
	 * Returns an ISO-8859-1 encoded string if UTF-8 encoded and current charset not UTF-8
	 *
	 * @param string $str the encode string
	 * @param boolean $htmlentities [optional] whether to convert applicable characters to HTML entities
	 * 
	 * @return string
	 */
	function tbg_decodeUTF8($str, $htmlentities = false)
	{
		if (tbg_isUTF8($str) && !stristr(TBGContext::getI18n()->getCharset(), 'UTF-8'))
		{
			$str = utf8_decode($str);
		}
		
		if ($htmlentities)
		{
			$str = htmlentities($str, ENT_NOQUOTES, TBGContext::getI18n()->getCharset());
		}
		return $str;
	}
	
	/**
	 * Determine if a string is UTF-8 encoded
	 * @filesource http://www.php.net/manual/en/function.mb-detect-encoding.php#68607
	 *
	 * @param string $str the string
	 * 
	 * @return boolean
	 */	
	function tbg_isUTF8($str)
	{
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]
        |\xE0[\xA0-\xBF][\x80-\xBF]
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
        |\xED[\x80-\x9F][\x80-\xBF]
        |\xF0[\x90-\xBF][\x80-\xBF]{2}
        |[\xF1-\xF3][\x80-\xBF]{3}
        |\xF4[\x80-\x8F][\x80-\xBF]{2}
        )+%xs', $str);
	}
