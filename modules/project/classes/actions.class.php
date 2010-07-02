<?php

	/**
	 * actions for the project module
	 */
	class projectActions extends TBGAction
	{

		/**
		 * The currently selected project
		 * 
		 * @var TBGProject
		 * @access protected
		 * @property $selected_project
		 */

		/**
		 * Pre-execute function
		 *
		 * @param TBGRequest 	$request
		 * @param string		$action
		 */
		public function preExecute(TBGRequest $request, $action)
		{
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$this->selected_project = TBGProject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			elseif ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$this->selected_project = TBGFactory::projectLab($project_id);
				}
				catch (Exception $e) {}
			}
			if ($this->selected_project instanceof TBGProject)
			{
				TBGContext::setCurrentProject($this->selected_project);
				$this->project_key = $this->selected_project->getKey();
			}
			else
			{
				$this->return404(TBGContext::getI18n()->__('This project does not exist'));
			}
		}

		/**
		 * The project dashboard
		 * 
		 * @param TBGRequest $request
		 */
		public function runDashboard(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_dashboard', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->recent_activities = $this->selected_project->getRecentActivities(10);
			$this->recent_issues = $this->selected_project->getRecentIssues(10);
			$this->recent_features = $this->selected_project->getRecentFeatures();
			$this->recent_ideas = $this->selected_project->getRecentIdeas();
			$this->priority_count = $this->selected_project->getPriorityCount();
			$this->assignees = $this->selected_project->getAssignees();
		}

		/**
		 * The project planning page
		 *
		 * @param TBGRequest $request
		 */
		public function runPlanning(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_planning', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));

			$this->recent_ideas = $this->selected_project->getRecentIdeas();

		}

		/**
		 * The project files page
		 *
		 * @param TBGRequest $request
		 */
		public function runFiles(TBGRequest $request)
		{
		}

		/**
		 * The project roadmap page
		 *
		 * @param TBGRequest $request
		 */
		public function runRoadmap(TBGRequest $request)
		{
		}

		/**
		 * The project planning page
		 *
		 * @param TBGRequest $request
		 */
		public function runTimeline(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_timeline', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->recent_activities = $this->selected_project->getRecentActivities();
			if ($request->getParameter('format') == 'rss')
			{
				return $this->renderComponent('project/timelinerss', array('recent_activities' => $this->recent_activities));
			}
		}

		/**
		 * The project scrum page
		 *
		 * @param TBGRequest $request
		 */
		public function runScrum(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->unassigned_issues = $this->selected_project->getUnassignedStories();
		}

		/**
		 * The project scrum page
		 *
		 * @param TBGRequest $request
		 */
		public function runScrumShowDetails(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$selected_sprint = null;
			if ($s_id = $request->getParameter('sprint_id'))
			{
				$selected_sprint = TBGFactory::TBGMilestoneLab($s_id);
			}
			else
			{
				$sprints = $this->selected_project->getUpcomingMilestonesAndSprints();
				if (count($sprints))
				{
					$selected_sprint = array_shift($sprints);
				}
			}
			$this->selected_sprint = $selected_sprint;
			$this->total_estimated_points = 0;
			$this->total_estimated_hours = 0;
			//$this->unassigned_issues = $this->selected_project->getUnassignedStories();
		}

		/**
		 * Add a task to a scrum user story
		 *
		 * @param TBGRequest $request
		 */
		public function runScrumAddTask(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$issue = TBGFactory::TBGIssueLab($request->getParameter('story_id'));
			try
			{
				if ($issue instanceof TBGIssue)
				{
					$task = TBGIssue::createNew($request->getParameter('task_name'), TBGIssuetype::getTask()->getID(), $issue->getProjectID());
					$comment = $issue->addChildIssue($task);
					$task->setMilestone(($issue->getMilestone() instanceof TBGMilestone) ? $issue->getMilestone()->getID() : null);
					$mode = $request->getParameter('mode', 'scrum');
					if ($mode == 'scrum')
					{
						return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('project/scrumstorytask', array('task' => $task)), 'count' => count($issue->getChildIssues())));
					}
					elseif ($mode == 'sprint')
					{
						return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('project/scrumsprintdetailstask', array('task' => $task, 'can_estimate' => $issue->canEditEstimatedTime())), 'count' => count($issue->getChildIssues())));
					}
					else
					{
						return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('main/relatedissue', array('theIssue' => $issue, 'related_issue' => $task)), 'comment' => (($comment instanceof TBGComment) ? $this->getTemplateHTML('main/comment', array('aComment' => $comment, 'theIssue' => $issue)) : false), 'message' => TBGContext::getI18n()->__('The task was added')));
					}
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid user story')));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__("An error occured while trying to create a new task: %exception_message%", array('%exception_message%' => $e->getMessage()))));
			}
		}

		/**
		 * Show the scrum burndown chart for a specified sprint
		 *
		 * @param TBGRequest $request
		 */
		public function runScrumShowBurndownImage(TBGRequest $request)
		{
			$milestone = null;
			if ($m_id = $request->getParameter('sprint_id'))
			{
				$milestone = TBGFactory::TBGMilestoneLab($m_id);
			}
			else
			{
				$milestones = $this->selected_project->getUpcomingMilestonesAndSprints();
				if (count($milestones))
				{
					$milestone = array_shift($milestones);
				}
			}

			$this->getResponse()->setContentType('image/png');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			if ($milestone instanceof TBGMilestone)
			{
				$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
				$datasets = array();

				$burndown_data = $milestone->getBurndownData();

				if (count($burndown_data['estimations']['hours']))
				{
					foreach ($burndown_data['estimations']['hours'] as $key => $e)
					{
						if (array_key_exists($key, $burndown_data['spent_times']['hours']))
						{
							$burndown_data['estimations']['hours'][$key] -= $burndown_data['spent_times']['hours'][$key];
						}
					}
					$datasets[] = array('values' => array_values($burndown_data['estimations']['hours']), 'label' => TBGContext::getI18n()->__('Remaining effort'));
					$this->labels = array_keys($burndown_data['estimations']['hours']);
				}
				else
				{
					$datasets[] = array('values' => array(0), 'label' => TBGContext::getI18n()->__('Remaining effort'));
					$this->labels = array(0);
				}
				$this->datasets = $datasets;
				$this->milestone = $milestone;
			}
			else
			{
				return $this->renderText('');
			}
		}

		/**
		 * Set color on a user story
		 *
		 * @param TBGRequest $request
		 */
		public function runScrumSetStoryDetail(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$issue = TBGFactory::TBGIssueLab($request->getParameter('story_id'));
			if ($issue instanceof TBGIssue)
			{
				switch ($request->getParameter('detail'))
				{
					case 'color':
						$issue->setScrumColor($request->getParameter('color'));
						$issue->save();
						return $this->renderJSON(array('failed' => false));
						break;
					case 'estimates':
						if ($request->hasParameter('estimated_points'))
						{
							$issue->setEstimatedPoints((int) $request->getParameter('estimated_points'));
						}
						if ($request->hasParameter('estimated_hours'))
						{
							$issue->setEstimatedHours((int) $request->getParameter('estimated_hours'));
						}
						$issue->save();
						$sprint_id = ($issue->getMilestone() instanceof TBGMilestone) ? $issue->getMilestone()->getID() : 0;
						$new_sprint_points = ($sprint_id !== 0) ? $issue->getMilestone()->getPointsEstimated() : 0;
						$new_sprint_hours = ($sprint_id !== 0) ? $issue->getMilestone()->getHoursEstimated() : 0;
						
						return $this->renderJSON(array('failed' => false, 'points' => $issue->getEstimatedPoints(), 'hours' => $issue->getEstimatedHours(), 'sprint_id' => $sprint_id, 'new_estimated_points' => $new_sprint_points, 'new_estimated_hours' => $new_sprint_hours));
						break;
				}
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid user story')));
		}
		
		/**
		 * Assign a user story to a milestone id
		 *
		 * @param TBGRequest $request
		 */
		public function runScrumAssignStory(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			try
			{
				$issue = TBGFactory::TBGIssueLab($request->getParameter('story_id'));
				$new_sprint_id = (int) $request->getParameter('sprint_id');
				$sprint = null;
				try
				{
					$sprint = TBGFactory::TBGMilestoneLab($new_sprint_id);
				}
				catch (Exception $e) {}
				if ($issue instanceof TBGIssue)
				{
					$old_sprint = $issue->getMilestone();
					$issue->setMilestone($new_sprint_id);
					$issue->save();
					$old_sprint_id = ($old_sprint instanceof TBGMilestone) ? $old_sprint->getID() : 0;
					$old_issues = ($old_sprint instanceof TBGMilestone) ? $old_sprint->countIssues() : 0;
					$new_issues = ($sprint instanceof TBGMilestone) ? $sprint->countIssues() : 0;
					$old_e_points = ($old_sprint instanceof TBGMilestone) ? $old_sprint->getPointsEstimated() : 0;
					$new_e_points = ($sprint instanceof TBGMilestone) ? $sprint->getPointsEstimated() : 0;
					$old_s_points = ($old_sprint instanceof TBGMilestone) ? $old_sprint->getPointsSpent() : 0;
					$new_s_points = ($sprint instanceof TBGMilestone) ? $sprint->getPointsSpent() : 0;
					$old_e_hours = ($old_sprint instanceof TBGMilestone) ? $old_sprint->getHoursEstimated() : 0;
					$new_e_hours = ($sprint instanceof TBGMilestone) ? $sprint->getHoursEstimated() : 0;
					$old_s_hours = ($old_sprint instanceof TBGMilestone) ? $old_sprint->getHoursSpent() : 0;
					$new_s_hours = ($sprint instanceof TBGMilestone) ? $sprint->getHoursSpent() : 0;
					return $this->renderJSON(array('failed' => false, 'issue_id' => $issue->getID(), 'old_sprint_id' => $old_sprint_id, 'old_issues' => $old_issues, 'old_estimated_points' => $old_e_points, 'old_spent_points' => $old_s_points, 'old_estimated_hours' => $old_e_hours, 'old_spent_hours' => $old_s_hours, 'new_sprint_id' => $new_sprint_id, 'new_issues' => $new_issues, 'new_estimated_points' => $new_e_points, 'new_spent_points' => $new_s_points, 'new_estimated_hours' => $new_e_hours, 'new_spent_hours' => $new_s_hours));
				}
				return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid user story or sprint')));
			}
			catch (Exception $e)
			{
				return $this->renderJSON(array('failed' => true, 'error' => $e->getMessage()));
			}
		}
		
		/**
		 * Add a new sprint type milestone to a project
		 *  
		 * @param TBGRequest $request
		 */
		public function runScrumAddSprint(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			if (($sprint_name = $request->getParameter('sprint_name')) && trim($sprint_name) != '')
			{
				$sprint = TBGMilestone::createNew($sprint_name, TBGMilestone::TYPE_SCRUMSPRINT, $this->selected_project->getID());
				$sprint->setStarting();
				$sprint->setStartingDate(mktime(0, 0, 1, $request->getParameter('starting_month'), $request->getParameter('starting_day'), $request->getParameter('starting_year')));
				$sprint->setScheduled();
				$sprint->setScheduledDate(mktime(23, 59, 59, $request->getParameter('scheduled_month'), $request->getParameter('scheduled_day'), $request->getParameter('scheduled_year')));
				$sprint->save();
				return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('sprintbox', array('sprint' => $sprint)), 'sprint_id' => $sprint->getID()));
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Please specify a sprint name')));
		}

		/**
		 * The project issue list page
		 *
		 * @param TBGRequest $request
		 */
		public function runIssues(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_issues', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		/**
		 * The project team page
		 *
		 * @param TBGRequest $request
		 */
		public function runTeam(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_team', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$this->assignees = $this->selected_project->getAssignees();
		}

		/**
		 * The project statistics page
		 *
		 * @param TBGRequest $request
		 */
		public function runStatistics(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_statistics', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
		}

		public function runStatisticsLast30(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_statistics', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));

			if (!function_exists('imagecreatetruecolor'))
			{
				return $this->return404(TBGContext::getI18n()->__('The libraries to generate images are not installed. Please see http://www.thebuggenie.com for more information'));
			}
			
			$this->getResponse()->setContentType('image/png');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			$datasets = array();
			$issues = $this->selected_project->getLast30Counts();
			$datasets[] = array('values' => $issues['open'], 'label' => TBGContext::getI18n()->__('Issues opened'));
			$datasets[] = array('values' => $issues['closed'], 'label' => TBGContext::getI18n()->__('Issues closed'));
			$this->datasets = $datasets;
			$this->labels = array(30,'','','','',25,'','','','',20,'','','','',15,'','','','',10,'','','','',5,'','','','',0);
		}

		/**
		 * Return the project menu strip
		 *
		 * @param TBGRequest $request The request object
		 */
		public function runGetMenustrip(TBGRequest $request)
		{
			$this->forward403unless($request->isMethod(TBGRequest::POST) && $request->hasParameter('project_id'));
			$project = null;
			$hide_button = ($request->getParameter('page') == 'reportissue') ? true : false;
			$this->getResponse()->setPage($request->getParameter('page'));

			try
			{
				$project = TBGFactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}

			return $this->renderComponent('menustrip', array('project' => $project, 'hide_button' => $hide_button));
		}

		public function runStatisticsImagesets(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_statistics', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$success = false;
			if (in_array($request->getParameter('set'), array('issues_per_status', 'issues_per_priority', 'issues_per_category', 'issues_per_resolution', 'issues_per_reproducability')))
			{
				$success = true;
				$base_url = TBGContext::getRouting()->generate('project_statistics_image', array('project_key' => $this->selected_project->getKey(), 'key' => '%key%', 'mode' => '%mode%', 'image_number' => '%image_number%'));
				$key = urlencode('%key%');
				$mode = urlencode('%mode%');
				$image_number = urlencode('%image_number%');
				$set = $request->getParameter('set');
				$images = array('main' => str_replace(array($key, $mode, $image_number), array($set, 'main', 1), $base_url),
								'mini_1_small' => str_replace(array($key, $mode, $image_number), array($set, 'mini', 1), $base_url),
								'mini_1_large' => str_replace(array($key, $mode, $image_number), array($set, 'main', 1), $base_url),
								'mini_2_small' => str_replace(array($key, $mode, $image_number), array($set, 'mini', 2), $base_url),
								'mini_2_large' => str_replace(array($key, $mode, $image_number), array($set, 'main', 2), $base_url),
								'mini_3_small' => str_replace(array($key, $mode, $image_number), array($set, 'mini', 3), $base_url),
								'mini_3_large' => str_replace(array($key, $mode, $image_number), array($set, 'main', 3), $base_url));
			}
			else
			{
				$error = TBGContext::getI18n()->__('Invalid image set');
			}

			$this->getResponse()->setHttpStatus(($success) ? 200 : 400);
			return $this->renderJSON(($success) ? array('success' => $success, 'images' => $images) : array('success' => $success, 'error' => $error));
		}

		protected function _calculateImageDetails($counts)
		{
			$i18n = TBGContext::getI18n();
			$labels = array();
			$values = array();
			foreach ($counts as $item_id => $details)
			{
				if ($this->image_number == 1)
				{
					$value = $details['open'] + $details['closed'];
				}
				if ($this->image_number == 2)
				{
					$value = $details['open'];
				}
				if ($this->image_number == 3)
				{
					$value = $details['closed'];
				}
				if ($value > 0)
				{
					if ($item_id != 0)
					{
						switch ($this->key)
						{
							case 'issues_per_status':
								$item = TBGFactory::TBGStatusLab($item_id);
								break;
							case 'issues_per_priority':
								$item = TBGFactory::TBGPriorityLab($item_id);
								break;
							case 'issues_per_category':
								$item = TBGFactory::TBGCategoryLab($item_id);
								break;
							case 'issues_per_resolution':
								$item = TBGFactory::TBGResolutionLab($item_id);
								break;
							case 'issues_per_reproducability':
								$item = TBGFactory::TBGReproducabilityLab($item_id);
								break;
						}
						$labels[] = ($item instanceof TBGIdentifiableClass) ? $item->getName() : $i18n->__('Unknown');
					}
					else
					{
						$labels[] = $i18n->__('Not determined');
					}
					$values[] = $value;
				}
			}

			return array($values, $labels);
		}

		protected function _generateImageDetailsFromKey($mode = null)
		{
			$this->graphmode = null;
			$i18n = TBGContext::getI18n();
			if ($mode == 'main')
			{
				$this->width = 695;
				$this->height = 310;
			}
			else
			{
				$this->width = 230;
				$this->height = 150;
			}
			switch ($this->key)
			{
				case 'issues_per_status':
					$this->graphmode = 'piechart';
					$counts = TBGIssuesTable::getTable()->getStatusCountByProjectID($this->selected_project->getID());
					if ($this->image_number == 1)
					{
						$this->title = $i18n->__('Total number of issues per status type');
					}
					elseif ($this->image_number == 2)
					{
						$this->title = $i18n->__('Open issues per status type');
					}
					elseif ($this->image_number == 3)
					{
						$this->title = $i18n->__('Closed issues per status type');
					}
					break;
				case 'issues_per_priority':
					$this->graphmode = 'piechart';
					$counts = TBGIssuesTable::getTable()->getPriorityCountByProjectID($this->selected_project->getID());
					if ($this->image_number == 1)
					{
						$this->title = $i18n->__('Total number of issues per priority level');
					}
					elseif ($this->image_number == 2)
					{
						$this->title = $i18n->__('Open issues per priority level');
					}
					elseif ($this->image_number == 3)
					{
						$this->title = $i18n->__('Closed issues per priority level');
					}
					break;
				case 'issues_per_category':
					$this->graphmode = 'piechart';
					$counts = TBGIssuesTable::getTable()->getCategoryCountByProjectID($this->selected_project->getID());
					if ($this->image_number == 1)
					{
						$this->title = $i18n->__('Total number of issues per category');
					}
					elseif ($this->image_number == 2)
					{
						$this->title = $i18n->__('Open issues per category');
					}
					elseif ($this->image_number == 3)
					{
						$this->title = $i18n->__('Closed issues per category');
					}
					break;
				case 'issues_per_resolution':
					$this->graphmode = 'piechart';
					$counts = TBGIssuesTable::getTable()->getResolutionCountByProjectID($this->selected_project->getID());
					if ($this->image_number == 1)
					{
						$this->title = $i18n->__('Total number of issues per resolution');
					}
					elseif ($this->image_number == 2)
					{
						$this->title = $i18n->__('Open issues per resolution');
					}
					elseif ($this->image_number == 3)
					{
						$this->title = $i18n->__('Closed issues per resolution');
					}
					break;
				case 'issues_per_reproducability':
					$this->graphmode = 'piechart';
					$counts = TBGIssuesTable::getTable()->getReproducabilityCountByProjectID($this->selected_project->getID());
					if ($this->image_number == 1)
					{
						$this->title = $i18n->__('Total number of issues per reproducability level');
					}
					elseif ($this->image_number == 2)
					{
						$this->title = $i18n->__('Open issues per reproducability level');
					}
					elseif ($this->image_number == 3)
					{
						$this->title = $i18n->__('Closed issues per reproducability level');
					}
					break;
				default:
					throw new Exception(__("unknown key '%key%'", array('%key%' => $this->key)));
			}
			list ($values, $labels) = $this->_calculateImageDetails($counts);
			$this->values = $values;
			$this->labels = $labels;
		}

		public function runStatisticsGetImage(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_statistics', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));

			if (!function_exists('imagecreatetruecolor'))
			{
				return $this->return404(TBGContext::getI18n()->__('The libraries to generate images are not installed. Please see http://www.thebuggenie.com for more information'));
			}

			$this->getResponse()->setContentType('image/png');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);

			$this->key = $request->getParameter('key');
			$this->image_number = (int) $request->getParameter('image_number');
			$this->_generateImageDetailsFromKey($request->getParameter('mode'));
		}

	}