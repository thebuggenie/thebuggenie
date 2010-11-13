<?php

	/**
	 * Project action components
	 */
	class projectActionComponents extends TBGActionComponent
	{

		public function componentOverview()
		{
		}

		public function componentMenustrip()
		{
			$this->show_report_button = ($this->getResponse()->getPage() == 'reportissue') ? false : true;
			$this->selected_tab = $this->getResponse()->getPage();
		}

		public function componentScrumcard()
		{
			$this->colors = array('#E20700', '#6094CF', '#37A42B', '#E3AA00', '#FFE955', '#80B5FF', '#80FF80', '#00458A', '#8F6A32', '#FFF');
		}
		
		public function componentRecentActivities()
		{
			$this->default_displayed = isset($this->default_displayed) ? $this->default_displayed : false;
		}

	}