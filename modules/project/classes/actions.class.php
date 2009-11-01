<?php

	/**
	 * actions for the project module
	 */
	class projectActions extends BUGSaction
	{

		/**
		 * The currently selected project
		 *
		 * @access protected
		 * @property BUGSproject $selected_project The currently selected project
		 */

		/**
		 * Pre-execute function
		 *
		 * @param BUGSrequest 	$request
		 * @param string		$action
		 */
		public function preExecute($request, $action)
		{
			if ($project_key = $request->getParameter('project_key'))
			{
				try
				{
					$this->selected_project = BUGSproject::getByKey($project_key);
				}
				catch (Exception $e) {}
			}
			elseif ($project_id = $request->getParameter('project_id'))
			{
				try
				{
					$this->selected_project = BUGSfactory::projectLab($project_id);
				}
				catch (Exception $e) {}
			}
			if ($this->selected_project instanceof BUGSproject)
			{
				BUGScontext::setCurrentProject($this->selected_project);
				$this->project_key = $this->selected_project->getKey();
			}
		}

		/**
		 * The project dashboard
		 * 
		 * @param BUGSrequest $request
		 */
		public function runDashboard($request)
		{
			$this->recent_activities = $this->selected_project->getRecentActivities(10);
			$this->recent_issues = $this->selected_project->getRecentIssues();
			$this->recent_features = $this->selected_project->getRecentFeatures();
		}

		/**
		 * The project planning page
		 *
		 * @param BUGSrequest $request
		 */
		public function runPlanning($request)
		{
		}

		/**
		 * The project scrum page
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrum($request)
		{
			$this->unassigned_issues = $this->selected_project->getUnassignedStories();
		}

		/**
		 * Set color on a user story
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrumSetStoryColor($request)
		{
			$issue = BUGSfactory::BUGSissueLab($request->getParameter('story_id'));
			if ($issue instanceof BUGSissue)
			{
				$issue->setScrumColor($request->getParameter('color'));
				$issue->save();
				return $this->renderJSON(array('failed' => false));
			}
			return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Invalid user story or color')));
		}

		/**
		 * Assign a user story to a milestone id
		 *
		 * @param BUGSrequest $request
		 */
		public function runScrumAssignStory($request)
		{
			$issue = BUGSfactory::BUGSissueLab($request->getParameter('story_id'));
			$new_sprint_id = (int) $request->getParameter('sprint_id');
			$sprint = null;
			try
			{
				$sprint = BUGSfactory::milestoneLab($new_sprint_id);
			}
			catch (Exception $e) {}
			if ($issue instanceof BUGSissue)
			{
				$old_sprint = $issue->getMilestone();
				$issue->setMilestone($new_sprint_id);
				$issue->save();
				$old_sprint_id = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->getID() : 0;
				$old_issues = ($old_sprint instanceof BUGSmilestone) ? $old_sprint->countIssues() : 0;
				$new_issues = ($sprint instanceof BUGSmilestone) ? $sprint->countIssues() : 0;
				return $this->renderJSON(array('failed' => false, 'issue_id' => $issue->getID(), 'old_sprint_id' => $old_sprint_id, 'old_issues' => $old_issues, 'new_sprint_id' => $new_sprint_id, 'new_issues' => $new_issues));
			}
			return $this->renderJSON(array('failed' => true, 'error' => BUGScontext::getI18n()->__('Invalid user story or sprint')));
		}

		/**
		 * The project issue list page
		 *
		 * @param BUGSrequest $request
		 */
		public function runIssues($request)
		{
		}

		/**
		 * The project team page
		 *
		 * @param BUGSrequest $request
		 */
		public function runTeam($request)
		{
		}

		/**
		 * The project statistics page
		 *
		 * @param BUGSrequest $request
		 */
		public function runStatistics($request)
		{
		}

		/**
		 * Return the project menu strip
		 *
		 * @param BUGSrequest $request The request object
		 */
		public function runGetMenustrip($request)
		{
			$this->forward403unless($request->isMethod(BUGSrequest::POST) && $request->hasParameter('project_id'));
			$project = null;
			$hide_button = ($request->getParameter('page') == 'reportissue') ? true : false;
			$this->getResponse()->setPage($request->getParameter('page'));

			try
			{
				$project = BUGSfactory::projectLab($request->getParameter('project_id'));
			}
			catch (Exception $e) {}

			return $this->renderComponent('menustrip', array('project' => $project, 'hide_button' => $hide_button));
		}

	}