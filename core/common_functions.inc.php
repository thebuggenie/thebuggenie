<?php

	function tbg__sanitize_string($string)
	{
		if (get_magic_quotes_gpc() == 1)
		{
			$string = stripslashes($string);
		}
		return htmlspecialchars($string, ENT_QUOTES, TBGContext::getI18n()->getCharset());
	}
	
	/**
	 *
	 * Returns a formatted string of the given timestamp
	 *
	 */
	function tbg__formatTime($tstamp, $format = 0, $skiptimestamp = 0)
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

	function tbg__getTaskIssueType()
	{
		try
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tIssueTypes::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(B2tIssueTypes::IS_TASK, 1);
			return B2DB::getTable('B2tIssueTypes')->doSelect($crit)->get(B2tIssueTypes::ID);
		}
		catch (Exception $e)
		{
			return 0;
		}
	}

	function tbg__getAvailableAssignees($p_id, $e_id = 0, $b_id = 0, $u_id = 0)
	{
		$uids = array();

		if ($e_id != 0)
		{
			$sql = "select distinct b2ua.uid, b2ut.uname, b2ut.buddyname from tbg_2_userassigns b2ua join tbg_2_users b2ut on (b2ut.id = b2ua.uid) join tbg_2_components b2ct on (b2ct.project = $p_id) join tbg_2_scopes b2sc on (b2sc.id = b2ut.scope) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = $e_id and b2ua.target_type = 2) or (b2ua.target_type = 3)) and b2ut.scope = " . TBGContext::getScope()->getID() . " and b2sc.enabled = 1 order by b2ut.buddyname asc";
		}
		else
		{
			$sql = "select distinct b2ua.uid, b2ut.uname, b2ut.buddyname from tbg_2_userassigns b2ua join tbg_2_editions b2et on (b2et.project = $p_id) join tbg_2_users b2ut on (b2ut.id = b2ua.uid) join tbg_2_components b2ct on (b2ct.project = $p_id) join tbg_2_scopes b2sc on (b2sc.id = b2ut.scope) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = b2et.id and b2ua.target_type = 2) or (b2ua.target_type = 3 and b2ct.id = b2ua.target and b2ct.project = $p_id)) and b2ut.scope = " . TBGContext::getScope()->getID() . " and b2sc.enabled = 1 order by b2ut.buddyname asc";
			$sql_2 = "select distinct b2ua.target, b2ua.target_type from tbg_2_userassigns b2ua join tbg_2_editions b2et on (b2et.project = $p_id) join tbg_2_components b2ct on (b2ct.project = $p_id) where ((b2ua.target = $p_id and b2ua.target_type = 1) or (b2ua.target = b2et.id and b2ua.target_type = 2) or (b2ua.target_type = 3)) and uid = ";
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

	function tbg__getIssueNotifications($uid, $notify_type)
	{
		$crit = new B2DBCriteria();
		$crit->addWhere(B2tNotifications::UID, $uid);
		$crit->addWhere(B2tNotifications::NOTIFY_TYPE, $notify_type);
		$notifications = B2DB::getTable('B2tNotifications')->doCount($crit);
		
		return $notifications;
	}

	function tbg__removeIssueNotification($uid, $issue_id)
	{
		$crit = new B2DBCriteria();
		$crit->addWhere(B2tNotifications::UID, $uid);
		$crit->addWhere(B2tNotifications::NOTIFY_TYPE, 3, B2DBCriteria::DB_LESS_THAN_EQUAL);
		$crit->addWhere(B2tNotifications::TARGET_ID, $issue_id);
		
		$res = B2DB::getTable('B2tNotifications')->doDelete($crit);
	}

	function tbg__createPassword($len = 8)
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

	function tbg__printRandomNumber()
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

	function tbg__viewMinutes($minutes, $tformat, $hrs_pr_day)
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
	function tbg__showError($title, $text, $fatal = false)
	{
		print "<div style=\"position: absolute; top: 25%; left: 20%;\"><div style=\"width: 500px; font-size: 11px; font-family: tahoma, sans-serif; padding: 2px 4px 2px 4px; color: #555; background-color: #DDD; font-weight: bold; border: 1px solid #BBB;\"><b>" . $title . "</b></div><div style=\"width: 500px; font-size: 11px; font-family: tahoma, sans-serif; padding: 5px 4px 5px 4px; color: #555; background-color: #FFF; border: 1px solid #BBB; border-top: 0px;\">";
		print $text . "</div></div>";
		if ($fatal)
		{
			require_once TBGContext::getIncludePath() . "/include/footer.inc.php";
		}
	}
	
	function tbg__cleanString($s)
	{
		$preg = array('/</' => "&lt;", '/>/' => "&gt;");
		$s = preg_replace(array_keys($preg), array_values($preg), $s);
		return $s;
	}
	
	function __($text, $replacements = array())
	{
		if (TBGContext::getI18n() instanceof TBGI18n)
		{
			return TBGContext::getI18n()->__($text, $replacements);
		}
		else
		{
			throw new Exception('Language support not loaded yet');
		}
	}

?>
