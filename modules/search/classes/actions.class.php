<?php

	/**
	 * actions for the search module
	 */
	class searchActions extends TBGAction
	{

		protected $foundissues = array();
		protected $filters = array();

		/**
		 * Pre-execute function for search functions
		 *
		 * @param TBGRequest $request
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('search') && TBGContext::getUser()->canSearchForIssues());
			if ($request->hasParameter('project_key'))
			{
				if (($project = TBGProject::getByKey($request->getParameter('project_key'))) instanceof TBGProject)
				{
					$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_allpages', $project->getID()));
					TBGContext::getResponse()->setPage('project_issues');
					TBGContext::setCurrentProject($project);
					$this->getResponse()->setProjectMenuStripHidden(false);
				}
			}
			else
			{
				TBGContext::getResponse()->setProjectMenuStripHidden();
			}
			$filters = $request->getParameter('filters', array());
			$this->searchterm = null;
			if (array_key_exists('text', $filters) && array_key_exists('value', $filters['text']))
			{
				$this->searchterm = $filters['text']['value'];
			}
		}

		/**
		 * Performs quicksearch
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runQuickSearch(TBGRequest $request)
		{
			$results = array();

			if ($this->searchterm != '')
			{
				$issue = TBGIssue::getIssueFromLink($this->searchterm);
				if ($issue instanceof TBGIssue)
				{
					if (!TBGContext::isProjectContext() || (TBGContext::isProjectContext() && $issue->getProjectID() == TBGContext::getCurrentProject()->getID()))
					{
						$results[] = $issue;
					}
				}
			}

			$this->results = $results;
		}

		protected function _getSearchDetailsFromRequest(TBGRequest $request)
		{
			$this->ipp = $request->getParameter('issues_per_page', 30);
			$this->offset = $request->getParameter('offset', 0);
			$this->filters = $request->getParameter('filters', array());
			if (TBGContext::isProjectContext())
			{
				$this->filters['project_id'][0] = array('operator' => '=', 'value' => TBGContext::getCurrentProject()->getID());
			}
			$this->groupby = $request->getParameter('groupby');
			$this->grouporder = $request->getParameter('grouporder', 'asc');
			$this->predefined_search = $request->getParameter('predefined_search', false);
			$this->templatename = ($request->hasParameter('template') && in_array($request->getParameter('template'), array_keys($this->getTemplates(false)))) ? $request->getParameter('template') : 'results_normal';
			$this->template_parameter = $request->getParameter('template_parameter');
			$this->searchtitle = TBGContext::getI18n()->__('Search results');
			$this->issavedsearch = false;
			$this->show_results = ($request->hasParameter('quicksearch') || $request->hasParameter('filters') || $request->getParameter('search', false)) ? true : false;

			if ($request->hasParameter('saved_search'))
			{
				$savedsearch = B2DB::getTable('TBGSavedSearchesTable')->doSelectById($request->getParameter('saved_search'));
				if ($savedsearch instanceof B2DBRow && TBGContext::getUser()->canAccessSavedSearch($savedsearch))
				{
					$this->issavedsearch = true;
					$this->savedsearch = $savedsearch;
					$this->templatename = $savedsearch->get(TBGSavedSearchesTable::TEMPLATE_NAME);
					$this->template_parameter = $savedsearch->get(TBGSavedSearchesTable::TEMPLATE_PARAMETER);
					$this->groupby = $savedsearch->get(TBGSavedSearchesTable::GROUPBY);
					$this->grouporder = $savedsearch->get(TBGSavedSearchesTable::GROUPORDER);
					$this->ipp = $savedsearch->get(TBGSavedSearchesTable::ISSUES_PER_PAGE);
					$this->searchtitle = $savedsearch->get(TBGSavedSearchesTable::NAME);
					$this->filters = B2DB::getTable('TBGSavedSearchFiltersTable')->getFiltersBySavedSearchID($savedsearch->get(TBGSavedSearchesTable::ID));
				}
			}
		}

		protected function doSearch(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			if ($this->searchterm)
			{
				preg_replace_callback('#(?<!\!)((bug|issue|ticket|story)\s\#?(([A-Z0-9]+\-)?\d+))#i', array($this, 'extractIssues'), $this->searchterm);
			}

			if (count($this->foundissues) == 0)
			{
				if ($request->hasParameter('predefined_search'))
				{
					switch ((int) $request->getParameter('predefined_search'))
					{
						case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => TBGIssue::STATE_OPEN);
							$this->groupby = 'issuetype';
							break;
						case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => TBGIssue::STATE_CLOSED);
							$this->groupby = 'issuetype';
							break;
						case TBGContext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
							$this->groupby = 'milestone';
							break;
						case TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
							$this->filters['state'] = array('operator' => '=', 'value' => TBGIssue::STATE_OPEN);
							$this->groupby = 'votes';
							$this->grouporder = 'desc';
							break;
						case TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
							$this->filters['posted_by'] = array('operator' => '=', 'value' => TBGContext::getUser()->getID());
							$this->groupby = 'issuetype';
							break;
						case TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => TBGIssue::STATE_OPEN);
							$this->filters['assigned_type'] = array('operator' => '=', 'value' => TBGIdentifiableClass::TYPE_USER);
							$this->filters['assigned_to'] = array('operator' => '=', 'value' => TBGContext::getUser()->getID());
							$this->groupby = 'issuetype';
							break;
						case TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => TBGIssue::STATE_OPEN);
							$this->filters['assigned_type'] = array('operator' => '=', 'value' => TBGIdentifiableClass::TYPE_TEAM);
							foreach (TBGContext::getUser()->getTeams() as $team_id => $team)
							{
								$this->filters['assigned_to'][] = array('operator' => '=', 'value' => $team_id);
							}
							$this->groupby = 'issuetype';
							break;
					}
				}
				elseif (in_array($this->templatename, array('results_userpain_singlepainthreshold', 'results_userpain_totalpainthreshold')))
				{
					$this->searchtitle = $i18n->__('Showing "bug report" issues sorted by user pain, threshold set at %threshold%', array('%threshold%' => $this->template_parameter));
					$this->ipp = 0;
					$this->groupby = 'user_pain';
					$this->grouporder = 'desc';
					$ids = TBGIssueTypesTable::getTable()->getBugReportTypeIDs();
					$this->filters['issuetype'] = array();
					foreach ($ids as $id)
					{
						$this->filters['issuetype'][] = array('operator' => '=', 'value' => $id);
					}
				}
				elseif ($this->templatename == 'results_votes')
				{
					$this->searchtitle = $i18n->__('Showing issues ordered by number of votes');
					$this->ipp = 100;
					$this->groupby = 'votes';
					$this->grouporder = 'desc';
				}
				list ($this->foundissues, $this->resultcount) = TBGIssue::findIssues($this->filters, $this->ipp, $this->offset, $this->groupby, $this->grouporder);
			}
			elseif (count($this->foundissues) == 1 && $request->getParameter('quicksearch'))
			{
				$issue = array_shift($this->foundissues);
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			elseif ($request->hasParameter('sortby'))
			{

			}
			else
			{
				$this->resultcount = count($this->foundissues);
				if ($this->templatename == 'results_userpain_singlepainthreshold')
				{
					usort($this->foundissues, array('searchActions', 'userPainSort'));
				}
			}
			
			if ($request->hasParameter('predefined_search'))
			{
				switch ((int) $request->getParameter('predefined_search'))
				{
					case TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
						$this->searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Open issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('All open issues');
						break;
					case TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
						$this->searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Closed issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('All closed issues');
						break;
					case TBGContext::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO:
						$this->searchtitle = $i18n->__('Milestone todo-list for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName()));
						$this->templatename = 'results_todo';
						break;
					case TBGContext::PREDEFINED_SEARCH_PROJECT_MOST_VOTED:
						$this->searchtitle = (TBGContext::isProjectContext()) ? $i18n->__('Most voted issues for %project_name%', array('%project_name%' => TBGContext::getCurrentProject()->getName())) : $i18n->__('Most voted issues');
						$this->templatename = 'results_votes';
						break;
					case TBGContext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
						$this->searchtitle = $i18n->__('Open issues assigned to me');
						break;
					case TBGContext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
						$this->searchtitle = $i18n->__('Open issues assigned to my teams');
						break;
					case TBGContext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
						$this->searchtitle = $i18n->__('Issues reported by me');
						break;
				}
			}

		}

		protected function getTemplates($display_only = true)
		{
			$templates = array();
			$templates['results_normal'] = TBGContext::getI18n()->__('Standard search results');
			$templates['results_todo'] = TBGContext::getI18n()->__('Todo-list with progress indicator');
			$templates['results_votes'] = TBGContext::getI18n()->__('Most voted-for issues');
			$templates['results_userpain_singlepainthreshold'] = TBGContext::getI18n()->__('User pain indicator with custom single bug pain threshold');
			//$templates['results_userpain_totalpainthreshold'] = TBGContext::getI18n()->__('User pain indicator with custom total pain threshold');
			if (!$display_only)
			{
				$templates['results_rss'] = TBGContext::getI18n()->__('RSS feed');
			}
			return $templates;
		}

		/**
		 * Performs the "find issues" action
		 *
		 * @param TBGRequest $request
		 */
		public function runFindIssues(TBGRequest $request)
		{
			$this->_getSearchDetailsFromRequest($request);

			if ($request->isMethod(TBGRequest::POST))
			{
				if ($request->getParameter('saved_search_name') != '')
				{
					$project_id = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0;
					B2DB::getTable('TBGSavedSearchesTable')->saveSearch($request->getParameter('saved_search_name'), $request->getParameter('saved_search_description'), $request->getParameter('saved_search_public'), $this->filters, $this->groupby, $this->grouporder, $this->ipp, $this->templatename, $this->template_parameter, $project_id, $request->getParameter('saved_search_id'));
					if ($request->getParameter('saved_search_id'))
					{
						TBGContext::setMessage('search_message', TBGContext::getI18n()->__('The saved search was updated'));
					}
					else
					{
						TBGContext::setMessage('search_message', TBGContext::getI18n()->__('The saved search has been created'));
					}
					$params = array();
				}
				else
				{
					TBGContext::setMessage('search_error', TBGContext::getI18n()->__('You have to specify a name for the saved search'));
					$params = array('filters' => $this->filters, 'groupby' => $this->groupby, 'grouporder' => $this->grouporder, 'templatename' => $this->templatename, 'saved_search' => $request->getParameter('saved_search_id'), 'issues_per_page' => $this->ipp);
				}
				if (TBGContext::isProjectContext())
				{
					$route = 'project_issues';
					$params['project_key'] = TBGContext::getCurrentProject()->getKey();
				}
				else
				{
					$route = 'search';
				}
				$this->forward(TBGContext::getRouting()->generate($route, $params));
			}
			else //if ($this->show_results) // generate error when using export after loading issues from the 'Show details'-link in project dashboard
			{
				$this->doSearch($request);
				$this->issues = $this->foundissues;
			}
			$this->search_error = TBGContext::getMessageAndClear('search_error');
			$this->search_message = TBGContext::getMessageAndClear('search_message');
			$this->appliedfilters = $this->filters;
			$this->templates = $this->getTemplates();
			
			$this->savedsearches = B2DB::getTable('TBGSavedSearchesTable')->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID(), (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0);
		}

		public function runFindIssuesPaginated(TBGRequest $request)
		{
			$this->_getSearchDetailsFromRequest($request);

			if ($this->show_results)
			{
				$this->doSearch($request);
				$this->issues = $this->foundissues;
			}
			$this->appliedfilters = $this->filters;
			$this->templates = $this->getTemplates();
		}

		public function runAddFilter(TBGRequest $request)
		{
			if ($request->getParameter('filter_name') == 'project_id' && count(TBGProject::getAll()) == 0)
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('No projects exist so this filter can not be added')));
			}
			elseif (in_array($request->getParameter('filter_name'), TBGIssuesTable::getValidSearchFilters()) || TBGCustomDatatype::doesKeyExist($request->getParameter('filter_name')))
			{
				return $this->renderJSON(array('failed' => false, 'content' => $this->getComponentHTML('search/filter', array('filter' => $request->getParameter('filter_name'), 'key' => $request->getParameter('key', 0)))));
			}
			else
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('This is not a valid search field')));
			}
		}

		protected function extractIssues($matches)
		{
			$issue = TBGIssue::getIssueFromLink($matches[0]);
			if ($issue instanceof TBGIssue)
			{
				if (!TBGContext::isProjectContext() || (TBGContext::isProjectContext() && $issue->getProjectID() == TBGContext::getCurrentProject()->getID()))
				{
					$this->foundissues[$issue->getID()] = $issue;
				}
			}
		}

		static function resultGrouping(TBGIssue $issue, $groupby, $cc, $prevgroup_id)
		{
			$i18n = TBGContext::getI18n();
			$showtablestart = false;
			$showheader = false;
			$groupby_id = 0;
			$groupby_description = '';
			if ($cc == 1) $showtablestart = true;
			if ($groupby != '')
			{
				switch ($groupby)
				{
					case 'category':
						if ($issue->getCategory() instanceof TBGCategory)
						{
							$groupby_id = $issue->getCategory()->getID();
							$groupby_description = $issue->getCategory()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'status':
						if ($issue->getStatus() instanceof TBGStatus)
						{
							$groupby_id = $issue->getStatus()->getID();
							$groupby_description = $issue->getStatus()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'severity':
						if ($issue->getSeverity() instanceof TBGSeverity)
						{
							$groupby_id = $issue->getSeverity()->getID();
							$groupby_description = $issue->getSeverity()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'resolution':
						if ($issue->getResolution() instanceof TBGResolution)
						{
							$groupby_id = $issue->getResolution()->getID();
							$groupby_description = $issue->getResolution()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'edition':
						if ($issue->getEditions())
						{
							$groupby_id = $issue->getFirstAffectedEdition()->getID();
							$groupby_description = $issue->getFirstAffectedEdition()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'build':
						if ($issue->getBuilds())
						{
							$groupby_id = $issue->getFirstAffectedBuild()->getID();
							$groupby_description = $issue->getFirstAffectedBuild()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'component':
						if ($issue->getComponents())
						{
							$groupby_id = $issue->getFirstAffectedComponent()->getID();
							$groupby_description = $issue->getFirstAffectedComponent()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'priority':
						if ($issue->getPriority() instanceof TBGPriority)
						{
							$groupby_id = $issue->getPriority()->getID();
							$groupby_description = $issue->getPriority()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'issuetype':
						if ($issue->getIssueType() instanceof TBGIssuetype)
						{
							$groupby_id = $issue->getIssueType()->getID();
							$groupby_description = $issue->getIssueType()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'milestone':
						if ($issue->getMilestone() instanceof TBGMilestone)
						{
							$groupby_id = $issue->getMilestone()->getID();
							$groupby_description = $issue->getMilestone()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Not targetted');
						}
						break;
					case 'assignee':
						if ($issue->getAssignee() instanceof TBGIdentifiableClass)
						{
							$groupby_id = $issue->getAssigneeID();
							$groupby_description = $issue->getAssignee()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Not assigned');
						}
						break;
					case 'state':
						if ($issue->isClosed())
						{
							$groupby_id = TBGIssue::STATE_CLOSED;
							$groupby_description = $i18n->__('Closed');
						}
						else
						{
							$groupby_id = TBGIssue::STATE_OPEN;
							$groupby_description = $i18n->__('Open');
						}
						break;
				}
				if ($groupby_id !== $prevgroup_id)
				{
					$showtablestart = true;
					$showheader = true;
				}
				$prevgroup_id = $groupby_id;
			}
			return array($showtablestart, $showheader, $prevgroup_id, $groupby_description);
		}

		static function userPainSort(TBGIssue $first_issue, TBGIssue $second_issue)
		{
			$first_issue_pain = $first_issue->getUserPain();
			$second_issue_pain = $second_issue->getUserPain();
			if ($first_issue_pain == $second_issue_pain)
			{
				return 0;
			}
			return ($first_issue_pain < $second_issue_pain) ? -1 : 1;
		}

	}