<?php

	class searchActionComponents extends TBGActionComponent
	{

		/**
		 * @protected TBGSavedSearch $search_object
		 */

		public function componentPagination()
		{
			$this->currentpage = $this->search_object->getCurrentPage();
			$this->pagecount = $this->search_object->getNumberOfPages();
			$this->ipp = $this->search_object->getIssuesPerPage();
			$this->route = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('project_search_paginated', array('project_key' => TBGContext::getCurrentProject()->getKey())) : TBGContext::getRouting()->generate('search_paginated');
			$this->parameters = $this->search_object->getParametersAsString();
		}

		public function componentInteractiveFilter()
		{

		}

		public function componentFilter()
		{
			$pkey = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : null;

			$i18n = TBGContext::getI18n();
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
			$filters['status'] = array('description' => $i18n->__('Status'), 'options' => TBGStatus::getAll());
			$filters['category'] = array('description' => $i18n->__('Category'), 'options' => TBGCategory::getAll());
			$filters['priority'] = array('description' => $i18n->__('Priority'), 'options' => TBGPriority::getAll());
			$filters['severity'] = array('description' => $i18n->__('Severity'), 'options' => TBGSeverity::getAll());
			$filters['reproducability'] = array('description' => $i18n->__('Reproducability'), 'options' => TBGReproducability::getAll());
			$filters['resolution'] = array('description' => $i18n->__('Resolution'), 'options' => TBGResolution::getAll());
			$filters['issuetype'] = array('description' => $i18n->__('Issue type'), 'options' => TBGIssuetype::getAll());
			$filters['component'] = array('description' => $i18n->__('Component'), 'options' => array());
			$filters['build'] = array('description' => $i18n->__('Build'), 'options' => array());
			$filters['edition'] = array('description' => $i18n->__('Edition'), 'options' => array());
			$filters['milestone'] = array('description' => $i18n->__('Milestone'), 'options' => array());

			if (TBGContext::isProjectContext())
			{
				$filters['subprojects'] = array('description' => $i18n->__('Include subproject(s)'), 'options' => array('all' => $this->getI18n()->__('All subprojects'), 'none' => $this->getI18n()->__("Don't include subprojects (default, unless specified otherwise)")));
				$projects = TBGProject::getIncludingAllSubprojectsAsArray(TBGContext::getCurrentProject());
				foreach ($projects as $project)
				{
					if ($project->getID() == TBGContext::getCurrentProject()->getID()) continue;
					
					$filters['subprojects']['options'][$project->getID()] = "{$project->getName()} ({$project->getKey()})";
				}
			}
			else
			{
				$projects = array();
				foreach (TBGProject::getAllRootProjects() as $project)
				{
					TBGProject::getSubprojectsArray($project, $projects);
				}
			}
			if (count($projects) > 0)
			{
				foreach ($projects as $project)
				{
					foreach ($project->getComponents() as $component) $filters['component']['options'][] = $component;
					foreach ($project->getBuilds() as $build) $filters['build']['options'][] = $build;
					foreach ($project->getEditions() as $edition) $filters['edition']['options'][] = $edition;
					foreach ($project->getMilestones() as $milestone) $filters['milestone']['options'][] = $milestone;
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
			$columns = array('title', 'assigned_to', 'status', 'resolution', 'last_updated', 'comments');
			$this->default_columns = $columns;
			if ($cols = TBGSettings::get('search_scs_results_normal'))
			{
				$columns = explode(',', $cols);
			}
			$this->visible_columns = $columns;
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
			if ($this->view->getType() == TBGDashboardView::VIEW_PREDEFINED_SEARCH)
			{
				list($filters, $groupby, $grouporder) = TBGSavedSearchesTable::getPredefinedVariables($this->view->getDetail());
			}
			elseif ($this->view->getType() == TBGDashboardView::VIEW_SAVED_SEARCH)
			{
				$search = TBGSavedSearchesTable::getTable()->selectById($this->view->getDetail());
				$filters = $search->getFilters(); // TBGSavedSearchFiltersTable::getTable()->getFiltersBySavedSearchID($this->view->getDetail());
			}
			list ($this->issues, $this->resultcount) = TBGIssue::findIssues($filters);
		}		
		
		public function componentSidebar()
		{
			$savedsearches = TBGSavedSearchesTable::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID(), (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0);
			foreach ($savedsearches['user'] as $a_savedsearch)
				$this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), __($a_savedsearch->getName()));

			foreach ($savedsearches['public'] as $a_savedsearch)
				$this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->getID(), 'search' => true, 'format' => 'rss')), __($a_savedsearch->getName()));
			
			$this->savedsearches = $savedsearches;
		}
		
		public function componentSearchbuilder()
		{
			$this->templates = TBGSavedSearch::getTemplates();
			$this->filters = $this->appliedfilters;
			$this->nondatecustomfields = TBGCustomDatatype::getAllExceptTypes(array(TBGCustomDatatype::DATE_PICKER));
			$this->datecustomfields = TBGCustomDatatype::getByFieldType(TBGCustomDatatype::DATE_PICKER);
			$i18n = TBGContext::getI18n();
			$this->columns = array('title' => $i18n->__('Issue title'), 'issuetype' => $i18n->__('Issue type'), 'assigned_to' => $i18n->__('Assigned to'), 'status' => $i18n->__('Status'), 'resolution' => $i18n->__('Resolution'), 'category' => $i18n->__('Category'), 'severity' => $i18n->__('Severity'), 'percent_complete' => $i18n->__('% completed'), 'reproducability' => $i18n->__('Reproducability'), 'priority' => $i18n->__('Priority'), 'components' => $i18n->__('Component(s)'), 'milestone' => $i18n->__('Milestone'), 'estimated_time' => $i18n->__('Estimate'), 'spent_time' => $i18n->__('Time spent'), 'last_updated' => $i18n->__('Last updated time'), 'comments' => $i18n->__('Number of comments'));
			$groupoptions = array();
			if (!TBGContext::isProjectContext()) $groupoptions['project_id'] = $i18n->__('Project');

			$groupoptions['milestone'] = $i18n->__('Milestone');
			$groupoptions['assignee'] = $i18n->__("Who's assigned");
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

			$this->groupoptions = $groupoptions;
		}

		public function componentExtralinks()
		{
			switch (true)
			{
				case TBGContext::getRequest()->hasParameter('quicksearch'):
					$searchfor = TBGContext::getRequest()->getParameter('searchfor');
					$project_key = (TBGContext::getCurrentProject() instanceof TBGProject) ? TBGContext::getCurrentProject()->getKey() : 0;
					$this->csv_url = TBGContext::getRouting()->generate('project_issues', array('project_key' => $project_key, 'quicksearch' => 'true', 'format' => 'csv')).'?searchfor='.$searchfor;
					$this->rss_url = TBGContext::getRouting()->generate('project_issues', array('project_key' => $project_key, 'quicksearch' => 'true', 'format' => 'rss')).'?searchfor='.$searchfor;
					break;
				case TBGContext::getRequest()->hasParameter('predefined_search'):
					$searchno = TBGContext::getRequest()->getParameter('predefined_search');
					$project_key = (TBGContext::getCurrentProject() instanceof TBGProject) ? TBGContext::getCurrentProject()->getKey() : 0;
					$url = (TBGContext::getCurrentProject() instanceof TBGProject) ? 'project_issues' : 'search';
					$this->csv_url = TBGContext::getRouting()->generate($url, array('project_key' => $project_key, 'predefined_search' => $searchno, 'search' => '1', 'format' => 'csv'));
					$this->rss_url = TBGContext::getRouting()->generate($url, array('project_key' => $project_key, 'predefined_search' => $searchno, 'search' => '1', 'format' => 'rss'));
					break;
				default:
					preg_match('/((?<=\/)issues).+$/i', TBGContext::getRequest()->getQueryString(), $get);
					
					if (!isset($get[0])) preg_match('/((?<=url=)issues).+$/i', TBGContext::getRequest()->getQueryString(), $get);

					if (isset($get[0]))
					{
						if (TBGContext::isProjectContext())
						{
							$this->csv_url = TBGContext::getRouting()->generate('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'csv')).'/'.$get[0];
							$this->rss_url = TBGContext::getRouting()->generate('project_issues', array('project_key' => TBGContext::getCurrentProject()->getKey(), 'format' => 'rss')).'?'.$get[0];
						}
						else
						{
							$this->csv_url = TBGContext::getRouting()->generate('search', array('format' => 'csv')).'/'.$get[0];
							$this->rss_url = TBGContext::getRouting()->generate('search', array('format' => 'rss')).'?'.$get[0];
						}
					}
					break;
			}
			$i18n = TBGContext::getI18n();
			$this->columns = array('title' => $i18n->__('Issue title'), 'issuetype' => $i18n->__('Issue type'), 'assigned_to' => $i18n->__('Assigned to'), 'status' => $i18n->__('Status'), 'resolution' => $i18n->__('Resolution'), 'category' => $i18n->__('Category'), 'severity' => $i18n->__('Severity'), 'percent_complete' => $i18n->__('% completed'), 'reproducability' => $i18n->__('Reproducability'), 'priority' => $i18n->__('Priority'), 'components' => $i18n->__('Component(s)'), 'milestone' => $i18n->__('Milestone'), 'estimated_time' => $i18n->__('Estimate'), 'spent_time' => $i18n->__('Time spent'), 'last_updated' => $i18n->__('Last updated time'), 'comments' => $i18n->__('Number of comments'));
		}

		public function componentBulkWorkflow()
		{
			$workflow_items = array();
			$project = null;
			$issues = array();
			$first = true;
			foreach ($this->issue_ids as $issue_id)
			{
				$issue = new TBGIssue($issue_id);
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
				if (!count($workflow_items)) break;
			}

			$this->issues = $issues;
			$this->project = $project;
			$this->available_transitions = $workflow_items;
		}
		
	}
