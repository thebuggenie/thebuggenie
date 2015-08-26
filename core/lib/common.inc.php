<?php

    /**
     * Common helper functions
     */

    /**
     * Run the I18n translation function
     *
     * @param string $text the text to translate
     * @param array $replacements [optional] replacements
     *
     * @return string
     */
    function __($text, $replacements = array(), $html_decode = false)
    {
        return \thebuggenie\core\framework\Context::getI18n()->__($text, $replacements, $html_decode);
    }

    /**
     * Template escaping translation function
     *
     * @param string $text the text to translate
     * @param array $replacements [optional] replacements
     *
     * @return string
     */
    function __e($text, $replacements = array())
    {
        return \thebuggenie\core\framework\Context::getI18n()->__e($text, $replacements);
    }

    /**
     * Template escaping function without translation
     *
     * @param string $text the text to translate
     *
     * @return string
     */
    function tbg_template_escape($text)
    {
        return htmlentities($text, ENT_QUOTES, \thebuggenie\core\framework\Context::getI18n()->getCharset());
    }

    /**
     * Truncate a string, and optionally add padding dots
     *
     * @param string $text
     * @param integer $length
     *
     * @return string The truncated string
     */
    function tbg_truncateText($text, $length = 300)
    {
        if (mb_strlen($text) > $length)
        {
            $string = wordwrap($text, $length - 3, '|||WORDWRAP|||');
            $text = mb_substr($string, 0, mb_strpos($string, "|||WORDWRAP|||")) . '...';
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

    function day_delta($tstamp, $tzoffset)
    {
        $mdy = explode(':', date('m:d:Y', time() + $tzoffset));
        $midnight = mktime(0, 0, 0, $mdy[0], $mdy[1], $mdy[2]);
        return floor(($tstamp - $midnight) / 24 / 60 / 60);
    }

    /**
     * Returns a formatted string of the given timestamp
     *
     * @param integer $tstamp the timestamp to format
     * @param integer $format [optional] the format
     * @param boolean $skipusertimestamp ignore user timestamp
     * @param boolean $skiptimestamp ignore rebasing timestamp
     */
    function tbg_formatTime($tstamp, $format = 0, $skipusertimestamp = false, $skiptimestamp = false)
    {
        $tzoffset = 0;
        // offset the timestamp properly
        if (!$skiptimestamp)
        {
            $tzoffset = tbg_get_timezone_offset($skipusertimestamp);
            $tstamp += $tzoffset;
        }

        switch ($format)
        {
            case 1:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(1), $tstamp);
                break;
            case 2:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(2), $tstamp);
                break;
            case 3:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(3), $tstamp);
                break;
            case 4:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(4), $tstamp);
                break;
            case 5:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(5), $tstamp);
                break;
            case 6:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(6), $tstamp);
                break;
            case 7:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(7), $tstamp);
                break;
            case 8:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(8), $tstamp);
                break;
            case 9:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(9), $tstamp);
                break;
            case 10:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(10), $tstamp);
                break;
            case 11:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(9), $tstamp);
                break;
            case 12:
                $tstring = '';
                $days = day_delta($tstamp, $tzoffset);
                if ($days == 0)
                {
                    $tstring .= __('Today') . ', ';
                }
                elseif ($days == -1)
                {
                    $tstring .= __('Yesterday') . ', ';
                }
                elseif ($days == 1)
                {
                    $tstring .= __('Tomorrow') . ', ';
                }
                else
                {
                    $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(12) . ', ', $tstamp);
                }
                $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(14), $tstamp);
                break;
            case 13:
                $tstring = '';
                $days = day_delta($tstamp, $tzoffset);
                if ($days == 0)
                {
                    //$tstring .= __('Today') . ', ';
                }
                elseif ($days == -1)
                {
                    $tstring .= __('Yesterday') . ', ';
                }
                elseif ($days == 1)
                {
                    $tstring .= __('Tomorrow') . ', ';
                }
                else
                {
                    $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(12) . ', ', $tstamp);
                }
                $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(14), $tstamp);
                break;
            case 14:
                $tstring = '';
                $days = day_delta($tstamp, $tzoffset);
                if ($days == 0)
                {
                    $tstring .= __('Today');
                }
                elseif ($days == -1)
                {
                    $tstring .= __('Yesterday');
                }
                elseif ($days == 1)
                {
                    $tstring .= __('Tomorrow');
                }
                else
                {
                    $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(12), $tstamp);
                }
                break;
            case 15:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(11), $tstamp);
                break;
            case 16:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(12), $tstamp);
                break;
            case 17:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(13), $tstamp);
                break;
            case 18:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(16), $tstamp);
                break;
            case 19:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(14), $tstamp);
                break;
            case 20:
                $tstring = '';
                $days = day_delta($tstamp, $tzoffset);
                if ($days == 0)
                {
                    $tstring .= __('Today') . ' (' . strftime('%H:%M', $tstamp) . ')';
                }
                elseif ($days == -1)
                {
                    $tstring .= __('Yesterday') . ' (' . strftime('%H:%M', $tstamp) . ')';
                }
                elseif ($days == 1)
                {
                    $tstring .= __('Tomorrow') . ' (' . strftime('%H:%M', $tstamp) . ')';
                }
                else
                {
                    $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(15), $tstamp);
                }
                break;
            case 21:
                $tstring = strftime('%a, %d %b %Y %H:%M:%S ', $tstamp);
//                if (!$skipusertimestamp && \thebuggenie\core\framework\Settings::getUserTimezone() != 'sys')
//                {
//                    if (\thebuggenie\core\framework\Settings::getUserTimezone() != 0)
//                    {
//                        $offset = \thebuggenie\core\framework\Settings::getUserTimezone() * 100;
//                    }
//                }
//                elseif (\thebuggenie\core\framework\Settings::getGMToffset() != 0)
//                {
//                    $offset = \thebuggenie\core\framework\Settings::getGMToffset() * 100;
//                }
//
//                if (!isset($offset))
//                {
//                    $offset = 'GMT';
//                }
//
//                if ($offset == 0)
//                {
//                    $offset = 'GMT';
//                }
//                elseif ($offset != 'GMT')
//                {
//                    $negative = false;
//                    if (strstr($offset, '-'))
//                    {
//                        $offset = trim($offset, '-');
//                        $negative = true;
//                    }
//
//                    if ($offset < 1000)
//                    {
//                        $offset = '0'.$offset;
//                    }
//
//                    if ($negative)
//                    {
//                        $offset = '-'.$offset;
//                    }
//                    else
//                    {
//                        $offset = '+'.$offset;
//                    }
//                }
//
//                $tstring .= $offset;
                return ($tstring);
                break;
            case 22:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(15), $tstamp);
                break;
            case 23:
                $tstring = '';
                $days = day_delta($tstamp, $tzoffset);
                if ($days == 0)
                {
                    $tstring .= __('Today');
                }
                elseif ($days == -1)
                {
                    $tstring .= __('Yesterday');
                }
                elseif ($days == 1)
                {
                    $tstring .= __('Tomorrow');
                }
                else
                {
                    $tstring .= strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(15), $tstamp);
                }
                break;
            case 24:
                $tstring = strftime(\thebuggenie\core\framework\Context::getI18n()->getDateTimeFormat(18), $tstamp);
                break;
            default:
                return $tstamp;
        }
        return $tstring;
    }

    /**
     * Return parsed text, based on provided syntax and options
     *
     * @param string $text The text that should be parsed
     * @param boolean $toc [optional] Whether a TOC should be generated and included
     * @param mixed $article_id [optional] An article id to use as an element id prefix
     * @param array $options [optional] Parser options
     * @param integer $syntax [optional] Which parser syntax to use
     *
     * @return string
     */
    function tbg_parse_text($text, $toc = false, $article_id = null, $options = array(), $syntax = \thebuggenie\core\framework\Settings::SYNTAX_MW)
    {
        switch ($syntax)
        {
            default:
            case \thebuggenie\core\framework\Settings::SYNTAX_PT:
                $options = array('plain' => true);
            case \thebuggenie\core\framework\Settings::SYNTAX_MW:
                $wiki_parser = new \thebuggenie\core\helpers\TextParser($text, $toc, 'article_' . $article_id);
                foreach ($options as $option => $value)
                {
                    $wiki_parser->setOption($option, $value);
                }
                $text = $wiki_parser->getParsedText();
                break;
            case \thebuggenie\core\framework\Settings::SYNTAX_MD:
                $parser = new \thebuggenie\core\helpers\TextParserMarkdown();
                $text = $parser->transform($text);
                break;
        }

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
        if (tbg_isUTF8($str) && !mb_stristr(\thebuggenie\core\framework\Context::getI18n()->getCharset(), 'UTF-8'))
        {
            $str = utf8_decode($str);
        }

        if ($htmlentities)
        {
            $str = htmlentities($str, ENT_NOQUOTES+ENT_IGNORE, \thebuggenie\core\framework\Context::getI18n()->getCharset());
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
        $addr_regex = '(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@' . $dns_regex;
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
        return \thebuggenie\core\framework\Context::getResponse()->getPredefinedBreadcrumbLinks($type, $project);
    }

    function tbg_get_pagename($page)
    {
        $links = tbg_get_breadcrumblinks('project_summary', \thebuggenie\core\framework\Context::getCurrentProject());
        return (isset($links[$page]) && $page != 'project_issues') ? $links[$page]['title'] : __('Dashboard');
    }

    function tbg_get_javascripts()
    {
        return \thebuggenie\core\framework\Context::getResponse()->getJavascripts();
    }

    function tbg_get_stylesheets()
    {
        return \thebuggenie\core\framework\Context::getResponse()->getStylesheets();
    }

    function tbg_get_timezone_offset($skipusertimestamp = false)
    {
        // offset the timestamp properly
        if (!$skipusertimestamp)
        {
            $tz = \thebuggenie\core\framework\Context::getUser()->getTimezone();
            $tstamp = $tz->getOffset(new DateTime(null, \thebuggenie\core\framework\Settings::getServerTimezone()));
        }
        else
        {
            $tstamp = \thebuggenie\core\framework\Settings::getServerTimezone()->getOffset(new DateTime('GMT'));
        }
        return $tstamp;
    }

    function tbg_get_timezones()
    {
        return \thebuggenie\core\framework\I18n::getTimezones();
    }

    function tbg_hex_to_rgb($hex)
    {
        $hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
        $rgb = array();
        if (strlen($hex) == 6)
        {
            $color = hexdec($hex);
            $rgb['r'] = 0xFF & ($color >> 0x10);
            $rgb['g'] = 0xFF & ($color >> 0x8);
            $rgb['b'] = 0xFF & $color;
        }
        elseif (strlen($hex) == 3)
        {
            $rgb['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        }
        else
        {
            return false; //Invalid hex color code
        }
        return $rgb;
    }

    function tbg_get_userstate_image(\thebuggenie\core\entities\User $user)
    {
        switch (true)
        {
            case $user->isOffline():
                return image_tag('user-offline.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isBusy():
            case $user->getState()->isUnavailable():
                return image_tag('user-busy.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isAbsent():
                return image_tag('user-invisible.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isInMeeting():
                return image_tag('user-away-extended.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isUnavailable():
                return image_tag('user-away.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
            default:
                return image_tag('user-online.png', array('class' => 'userstate', 'title' => __($user->getState()->getName())));
                break;
        }
    }

    /**
     * Returns a boolean value to determine if a url is a youtube link
     *
     * @param string $url URL
     *
     * @return boolean
     */
    function tbg_youtube_link($url) //Ticket #2308
    {
        $is_youtube = false;

        // check to see if video contains the shortened youtube link
        // if so convert it to the long link
        if (preg_match("/(youtu\.be)|(youtube\.com)/", $url))
        {
            $is_youtube = true;
        }

        return $is_youtube;
    }

    /**
     * Returns a properly formatted youtube link
     *
     * @param string $url URL
     *
     * @return string
     */
    function tbg_youtube_prepare_link($url) //Ticket #2308
    {
        // check to see if video contains the shortened youtube link
        // if so convert it to the long link
        if (preg_match("/youtu\.be/", $url))
        {
            $url = preg_replace("/youtu\.be/", "youtube.com/embed", $url);
        }

        // check to see if the http(s): is included
        // if so remove http: or https: from the url link
        if (preg_match("/http(s)?\:/", $url))
        {
            $url = preg_replace("/http(s)?\:/", "", $url);
        }

        // check to see if the // now appears at the front of the link
        // if not add it
        if (!preg_match("/^\/\//", $url))
        {
            $url = "//" . $url;
        }

        // check to see if video contains the watch param
        // if so convert it to an embedded link
        if (preg_match("/watch\?v\=(.*?)/", $url))
        {
            $url = preg_replace("/watch\?v\=/", "embed/", $url);
        }

        return $url;
    }
