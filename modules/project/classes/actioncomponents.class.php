<?php

	/**
	 * Project action components
	 */
	class projectActionComponents extends BUGSactioncomponent
	{

		public function componentOverview()
		{
		}

		public function componentMenustrip()
		{
			$this->show_report_button = ($this->getResponse()->getPage() == 'reportissue') ? false : true;
			$this->selected_tab = '';
			if ($this->getResponse()->getPage() == 'project_dashboard') $this->selected_tab = 'dashboard';
			if ($this->getResponse()->getPage() == 'project_planning') $this->selected_tab = 'planning';
			if ($this->getResponse()->getPage() == 'project_scrum') $this->selected_tab = 'scrum';
			if ($this->getResponse()->getPage() == 'project_issues') $this->selected_tab = 'issues';
			if ($this->getResponse()->getPage() == 'project_team') $this->selected_tab = 'team';
			if ($this->getResponse()->getPage() == 'project_statistics') $this->selected_tab = 'statistics';
		}

		public function componentScrumcard()
		{
			
		}

	}