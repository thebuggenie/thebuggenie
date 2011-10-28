<?php

	/**
	 * Project action components
	 */
	class projectActionComponents extends TBGActionComponent
	{

		public function componentOverview()
		{
			$this->issuetypes = $this->project->getIssuetypeScheme()->getReportableIssuetypes();
		}

		public function componentScrumcard()
		{
			$this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
		}
		
		public function componentMilestoneIssue()
		{
			$this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
		}
		
		public function componentRecentActivities()
		{
			$this->default_displayed = isset($this->default_displayed) ? $this->default_displayed : false;
		}
		
		public function componentTimeline()
		{
			$this->prev_date = null;
			$this->prev_timestamp = null;
			$this->prev_issue = null;
		}
		
		public function componentMilestone()
		{
			if (!isset($this->milestone)) 
			{
				$this->milestone = new TBGMilestone();
				$this->milestone->setProject($this->project);
			}
		}

		public function componentDashboardViewProjectInfo()
		{

		}

		public function componentDashboardViewProjectTeam()
		{
			$this->assignees = TBGContext::getCurrentProject()->getAssignees();
		}

		public function componentDashboardViewProjectClient()
		{
			$this->client = TBGContext::getCurrentProject()->getClient();
		}

		public function componentDashboardViewProjectSubprojects()
		{
			$this->subprojects = TBGContext::getCurrentProject()->getChildren(false);
		}

		public function componentDashboardViewProjectStatisticsLast15()
		{
			$this->issues = TBGContext::getCurrentProject()->getLast15Counts();
		}

		public function componentDashboardViewProjectStatistics()
		{
			switch ($this->view->getType())
			{
				case TBGDashboardView::VIEW_PROJECT_STATISTICS_PRIORITY:
					$counts = TBGContext::getCurrentProject()->getPriorityCount();
					$items = TBGPriority::getAll();
					$key = 'priority';
					break;
				case TBGDashboardView::VIEW_PROJECT_STATISTICS_CATEGORY:
					$counts = TBGContext::getCurrentProject()->getCategoryCount();
					$items = TBGCategory::getAll();
					$key = 'category';
					break;
				case TBGDashboardView::VIEW_PROJECT_STATISTICS_RESOLUTION:
					$counts = TBGContext::getCurrentProject()->getResolutionCount();
					$items = TBGResolution::getAll();
					$key = 'resolution';
					break;
				case TBGDashboardView::VIEW_PROJECT_STATISTICS_STATUS:
					$counts = TBGContext::getCurrentProject()->getStatusCount();
					$items = TBGStatus::getAll();
					$key = 'status';
					break;
				case TBGDashboardView::VIEW_PROJECT_STATISTICS_STATE:
					$counts = TBGContext::getCurrentProject()->getStateCount();
					$items = array('open' => $this->getI18n()->__('Open'), 'closed' => $this->getI18n()->__('Closed'));
					$key = 'state';
					break;
			}
			$this->counts = $counts;
			$this->key = $key;
			$this->items = $items;
		}

		public function componentDashboardViewProjectUpcoming()
		{
		}

		public function componentDashboardViewProjectRecentIssues()
		{
			$this->issues = TBGContext::getCurrentProject()->getRecentIssues($this->view->getDetail());
		}

		public function componentDashboardViewProjectRecentActivities()
		{
			$this->recent_activities = TBGContext::getCurrentProject()->getRecentActivities(10);
		}

		public function componentDashboardViewRecentComments()
		{
			$this->comments = TBGComment::getRecentCommentsByAuthor($this->getUser()->getID());
		}

		public function componentDashboardViewProjectDownloads()
		{
		}

	}