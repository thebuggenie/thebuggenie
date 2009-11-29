<?php
	
	if (isset($argc))
	{
		define('THEBUGGENIE_PATH', '/var/www/dev/b2/');
		$author = $argv[1];
		$commit_msg = $argv[3];
		$new_rev = $argv[2];
		$old_rev = $new_rev - 1;
		$changed = $argv[4];
	}
	else
	{
		define('THEBUGGENIE_PATH', '../../');
		$commit_msg = urldecode($_REQUEST['commit_msg']);
		$new_rev = $_REQUEST['rev'];
		$old_rev = $new_rev - 1;
		$changed = $_REQUEST['changed'];
		$author = $_REQUEST['author'];
		$passkey = $_REQUEST['passkey'];
	}
	
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';
	require THEBUGGENIE_PATH . 'include/ui_functions.inc.php';
	
	if (!isset($argc))
	{
		if ($passkey != BUGScontext::getModule('svn_integration')->getSetting('svn_passkey'))
		{
			echo 'incorrect passkey';
			exit();
		}
	}
	
	$fixes_grep = "#((bug|issue|ticket)\s\#?(([A-Z0-9]+\-)?\d+))#ie";
	
	$f_issues = array();
	
	if (preg_match_all($fixes_grep, $commit_msg, &$f_issues))
   	{
		$f_issues = array_unique($f_issues[3]);

	   	$file_lines = preg_split('/[\n\r]+/', $changed);
   		$files = array();

	   	foreach ($file_lines as $aline)
	   	{
	   		if (substr($aline, 0, 1) == ("A") || substr($aline, 0, 1) == ("U"))
	   		{
	   			$theline = trim(substr($aline, 1));
	   			$files[] = $theline;
	   		}
	   	}
   		
	   	foreach ($f_issues as $issue_no)
	   	{
			$theIssue = BUGSissue::getIssueFromLink($issue_no, true);
			if ($theIssue instanceof BUGSissue)
			{
                                $uid = 0;
                                $crit = new B2DBCriteria();
                                $crit->addWhere(B2tUsers::UNAME, $author);
                                $row = B2DB::getTable('B2tUsers')->doSelectOne($crit);
                                $uid = $row->get(B2tUsers::ID);
				$theIssue->addSystemComment('Issue updated from SVN', 'This issue has been updated with the latest changes from SVN.[quote]' . $commit_msg . '[/quote]', $uid, true);
				foreach ($files as $afile)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(B2tSVNintegration::ISSUE_NO, $theIssue->getID()); 
					$crit->addInsert(B2tSVNintegration::FILE_NAME, $afile); 
					$crit->addInsert(B2tSVNintegration::NEW_REV, $new_rev);
					$crit->addInsert(B2tSVNintegration::OLD_REV, $old_rev);
					$crit->addInsert(B2tSVNintegration::AUTHOR, $uid);
					$crit->addInsert(B2tSVNintegration::DATE, $_SERVER["REQUEST_TIME"]);
					$crit->addInsert(B2tSVNintegration::SCOPE, BUGScontext::getScope()->getID());
					B2DB::getTable('B2tSVNintegration')->doInsert($crit);
				}
				echo 'Updated ' . $theIssue->getFormattedIssueNo() . "\n";
			}
			else
			{
				echo 'Can\'t find ' . $issue_no . ' so not updating that one.' . "\n";
			}
   		}
   	}
   	return true;
?>
