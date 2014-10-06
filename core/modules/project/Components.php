<?php

    namespace thebuggenie\core\modules\project;

    use thebuggenie\core\entities\DashboardView,
        TBGContext;

    /**
     * Project action components
     */
    class Components extends \TBGActionComponent
    {

        public function componentOverview()
        {
            $this->issuetypes = $this->project->getIssuetypeScheme()->getReportableIssuetypes();
        }

        public function componentBoardSwimlane()
        {
            $this->issues = $this->swimlane->getIssues();
        }

        public function componentBoardColumnheader()
        {
            $this->statuses = \TBGStatus::getAll();
        }

        public function componentMilestoneIssue()
        {

        }

        public function componentMilestoneWhiteboardStatusDetails()
        {
            $this->statuses = \TBGStatus::getAll();
            if ($this->milestone instanceof \TBGMilestone)
                $this->status_details = \TBGIssuesTable::getTable()->getMilestoneDistributionDetails($this->milestone->getID());
        }

        public function componentRecentActivities()
        {
            $this->default_displayed = isset($this->default_displayed) ? $this->default_displayed : false;
        }

        public function componentMilestoneEpic()
        {

        }

        public function componentPlanningColorPicker()
        {
            $this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
        }

        public function componentTimeline()
        {
            $this->prev_date = null;
            $this->prev_timestamp = null;
            $this->prev_issue = null;
        }

        public function componentEditAgileBoard()
        {
            $i18n = TBGContext::getI18n();
            $this->autosearches = array(
                TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => $i18n->__('Project open issues (recommended)'),
                TBGContext::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project open issues (including subprojects)'),
                TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => $i18n->__('Project closed issues'),
                TBGContext::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project closed issues (including subprojects)'),
                TBGContext::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH => $i18n->__('Project issues reported last month'),
                TBGContext::PREDEFINED_SEARCH_PROJECT_WISHLIST => $i18n->__('Project wishlist')
            );
            $this->savedsearches = \TBGSavedSearchesTable::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(\TBGContext::getUser()->getID(), $this->board->getProject()->getID());
            $this->issuetypes = $this->board->getProject()->getIssuetypeScheme()->getIssuetypes();
            $this->swimlane_groups = array(
                'priority' => $i18n->__('Issue priority'),
                'severity' => $i18n->__('Issue severity'),
                'category' => $i18n->__('Issue category'),
            );
            $this->priorities = \TBGPriority::getAll();
            $this->severities = \TBGSeverity::getAll();
            $this->categories = \TBGCategory::getAll();
            $fakecolumn = new \thebuggenie\core\entities\BoardColumn();
            $fakecolumn->setBoard($this->board);
            $this->fakecolumn = $fakecolumn;
        }

        public function componentEditBoardColumn()
        {
            $this->statuses = \TBGStatus::getAll();
        }

        public function componentAgileBoardbox()
        {

        }

        public function componentMilestoneFinish()
        {
        }

        public function componentMilestoneBox()
        {
            $this->include_counts = (isset($this->include_counts)) ? $this->include_counts : false;
            $this->include_buttons = (isset($this->include_buttons)) ? $this->include_buttons : true;
        }

        public function componentMilestone()
        {
            if (!isset($this->milestone))
            {
                $this->milestone = new \TBGMilestone();
                $this->milestone->setProject($this->project);
            }
        }

        public function componentMilestoneDetails()
        {
            $this->total_estimated_points = 0;
            $this->total_spent_points = 0;
            $this->total_estimated_hours = 0;
            $this->total_spent_hours = 0;
            $this->burndown_data = $this->milestone->getBurndownData();
        }

        public function componentDashboardViewProjectInfo()
        {

        }

        public function componentDashboardViewProjectTeam()
        {
            $assignees = array();
            foreach (\TBGContext::getCurrentProject()->getAssignedUsers() as $user)
            {
                $assignees[] = $user;
            }
            foreach (\TBGContext::getCurrentProject()->getAssignedTeams() as $team)
            {
                $assignees[] = $team;
            }
            $this->assignees = $assignees;
            $this->project = \TBGContext::getCurrentProject();
        }

        public function componentDashboardViewProjectClient()
        {
            $this->client = \TBGContext::getCurrentProject()->getClient();
        }

        public function componentDashboardViewProjectSubprojects()
        {
            $this->subprojects = \TBGContext::getCurrentProject()->getChildren(false);
        }

        public function componentDashboardViewProjectStatisticsLast15()
        {
            $this->issues = \TBGContext::getCurrentProject()->getLast15Counts();
        }

        public function componentDashboardViewProjectStatistics()
        {
            switch ($this->view->getType())
            {
                case DashboardView::VIEW_PROJECT_STATISTICS_PRIORITY:
                    $counts = \TBGContext::getCurrentProject()->getPriorityCount();
                    $items = \TBGPriority::getAll();
                    $key = 'priority';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_SEVERITY:
                    $counts = \TBGContext::getCurrentProject()->getSeverityCount();
                    $items = \TBGSeverity::getAll();
                    $key = 'priority';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_CATEGORY:
                    $counts = \TBGContext::getCurrentProject()->getCategoryCount();
                    $items = \TBGCategory::getAll();
                    $key = 'category';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_RESOLUTION:
                    $counts = \TBGContext::getCurrentProject()->getResolutionCount();
                    $items = \TBGResolution::getAll();
                    $key = 'resolution';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_STATUS:
                    $counts = \TBGContext::getCurrentProject()->getStatusCount();
                    $items = \TBGStatus::getAll();
                    $key = 'status';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    $counts = \TBGContext::getCurrentProject()->getWorkflowCount();
                    $items = \TBGWorkflowStep::getAllByWorkflowSchemeID(\TBGContext::getCurrentProject()->getWorkflowScheme()->getID());
                    $key = 'workflowstep';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_STATE:
                    $counts = \TBGContext::getCurrentProject()->getStateCount();
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
            $this->issues = \TBGContext::getCurrentProject()->getRecentIssues($this->view->getDetail());
        }

        public function componentDashboardViewProjectRecentActivities()
        {
            $this->recent_activities = \TBGContext::getCurrentProject()->getRecentActivities(10);
        }

        public function componentDashboardViewProjectDownloads()
        {
            $builds = \TBGContext::getCurrentProject()->getBuilds();
            $active_builds = array();

            foreach (\TBGContext::getCurrentProject()->getEditions() as $edition_id => $edition)
            {
                $active_builds[$edition_id] = array();
            }

            foreach ($builds as $build)
            {
                if ($build->isReleased() && $build->hasFile())
                    $active_builds[$build->getEditionID()][] = $build;
            }

            $this->editions = $active_builds;
        }

        public function componentProjectConfig_Container()
        {
            $this->access_level = ($this->getUser()->canEditProjectDetails(\TBGContext::getCurrentProject())) ? \TBGSettings::ACCESS_FULL : \TBGSettings::ACCESS_READ;
            $this->section = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectConfig()
        {
            $this->access_level = ($this->getUser()->canEditProjectDetails(\TBGContext::getCurrentProject())) ? \TBGSettings::ACCESS_FULL : \TBGSettings::ACCESS_READ;
            $this->statustypes = \TBGStatus::getAll();
            $this->selected_tab = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectInfo()
        {
            $this->valid_subproject_targets = \TBGProject::getValidSubprojects($this->project);
        }

        public function componentProjectSettings()
        {
            $this->statustypes = \TBGStatus::getAll();
        }

        public function componentProjectEdition()
        {
            $this->access_level = ($this->getUser()->canManageProject(\TBGContext::getCurrentProject())) ? \TBGSettings::ACCESS_FULL : \TBGSettings::ACCESS_READ;
        }

        public function componentProjecticons()
        {

        }

        public function componentProjectworkflow()
        {

        }

        public function componentProjectPermissions()
        {
            $this->roles = \TBGRole::getAll();
            $this->project_roles = \TBGRole::getByProjectID($this->project->getID());
        }

        public function componentBuildbox()
        {
            $this->access_level = ($this->getUser()->canManageProject(\TBGContext::getCurrentProject())) ? \TBGSettings::ACCESS_FULL : \TBGSettings::ACCESS_READ;
        }

        public function componentBuild()
        {
            if (!isset($this->build))
            {
                $this->build = new \TBGBuild();
                $this->build->setProject(\TBGContext::getCurrentProject());
                $this->build->setName(\TBGContext::getI18n()->__('%project_name version 0.0.0', array('%project_name' => $this->project->getName())));
                if (\TBGContext::getRequest()->getParameter('edition_id') && $edition = \TBGContext::factory()->TBGEdition(\TBGContext::getRequest()->getParameter('edition_id')))
                {
                    $this->build->setEdition($edition);
                }
            }
        }

    }
