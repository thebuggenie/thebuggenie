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
			$assignees = array();
			foreach (TBGContext::getCurrentProject()->getAssignedUsers() as $user)
			{
				$assignees[] = $user;
			}
			foreach (TBGContext::getCurrentProject()->getAssignedTeams() as $team)
			{
				$assignees[] = $team;
			}
			$this->assignees = $assignees;
			$this->project = TBGContext::getCurrentProject();
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

		public function componentDashboardViewProjectDownloads()
		{
		}

		public function componentProjectConfig_Container()
		{
			$this->access_level = ($this->getUser()->canEditProjectDetails(TBGContext::getCurrentProject())) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
			$this->section = isset($this->section) ? $this->section : 'info';
		}

		public function componentProjectConfig()
		{
			$this->access_level = ($this->getUser()->canEditProjectDetails(TBGContext::getCurrentProject())) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
			$this->statustypes = TBGStatus::getAll();
			$this->selected_tab = isset($this->section) ? $this->section : 'info';
		}

		public function componentProjectInfo()
		{
			$this->valid_subproject_targets = TBGProject::getValidSubprojects($this->project);
		}

		public function componentProjectSettings()
		{
			$this->statustypes = TBGStatus::getAll();
		}

		public function componentProjectEdition()
		{
			$this->access_level = ($this->getUser()->canManageProject(TBGContext::getCurrentProject())) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
		}

		public function componentProjecticons()
		{
		}

		public function componentProjectworkflow()
		{
		}

		public function componentProjectPermissions()
		{
			$this->roles = TBGRole::getAll();
			$this->project_roles = TBGRole::getByProjectID($this->project->getID());
		}

		public function componentBuildbox()
		{
			$this->access_level = ($this->getUser()->canManageProject(TBGContext::getCurrentProject())) ? TBGSettings::ACCESS_FULL : TBGSettings::ACCESS_READ;
		}

		public function componentBuild()
		{
			if (!isset($this->build))
			{
				$this->build = new TBGBuild();
				$this->build->setProject(TBGContext::getCurrentProject());
				$this->build->setName(TBGContext::getI18n()->__('%project_name% version 0.0.0', array('%project_name%' => $this->project->getName())));
				if (TBGContext::getRequest()->getParameter('edition_id') && $edition = TBGContext::factory()->TBGEdition(TBGContext::getRequest()->getParameter('edition_id')))
				{
					$this->build->setEdition($edition);
				}
			}
		}

	}