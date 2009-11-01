<?php

	function bugs_sanitize_string($string)
	{
		if (get_magic_quotes_gpc() == 1)
		{
			$string = stripslashes($string);
		}
		return htmlspecialchars($string, ENT_QUOTES, BUGScontext::getI18n()->getCharset());
	}
	
	/**
	 *
	 * Returns a formatted string of the given timestamp
	 *
	 */
	function bugs_formatTime($tstamp, $format = 0, $skiptimestamp = 0)
	{
		if (BUGSsettings::getUserTimezone() !== null && $skiptimestamp == 0)
		{
			$tstamp += -(BUGSsettings::getGMToffset() * 60 * 60);
			$tstamp += (BUGSsettings::getUserTimezone() * 60 * 60);
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
			default:
				return $tstamp;
		}
	}

	/**
	 *
	 * Turn smileys into img tags except in code blocks
	 *
	 */
	function bugs_smileydecode($text)
	{
		$smileys = '\:\(|\:-\(|\:\)|\:-\)|8\)|8-\)|B\)|B-\)|\:-\/|\:D|\:-D|\:P|\:-P|\(\!\)|\(\?\)';
		$code_tag = '\[code\].*\[\/code\]';
		$end_code_tag = '\[\/code\]';

		return preg_replace("/($smileys)((?=.*$code_tag)|(?!.*$end_code_tag))/ei",'bugs_showsmiley("$1");',$text);
	}

	/**
	 *
	 * Return a smiley img tag for a specific smiley
	 *
	 */
	function bugs_showsmiley($smiley_code)
	{
	        switch ($smiley_code)
	        {
			case ":(": return(image_tag('smileys/4.png'));
			case ":-(": return(image_tag('smileys/4.png'));
			case ":)": return(image_tag('smileys/2.png'));
			case ":-)": return(image_tag('smileys/2.png'));
			case "8)": return(image_tag('smileys/3.png'));
			case "8-)": return(image_tag('smileys/3.png'));
			case "B)": return(image_tag('smileys/3.png'));
			case "B-)": return(image_tag('smileys/3.png'));
			case ":-/": return(image_tag('smileys/10.png'));
			case ":D": return(image_tag('smileys/5.png'));
			case ":-D": return(image_tag('smileys/5.png'));
			case ":P": return(image_tag('smileys/6.png'));
			case ":-P": return(image_tag('smileys/6.png'));
			case "(!)": return(image_tag('smileys/8.png'));
			case "(?)": return(image_tag('smileys/9.png'));
		}
	}

	/**
	 *
	 * Performs BB-tags formatting of a given string into html-formatted text
	 *
	 */
	function bugs_BBDecode($text, $formatting = true)
	{
		if ($formatting)
		{
			$text = bugs_smileydecode($text);
			$preg = array(
				'/(?<!\\\\)\[color(?::\w+)?=(.*?)\](.*?)\[\/color(?::\w+)?\]/si'	=> "<span style=\"color:\\1\">\\2</span>",
				'/(?<!\\\\)\[size(?::\w+)?=(.*?)\](.*?)\[\/size(?::\w+)?\]/si'		=> "<span style=\"font-size:\\1\">\\2</span>",
				'/(?<!\\\\)\[font(?::\w+)?=(.*?)\](.*?)\[\/font(?::\w+)?\]/si'	 	=> "<span style=\"font-family:\\1\">\\2</span>",
				'/(?<!\\\\)\[align(?::\w+)?=(.*?)\](.*?)\[\/align(?::\w+)?\]/si'   	=> "<div style=\"text-align:\\1\">\\2</div>",
				'/(?<!\\\\)\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'				 	=> "<span style=\"font-weight:bold\">\\1</span>",
				'/(?<!\\\\)\[p(?::\w+)?\](.*?)\[\/p(?::\w+)?\]/si'				 	=> "<p>\\1</p>",
				'/(?<!\\\\)\[s(?::\w+)?\](.*?)\[\/s(?::\w+)?\]/si'				 	=> "<span style=\"text-decoration:line-through\">\\1</span>",
				'/(?<!\\\\)\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'				 	=> "<span style=\"font-style:italic\">\\1</span>",
				'/(?<!\\\\)\[u(?::\w+)?\](.*?)\[\/u(?::\w+)?\]/si'				 	=> "<span style=\"text-decoration:underline\">\\1</span>",
				'/(?<!\\\\)\[center(?::\w+)?\](.*?)\[\/center(?::\w+)?\]/si'	   	=> "<div style=\"text-align:center\">\\1</div>",
				'#((bug|issue)\s\#?(([A-Z0-9]+\-)?\d+))#ie' 						=> 'bugs_issueLinkHelper( "\\3", "\\1" )',
				// [code]
				'/(?<!\\\\)\[code(?::\w+)?\](.*?)\[\/code(?::\w+)?\]/si'		   	=> "<div class=\"bb_code\">Code:<br>\\1</div>",
				// [email]
				'/(?<!\\\\)\[email(?::\w+)?\](.*?)\[\/email(?::\w+)?\]/si'		 	=> "<a href=\"mailto:\\1\">\\1</a>",
				'/(?<!\\\\)\[email(?::\w+)?=(.*?)\](.*?)\[\/email(?::\w+)?\]/si'   	=> "<a href=\"mailto:\\1\">\\2</a>",
				// [url]
				'/(?<!\\\\)\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'			=> "<a href=\"http://www.\\1\" target=\"_blank\">\\1</a>",
				'/(?<!\\\\)\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'			 	=> "<a href=\"\\1\" target=\"_blank\">\\1</a>",
				'/(?<!\\\\)\[url(?::\w+)?=(.*?)?\](.*?)\[\/url(?::\w+)?\]/si'	  	=> "<a href=\"\\1\" target=\"_blank\">\\2</a>",
				// [img]
				'/(?<!\\\\)\[img(?::\w+)?\](.*?)\[\/img(?::\w+)?\]/sie'			 	=> "image_tag('\\1', '', '\\1', '\\1')",
				'/(?<!\\\\)\[img(?::\w+)?=(.*?)x(.*?)\](.*?)\[\/img(?::\w+)?\]/sie' => "image_tag('\\3', '', '\\3', '\\3', 0, \\1, \\2)",
				// [quote]
				'/(?<!\\\\)\[quote(?::\w+)?\](.*?)\[\/quote(?::\w+)?\]/si'		 	=> "<div class=\"bb_quote\">Quote: <br>\\1</div>",
				'/(?<!\\\\)\[quote(?::\w+)?=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote\]/si'   => "<div class=\"bb_quote\">Quote \\1:<br>\\2</div>",
				// [list]
				'/\[\*\](.*?)\[\/\*\]/si' 	=> "<li class=\"bb-listitem\">\\1</li>",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list(:(?!u|o)\w+)?\](?:<br\s*\/?>)?/si'												=> "</ul>",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:u(:\w+)?\](?:<br\s*\/?>)?/si'		 											=> "</ul>",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:o(:\w+)?\](?:<br\s*\/?>)?/si'													=> "</ol>",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(:(?!u|o)\w+)?\]\s*(?:<br\s*\/?>)?/si'											=> "<ul class=\"bb-list-unordered\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:u(:\w+)?\]\s*(?:<br\s*\/?>)?/si'													=> "<ul class=\"bb-list-unordered\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:o(:\w+)?\]\s*(?:<br\s*\/?>)?/si'													=> "<ol class=\"bb-list-ordered\">",
	
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=1\]\s*(?:<br\s*\/?>)?/si' => "<ol class=\"bb-list-ordered,bb-list-ordered-d\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=i\]\s*(?:<br\s*\/?>)?/s'  => "<ol class=\"bb-list-ordered,bb-list-ordered-lr\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=I\]\s*(?:<br\s*\/?>)?/s'  => "<ol class=\"bb-list-ordered,bb-list-ordered-ur\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=a\]\s*(?:<br\s*\/?>)?/s'  => "<ol class=\"bb-list-ordered,bb-list-ordered-la\">",
				'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=A\]\s*(?:<br\s*\/?>)?/s'  => "<ol class=\"bb-list-ordered,bb-list-ordered-ua\">",
				// escaped tags like \[b], \[color], \[url], ...
				'/\\\\(\[\/?\w+(?::\w+)*\])/'									  => "\\1",
				// Found this on truerwords.net, thank you Seth Dillingham
				'/(^|[ \t\r\n])((ftp|http|https|gopher|mailto|news|nntp|telnet|wais|file|prospero|aim|webcal):(([A-Za-z0-9$_.+!*(),;\/?:@&~=-])|%[A-Fa-f0-9]{2}){2,}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;\/?:@&~=%-]*))?([A-Za-z0-9$_+!*();\/?:~-]))/' => '<a href="\\0">\\0</a>'
			);
			$text    = preg_replace(array_keys($preg), array_values($preg), $text);
			$text = nl2br($text);
		}
		else
		{
			$text = bugs_sanitize_string($text);
		}
		
		return $text;	
		
	}

	function bugs_getTaskIssueType()
	{
		try
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tIssueTypes::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tIssueTypes::IS_TASK, 1);
			return B2DB::getTable('B2tIssueTypes')->doSelect($crit)->get(B2tIssueTypes::ID);
		}
		catch (Exception $e)
		{
			return 0;
		}
	}

	function bugs_getAvailableAssignees($p_id, $e_id = 0, $b_id = 0, $u_id = 0)
	{
		$uids = array();

		if ($e_id != 0)
		{
			$sql = "select distinct b2ua.uid, b2ut.uname, b2ut.buddyname from bugs2_userassigns b2ua join bugs2_users b2ut on (b2ut.id = b2ua.uid) join bugs2_components b2ct on (b2ct.project = $p_id) join bugs2_scopes b2sc on (b2sc.id = b2ut.scope) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = $e_id and b2ua.target_type = 2) or (b2ua.target_type = 3)) and b2ut.scope = " . BUGScontext::getScope()->getID() . " and b2sc.enabled = 1 order by b2ut.buddyname asc";
		}
		else
		{
			$sql = "select distinct b2ua.uid, b2ut.uname, b2ut.buddyname from bugs2_userassigns b2ua join bugs2_editions b2et on (b2et.project = $p_id) join bugs2_users b2ut on (b2ut.id = b2ua.uid) join bugs2_components b2ct on (b2ct.project = $p_id) join bugs2_scopes b2sc on (b2sc.id = b2ut.scope) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = b2et.id and b2ua.target_type = 2) or (b2ua.target_type = 3 and b2ct.id = b2ua.target and b2ct.project = $p_id)) and b2ut.scope = " . BUGScontext::getScope()->getID() . " and b2sc.enabled = 1 order by b2ut.buddyname asc";
			$sql_2 = "select distinct b2ua.target, b2ua.target_type from bugs2_userassigns b2ua join bugs2_editions b2et on (b2et.project = $p_id) join bugs2_components b2ct on (b2ct.project = $p_id) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = b2et.id and b2ua.target_type = 2) or (b2ua.target_type = 3)) and uid = ";
		}

		//$res = b2db_sql_query($sql, B2DB::getDBlink());
		$res = B2DB::simpleQuery($sql);
		while ($row = $res->fetch_array())
		{
			if ($e_id == 0)
			{
				$sql2 = $sql_2 . $row['uid'];
				#print $sql2;
				//$res2 = b2db_sql_query($sql2, B2DB::getDBlink());
				$res2 = B2DB::simpleQuery($sql2);
				$uid_ua = array();
				while ($row2 = $res2->fetch_array())
				{
					$uid_ua[] = $row2;
					#print "fu";
				}
				$row['assigns'] = $uid_ua;
			}
			$uids[] = $row;
		}

		return $uids;
	}

	function bugs_getIssueNotifications($uid, $notify_type)
	{
		$crit = new B2DBCriteria();
		$crit->addWhere(B2tNotifications::UID, $uid);
		$crit->addWhere(B2tNotifications::NOTIFY_TYPE, $notify_type);
		$notifications = B2DB::getTable('B2tNotifications')->doCount($crit);
		
		return $notifications;
	}

	function bugs_removeIssueNotification($uid, $issue_id)
	{
		$crit = new B2DBCriteria();
		$crit->addWhere(B2tNotifications::UID, $uid);
		$crit->addWhere(B2tNotifications::NOTIFY_TYPE, 3, B2DBCriteria::DB_LESS_THAN_EQUAL);
		$crit->addWhere(B2tNotifications::TARGET_ID, $issue_id);
		
		$res = B2DB::getTable('B2tNotifications')->doDelete($crit);
	}


	function bugs_newTextArea($area_name, $height, $width, $value = '')
	{
		$retval = '';
		$retval .= '<textarea name="' . $area_name . '" id="' . $area_name . '" style="height: ' . $height . '; width: ' . $width . ';">' . $value . '</textarea>';
		$retval .= '<script type="text/javascript">';
		$retval .= 'tinyMCE.execCommand(\'mceAddControl\', false, \'' . $area_name . '\');';
		$retval .= '</script>';
		$retval .= '<p>';
		$retval .= __('To link to an existing issue, write "issue" followed by the issue number.');
		$retval .= '</p>';
		return $retval;
	}

	function bugs_createPassword($len = 8)
	{
		$pass = '';
		$lchar = 0;
		$char = 0;
		for($i = 0; $i < $len; $i++)
		{
			while($char == $lchar)
			{
				$char = rand(48, 109);
				if($char > 57) $char += 7;
				if($char > 90) $char += 6;
			}
			$pass .= chr($char);
			$lchar = $char;
		}
		return $pass;
	}

	function bugs_printRandomNumber()
	{
		$randomNumber = "";

		for($cc = 1;$cc <= 6;$cc++)
		{
			$rndNo = rand(0,9);
			$randomNumber .= $rndNo;

			?><?php echo image_tag('/numbers/' . $rndNo . '.png'); ?><?php
		}

		return $randomNumber;
	}

	function bugs_viewMinutes($minutes, $tformat, $hrs_pr_day)
	{
		$theTime = $minutes;
		switch ($tformat)
		{
			case 0: // hours
				return floor($theTime / 60) . " hour(s)";
			case 1: // hours and days
				$days = 0;
				$hrs = 0;
				$timeVal = "";

				$hrs = floor($theTime / 60);
				$days = floor($hrs / $hrs_pr_day);
				$hrs = $hrs - ($days * $hrs_pr_day);

				$timeVal = $days . " day(s)";
				$timeVal .= ", " . $hrs . " hour(s)";

				return $timeVal;
			case 2: // hours days and weeks
				$days = 0;
				$hrs = 0;
				$mins = 0;
				$timeVal = "";

				$hrs = floor($theTime / 60);
				$days = floor($hrs / $hrs_pr_day);
				$mins = $theTime - ($hrs * 60);
				$hrs = $hrs - ($days * $hrs_pr_day);

				$timeVal = $days . " day(s)";
				$timeVal .= ", " . $hrs . " hour(s)";
				$timeVal .= ", " . $mins . " minute(s)";

				return $timeVal;
			case 3: // days
				return $theTime . " minute(s)";
			case 4: // days and weeks
				$hrs = floor($theTime / 60);
				$mins = $theTime - ($hrs * 60);

				$timeVal = $hrs . " hour(s)";
				$timeVal .= ", " . $mins . " minute(s)";

				return $timeVal;
		}
		
	}
	
	/**
	 *
	 * Displays an error dialog box (HTML)
	 */
	function bugs_showError($title, $text, $fatal = false)
	{
		print "<div style=\"position: absolute; top: 25%; left: 20%;\"><div style=\"width: 500px; font-size: 11px; font-family: tahoma, sans-serif; padding: 2px 4px 2px 4px; color: #555; background-color: #DDD; font-weight: bold; border: 1px solid #BBB;\"><b>" . $title . "</b></div><div style=\"width: 500px; font-size: 11px; font-family: tahoma, sans-serif; padding: 5px 4px 5px 4px; color: #555; background-color: #FFF; border: 1px solid #BBB; border-top: 0px;\">";
		print $text . "</div></div>";
		if ($fatal)
		{
			require_once BUGScontext::getIncludePath() . "/include/footer.inc.php";
		}
	}
	
	function bugs_cleanString($s)
	{
		$preg = array('/</' => "&lt;", '/>/' => "&gt;");
		$s = preg_replace(array_keys($preg), array_values($preg), $s);
		return $s;
	}
	
	function __($text, $replacements = array())
	{
		if (BUGScontext::getI18n() instanceof BUGSi18n)
		{
			return BUGScontext::getI18n()->__($text, $replacements);
		}
		else
		{
			throw new Exception('Language support not loaded yet');
		}
	}

?>
