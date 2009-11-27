<?php

	/**
	 * actions for the search module
	 */
	class searchActions extends BUGSaction
	{

		protected $foundissues = array();
		
		/**
		 * Pre-execute function for search functions
		 *
		 * @param BUGSrequest $request
		 */
		public function preExecute(BUGSrequest $request, $action)
		{
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
			$this->searchterm = $request->getParameter('searchfor');
			$this->ipp = $request->getParameter('issues_per_page', 30);
			$this->offset = $request->getParameter('offset', 0);
			$this->filters = $request->getParameter('filters', array());
			$this->groupby = $request->getParameter('groupby');
		}

		protected function doSearch()
		{
			preg_replace_callback('#(?<!\!)((bug|issue|ticket|story)\s\#?(([A-Z0-9]+\-)?\d+))#i', array($this, 'extractIssues'), $this->searchterm);

			if (count($this->foundissues) == 0)
			{
				$filters = array();
				if (BUGScontext::isProjectContext())
				{
					$filters[B2tIssues::PROJECT_ID] = BUGScontext::getCurrentProject()->getID();
				}
				list ($this->foundissues, $this->resultcount) = BUGSissue::findIssues($this->searchterm, $this->ipp, $this->offset, $filters, null);
			}
			elseif (count($this->foundissues) == 1 && $request->getParameter('quicksearch'))
			{
				$issue = array_shift($this->foundissues);
				$this->forward(BUGScontext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			elseif ($request->hasParameter('sortby'))
			{

			}

			$this->templatename = 'results_normal';
		}

		/**
		 * Performs the "find issues" action
		 *
		 * @param BUGSrequest $request
		 */
		public function runFindIssues(BUGSrequest $request)
		{
			$this->show_results = $request->hasParameter('searchfor');
			$this->_getSearchDetailsFromRequest($request);

			if ($request->hasParameter('searchfor'))
			{
				$this->doSearch();
				$this->issues = $this->foundissues;
			}
		}

		public function runFindIssuesPaginated(BUGSrequest $request)
		{
			$this->_getSearchDetailsFromRequest($request);

			if ($request->hasParameter('searchfor'))
			{
				$this->doSearch();
				$this->issues = $this->foundissues;
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