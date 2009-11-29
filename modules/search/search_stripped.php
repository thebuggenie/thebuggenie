<?php


	define ('THEBUGGENIE_PATH', '../../');
	$page = "search";

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';

	BUGScontext::getModule('search')->activate();
	
	$output_format = BUGScontext::getRequest()->getParameter('output');
	
	switch ($output_format)
	{
		case 'json':
			$output = '["' . BUGScontext::getRequest()->getParameter('searchfor', null, false) . '", ';
			break;
		case 'ul':
			$output = '<ul>';
			break;
	}
	
	if ($output_format == 'ul')
	{
		$output .= '<li>' . BUGScontext::getRequest()->getParameter('searchfor');
		$output .= '<br><span class="informal"><i>' . __('Press enter twice to search') . ' ...</i></span>';
		$output .= '</li>';
	}
	
	//TODO: use static function to retrieve issue from parameter in BUGSissue instead
	if (is_numeric(BUGScontext::getRequest()->getParameter('searchfor')))
	{
		if (BUGSissue::hasPrefix(BUGScontext::getRequest()->getParameter('searchfor')) == false)
		{
			$issue_uniqueid = BUGScontext::getRequest()->getParameter('searchfor');
			$issue_prefix = "";
			if (BUGScontext::getUser()->hasPermission("b2viewissue", $issue_uniqueid, "core") == true)
			{
				$explicit = true;
			}
			else
			{
				$theIssue = BUGSfactory::BUGSissueLab($issue_uniqueid);
				if (BUGScontext::getUser()->hasPermission("b2notviewissue", $theIssue->getID(), "core") == false)
				{
					if (BUGScontext::getUser()->hasPermission("b2projectaccess", $theIssue->getProject()->getID(), "core"))
					{
						switch ($output_format)
						{
							case 'json':
								$output .= '["';
								$output .= __('Issue #') . BUGScontext::getRequest()->getParameter('searchfor') . ' - ' . html_entity_decode($theIssue->getTitle());
								$output .= '"]';
								break;
							case 'ul':
								$output = '<li>';
								$output .= __('Issue #') . BUGScontext::getRequest()->getParameter('searchfor') . ' - ' . addslashes($theIssue->getTitle());
								$output .= '<br><span class="informal">' . __('Last updated') . ' ' . bugs_formatTime($theIssue->getLastUpdatedTime(), 6) . '</span>';
								$output = '</li>';
								break;
						}
					}
				}
			}
		}
	}
	else
	{
		$issue_no = array();
		$output_done = false;
		if (BUGScontext::getRequest()->getParameter('searchfor') && strstr(BUGScontext::getRequest()->getParameter('searchfor'), "-"))
		{
			$theIssue = BUGSissue::getIssueFromLink(BUGScontext::getRequest()->getParameter('searchfor'));
			if ($theIssue instanceof BUGSissue)
			{
				switch ($output_format)
				{
					case 'json':
						$output .= '["';
						$output .= __('Issue ') . $theIssue->getFormattedIssueNo() . ' - ' . addslashes($theIssue->getTitle());
						$output .= '"]';
						break;
					case 'ul':
						$output .= '<li>';
						$output .= __('Issue ') . $theIssue->getFormattedIssueNo() . ' - ';
						$output .= (strlen($theIssue->getTitle()) > 26) ? rtrim(substr($theIssue->getTitle(), 0, 24)) . "<span class=\"informal\">...</span>" : $theIssue->getTitle();
						$output .= '<br><span class="informal"><i>' . __('Last updated') . ' ' . bugs_formatTime($theIssue->getLastUpdatedTime(), 3) . '</i></span>';
						$output .= '</li>';
						break;
				}
				$output_done = true;
			}
			else
			{
				BUGScontext::getRequest()->setParameter('simplesearch', "true");
				BUGScontext::getRequest()->setParameter('lookthrough', "all");
			}
		}
		elseif (BUGScontext::getRequest()->getParameter('searchfor'))
		{
			BUGScontext::getRequest()->setParameter('simplesearch', "true");
			BUGScontext::getRequest()->setParameter('lookthrough', "all");
		}

		if ($output_done == false)
		{
			$_SESSION['searchfields'] = array();
	
			unset($_SESSION['simplefilters']);
			$appliedFilters = BUGScontext::getModule('search')->getSearchFields();
	
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => B2tIssues::TITLE, "value" => '%'.BUGScontext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => B2tIssues::LONG_DESCRIPTION, "value" => '%'.BUGScontext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => B2tComments::CONTENT, "value" => '%'.BUGScontext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
	
			$searchmatches = BUGScontext::getModule('search')->doSearch(0, false, true, false, ((count($_SESSION['simplefilters']) > 0) ? true : false));
			$searchmatches = $searchmatches['issues'];
			
			#print $searchmatches->count();
			switch ($output_format)
			{
				case 'json':
					$output .= '[';
					break;
			}
			$firstres = true;
			foreach ($searchmatches as $theIssue)
			{
				if (BUGScontext::getUser()->hasPermission("b2notviewissue", $theIssue->getID(), "core") == false)
				{
					if (BUGScontext::getUser()->hasPermission("b2projectaccess", $theIssue->getProject()->getID(), "core"))
					{
						switch ($output_format)
						{
							case 'json':
								$output .= ($firstres == false) ? ', ' : '';
								$output .= '"Issue ' . $theIssue->getFormattedIssueNo() . ' - ' . addslashes(html_entity_decode($theIssue->getTitle())) . '"';
								break;
							case 'ul':
								$output .= '<li>';
								$output .= 'Issue ' . $theIssue->getFormattedIssueNo() . ' - ';
								$output .= (strlen($theIssue->getTitle()) > 24) ? rtrim(substr($theIssue->getTitle(), 0, 22)) . "<span class=\"informal\">...</span>" : $theIssue->getTitle();
								$output .= '<br><span class="informal"><i>' . __('Last updated') . ' ' . bugs_formatTime($theIssue->getLastUpdatedTime(), 3) . '</i></span>';
								$output .= '</li>';
								break;
						}
						$firstres = false;
					}
				}
			}
			switch ($output_format)
			{
				case 'json':
					$output .= ']';
					break;
			}
		}
		
	}
	
	switch ($output_format)
	{
		case 'json':
			$output .= ']';
			break;
		case 'ul':
			$output .= '</ul>';
			break;
	}
	
	print $output;
	
?>