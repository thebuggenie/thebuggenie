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
		if (mb_strlen($text) > $length)
		{
			$string = wordwrap($text, $length - 3);
			$text = mb_substr($string, 0, mb_strpos($string, "\n"));
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
	function tbg_formatTime($tstamp, $format = 0)
	{
		// offset the timestamp properly
		if (TBGSettings::getGMToffset() != 0)
			$tstamp += TBGSettings::getGMToffset() * 60 * 60;

		if ((TBGSettings::getUserTimezone() != 0) && TBGSettings::getUserTimezone() != 'sys')
			$tstamp += TBGSettings::getUserTimezone() * 60 * 60;
			
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
				$tstring = strftime(TBGContext::getI18n()->getDateTimeFormat(16), $tstamp);
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
				$tstring = (TBGContext::isCLI()) ? strftime('%a, %d %b %Y %H:%M:%S GMT', $tstamp) : strftime(TBGContext::getI18n()->getDateTimeFormat(17), $tstamp);
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
		return htmlentities($tstring, ENT_COMPAT, TBGContext::getI18n()->getCharset());
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
		if (tbg_isUTF8($str) && !mb_stristr(TBGContext::getI18n()->getCharset(), 'UTF-8'))
		{
			$str = utf8_decode($str);
		}
		
		if ($htmlentities)
		{
			$str = htmlentities($str, ENT_NOQUOTES+ENT_IGNORE, TBGContext::getI18n()->getCharset());
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

	/**
	 * Determine if a string valid regarding a specific syntax (email address, DNS name, IP...)
	 *
	 * @param string $str the string to be checked
	 * @param string $format the referal syntax 
	 * @param boolean $exact_match [option] set if the string must only contain this syntax (default=true)
	 * @param boolean $case_sensitive [option] set if the match is case sensitive (default=false)
	 * 
	 * @return boolean
	 */		
	function tbg_check_syntax($str, $format, $exact_match=true, $case_sensitive = false)
	{
		// based on RFC 2822
		$ip_regex = '\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b';
		$dns_regex = '(' . $ip_regex . '|(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\]))';
		$addr_regex = '(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@' . $dns_regex;
		$serv_regex = '(ssl:\/\/)?(' . $dns_regex . ')';
		// list of supported character sets based on PHP doc : http://www.php.net/manual/en/function.htmlentities.php
		$charset_regex = '((ISO-?8859-1)|(ISO-?8859-15)|(UTF-8)|((cp|ibm)?866)|((cp|Windows-|win-)+1251)|((cp|Windows-)+1252)|(KOI8-?RU?)|(BIG5)|(950)|(GB2312)|(936)|(BIG5-HKSCS)|(S(hift_)?JIS)|(932)|(EUC-?JP))';
		
		switch ($format)
		{
			case "IP":
				$regex = $ip_regex;
				break;
			case "DNSNAME":
				$regex = $dns_regex;
				break;
			case "EMAIL":
				$regex = $addr_regex;
				break;
			case "MAILSERVER":
				$regex = $serv_regex;
				break;
			case "CHARSET":
				$regex = $charset_regex;
				break;		
		}
		return preg_match("/" . ($exact_match ? '^' : '') . $regex . ($exact_match ? '$' : '') . "/" . ($case_sensitive ? '' : 'i'), $str);
	}

	function tbg_get_breadcrumblinks($type, $project = null)
	{
		return TBGContext::getResponse()->getPredefinedBreadcrumbLinks($type, $project);
	}

	function tbg_get_javascripts()
	{
		$tbg_response = TBGContext::getResponse();
		$tbg_response->addJavascript('prototype.js', true, true);
		$tbg_response->addJavascript('jquery-1.6.2.min.js', true, true);
		$tbg_response->addJavascript('builder.js');
		$tbg_response->addJavascript('effects.js');
		$tbg_response->addJavascript('dragdrop.js');
		$tbg_response->addJavascript('controls.js');
		$tbg_response->addJavascript('jquery.markitup.js');
		$tbg_response->addJavascript('thebuggenie.js');
		$tbg_response->addJavascript('tablekit.js');

		$jsstrings = array();
		$sepjs = array();

		// Add scripts to minify and non-minify lists
		foreach ($tbg_response->getJavascripts() as $script => $minify)
		{
			if ($minify == true && file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $script))
				$jsstrings[] = 'js/'.$script;
			else
				$sepjs[] = $script;
		}

		$jsstrings = join(',', $jsstrings);

		return array($jsstrings, $sepjs);
	}

	function tbg_get_stylesheets()
	{
		$tbg_response = TBGContext::getResponse();
		$cssstrings = array();
		$sepcss = array();

		// Add stylesheets to minify and non-minify lists
		foreach ($tbg_response->getStylesheets() as $stylesheet => $minify)
		{
			if ($minify == true && file_exists(THEBUGGENIE_PATH . THEBUGGENIE_PUBLIC_FOLDER_NAME . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . TBGSettings::getThemeName() . DIRECTORY_SEPARATOR .$stylesheet))
				$cssstrings[] = 'themes/'.TBGSettings::getThemeName().'/'.$stylesheet;
			else
				$sepcss[] = $stylesheet;
		}

		$cssstrings = join(',', $cssstrings);

		return array($cssstrings, $sepcss);
	}
	
