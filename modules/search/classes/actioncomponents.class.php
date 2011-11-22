<?php

	class searchActionComponents extends TBGActionComponent
	{

		public function componentPagination()
		{
			$this->currentpage = ceil($this->offset / $this->ipp) + 1;
			$this->pagecount = ceil($this->resultcount / $this->ipp);
			$parameters = array();
			foreach ($this->filters as $key => $filter)
			{
				if (is_array($filter))
				{
					foreach ($filter as $subkey => $subfilter)
					{
						if (is_array($subfilter))
						{
							foreach ($subfilter as $subsubkey => $subsubfilter)
							{
								$parameters[] = "filters[{$key}][{$subkey}][{$subsubkey}]=".urlencode($subsubfilter);
							}
						}
						else
						{
							$parameters[] = "filters[{$key}][{$subkey}]=".urlencode($subfilter);
						}
					}
				}
				else
				{
					$parameters[] = "filters[{$key}]=".urlencode($filter);
				}
			}
			$parameters[] = 'template='.$this->templatename;
			$parameters[] = 'template_parameter='.$this->template_parameter;
			$parameters[] = 'searchterm='.$this->searchterm;
			$parameters[] = 'groupby='.$this->groupby;
			$parameters[] = 'grouporder='.$this->grouporder;
			$parameters[] = 'issues_per_page='.$this->ipp;
			$route = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('project_search_paginated', array('project_key' => TBGContext::getCurrentProject()->getKey())) : TBGContext::getRouting()->generate('search_paginated');
			$this->route = $route;
			$this->parameters = join('&', $parameters);
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
				$this->selected_value = ($this->selected_value) ? $this->selected_value : time();
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
			if (TBGContext::isProjectContext())
			{
				$filters['component'] = array('description' => $i18n->__('Component'), 'options' => TBGContext::getCurrentProject()->getComponents());
				$filters['build'] = array('description' => $i18n->__('Build'), 'options' => TBGContext::getCurrentProject()->getBuilds());
				$filters['edition'] = array('description' => $i18n->__('Edition'), 'options' => TBGContext::getCurrentProject()->getEditions());
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
				$filters = TBGSavedSearchFiltersTable::getTable()->getFiltersBySavedSearchID($request['saved_search']);
			}
			list ($this->issues, $this->resultcount) = TBGIssue::findIssues($filters);
		}		
		
		public function componentSidebar()
		{
			$savedsearches = \b2db\Core::getTable('TBGSavedSearchesTable')->getAllSavedSearchesByUserIDAndPossiblyProjectID(TBGContext::getUser()->getID(), (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0);
			foreach ($savedsearches['user'] as $a_savedsearch)
				$this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), __($a_savedsearch->get(TBGSavedSearchesTable::NAME)));

			foreach ($savedsearches['public'] as $a_savedsearch)
				$this->getResponse()->addFeed(make_url('search', array('saved_search' => $a_savedsearch->get(TBGSavedSearchesTable::ID), 'search' => true, 'format' => 'rss')), __($a_savedsearch->get(TBGSavedSearchesTable::NAME)));
			
			$this->savedsearches = $savedsearches;
		}
		
		public function componentSearchbuilder()
		{
			$this->templates = searchActions::getTemplates();
			$this->filters = $this->appliedfilters;
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
			$this->columns = array('title' => $i18n->__('Issue title'), 'issuetype' => $i18n->__('Issue type'), 'assigned_to' => $i18n->__('Assigned to'), 'status' => $i18n->__('Status'), 'resolution' => $i18n->__('Resolution'), 'category' => $i18n->__('Category'), 'severity' => $i18n->__('Severity'), 'percent_complete' => $i18n->__('% completed'), 'reproducability' => $i18n->__('Reproducability'), 'priority' => $i18n->__('Priority'), 'milestone' => $i18n->__('Milestone'), 'last_updated' => $i18n->__('Last updated time'), 'comments' => $i18n->__('Number of comments'));
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
