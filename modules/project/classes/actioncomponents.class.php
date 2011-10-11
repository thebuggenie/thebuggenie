<?php

	/**
	 * Project action components
	 */
	class projectActionComponents extends TBGActionComponent
	{

		public function componentOverview()
		{
			$this->issuetypes = $this->project->getIssuetypeScheme()->getReportableIssuetypes();
		}

		public function componentScrumcard()
		{
			$this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
		}
		
		public function componentMilestoneIssue()
		{
			$this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
		}
		
		public function componentRecentActivities()
		{
			$this->default_displayed = isset($this->default_displayed) ? $this->default_displayed : false;
		}
		
		public function componentTimeline()
		{
			$this->prev_date = null;
			$this->prev_timestamp = null;
			$this->prev_issue = null;
		}
		
		public function componentMilestone()
		{
			if (!isset($this->milestone)) 
			{
				$this->milestone = new TBGMilestone();
				$this->milestone->setProject($this->project);
			}
		}

	}