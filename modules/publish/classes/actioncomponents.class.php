<?php

	class publishActionComponents extends BUGSactioncomponent
	{
		
		public function componentLatestArticles()
		{
			$publish_module = BUGScontext::getModule('publish');
			$publish_module->log('retrieving latest articles');
			
			$this->latest_articles = $publish_module->getLatestArticles();
			$publish_module->log('done (retrieving latest articles)');
		}

		public function componentArticledisplay()
		{
			$this->show_title = (isset($this->show_title)) ? $this->show_title : true;
			$this->show_details = (isset($this->show_details)) ? $this->show_details : true;
			$this->show_link = (isset($this->show_link)) ? $this->show_link : false;
			$this->show_intro = (isset($this->show_intro)) ? $this->show_intro : true;
		}
	}