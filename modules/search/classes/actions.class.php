<?php

	/**
	 * actions for the search module
	 */
	class searchActions extends TBGAction
	{

		protected $foundissues = array();
		protected $filters = array();

		/**
		 * @var TBGSavedSearch
		 * @property $search_object
		 */

		/**
		 * Pre-execute function for search functions
		 *
		 * @param TBGRequest $request
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('search') && TBGContext::getUser()->canSearchForIssues());
			
			if ($project_key = $request['project_key']) {
				$project = TBGProject::getByKey($project_key);
			} elseif (is_numeric($request['project_id']) && $project_id = (int) $request['project_id']) {
				$project = TBGProjectsTable::getTable()->selectById($project_id);
			} else {
				$project = false;
			}

			if ($project instanceof TBGProject)
			{
				$this->forward403unless(TBGContext::getUser()->hasProjectPageAccess('project_issues', $project));
				TBGContext::getResponse()->setPage('project_issues');
				TBGContext::setCurrentProject($project);
			}
			$this->search_object = TBGSavedSearch::getFromRequest($request);
			$this->issavedsearch = ($this->search_object instanceof TBGSavedSearch && $this->search_object->getB2DBID());
			$this->show_results = ($this->issavedsearch || $request->hasParameter('quicksearch') || $request->hasParameter('filters') || $request->getParameter('search', false)) ? true : false;

			$this->searchterm = $this->search_object->getSearchterm();
			$this->searchtitle = $this->search_object->getTitle();

			if ($this->issavedsearch)
			{
				if (!($this->search_object instanceof TBGSavedSearch && TBGContext::getUser()->canAccessSavedSearch($this->search_object)))
				{
					TBGContext::setMessage('search_error', TBGContext::getI18n()->__("You don't have access to this saved search"));
				}
			}
		}

		/**
		 * Performs quicksearch
		 * 
		 * @param TBGRequest $request The request object
		 */		
		public function runQuickSearch(TBGRequest $request)
		{
			if ($this->getUser()->canAccessConfigurationPage(TBGSettings::CONFIGURATION_SECTION_USERS))
			{
				$this->found_users = TBGUsersTable::getTable()->findInConfig($this->searchterm, 10, false);
				$this->found_teams = TBGTeamsTable::getTable()->quickfind($this->searchterm);
				$this->found_clients = TBGClientsTable::getTable()->quickfind($this->searchterm);
				$this->num_users = count($this->found_users);
				$this->num_teams = count($this->found_teams);
				$this->num_clients = count($this->found_clients);
			}
			$found_projects = TBGProjectsTable::getTable()->quickfind($this->searchterm);
			$projects = array();
			foreach ($found_projects as $project)
			{
				if ($project->hasAccess()) $projects[$project->getID()] = $project;
			}
			$this->found_projects = $projects;
			$this->num_projects = count($projects);
		}

		protected function doSearch(TBGRequest $request)
		{
			$i18n = TBGContext::getI18n();
			if ($this->searchterm)
			{
				preg_replace_callback(TBGTextParser::getIssueRegex(), array($this, 'extractIssues'), $this->searchterm);

				if (!count($this->foundissues))
				{
					$issue = TBGIssue::getIssueFromLink($this->searchterm);
					if ($issue instanceof TBGIssue)
					{
						$this->foundissues = array($issue);
						$this->resultcount = 1;
					}
				}
			}

			if (count($this->foundissues) == 0)
			{
				$this->foundissues = $this->search_object->getIssues();
				$this->resultcount = $this->search_object->getTotalNumberOfIssues();
			}
			elseif (count($this->foundissues) == 1 && !$request['quicksearch'])
			{
				$issue = array_shift($this->foundissues);
				$this->forward(TBGContext::getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
			}
			else
			{
				$this->resultcount = count($this->foundissues);
				if ($this->templatename == 'results_userpain_singlepainthreshold')
				{
					usort($this->foundissues, array('searchActions', 'userPainSort'));
				}
			}
		}

		public function runSaveSearch(TBGRequest $request)
		{
			$name = trim($request['name']);
			if (strlen($name) > 0)
			{
				$this->search_object->setName($request['name']);
				$this->search_object->setDescription($request['description']);
				$this->search_object->setIsPublic((bool) $request['is_public']);
				$this->search_object->setUser($this->getUser());
				$this->search_object->setValuesFromRequest($request);
				if ($request['project_id']) $this->search_object->setAppliesToProject((int) $request['project_id']);

				if (!$request['update_saved_search']) $this->search_object->clearID();

				$this->search_object->save();
				TBGContext::setMessage('search_message', 'saved_search');

				if ($request['project_id'])
					return $this->renderJSON(array('forward' => $this->getRouting()->generate('project_issues', array('project_key' => $this->search_object->getProject()->getKey(), 'saved_search_id' => $this->search_object->getID()), false)));
				else
					return $this->renderJSON(array('forward' => $this->getRouting()->generate('search', array('saved_search_id' => $this->search_object->getID()), false)));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => $this->getI18n()->__('Please provide a name for the saved search')));
			}
		}

		public function runEditSavedSearch(TBGRequest $request)
		{
			if ($request->isPost())
			{
				if ($request['delete_saved_search'])
				{
					try
					{
						if (!$this->search_object instanceof TBGSavedSearch || !$this->search_object->getB2DBID()) throw new Exception('not a saved search');

						if ($this->search_object->getUserID() == TBGContext::getUser()->getID() || $this->search_object->isPublic() && TBGContext::getUser()->canCreatePublicSearches())
						{
							$search->delete();
							return $this->renderJSON(array('failed' => false, 'message' => TBGContext::getI18n()->__('The saved search was deleted successfully')));
						}
					}
					catch (Exception $e)
					{
						return $this->renderJSON(array('failed' => true, 'message' => TBGContext::getI18n()->__('Cannot delete this saved search')));
					}
				}
				elseif ($request['saved_search_name'] != '')
				{
//					$project_id = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0;
//					TBGSavedSearchesTable::getTable()->saveSearch($request['saved_search_name'], $request['saved_search_description'], $request['saved_search_public'], $this->filters, $this->groupby, $this->grouporder, $this->ipp, $this->templatename, $this->template_parameter, $project_id, $request['saved_search_id']);

					if (!$search instanceof TBGSavedSearch) $search = new TBGSavedSearch();

					$search->setName($request['saved_search_name']);
					$search->setDescription($request['saved_search_description']);
					$search->setIsPublic((bool) $request['saved_search_public']);
					$search->save();

					if ($request['saved_search_id'])
					{
						TBGContext::setMessage('search_message', TBGContext::getI18n()->__('The saved search was updated'));
					}
					else
					{
						TBGContext::setMessage('search_message', TBGContext::getI18n()->__('The saved search has been created'));
					}
					$params = array();
				}
				else
				{
					TBGContext::setMessage('search_error', TBGContext::getI18n()->__('You have to specify a name for the saved search'));
					$params = array('filters' => $this->filters, 'groupby' => $this->groupby, 'grouporder' => $this->grouporder, 'templatename' => $this->templatename, 'saved_search' => $request['saved_search_id'], 'issues_per_page' => $this->ipp);
				}
				if (TBGContext::isProjectContext())
				{
					$route = 'project_issues';
					$params['project_key'] = TBGContext::getCurrentProject()->getKey();
				}
				else
				{
					$route = 'search';
				}
				$this->forward(TBGContext::getRouting()->generate($route, $params));
			}
		}

		/**
		 * Performs the "find issues" action
		 *
		 * @param TBGRequest $request
		 */
		public function runFindIssues(TBGRequest $request)
		{
			$this->resultcount = 0;
			if ($this->show_results)
			{
				$this->doSearch($request);
				$this->issues = $this->foundissues;
			}
			if ($request['quicksearch'] == true)
			{
				if ($request->isAjaxCall())
				{
					$this->redirect('quicksearch');
				}
				else
				{
					$issues = $this->issues;
					$issue = array_shift($issues);
					if ($issue instanceof TBGIssue)
					{
						return $this->forward($this->getRouting()->generate('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
					}
				}
			}
			$this->search_error = TBGContext::getMessageAndClear('search_error');
			$this->search_message = TBGContext::getMessageAndClear('search_message');
			$this->appliedfilters = $this->filters;
			$this->templates = TBGSavedSearch::getTemplates();
		}

		public function runFindIssuesPaginated(TBGRequest $request)
		{
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);

//			if ($this->show_results)
//			{
//				$this->doSearch($request);
//				$this->issues = $this->foundissues;
//			}
//			$this->appliedfilters = $this->filters;
//			$this->templates = TBGSavedSearch::getTemplates();

			return $this->renderJSON(array(
				'content' => $this->getTemplateHTML('search/issues_paginated', array('search_object' => $this->search_object, 'cc' => 1, 'prevgroup_id' => null)),
				'num_issues' => $this->search_object->getTotalNumberOfIssues()
			));
		}

		public function runAddFilter(TBGRequest $request)
		{
			if ($request['filter_name'] == 'project_id' && count(TBGProject::getAll()) == 0)
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('No projects exist so this filter can not be added')));
			}
			elseif (in_array($request['filter_name'], TBGSearchFilter::getValidSearchFilters()) || TBGCustomDatatype::doesKeyExist($request['filter_name']))
			{
				return $this->renderJSON(array('content' => $this->getComponentHTML('search/filter', array('filter' => $request['filter_name'], 'key' => $request->getParameter('key', 0)))));
			}
			else
			{
				$this->getResponse()->setHttpStatus(400);
				return $this->renderJSON(array('error' => TBGContext::getI18n()->__('This is not a valid search field')));
			}
		}
		
		public function runFilterFindUsers(TBGRequest $request)
		{
			$filter = $request['filter'];
			$filterkey = $request['filterkey'];
			$existing_users = $request['existing_id'];
			
			if (strlen($filter) < 3) return $this->renderJSON(array('results' => '<li>'.$this->getI18n()->__('Please enter 3 characters or more').'</li>'));
			
			$users = TBGUsersTable::getTable()->getByDetails($filter, 10);
			foreach ($existing_users as $id) 
			{ 
				if (isset($users[$id])) 
					unset($users[$id]); 
			}
			
			return $this->renderJSON(array('results' => $this->getTemplateHTML('search/filterfindusers', compact('users', 'filterkey'))));
		}

		public function runFilterFindTeams(TBGRequest $request)
		{
			$filter = $request['filter'];
			$filterkey = $request['filterkey'];
			$existing_teams = $request['existing_id'];
			
			if (strlen($filter) < 3) return $this->renderJSON(array('results' => '<li>'.$this->getI18n()->__('Please enter 3 characters or more').'</li>'));
			
			$teams = TBGTeamsTable::getTable()->quickfind($filter, 10);
			foreach ($existing_teams as $id) 
			{ 
				if (isset($teams[$id])) 
					unset($teams[$id]); 
			}
			
			return $this->renderJSON(array('results' => $this->getTemplateHTML('search/filterfindteams', compact('teams', 'filterkey'))));
		}

		public function runFilterGetDynamicChoices(TBGRequest $request)
		{
			$subproject_ids = explode(',', $request['subprojects']);
			$existing_ids = $request['existing_ids'];
			$results = array();
			$projects = ($request['project_id'] != '') ? TBGProject::getAllByIDs(explode(',', $request['project_id'])) : TBGProject::getAll();

			$items = array('build' => array(), 'edition' => array(), 'component' => array(), 'milestone' => array());

			foreach ($projects as $project)
			{
				foreach ($project->getBuilds() as $build) 
					$items['build'][$build->getID()] = $build;

				foreach ($project->getEditions() as $edition) 
					$items['edition'][$edition->getID()] = $edition;

				foreach ($project->getComponents() as $component) 
					$items['component'][$component->getID()] = $component;

				foreach ($project->getMilestones() as $milestone) 
					$items['milestone'][$milestone->getID()] = $milestone;
			}

			$filters = array();
			$filters['build'] = TBGSearchFilter::createFilter('build');
			$filters['edition'] = TBGSearchFilter::createFilter('edition');
			$filters['component'] = TBGSearchFilter::createFilter('component');
			$filters['milestone'] = TBGSearchFilter::createFilter('milestone');
			if (isset($existing_ids['build']))
			{
				foreach (TBGBuildsTable::getTable()->getByIDs($existing_ids['build']) as $build)
					$items['build'][$build->getID()] = $build;

				$filters['build']->setValue(join(',', $existing_ids['build']));
			}
			if (isset($existing_ids['edition']))
			{
				foreach (TBGEditionsTable::getTable()->getByIDs($existing_ids['edition']) as $edition)
					$items['edition'][$edition->getID()] = $edition;

				$filters['edition']->setValue(join(',', $existing_ids['edition']));
			}
			if (isset($existing_ids['component']))
			{
				foreach (TBGComponentsTable::getTable()->getByIDs($existing_ids['component']) as $component)
					$items['component'][$component->getID()] = $component;

				$filters['component']->setValue(join(',', $existing_ids['component']));
			}
			if (isset($existing_ids['milestone']))
			{
				foreach (TBGMilestonesTable::getTable()->getByIDs($existing_ids['milestone']) as $milestone)
					$items['milestone'][$milestone->getID()] = $milestone;

				$filters['milestone']->setValue(join(',', $existing_ids['milestone']));
			}

			foreach (array('build', 'edition', 'component', 'milestone') as $k)
			{
				$results[$k] = $this->getTemplateHTML('search/interactivefilterdynamicchoicelist', array('filter' => $filters[$k], 'items' => $items[$k]));
			}

			return $this->renderJSON(compact('results'));
		}

		public function extractIssues($matches)
		{
			$issue = TBGIssue::getIssueFromLink($matches["issues"]);
			if ($issue instanceof TBGIssue)
			{
				if (!TBGContext::isProjectContext() || (TBGContext::isProjectContext() && $issue->getProjectID() == TBGContext::getCurrentProject()->getID()))
				{
					$this->foundissues[$issue->getID()] = $issue;
					$this->resultcount++;
				}
			}
		}
		
		public function runOpensearch(TBGRequest $request)
		{
			
		}

		static function resultGrouping(TBGIssue $issue, $groupby, $cc, $prevgroup_id)
		{
			$i18n = TBGContext::getI18n();
			$showtablestart = false;
			$showheader = false;
			$groupby_id = 0;
			$groupby_description = '';
			if ($cc == 1) $showtablestart = true;
			if ($groupby != '')
			{
				switch ($groupby)
				{
					case 'category':
						if ($issue->getCategory() instanceof TBGCategory)
						{
							$groupby_id = $issue->getCategory()->getID();
							$groupby_description = $issue->getCategory()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'status':
						if ($issue->getStatus() instanceof TBGStatus)
						{
							$groupby_id = $issue->getStatus()->getID();
							$groupby_description = $issue->getStatus()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'severity':
						if ($issue->getSeverity() instanceof TBGSeverity)
						{
							$groupby_id = $issue->getSeverity()->getID();
							$groupby_description = $issue->getSeverity()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'resolution':
						if ($issue->getResolution() instanceof TBGResolution)
						{
							$groupby_id = $issue->getResolution()->getID();
							$groupby_description = $issue->getResolution()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'edition':
						if ($issue->getEditions())
						{
							$groupby_id = $issue->getFirstAffectedEdition()->getID();
							$groupby_description = $issue->getFirstAffectedEdition()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'build':
						if ($issue->getBuilds())
						{
							$groupby_id = $issue->getFirstAffectedBuild()->getID();
							$groupby_description = $issue->getFirstAffectedBuild()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'component':
						if ($issue->getComponents())
						{
							$groupby_id = $issue->getFirstAffectedComponent()->getID();
							$groupby_description = $issue->getFirstAffectedComponent()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('None');
						}
						break;
					case 'priority':
						if ($issue->getPriority() instanceof TBGPriority)
						{
							$groupby_id = $issue->getPriority()->getID();
							$groupby_description = $issue->getPriority()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'issuetype':
						if ($issue->getIssueType() instanceof TBGIssuetype)
						{
							$groupby_id = $issue->getIssueType()->getID();
							$groupby_description = $issue->getIssueType()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Unknown');
						}
						break;
					case 'milestone':
						if ($issue->getMilestone() instanceof TBGMilestone)
						{
							$groupby_id = $issue->getMilestone()->getID();
							$groupby_description = $issue->getMilestone()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Not targetted');
						}
						break;
					case 'assignee':
						if ($issue->getAssignee() instanceof TBGIdentifiableClass)
						{
							$groupby_id = $issue->getAssignee()->getID();
							$groupby_description = $issue->getAssignee()->getName();
						}
						else
						{
							$groupby_id = 0;
							$groupby_description = $i18n->__('Not assigned');
						}
						break;
					case 'state':
						if ($issue->isClosed())
						{
							$groupby_id = TBGIssue::STATE_CLOSED;
							$groupby_description = $i18n->__('Closed');
						}
						else
						{
							$groupby_id = TBGIssue::STATE_OPEN;
							$groupby_description = $i18n->__('Open');
						}
						break;
				}
				if ($groupby_id !== $prevgroup_id)
				{
					$showtablestart = true;
					$showheader = true;
				}
				$prevgroup_id = $groupby_id;
			}
			return array($showtablestart, $showheader, $prevgroup_id, $groupby_description);
		}

		public static function userPainSort(TBGIssue $first_issue, TBGIssue $second_issue)
		{
			$first_issue_pain = $first_issue->getUserPain();
			$second_issue_pain = $second_issue->getUserPain();
			if ($first_issue_pain == $second_issue_pain)
			{
				return 0;
			}
			return ($first_issue_pain < $second_issue_pain) ? -1 : 1;
		}
		
		public function runSaveColumnSettings(TBGRequest $request)
		{
			TBGSettings::saveSetting('search_scs_'.$request['template'], join(',', $request['columns']));
			return $this->renderJSON('template '.$request['template'].' columns saved ok');
		}

		public function runBulkUpdateIssues(TBGRequest $request)
		{
			$issue_ids = $request['issue_ids'];
			$options = array('issue_ids' => array_values($issue_ids));
			TBGContext::loadLibrary('common');
			$options['last_updated'] = tbg_formatTime(time(), 20);

			if (!empty($issue_ids))
			{
				$options['bulk_action'] = $request['bulk_action'];
				switch ($request['bulk_action'])
				{
					case 'assign_milestone':
						$milestone = null;
						if ($request['milestone'] == 'new')
						{
							$milestone = new TBGMilestone();
							$milestone->setProject(TBGContext::getCurrentProject());
							$milestone->setName($request['milestone_name']);
							$milestone->save();
							$options['milestone_url'] = TBGContext::getRouting()->generate('project_planning_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()));
						}
						elseif ($request['milestone'])
						{
							$milestone = new TBGMilestone($request['milestone']);
						}
						$milestone_id = ($milestone instanceof TBGMilestone) ? $milestone->getID() : null;
						foreach (array_keys($issue_ids) as $issue_id)
						{
							if (is_numeric($issue_id))
							{
								$issue = new TBGIssue($issue_id);
								$issue->setMilestone($milestone_id);
								$issue->save();
							}
						}
						$options['milestone_id'] = $milestone_id;
						$options['milestone_name'] = ($milestone_id) ? $milestone->getName() : '-';
						break;
					case 'set_status':
						if (is_numeric($request['status']))
						{
							$status = new TBGStatus($request['status']);
							foreach (array_keys($issue_ids) as $issue_id)
							{
								if (is_numeric($issue_id))
								{
									$issue = new TBGIssue($issue_id);
									$issue->setStatus($status->getID());
									$issue->save();
								}
							}
							$options['status'] = array('color' => $status->getColor(), 'name' => $status->getName(), 'id' => $status->getID());
						}
						break;
					case 'set_severity':
						if (is_numeric($request['severity']))
						{
							$severity = ($request['severity']) ? new TBGSeverity($request['severity']) : null;
							foreach (array_keys($issue_ids) as $issue_id)
							{
								if (is_numeric($issue_id))
								{
									$issue = new TBGIssue($issue_id);
									$severity_id = ($severity instanceof TBGSeverity) ? $severity->getID() : 0;
									$issue->setSeverity($severity_id);
									$issue->save();
								}
							}
							$options['severity'] = array('name' => ($severity instanceof TBGSeverity) ? $severity->getName() : '-', 'id' => ($severity instanceof TBGSeverity) ? $severity->getID() : 0);
						}
						break;
					case 'set_resolution':
						if (is_numeric($request['resolution']))
						{
							$resolution = ($request['resolution']) ? new TBGResolution($request['resolution']) : null;
							foreach (array_keys($issue_ids) as $issue_id)
							{
								if (is_numeric($issue_id))
								{
									$issue = new TBGIssue($issue_id);
									$resolution_id = ($resolution instanceof TBGResolution) ? $resolution->getID() : 0;
									$issue->setResolution($resolution_id);
									$issue->save();
								}
							}
							$options['resolution'] = array('name' => ($resolution instanceof TBGResolution) ? $resolution->getName() : '-', 'id' => ($resolution instanceof TBGResolution) ? $resolution->getID() : 0);
						}
						break;
					case 'set_priority':
						if (is_numeric($request['priority']))
						{
							$priority = ($request['priority']) ? new TBGPriority($request['priority']) : null;
							foreach (array_keys($issue_ids) as $issue_id)
							{
								if (is_numeric($issue_id))
								{
									$issue = new TBGIssue($issue_id);
									$priority_id = ($priority instanceof TBGPriority) ? $priority->getID() : 0;
									$issue->setPriority($priority_id);
									$issue->save();
								}
							}
							$options['priority'] = array('name' => ($priority instanceof TBGPriority) ? $priority->getName() : '-', 'id' => ($priority instanceof TBGPriority) ? $priority->getID() : 0);
						}
						break;
					case 'set_category':
						if (is_numeric($request['category']))
						{
							$category = ($request['category']) ? new TBGCategory($request['category']) : null;
							foreach (array_keys($issue_ids) as $issue_id)
							{
								if (is_numeric($issue_id))
								{
									$issue = new TBGIssue($issue_id);
									$category_id = ($category instanceof TBGCategory) ? $category->getID() : 0;
									$issue->setCategory($category_id);
									$issue->save();
								}
							}
							$options['category'] = array('name' => ($category instanceof TBGCategory) ? $category->getName() : '-', 'id' => ($category instanceof TBGCategory) ? $category->getID() : 0);
						}
						break;
				}
			}
			return $this->renderJSON($options);
		}

	}
