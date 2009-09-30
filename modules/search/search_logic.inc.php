<?php

	if (!defined('BUGS2_INCLUDE_PATH')) exit();
	
	$matchfor = BUGScontext::getRequest()->getParameter('searchfor');
	$matchfor = (substr($matchfor, 0, 6) == 'Issue ') ? substr($matchfor, 6) : $matchfor;	
	if (strpos($matchfor, ' ') !== false)
	{
		$matchfor = substr($matchfor, 0, strpos($matchfor, ' '));
	}
	else
	{
		$matchfor = $matchfor;
	}
	$matchfor = (substr($matchfor, 0, 1) == '#') ? substr($matchfor, 1) : $matchfor;	
	
	if (BUGScontext::getRequest()->getParameter('next_issue') || BUGScontext::getRequest()->getParameter('previous_issue'))
	{
		$theIssue = BUGSissue::getIssueFromLink(BUGScontext::getRequest()->getParameter('current'));
		
		$queue = null;
		$url_options = '';
		if ((int) BUGScontext::getRequest()->getParameter('search_queue') > 0) $queue = BUGScontext::getRequest()->getParameter('search_queue');
		if ($queue === null && isset($_SESSION['search_queue']) && (int) $_SESSION['search_queue'] > 0) $queue = $_SESSION['search_queue'];

		if ($queue) $url_options = '&search_queue=' . $queue;
		
		if ($theIssue instanceof BUGSissue)
		{
			if ($queue)
			{
				$issues = BUGScontext::getModule('search')->doSearch($queue);
				if (BUGScontext::getRequest()->getParameter('next_issue')) 
				{
					$issues = array_reverse($issues['issues']);
				}
				else
				{
					$issues = $issues['issues'];
				}
				$hit = false;
				foreach ($issues as $anIssue)
				{
					if ($hit)
					{
						bugs_msgbox(false, __('Redirecting'), __('Redirecting to the specified issue, please wait') . '...<br>' . __('If your browser does not redirect you, click here: ') . '<a href="' . BUGScontext::getTBGPath() . 'viewissue.php?issue_no=' . $anIssue->getFormattedIssueNo(true) . $url_options . '">Issue ' . $anIssue->getFormattedIssueNo() . '</a>');
						bugs_moveTo("viewissue.php?issue_no=" . $anIssue->getFormattedIssueNo(true) . $url_options);
						exit();
					}
					else
					{
						if ($theIssue->getID() == $anIssue->getID()) $hit = true;
					}
				}
			}
			else
			{
				$crit = new B2DBCriteria();
				$crit->addWhere(B2tIssues::PROJECT_ID, $theIssue->getProject()->getID());
				$crit->addWhere(B2tIssues::DELETED, 0);
				if (BUGScontext::getRequest()->getParameter('open'))
				{
					$crit->addWhere(B2tIssues::STATE, BUGSissue::STATE_OPEN);
				}
				if (BUGScontext::getRequest()->getParameter('next_issue'))
				{
					$crit->addWhere(B2tIssues::ID, $theIssue->getID(), B2DBCriteria::DB_GREATER_THAN);
					$crit->addOrderBy(B2tIssues::ID, B2DBCriteria::SORT_ASC);
				}
				else
				{
					$crit->addWhere(B2tIssues::ID, $theIssue->getID(), B2DBCriteria::DB_LESS_THAN);
					$crit->addOrderBy(B2tIssues::ID, B2DBCriteria::SORT_DESC);
				}
				$res = B2DB::getTable('B2tIssues')->doSelect($crit);
				while ($row = $res->getNextRow())
				{
					if (BUGSissue::hasAccess($row->get(B2tIssues::ID)))
					{
						try
						{
							$gotoIssue = new BUGSissue($row->get(B2tIssues::ID));
							bugs_msgbox(false, __('Redirecting'), __('Redirecting to the specified issue, please wait') . '...<br>' . __('If your browser does not redirect you, click here: ') . '<a href="' . BUGScontext::getTBGPath() . 'viewissue.php?issue_no=' . $gotoIssue->getFormattedIssueNo(true) . $url_options . '">Issue ' . $gotoIssue->getFormattedIssueNo() . '</a>');
							bugs_moveTo("viewissue.php?issue_no=" . $gotoIssue->getFormattedIssueNo(true) . $url_options);
							exit();
						}
						catch (Exception $e) {}
					}
				}
			}
			bugs_msgbox(false, __('Redirecting'), __('Redirecting to the specified issue, please wait') . '...<br>' . __('If your browser does not redirect you, click here: ') . '<a href="' . BUGScontext::getTBGPath() . 'viewissue.php?issue_no=' . $theIssue->getFormattedIssueNo(true) . $url_options . '">Issue ' . $theIssue->getFormattedIssueNo() . '</a>');
			$search_message = (BUGScontext::getRequest()->getParameter('next_issue')) ? ((BUGScontext::getRequest()->getParameter('open')) ? 2 : 1) : ((BUGScontext::getRequest()->getParameter('open')) ? 4 : 3);
			bugs_moveTo("viewissue.php?issue_no=" . $theIssue->getFormattedIssueNo(true) . "&amp;search_message=" . $search_message . $url_options);
			exit();
		}
		else
		{
			bugs_msgbox(false, __('Redirecting'), __('This issue does not exist.'));
			bugs_moveTo("index.php");
			exit();
		}
	}
	elseif (isset($matchfor) && is_numeric($matchfor))
	{
		bugs_msgbox(false, __('Redirecting'), __('Redirecting to the specified issue, please wait') . '...<br>' . __('If your browser does not redirect you, click here: ') . '<a href="' . BUGScontext::getTBGPath() . 'viewissue.php?issue_no=' . $matchfor . '">Issue ' . $matchfor . '</a>');
		bugs_moveTo("viewissue.php?issue_no=" . $matchfor);
		exit();
	}
	else
	{
		$issue_no = array();
		if (isset($matchfor) && strstr($matchfor, "-"))
		{
			$issue_uniqueid = BUGSissue::getIssueIDfromLink($matchfor);
			if ($issue_uniqueid != 0)
			{
				bugs_msgbox(false, __('Redirecting'), __('Redirecting to the specified issue, please wait') . '...<br>' . __('If your browser does not redirect you, click here: ') . '<a href="' . BUGScontext::getTBGPath() . 'viewissue.php?issue_no=' . $matchfor . '">Issue ' . $matchfor . '</a>');
				bugs_moveTo("viewissue.php?issue_no=" . $matchfor);
				exit();
			}
			else
			{
				BUGScontext::getRequest()->setParameter('simplesearch', "true");
				BUGScontext::getRequest()->setParameter('lookthrough', "all");
			}
		}
		elseif (BUGScontext::getRequest()->getParameter('searchfor') && $matchfor == '')
		{
			BUGScontext::getRequest()->setParameter('simplesearch', null);
			BUGScontext::getRequest()->setParameter('lookthrough', null);
			BUGScontext::getRequest()->setParameter('searchfor', null);
			unset($_SESSION['search_queue']);
		}
		elseif (BUGScontext::getRequest()->getParameter('searchfor'))
		{
			BUGScontext::getRequest()->setParameter('simplesearch', "true");
			BUGScontext::getRequest()->setParameter('lookthrough', "all");
		}
	}

	$showsearch = false;
	$savedsearch = false;
	$editingsavedsearch = false;
	$appliedFilters = array();
	$clearedall = false;
	
	if ($_SESSION['searchlayout'] == '')
	{
		$_SESSION['searchlayout'] = BUGScontext::getModule('search')->getSetting('defaultsearchlayout');
	}

	if (BUGScontext::getRequest()->getParameter('saved_search') && is_numeric(BUGScontext::getRequest()->getParameter('s_id')))
	{
		if (BUGScontext::getRequest()->getParameter('remove_savedsearch'))
		{
			B2DB::getTable('B2tSavedSearches')->doDeleteById(BUGScontext::getRequest()->getParameter('s_id'));
			BUGScontext::getRequest()->setParameter('saved_search', null);
			BUGScontext::getRequest()->setParameter('s_id', null);
			BUGScontext::getRequest()->setParameter('remove_savedsearch', null);
			unset($_SESSION['search_queue']);
		}

		$sid = BUGScontext::getRequest()->getParameter('s_id');
		if (is_numeric($sid))
		{
			$showsearch = true;
			$savedsearch = true;
		}
		if (BUGScontext::getRequest()->getParameter('rename_search') && BUGScontext::getRequest()->getParameter('saved_search_title'))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tSavedSearches::NAME, BUGScontext::getRequest()->getParameter('saved_search_title'));
			B2DB::getTable('B2tSavedSearches')->doUpdateById($crit, $sid);
			$newname_saved = true;
		}
		if (BUGScontext::getRequest()->getParameter('make_private'))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tSavedSearches::IS_PUBLIC, 0);
			if (BUGScontext::getUser()->hasPermission('b2searchmaster', 1, "search") == false)
			{
				$crit->addWhere(B2tSavedSearches::UID, BUGScontext::getUser()->getUID());
			}
			B2DB::getTable('B2tSavedSearches')->doUpdateById($crit, $sid);
		}
		if (BUGScontext::getRequest()->getParameter('make_public'))
		{
			$crit = new B2DBCriteria();
			$crit->addUpdate(B2tSavedSearches::IS_PUBLIC, 1);
			if (BUGScontext::getUser()->hasPermission('b2searchmaster', 1, "search") == false)
			{
				$crit->addWhere(B2tSavedSearches::UID, BUGScontext::getUser()->getUID());
			}
			B2DB::getTable('B2tSavedSearches')->doUpdateById($crit, $sid);
		}
		$appliedFilters = BUGScontext::getModule('search')->getSearchFields(BUGScontext::getRequest()->getParameter('s_id'));
		unset($_SESSION['searchfields']);
		unset($_SESSION['search_queue']);
	}
	elseif (BUGScontext::getRequest()->getParameter('simplesearch') && strlen(trim($matchfor)) > 1)
	{
		if (BUGScontext::getRequest()->getParameter('searchlayout'))
		{
			$_SESSION['searchlayout'] = BUGScontext::getRequest()->getParameter('searchlayout');
		}
		if (BUGScontext::getRequest()->getParameter('groupby'))
		{
			$_SESSION['groupby'] = BUGScontext::getRequest()->getParameter('groupby');
		}
		if (!isset($_SESSION['custom_search']))
		{
			$_SESSION['searchfields'] = array();
		}

		unset($_SESSION['simplefilters']);
		unset($_SESSION['search_queue']);
		$appliedFilters = BUGScontext::getModule('search')->getSearchFields();

		switch (BUGScontext::getRequest()->getParameter('lookthrough'))
		{
			case "all":
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::TITLE, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::LONG_DESCRIPTION, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tComments::CONTENT, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				break;
			case "all_notcomments":
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::TITLE, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::LONG_DESCRIPTION, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				break;
			case "title":
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::TITLE, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				break;
			case "desc":
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tIssues::LONG_DESCRIPTION, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				break;
			case "comments":
				$_SESSION['simplefilters'][] = array('filter_table' => 'B2tIssues', "id" => 0, "filter_field" => B2tComments::CONTENT, "value" => '%'.$matchfor.'%', "operator" => B2DBCriteria::DB_LIKE, "filter_type" => '', "values_from" => '', "value_from_field" => '', "name_from_field" => '', "from_tbl_crit_field" => '', "from_tbl_crit_value" => '');
				break;
		}
		$_SESSION['simple_search'] = true;
		$showsearch = true;
	}
	elseif (BUGScontext::getRequest()->getParameter('custom_search'))
	{
		$_SESSION['custom_search'] = true;
		if (BUGScontext::getRequest()->getParameter('clear_filters'))
		{
			unset($_SESSION['searchfields']);
			unset($_SESSION['simplefilters']);
			unset($_SESSION['simple_search']);
			unset($_SESSION['searchlayout']);
			unset($_SESSION['groupby']);
			unset($_SESSION['search_queue']);
		}
		if (BUGScontext::getRequest()->getParameter('searchlayout'))
		{
			$_SESSION['searchlayout'] = BUGScontext::getRequest()->getParameter('searchlayout');
		}
		if (BUGScontext::getRequest()->getParameter('groupby'))
		{
			$_SESSION['groupby'] = BUGScontext::getRequest()->getParameter('groupby');
		}
		if ($_SESSION['searchlayout'] == '')
		{
			$_SESSION['searchlayout'] = BUGScontext::getModule('search')->getSetting('defaultsearchlayout');
		}
		if (is_array(BUGScontext::getRequest()->getParameter('add_filter')))
		{
			$_SESSION['searchfields'] = array();
			foreach(BUGScontext::getRequest()->getParameter('add_filter') as $add_filter)
			{
				$row = B2DB::getTable('B2tSearchFilters')->doSelectById($add_filter[0]);
				$_SESSION['searchfields'][] = array('filter_table' => $row->get(B2tSearchFilters::FILTER_TABLE), "id" => $row->get(B2tSearchFilters::ID), "filter_field" => $row->get(B2tSearchFilters::FILTER_FIELD), "value" => $add_filter[2], "operator" => $add_filter[1] , "filter_type" => $row->get(B2tSearchFilters::FILTER_TYPE), "values_from" => $row->get(B2tSearchFilters::VALUES_FROM), "value_from_field" => $row->get(B2tSearchFilters::VALUE_FROM_FIELD), "name_from_field" => $row->get(B2tSearchFilters::NAME_FROM_FIELD), "from_tbl_crit_field" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_FIELD), "from_tbl_crit_value" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_VALUE), "req_value" => $row->get(B2tSearchFilters::REQ_VALUE), "req_value_field" => $row->get(B2tSearchFilters::REQ_VALUE_FIELD), "value_length" => $row->get(B2tSearchFilters::VALUE_LENGTH), "value_type" => $row->get(B2tSearchFilters::VALUE_TYPE), "includes_notset" => $row->get(B2tSearchFilters::INCLUDES_NOTSET), "notset_description" => $row->get(B2tSearchFilters::NOTSET_DESCRIPTION), "notset_value" => $row->get(B2tSearchFilters::NOTSET_VALUE), "description" => $row->get(B2tSearchFilters::DESCRIPTION), 'join_issues_on' => $row->get(B2tSearchFilters::JOIN_ISSUES_ON));
			}
		}
		elseif (BUGScontext::getRequest()->getParameter('add_filter') != 0 || BUGScontext::getRequest()->getParameter('filter_cc') != '')
		{
			if (BUGScontext::getRequest()->getParameter('add_filter') != 0)
			{
				$row = B2DB::getTable('B2tSearchFilters')->doSelectById(BUGScontext::getRequest()->getParameter('add_filter'));
				$_SESSION['searchfields'][] = array('filter_table' => $row->get(B2tSearchFilters::FILTER_TABLE), "id" => $row->get(B2tSearchFilters::ID), "filter_field" => $row->get(B2tSearchFilters::FILTER_FIELD), "value" => '', "operator" => ($row->get(B2tSearchFilters::FILTER_TYPE) == 1) ? '=' : '' , "filter_type" => $row->get(B2tSearchFilters::FILTER_TYPE), "values_from" => $row->get(B2tSearchFilters::VALUES_FROM), "value_from_field" => $row->get(B2tSearchFilters::VALUE_FROM_FIELD), "name_from_field" => $row->get(B2tSearchFilters::NAME_FROM_FIELD), "from_tbl_crit_field" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_FIELD), "from_tbl_crit_value" => $row->get(B2tSearchFilters::FROM_TBL_CRIT_VALUE), "req_value" => $row->get(B2tSearchFilters::REQ_VALUE), "req_value_field" => $row->get(B2tSearchFilters::REQ_VALUE_FIELD), "value_length" => $row->get(B2tSearchFilters::VALUE_LENGTH), "value_type" => $row->get(B2tSearchFilters::VALUE_TYPE), "includes_notset" => $row->get(B2tSearchFilters::INCLUDES_NOTSET), "notset_description" => $row->get(B2tSearchFilters::NOTSET_DESCRIPTION), "notset_value" => $row->get(B2tSearchFilters::NOTSET_VALUE), "description" => $row->get(B2tSearchFilters::DESCRIPTION), 'join_issues_on' => $row->get(B2tSearchFilters::JOIN_ISSUES_ON));
			}
			if (BUGScontext::getRequest()->getParameter('filter_cc') != '')
			{
				if ($_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['value_type'] == 1 && is_numeric(BUGScontext::getRequest()->getParameter('value')))
				{
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['operator'] = BUGScontext::getRequest()->getParameter('operator');
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['value'] = BUGScontext::getRequest()->getParameter('value');
				}
				elseif ($_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['filter_type'] == 4)
				{
					$day_name = 'day_' . BUGScontext::getRequest()->getParameter('filter_cc');
					$month_name = 'month_' . BUGScontext::getRequest()->getParameter('filter_cc');
					$year_name = 'year_' . BUGScontext::getRequest()->getParameter('filter_cc');
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['operator'] = BUGScontext::getRequest()->getParameter('operator');
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['value'] = mktime(0, 0, 0, (int) BUGScontext::getRequest()->getParameter($month_name), (int) BUGScontext::getRequest()->getParameter($day_name), (int) BUGScontext::getRequest()->getParameter($year_name));
				}
				elseif ($_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['value_type'] == 0)
				{
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['operator'] = BUGScontext::getRequest()->getParameter('operator');
					$_SESSION['searchfields'][BUGScontext::getRequest()->getParameter('filter_cc')]['value'] = BUGScontext::getRequest()->getParameter('value');
				}
			}
		}
		elseif (BUGScontext::getRequest()->getParameter('remove_filter_cc') != '')
		{
			$tempfilters = array();
			for ($fcc = 0;$fcc <= count($_SESSION['searchfields']) - 1;$fcc++)
			{
				if ($fcc != BUGScontext::getRequest()->getParameter('remove_filter_cc'))
				{
					$tempfilters[] = $_SESSION['searchfields'][$fcc];
				}
			}
			$_SESSION['searchfields'] = $tempfilters;
		}
		elseif (BUGScontext::getRequest()->getParameter('save_search'))
		{
			if (BUGScontext::getRequest()->getParameter('saved_search_title'))
			{
				$applies_to = 0;
				foreach ($_SESSION['searchfields'] as $aSearchField)
				{
					if ($aSearchField['filter_field'] == 'B2tIssues::PROJECT_ID' && $aSearchField['operator'] == 'B2DBCriteria::DB_EQUALS')
					{
						if ($applies_to == 0)
						{
							$applies_to = $aSearchField['value'];
						}
						else
						{
							$applies_to = 0;
							break;
						}
					}
				}
				$crit = new B2DBCriteria();
				$crit->addInsert(B2tSavedSearches::NAME, BUGScontext::getRequest()->getParameter('saved_search_title'));
				$searchlayout = (isset($_SESSION['searchlayout'])) ? $_SESSION['searchlayout'] : 0;
				$groupby = (isset($_SESSION['groupby'])) ? $_SESSION['groupby'] : 0;
				$crit->addInsert(B2tSavedSearches::LAYOUT, $searchlayout);
				$crit->addInsert(B2tSavedSearches::GROUPBY, $groupby);
				$crit->addInsert(B2tSavedSearches::SCOPE, BUGScontext::getScope()->getID());
				$crit->addInsert(B2tSavedSearches::APPLIES_TO, $applies_to);
				$crit->addInsert(B2tSavedSearches::UID, BUGScontext::getUser()->getUID());
				$crit->addInsert(B2tSavedSearches::IS_PUBLIC, 0);

				$tmp_searchid = B2DB::getTable('B2tSavedSearches')->doInsert($crit)->getInsertID();

				foreach ($_SESSION['searchfields'] as $aSearchField)
				{
					$crit = new B2DBCriteria();
					$crit->addInsert(B2tSearchFields::FILTER_ID, $aSearchField['id']);
					$crit->addInsert(B2tSearchFields::OPERATOR, $aSearchField['operator']);
					$crit->addInsert(B2tSearchFields::VALUE, $aSearchField['value']);
					$crit->addInsert(B2tSearchFields::SEARCH, $tmp_searchid);
					$crit->addInsert(B2tSearchFields::SCOPE, BUGScontext::getScope()->getID());
					B2DB::getTable('B2tSearchFields')->doInsert($crit);
				}
				$search_issaved = true;
			}
		}
		else
		{
			$showsearch = true;
		}
		if (BUGScontext::getRequest()->getParameter('perform_search'))
		{
			$showsearch = true;
		}
		$appliedFilters = BUGScontext::getModule('search')->getSearchFields();
		if ($simplesearch)
		{
			foreach ($_SESSION['simplefilters'] as $aSimpleSearchFilter)
			{
				$appliedFilters[] = $aSimpleSearchFilter;
			}
		}
		if ($matchfor == '')
		{
			foreach ($appliedFilters as $aFilter)
			{
				$vals = array(B2tComments::CONTENT, B2tIssues::TITLE, B2tIssues::LONG_DESCRIPTION);
				if (in_array($aFilter['filter_field'], $vals) && $aFilter['value'] != '')
				{
					$matchfor = $aFilter['value'];
					break;
				}
			}
		}
	}
	elseif (!BUGScontext::getRequest()->getParameter('custom_search') && !BUGScontext::getRequest()->isAjaxCall())
	{
		unset($_SESSION['searchfields']);
		unset($_SESSION['simplefilters']);
		unset($_SESSION['custom_search']);
		unset($_SESSION['simple_search']);
		unset($_SESSION['searchlayout']);
		unset($_SESSION['groupby']);
		unset($_SESSION['search_queue']);
		$clearedall = true;
	}
	if (BUGScontext::getRequest()->getParameter('set_startpoint') && is_numeric(BUGScontext::getRequest()->getParameter('s_id')))
	{
		$_SESSION['searchfields'] = $appliedFilters;
		$_SESSION['custom_search'] = true;
		$savedsearch = false;
		$showsearch = false;
	}

	$notcompleteFilters = false;
	//var_dump($appliedFilters);
	foreach ($appliedFilters as $aFilter)
	{
		if ($aFilter['operator'] == "" || $aFilter['value'] == "")
		{
			$notcompleteFilters = true;
		}
	}

	if (BUGScontext::getRequest()->getParameter('edit_search') && is_numeric(BUGScontext::getRequest()->getParameter('s_id')))
	{
		$row = B2DB::getTable('B2tSavedSearches')->doSelectById(BUGScontext::getRequest()->getParameter('s_id'));
		if (BUGScontext::getUser()->hasPermission('b2searchmaster', 1, 'search') || BUGScontext::getUser()->hasPermission('b2cancreatepublicsearches', 1, 'search') || $row->get(B2tSavedSearches::UID) == BUGScontext::getUser()->getUID())
		{
			$editingsavedsearch = true;
			$savedsearchtitle = $row->get(B2tSavedSearches::NAME);
			$savedsearch = false;
			$showsearch = false;
		}
	}

	if (BUGScontext::getRequest()->isAjaxCall())
	{
		header ("Content-Type: text/html; charset=" . BUGScontext::getI18n()->getCharset());
		if (BUGScontext::getRequest()->getParameter('get_search_button'))
		{
			require_once BUGScontext::getIncludePath() . 'modules/search/search_button.inc.php';
		}
		if (BUGScontext::getRequest()->getParameter('update_filters'))
		{
			require_once BUGScontext::getIncludePath() . 'modules/search/search_filters.inc.php';
		}
		if (BUGScontext::getRequest()->getParameter('perform_search'))
		{
			$searchresults = BUGScontext::getModule('search')->doSearch(0, false, true, false, (count($_SESSION['simplefilters']) > 0) ? true : false);
			BUGScontext::getModule('search')->presentResultsHTML($_SESSION['searchlayout'], $searchresults['issues'], 0, true, '', $_SESSION['groupby']);
		}
		exit();
	}
	
?>