<?php


	define ('THEBUGGENIE_PATH', '../../');
	$page = "search";

	require THEBUGGENIE_PATH . 'include/checkcookie.inc.php';
	require THEBUGGENIE_PATH . 'include/b2_engine.inc.php';

	TBGContext::getModule('search')->activate();
	
	$output_format = TBGContext::getRequest()->getParameter('output');
	
	switch ($output_format)
	{
		case 'json':
			$output = '["' . TBGContext::getRequest()->getParameter('searchfor', null, false) . '", ';
			break;
		case 'ul':
			$output = '<ul>';
			break;
	}
	
	if ($output_format == 'ul')
	{
		$output .= '<li>' . TBGContext::getRequest()->getParameter('searchfor');
		$output .= '<br><span class="informal"><i>' . __('Press enter twice to search') . ' ...</i></span>';
		$output .= '</li>';
	}
	
	//TODO: use static function to retrieve issue from parameter in TBGIssue instead
	if (is_numeric(TBGContext::getRequest()->getParameter('searchfor')))
	{
		if (TBGIssue::hasPrefix(TBGContext::getRequest()->getParameter('searchfor')) == false)
		{
			$issue_uniqueid = TBGContext::getRequest()->getParameter('searchfor');
			$issue_prefix = "";
			if (TBGContext::getUser()->hasPermission("b2viewissue", $issue_uniqueid, "core") == true)
			{
				$explicit = true;
			}
			else
			{
				$theIssue = TBGContext::factory()->TBGIssue($issue_uniqueid);
				if (TBGContext::getUser()->hasPermission("b2notviewissue", $theIssue->getID(), "core") == false)
				{
					if (TBGContext::getUser()->hasPermission("b2projectaccess", $theIssue->getProject()->getID(), "core"))
					{
						switch ($output_format)
						{
							case 'json':
								$output .= '["';
								$output .= __('Issue #') . TBGContext::getRequest()->getParameter('searchfor') . ' - ' . html_entity_decode($theIssue->getTitle());
								$output .= '"]';
								break;
							case 'ul':
								$output = '<li>';
								$output .= __('Issue #') . TBGContext::getRequest()->getParameter('searchfor') . ' - ' . addslashes($theIssue->getTitle());
								$output .= '<br><span class="informal">' . __('Last updated') . ' ' . tbg_formatTime($theIssue->getLastUpdatedTime(), 6) . '</span>';
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
		if (TBGContext::getRequest()->getParameter('searchfor') && strstr(TBGContext::getRequest()->getParameter('searchfor'), "-"))
		{
			$theIssue = TBGIssue::getIssueFromLink(TBGContext::getRequest()->getParameter('searchfor'));
			if ($theIssue instanceof TBGIssue)
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
						$output .= '<br><span class="informal"><i>' . __('Last updated') . ' ' . tbg_formatTime($theIssue->getLastUpdatedTime(), 3) . '</i></span>';
						$output .= '</li>';
						break;
				}
				$output_done = true;
			}
			else
			{
				TBGContext::getRequest()->setParameter('simplesearch', "true");
				TBGContext::getRequest()->setParameter('lookthrough', "all");
			}
		}
		elseif (TBGContext::getRequest()->getParameter('searchfor'))
		{
			TBGContext::getRequest()->setParameter('simplesearch', "true");
			TBGContext::getRequest()->setParameter('lookthrough', "all");
		}

		if ($output_done == false)
		{
			$_SESSION['searchfields'] = array();
	
			unset($_SESSION['simplefilters']);
			$appliedFilters = TBGContext::getModule('search')->getSearchFields();
	
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => TBGIssuesTable::TITLE, "value" => '%'.TBGContext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => TBGIssuesTable::DESCRIPTION, "value" => '%'.TBGContext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
			$_SESSION['simplefilters'][] = array("id" => 0, "filter_field" => TBGCommentsTable::CONTENT, "value" => '%'.TBGContext::getRequest()->getParameter('searchfor').'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
	
			$searchmatches = TBGContext::getModule('search')->doSearch(0, false, true, false, ((count($_SESSION['simplefilters']) > 0) ? true : false));
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
				if (TBGContext::getUser()->hasPermission("b2notviewissue", $theIssue->getID(), "core") == false)
				{
					if (TBGContext::getUser()->hasPermission("b2projectaccess", $theIssue->getProject()->getID(), "core"))
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
								$output .= '<br><span class="informal"><i>' . __('Last updated') . ' ' . tbg_formatTime($theIssue->getLastUpdatedTime(), 3) . '</i></span>';
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