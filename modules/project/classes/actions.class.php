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
		
		public function runScrumAddTask(TBGRequest $request)
		{
			$this->forward403unless(TBGContext::getUser()->hasPageAccess('project_scrum', $this->selected_project->getID()) || TBGContext::getUser()->hasPageAccess('project_allpages', $this->selected_project->getID()));
			$issue = TBGFactory::TBGIssueLab($request->getParameter('story_id'));
			if ($issue instanceof TBGIssue)
			{
				$task = TBGIssue::createNew($request->getParameter('task_name'), TBGIssuetype::getTask()->getID(), $issue->getProjectID());
				$comment = $issue->addChildIssue($task);
				$mode = $request->getParameter('mode', 'scrum');
				if ($mode == 'scrum')
				{
					return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('project/scrumstorytask', array('task' => $task)), 'count' => count($issue->getChildIssues())));
				}
				else
				{
					return $this->renderJSON(array('failed' => false, 'content' => $this->getTemplateHTML('main/relatedissue', array('theIssue' => $issue, 'related_issue' => $task)), 'comment' => (($comment instanceof TBGComment) ? $this->getTemplateHTML('main/comment', array('aComment' => $comment, 'theIssue' => $issue)) : false), 'message' => TBGContext::getI18n()->__('The task was added')));
				}
			}
			return $this->renderJSON(array('failed' => true, 'error' => TBGContext::getI18n()->__('Invalid user story')));
		}

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
					$datasets[] = array('values' => array_values($burndown_data['estimations']['hours']), 'label' => __('Remaining effort'));
					$this->labels = array_keys($burndown_data['estimations']['hours']);
				}
				else
				{
					$datasets[] = array('values' => array(0), 'label' => __('Remaining effort'));
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
						$issue->setEstimatedPoints((int) $request->getParameter('estimated_points'));
						$issue->setEstimatedHours((int) $request->getParameter('estimated_hours'));
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
			$this->getResponse()->setContentType('image/png');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			$datasets = array();
			$issues = $this->selected_project->getLast30Counts();
			$datasets[] = array('values' => $issues['open'], 'label' => __('Issues opened'));
			$datasets[] = array('values' => $issues['closed'], 'label' => __('Issues closed'));
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

	}