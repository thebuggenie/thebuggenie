<?php

    namespace thebuggenie\core\modules\project;

    use thebuggenie\core\framework,
        thebuggenie\core\entities,
        thebuggenie\core\entities\tables;

    /**
     * Project action components
     */
    class Components extends framework\ActionComponent
    {

        public function componentOverview()
        {
            $this->issuetypes = $this->project->getIssuetypeScheme()->getReportableIssuetypes();
        }

        public function componentMilestoneIssue()
        {
        }

        public function componentMilestoneVirtualStatusDetails()
        {
            $this->statuses = \thebuggenie\core\entities\Status::getAll();
            if ($this->milestone instanceof \thebuggenie\core\entities\Milestone)
                $this->status_details = \thebuggenie\core\entities\tables\Issues::getTable()->getMilestoneDistributionDetails($this->milestone->getID());
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
                $this->milestone = new \thebuggenie\core\entities\Milestone();
                $this->milestone->setProject($this->project);
            }
        }

        public function componentMilestoneBox()
        {
            $this->include_counts = (isset($this->include_counts)) ? $this->include_counts : false;
            $this->include_buttons = (isset($this->include_buttons)) ? $this->include_buttons : true;
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
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_PRIORITY:
                    $counts = framework\Context::getCurrentProject()->getPriorityCount();
                    $items = entities\Priority::getAll();
                    $key = 'priority';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_SEVERITY:
                    $counts = framework\Context::getCurrentProject()->getSeverityCount();
                    $items = entities\Severity::getAll();
                    $key = 'priority';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_CATEGORY:
                    $counts = framework\Context::getCurrentProject()->getCategoryCount();
                    $items = entities\Category::getAll();
                    $key = 'category';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_RESOLUTION:
                    $counts = framework\Context::getCurrentProject()->getResolutionCount();
                    $items = entities\Resolution::getAll();
                    $key = 'resolution';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_STATUS:
                    $counts = framework\Context::getCurrentProject()->getStatusCount();
                    $items = entities\Status::getAll();
                    $key = 'status';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_WORKFLOW_STEP:
                    $counts = framework\Context::getCurrentProject()->getWorkflowCount();
                    $items = entities\WorkflowStep::getAllByWorkflowSchemeID(framework\Context::getCurrentProject()->getWorkflowScheme()->getID());
                    $key = 'workflowstep';
                    break;
                case entities\DashboardView::VIEW_PROJECT_STATISTICS_STATE:
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
            $this->statustypes = entities\Status::getAll();
            $this->selected_tab = isset($this->section) ? $this->section : 'info';
        }

        public function componentProjectInfo()
        {
            $this->valid_subproject_targets = entities\Project::getValidSubprojects($this->project);
        }

        public function componentProjectSettings()
        {
            $this->statustypes = entities\Status::getAll();
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
            $this->roles = entities\Role::getAll();
            $this->project_roles = entities\Role::getByProjectID($this->project->getID());
        }

        public function componentBuildbox()
        {
            $this->access_level = ($this->getUser()->canManageProject(framework\Context::getCurrentProject())) ? framework\Settings::ACCESS_FULL : framework\Settings::ACCESS_READ;
        }

        public function componentBuild()
        {
            if (!isset($this->build))
            {
                $this->build = new entities\Build();
                $this->build->setProject(framework\Context::getCurrentProject());
                $this->build->setName(framework\Context::getI18n()->__('%project_name version 0.0.0', array('%project_name' => $this->project->getName())));
                if (framework\Context::getRequest()->getParameter('edition_id') && $edition = entities\Edition::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('edition_id')))
                {
                    $this->build->setEdition($edition);
                }
            }
        }

    }
