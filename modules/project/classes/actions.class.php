<?php

	/**
	 * actions for the project module
	 */
	class projectActions extends BUGSaction
	{

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
			}
			//$this->project
		}

		/**
		 * The project dashboard
		 * 
		 * @param BUGSrequest $request
		 */
		public function runDashboard($request)
		{
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

			return $this->renderTemplate('menustrip', array('project' => $project, 'hide_button' => $hide_button));
		}

	}