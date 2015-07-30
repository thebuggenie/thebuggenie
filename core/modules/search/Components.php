<?php

    namespace thebuggenie\core\modules\search;

    use thebuggenie\core\framework,
        thebuggenie\core\entities,
        thebuggenie\core\entities\tables;

    class Components extends framework\ActionComponent
    {

        /**
         * @protected entities\SavedSearch $search_object
         */
        public function componentPagination()
        {
            $this->currentpage = $this->search_object->getCurrentPage();
            $this->pagecount = $this->search_object->getNumberOfPages();
            $this->ipp = $this->search_object->getIssuesPerPage();
            $this->route = (framework\Context::isProjectContext()) ? framework\Context::getRouting()->generate('project_search_paginated', array('project_key' => framework\Context::getCurrentProject()->getKey())) : framework\Context::getRouting()->generate('search_paginated');
            $this->parameters = $this->search_object->getParametersAsString();
        }

        public function componentInteractiveFilter()
        {

        }

        public function componentFilter()
        {
            $pkey = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getID() : null;

            $i18n = framework\Context::getI18n();
            $this->selected_operator = (isset($this->selected_operator)) ? $this->selected_operator : '=';
            $this->key = (isset($this->key)) ? $this->key : null;
            $this->filter = (isset($this->filter)) ? $this->filter : null;
            if (in_array($this->filter, array('posted', 'last_updated')))
            {
                $this->selected_value = ($this->selected_value) ? $this->selected_value : NOW;
            }
            else
            {
                $this->selected_value = (isset($this->selected_value)) ? $this->selected_value : 0;
            }
            $this->filter_info = (isset($this->filter_info)) ? $this->filter_info : null;

            $filters = array();
            $filters['status'] = array('description' => $i18n->__('Status'), 'options' => entities\Status::getAll());
            $filters['category'] = array('description' => $i18n->__('Category'), 'options' => entities\Category::getAll());
            $filters['priority'] = array('description' => $i18n->__('Priority'), 'options' => entities\Priority::getAll());
            $filters['severity'] = array('description' => $i18n->__('Severity'), 'options' => entities\Severity::getAll());
            $filters['reproducability'] = array('description' => $i18n->__('Reproducability'), 'options' => entities\Reproducability::getAll());
            $filters['resolution'] = array('description' => $i18n->__('Resolution'), 'options' => entities\Resolution::getAll());
            $filters['issuetype'] = array('description' => $i18n->__('Issue type'), 'options' => entities\Issuetype::getAll());
            $filters['component'] = array('description' => $i18n->__('Component'), 'options' => array());
            $filters['build'] = array('description' => $i18n->__('Build'), 'options' => array());
            $filters['edition'] = array('description' => $i18n->__('Edition'), 'options' => array());
            $filters['milestone'] = array('description' => $i18n->__('Milestone'), 'options' => array());

            if (framework\Context::isProjectContext())
            {
                $filters['subprojects'] = array('description' => $i18n->__('Include subproject(s)'), 'options' => array('all' => $this->getI18n()->__('All subprojects'), 'none' => $this->getI18n()->__("Don't include subprojects (default, unless specified otherwise)")));
                $projects = entities\Project::getIncludingAllSubprojectsAsArray(framework\Context::getCurrentProject());
                foreach ($projects as $project)
                {
                    if ($project->getID() == framework\Context::getCurrentProject()->getID())
                        continue;

                    $filters['subprojects']['options'][$project->getID()] = "{$project->getName()} ({$project->getKey()})";
                }
            }
            else
            {
                $projects = array();
                foreach (entities\Project::getAllRootProjects() as $project)
                {
                    entities\Project::getSubprojectsArray($project, $projects);
                }
            }
            if (count($projects) > 0)
            {
                foreach ($projects as $project)
                {
                    foreach ($project->getComponents() as $component)
                        $filters['component']['options'][] = $component;
                    foreach ($project->getBuilds() as $build)
                        $filters['build']['options'][] = $build;
                    foreach ($project->getEditions() as $edition)
                        $filters['edition']['options'][] = $edition;
                    foreach ($project->getMilestones() as $milestone)
                        $filters['milestone']['options'][] = $milestone;
                }
            }
            $filters['posted_by'] = array('description' => $i18n->__('Posted by'));
            $filters['assignee_user'] = array('description' => $i18n->__('Assigned to user'));
            $filters['assignee_team'] = array('description' => $i18n->__('Assigned to team'));
            $filters['owner_user'] = array('description' => $i18n->__('Owned by user'));
            $filters['owner_team'] = array('description' => $i18n->__('Owned by team'));
            $filters['posted'] = array('description' => $i18n->__('Date reported'));
            $filters['last_updated'] = array('description' => $i18n->__('Date last updated'));
            $this->filters = $filters;
        }

        public function componentResults_normal()
        {
            if (!property_exists($this, 'show_project'))
            {
                $this->show_project = false;
            }
            $this->default_columns = entities\SavedSearch::getDefaultVisibleColumns();
            $this->custom_columns = entities\CustomDatatype::getAll();
            $this->visible_columns = $this->search_object->getColumns();
            $this->cc = (isset($this->cc)) ? $this->cc : 0;
            $this->actionable = (isset($this->actionable)) ? $this->actionable : true;
            $this->prevgroup_id = (isset($this->prevgroup_id)) ? $this->prevgroup_id : 0;
        }

        public function componentResults_normal_sheet()
        {
            $this->custom_columns = entities\CustomDatatype::getAll();
            $this->cc = (isset($this->cc)) ? $this->cc : 0;
            require realpath(THEBUGGENIE_VENDOR_PATH) . DS . 'phpoffice' . DS . 'phpexcel' . DS . 'Classes' . DS . 'PHPExcel.php';
            $phpexcel = new \PHPExcel();
            foreach ($phpexcel->getAllSheets() as $index => $sheet)
            {
                $phpexcel->removeSheetByIndex($index);
            }

            $this->phpexcel = $phpexcel;
            $this->sheet = $phpexcel->createSheet();
        }

        public function componentResults_todo()
        {

        }

        public function componentResults_votes()
        {

        }

        public function componentResults_userpain_singlepainthreshold()
        {

        }

        public function componentResults_view()
        {
            if ($this->view->getType() == entities\DashboardView::VIEW_PREDEFINED_SEARCH)
            {
                $request = framework\Context::getRequest();
                $request->setParameter('predefined_search', $this->view->getDetail());
                $search = entities\SavedSearch::getFromRequest($request);
            }
            elseif ($this->view->getType() == entities\DashboardView::VIEW_SAVED_SEARCH)
            {
                $search = tables\SavedSearches::getTable()->selectById($this->view->getDetail());
            }
            $this->issues = $search->getIssues();
            $this->resultcount = $search->getTotalNumberOfIssues();
        }

        public function componentSidebar()
        {
            $savedsearches = tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID(), (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getID() : 0);
            foreach ($savedsearches['user'] as $a_savedsearch)
                $this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), __($a_savedsearch->getName()));

            foreach ($savedsearches['public'] as $a_savedsearch)
                $this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), __($a_savedsearch->getName()));

            $this->savedsearches = $savedsearches;
        }

        public function componentSearchbuilder()
        {
            $this->templates = entities\SavedSearch::getTemplates();
            $this->filters = $this->appliedfilters;
            $this->nondatecustomfields = entities\CustomDatatype::getAllExceptTypes(array(entities\CustomDatatype::DATE_PICKER));
            $this->datecustomfields = entities\CustomDatatype::getByFieldType(entities\CustomDatatype::DATE_PICKER);
            $i18n = framework\Context::getI18n();
            $columns = array('title' => $i18n->__('Issue title'), 'issuetype' => $i18n->__('Issue type'), 'assigned_to' => $i18n->__('Assigned to'), 'posted_by' => $i18n->__('Posted by'), 'status' => $i18n->__('Status'), 'resolution' => $i18n->__('Resolution'), 'category' => $i18n->__('Category'), 'severity' => $i18n->__('Severity'), 'percent_complete' => $i18n->__('Percent completed'), 'reproducability' => $i18n->__('Reproducability'), 'priority' => $i18n->__('Priority'), 'components' => $i18n->__('Component(s)'), 'milestone' => $i18n->__('Milestone'), 'estimated_time' => $i18n->__('Estimate'), 'spent_time' => $i18n->__('Time spent'), 'last_updated' => $i18n->__('Last updated time'), 'posted' => $i18n->__('Posted at'), 'comments' => $i18n->__('Number of comments'));
            foreach ($this->nondatecustomfields as $field)
            {
                $columns[$field->getKey()] = $i18n->__($field->getName());
            }
            foreach ($this->datecustomfields as $field)
            {
                $columns[$field->getKey()] = $i18n->__($field->getName());
            }
            $this->columns = $columns;
            $groupoptions = array();
            if (!framework\Context::isProjectContext())
                $groupoptions['project_id'] = $i18n->__('Project');

            $groupoptions['milestone'] = $i18n->__('Milestone');
            $groupoptions['assignee'] = $i18n->__("Who's assigned");
            $groupoptions['posted_by'] = $i18n->__("Who posted the issue");
            $groupoptions['state'] = $i18n->__('State (open or closed)');
            $groupoptions['status'] = $i18n->__('Status');
            $groupoptions['category'] = $i18n->__('Category');
            $groupoptions['priority'] = $i18n->__('Priority');
            $groupoptions['severity'] = $i18n->__('Severity');
            $groupoptions['resolution'] = $i18n->__('Resolution');
            $groupoptions['issuetype'] = $i18n->__('Issue type');
            $groupoptions['edition'] = $i18n->__('Edition');
            $groupoptions['build'] = $i18n->__('Release');
            $groupoptions['component'] = $i18n->__('Component');
            $groupoptions['posted'] = $i18n->__('Posted at');

            $this->groupoptions = $groupoptions;
        }

        public function componentBulkWorkflow()
        {
            $workflow_items = array();
            $project = null;
            $issues = array();
            $first = true;
            foreach ($this->issue_ids as $issue_id)
            {
                $issue = new entities\Issue($issue_id);
                $issues[$issue_id] = $issue;
                if ($first)
                {
                    $workflow_items = $issue->getAvailableWorkflowTransitions();
                    $project = $issue->getProject();
                    $first = false;
                }
                else
                {
                    $transitions = $issue->getAvailableWorkflowTransitions();
                    foreach ($workflow_items as $transition_id => $transition)
                    {
                        if (!array_key_exists($transition_id, $transitions))
                            unset($workflow_items[$transition_id]);
                    }
                    if ($issue->getProject()->getID() != $project->getID())
                    {
                        $project = null;
                        break;
                    }
                }
                if (!count($workflow_items))
                    break;
            }

            $this->issues = $issues;
            $this->project = $project;
            $this->available_transitions = $workflow_items;
        }

    }
