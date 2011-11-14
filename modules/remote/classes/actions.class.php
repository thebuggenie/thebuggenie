<?php 

	/**
	 * actions for the remote module
	 */
	class remoteActions extends TBGAction
	{

		/**
		 * The currently selected project in actions where there is one
		 *
		 * @access protected
		 * @property TBGProject $selected_project
		 */

		public function preExecute(TBGRequest $request, $action)
		{
			try
			{
				if ($project_key = $request['project_key'])
					$this->selected_project = TBGProject::getByKey($project_key);
				elseif ($project_id = (int) $request['project_id'])
					$this->selected_project = TBGContext::factory()->TBGProject($project_id);
				
				TBGContext::setCurrentProject($this->selected_project);
			}
			catch (Exception $e) {}
		}
		
		public function runListProjects(TBGRequest $request)
		{
			$projects = TBGProject::getAll();

			$return_array = array();
			foreach ($projects as $project)
			{
				$return_array[$project->getKey()] = $project->getName();
			}

			$this->projects = $return_array;
		}

		public function runListIssuetypes(TBGRequest $request)
		{
			$issuetypes = TBGIssuetype::getAll();

			$return_array = array();
			foreach ($issuetypes as $issuetype)
			{
				$return_array[] = $issuetype->getName();
			}

			$this->issuetypes = $return_array;
		}

		public function runListFieldvalues(TBGRequest $request)
		{
			$field_key = $request['field_key'];
			$return_array = array('description' => null, 'type' => null, 'choices' => null);
			if ($field_key == 'title' || in_array($field_key, TBGDatatypeBase::getAvailableFields(true)))
			{
				switch ($field_key)
				{
					case 'title':
						$return_array['description'] = TBGContext::getI18n()->__('Single line text input without formatting');
						$return_array['type'] = 'single_line_input';
						break;
					case 'description':
					case 'reproduction_steps':
						$return_array['description'] = TBGContext::getI18n()->__('Text input with wiki formatting capabilities');
						$return_array['type'] = 'wiki_input';
						break;
					case 'status':
					case 'resolution':
					case 'reproducability':
					case 'priority':
					case 'severity':
					case 'category':
						$return_array['description'] = TBGContext::getI18n()->__('Choose one of the available values');
						$return_array['type'] = 'choice';

						$classname = "TBG".ucfirst($field_key);
						$choices = $classname::getAll();
						foreach ($choices as $choice_key => $choice)
						{
							$return_array['choices'][$choice_key] = $choice->getName();
						}
						break;
					case 'percent_complete':
						$return_array['description'] = TBGContext::getI18n()->__('Value of percentage completed');
						$return_array['type'] = 'choice';
						$return_array['choices'][] = "1-100%";
						break;
					case 'owner':
					case 'assignee':
						$return_array['description'] = TBGContext::getI18n()->__('Select an existing user or <none>');
						$return_array['type'] = 'select_user';
						break;
					case 'estimated_time':
					case 'spent_time':
						$return_array['description'] = TBGContext::getI18n()->__('Enter time, such as points, hours, minutes, etc or <none>');
						$return_array['type'] = 'time';
						break;
					case 'milestone':
						$return_array['description'] = TBGContext::getI18n()->__('Select from available project milestones');
						$return_array['type'] = 'choice';
						if ($this->selected_project instanceof TBGProject)
						{
							$milestones = $this->selected_project->getMilestones();
							foreach ($milestones as $milestone)
							{
								$return_array['choices'][$milestone->getID()] = $milestone->getName();
							}
						}
						break;
				}
			}
			else
			{

			}

			$this->field_info = $return_array;
		}

}