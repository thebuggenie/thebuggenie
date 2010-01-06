<?php

	/**
	 * actions for the search module
	 */
	class searchActions extends BUGSaction
	{

		protected $foundissues = array();
		protected $filters = array();

		/**
		 * Pre-execute function for search functions
		 *
		 * @param BUGSrequest $request
		 */
		public function preExecute(BUGSrequest $request, $action)
		{
			$this->forward403unless(BUGScontext::getUser()->hasPageAccess('search'));
			if ($request->hasParameter('project_key'))
			{
				if (($project = BUGSproject::getByKey($request->getParameter('project_key'))) instanceof BUGSproject)
				{
					BUGScontext::getResponse()->setPage('project_issues');
					BUGScontext::setCurrentProject($project);
					$this->getResponse()->setProjectMenuStripHidden(false);
				}
			}
			else
			{
				BUGScontext::getResponse()->setProjectMenuStripHidden();
			}
		}

		/**
		 * Performs quicksearch
		 * 
		 * @param BUGSrequest $request The request object
		 */		
		public function runQuickSearch(BUGSrequest $request)
		{
			$this->searchterm = $request->getParameter('searchfor');
			$results = array();

			if ($this->searchterm != '')
			{
				$issue = BUGSissue::getIssueFromLink($this->searchterm);
				if ($issue instanceof BUGSissue)
				{
					if (!BUGScontext::isProjectContext() || (BUGScontext::isProjectContext() && $issue->getProjectID() == BUGScontext::getCurrentProject()->getID()))
					{
						$results[] = $issue;
					}
				}
			}

			$this->results = $results;
		}

		protected function _getSearchDetailsFromRequest(BUGSrequest $request)
		{
			$this->searchterm = $request->getParameter('searchfor', false);
			$this->ipp = $request->getParameter('issues_per_page', 30);
			$this->offset = $request->getParameter('offset', 0);
			$this->filters = $request->getParameter('filters', array());
			$this->groupby = $request->getParameter('groupby');
			$this->predefined_search = $request->getParameter('predefined_search', false);
			//var_dump($this->filters);die();
		}

		protected function doSearch(BUGSrequest $request)
		{
			$i18n = BUGScontext::getI18n();
			if ($this->searchterm)
			{
				preg_replace_callback('#(?<!\!)((bug|issue|ticket|story)\s\#?(([A-Z0-9]+\-)?\d+))#i', array($this, 'extractIssues'), $this->searchterm);
			}

			if (count($this->foundissues) == 0)
			{
				if (BUGScontext::isProjectContext())
				{
					$this->filters['project_id'][0] = array('operator' => '=', 'value' => BUGScontext::getCurrentProject()->getID());
				}
				if ($request->hasParameter('predefined_search'))
				{
					switch ((int) $request->getParameter('predefined_search'))
					{
						case BUGScontext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => BUGSissue::STATE_OPEN);
							break;
						case BUGScontext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => BUGSissue::STATE_CLOSED);
							break;
						case BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
							$this->filters['posted_by'] = array('operator' => '=', 'value' => BUGScontext::getUser()->getID());
							break;
						case BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => BUGSissue::STATE_OPEN);
							$this->filters['assigned_type'] = array('operator' => '=', 'value' => BUGSidentifiableclass::TYPE_USER);
							$this->filters['assigned_to'] = array('operator' => '=', 'value' => BUGScontext::getUser()->getID());
							break;
						case BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
							$this->filters['state'] = array('operator' => '=', 'value' => BUGSissue::STATE_OPEN);
							$this->filters['assigned_type'] = array('operator' => '=', 'value' => BUGSidentifiableclass::TYPE_TEAM);
							foreach (BUGScontext::getUser()->getTeams() as $team_id => $team)
							{
								$this->filters['assigned_to'][] = array('operator' => '=', 'value' => $team_id);
							}
							break;
					}
				}
				list ($this->foundissues, $this->resultcount) = BUGSissue::findIssues($this->searchterm, $this->ipp, $this->offset, $this->filters, null);
			}
			elseif (count($this->foundissues) == 1 && $request->getParameter('quicksearch'))
			{
				$issue = array_shift($this->foundissues);
				$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			elseif ($request->hasParameter('sortby'))
			{

			}
			else
			{
				$this->resultcount = count($this->foundissues);
			}

			$this->searchtitle = __('Search results');
			if ($request->hasParameter('predefined_search'))
			{
				switch ((int) $request->getParameter('predefined_search'))
				{
					case BUGScontext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES:
						$this->searchtitle = (BUGScontext::isProjectContext()) ? $i18n->__('Open issues for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())) : $i18n->__('All open issues');
						break;
					case BUGScontext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES:
						$this->searchtitle = (BUGScontext::isProjectContext()) ? $i18n->__('Closed issues for %project_name%', array('%project_name%' => BUGScontext::getCurrentProject()->getName())) : $i18n->__('All closed issues');
						break;
					case BUGScontext::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES:
						$this->searchtitle = $i18n->__('Open issues assigned to me');
						break;
					case BUGScontext::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES:
						$this->searchtitle = $i18n->__('Open issues assigned to my teams');
						break;
					case BUGScontext::PREDEFINED_SEARCH_MY_REPORTED_ISSUES:
						$this->searchtitle = $i18n->__('Issues reported by me');
						break;
				}
			}

		}

		protected function getTemplates($display_only = true)
		{
			$templates = array();
			$templates['results_normal'] = BUGScontext::getI18n()->__('Standard search results');
			$templates['results_todo'] = BUGScontext::getI18n()->__('Todo-list with progress indicator');
			if (!$display_only)
			{
				$templates['results_rss'] = BUGScontext::getI18n()->__('RSS feed');
			}
			return $templates;
		}

		/**
		 * Performs the "find issues" action
		 *
		 * @param BUGSrequest $request
		 */
		public function runFindIssues(BUGSrequest $request)
		{
			$this->show_results = ($request->hasParameter('searchfor') || $request->getParameter('search', false)) ? true : false;
			$this->_getSearchDetailsFromRequest($request);

			if ($this->show_results)
			{
				$this->doSearch($request);
				$this->issues = $this->foundissues;
			}
			$this->appliedfilters = $this->filters;
			$this->templates = $this->getTemplates();
			$this->templatename = ($request->hasParameter('template') && in_array($request->getParameter('template'), array_keys($this->getTemplates(false)))) ? $request->getParameter('template') : 'results_normal';
			if ($this->templatename == 'results_rss')
			{
				return $this->renderComponent('search/results_rss', array('issues' => $this->issues, 'searchtitle' => $this->searchtitle));
			}
		}

		public function runFindIssuesPaginated(BUGSrequest $request)
		{
			$this->_getSearchDetailsFromRequest($request);

			if ($request->hasParameter('searchfor'))
			{
				$this->doSearch($request);
				$this->issues = $this->foundissues;
			}
			$this->appliedfilters = $this->filters;
			$this->templates = $this->getTemplates();
			$this->templatename = ($request->hasParameter('template') && in_array($request->getParameter('template'), array_keys($this->getTemplates()))) ? $request->getParameter('template') : 'results_normal';
		}

		public function runAddFilter(BUGSrequest $request)
		{
			if (in_array($request->getParameter('filter_name'), B2tIssues::getValidSearchFilters()) || BUGScustomdatatype::doesKeyExist($request->getParameter('filter_name')))
			{
				return $this->renderJSON(array('failed' => false, 'content' => $this->getComponentHTML('search/filter', array('filter' => $request->getParameter('filter_name'), 'key' => $request->getParameter('key', 0)))));
			}
			else
			{
				return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('This is not a valid search field')));
			}
		}

		protected function extractIssues($matches)
		{
			$issue = BUGSissue::getIssueFromLink($matches[0]);
			if ($issue instanceof BUGSissue)
			{
				if (!BUGScontext::isProjectContext() || (BUGScontext::isProjectContext() && $issue->getProjectID() == BUGScontext::getCurrentProject()->getID()))
				{
					$this->foundissues[$issue->getID()] = $issue;
				}
			}
		}

	}