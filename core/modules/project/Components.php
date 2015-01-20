<?php

    namespace thebuggenie\core\modules\project;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\DashboardView;

    /**
     * Project action components
     */
    class Components extends framework\ActionComponent
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
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
        }

        public function componentMilestoneIssue()
        {
        }

        public function componentAgileWhiteboardTransitionSelector()
        {
            foreach ($this->board->getColumns() as $column)
            {
                if ($column->hasIssue($this->issue))
                {
                    $this->current_column = $column;
                    break;
                }
            }
        }

        public function componentMilestoneWhiteboardStatusDetails()
        {
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
            if ($this->milestone instanceof \thebuggenie\core\entities\Milestone)
                $this->status_details = \thebuggenie\core\entities\tables\Issues::getTable()->getMilestoneDistributionDetails($this->milestone->getID());
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
            $i18n = framework\Context::getI18n();
            $this->autosearches = array(
                framework\Context::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES => $i18n->__('Project open issues (recommended)'),
                framework\Context::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project open issues (including subprojects)'),
                framework\Context::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES => $i18n->__('Project closed issues'),
                framework\Context::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS => $i18n->__('Project closed issues (including subprojects)'),
                framework\Context::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH => $i18n->__('Project issues reported last month'),
                framework\Context::PREDEFINED_SEARCH_PROJECT_WISHLIST => $i18n->__('Project wishlist')
            );
            $this->savedsearches = \thebuggenie\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), $this->board->getProject()->getID());
            $this->issuetypes = $this->board->getProject()->getIssuetypeScheme()->getIssuetypes();
            $this->swimlane_groups = array(
                'priority' => $i18n->__('Issue priority'),
                'severity' => $i18n->__('Issue severity'),
                'category' => $i18n->__('Issue category'),
            );
            $this->priorities = \thebuggenie\core\entities\Priority::getAll();
            $this->severities = \thebuggenie\core\entities\Severity::getAll();
            $this->categories = \thebuggenie\core\entities\Category::getAll();
            $fakecolumn = new \thebuggenie\core\entities\BoardColumn();
            $fakecolumn->setBoard($this->board);
            $this->fakecolumn = $fakecolumn;
        }

        public function componentEditBoardColumn()
        {
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
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
                $this->milestone = new \thebuggenie\core\entities\Milestone();
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
            foreach (framework\Context::getCurrentProject()->getAssignedUsers() as $user)
            {
                $assignees[] = $user;
            }
            foreach (framework\Context::getCurrentProject()->getAssignedTeams() as $team)
            {
                $assignees[] = $team;
            }
            $this->assignees = $assignees;
            $this->project = framework\Context::getCurrentProject();
        }

        public function componentDashboardViewProjectClient()
        {
            $this->client = framework\Context::getCurrentProject()->getClient();
        }

        public function componentDashboardViewProjectSubprojects()
        {
            $this->subprojects = framework\Context::getCurrentProject()->getChildren(false);
        }

        public function componentDashboardViewProjectStatisticsLast15()
        {
            $this->issues = framework\Context::getCurrentProject()->getLast15Counts();
        }

        public function componentDashboardViewProjectStatistics()
        {
            switch ($this->view->getType())
            {
                case DashboardView::VIEW_PROJECT_STATISTICS_PRIORITY:
                    $counts = framework\Context::getCurrentProject()->getPriorityCount();
                    $items = \thebuggenie\core\entities\Priority::getAll();
                    $key = 'priority';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_SEVERITY:
                    $counts = framework\Context::getCurrentProject()->getSeverityCount();
                    $items = \thebuggenie\core\entities\Severity::getAll();
                    $key = 'priority';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_CATEGORY:
                    $counts = framework\Context::getCurrentProject()->getCategoryCount();
                    $items = \thebuggenie\core\entities\Category::getAll();
                    $key = 'category';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_RESOLUTION:
                    $counts = framework\Context::getCurrentProject()->getResolutionCount();
                    $items = \thebuggenie\core\entities\Resolution::getAll();
                    $key = 'resolution';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_STATUS:
                    $counts = framework\Context::getCurrentProject()->getStatusCount();
                    $items = \thebuggenie\core\entities\Status::getAll();
                    $key = 'status';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    $counts = framework\Context::getCurrentProject()->getWorkflowCount();
                    $items = \thebuggenie\core\entities\WorkflowStep::getAllByWorkflowSchemeID(framework\Context::getCurrentProject()->getWorkflowScheme()->getID());
                    $key = 'workflowstep';
                    break;
                case DashboardView::VIEW_PROJECT_STATISTICS_STATE:
                    $counts = framework\Context::getCurrentProject()->getStateCount();
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
            $this->issues = framework\Context::getCurrentProject()->getRecentIssues($this->view->getDetail());
        }

        public function componentDashboardViewProjectRecentActivities()
        {
            $this->recent_activities = framework\Context::getCurrentProject()->getRecentActivities(10);
        }

        public function componentDashboardViewProjectDownloads()
        {
            $builds = framework\Context::getCurrentProject()->getBuilds();
            $active_builds = array();

            foreach (framework\Context::getCurrentProject()->getEditions() as $edition_id => $edition)
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
            $this->access_level = ($this->getUser()->canEditProjectDetails(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
            $this->section = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectConfig()
        {
            $this->access_level = ($this->getUser()->canEditProjectDetails(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
            $this->statustypes = \thebuggenie\core\entities\Status::getAll();
            $this->selected_tab = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectInfo()
        {
            $this->valid_subproject_targets = \thebuggenie\core\entities\Project::getValidSubprojects($this->project);
        }

        public function componentProjectSettings()
        {
            $this->statustypes = \thebuggenie\core\entities\Status::getAll();
        }

        public function componentProjectEdition()
        {
            $this->access_level = ($this->getUser()->canManageProject(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        public function componentProjecticons()
        {

        }

        public function componentProjectworkflow()
        {

        }

        public function componentProjectPermissions()
        {
            $this->roles = \thebuggenie\core\entities\Role::getAll();
            $this->project_roles = \thebuggenie\core\entities\Role::getByProjectID($this->project->getID());
        }

        public function componentBuildbox()
        {
            $this->access_level = ($this->getUser()->canManageProject(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        public function componentBuild()
        {
            if (!isset($this->build))
            {
                $this->build = new \thebuggenie\core\entities\Build();
                $this->build->setProject(framework\Context::getCurrentProject());
                $this->build->setName(framework\Context::getI18n()->__('%project_name version 0.0.0', array('%project_name' => $this->project->getName())));
                if (framework\Context::getRequest()->getParameter('edition_id') && $edition = \thebuggenie\core\entities\Edition::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('edition_id')))
                {
                    $this->build->setEdition($edition);
                }
            }
        }

    }
