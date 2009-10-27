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
			$recent_activities = array();
			foreach ($this->selected_project->getBuilds() as $build)
			{
				if ($build->isReleased() && $build->getReleaseDate() <= time())
				{
					if (!array_key_exists($build->getReleaseDate(), $recent_activities))
					{
						$recent_activities[$build->getReleaseDate()] = array();
					}
					$recent_activities[$build->getReleaseDate()][] = array('change_type' => 'build_release', 'info' => $build->getName());
				}
			}

			foreach ($this->selected_project->getRecentLogItems() as $log_item)
			{
				if (!array_key_exists($log_item['timestamp'], $recent_activities))
				{
					$recent_activities[$log_item['timestamp']] = array();
				}
				$recent_activities[$log_item['timestamp']][] = $log_item;
			}
			krsort($recent_activities, SORT_NUMERIC);
			$recent_activities = array_slice($recent_activities, 0, 20, true);
			$this->recent_activities = $recent_activities;
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
			$this->unassigned_issues = $this->selected_project->getIssuesWithoutMilestone();
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