<?php

	class publishActionComponents extends TBGActionComponent
	{
		
		public function componentLatestArticles()
		{
			$publish_module = TBGContext::getModule('publish');
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
			$this->show_actions = (isset($this->show_actions)) ? $this->show_actions : true;
			$this->show_category_contains = (isset($this->show_category_contains)) ? $this->show_category_contains : true;
			$this->embedded = (isset($this->embedded)) ? $this->embedded : false;
		}

		public function componentSettings()
		{
			
		}

		public function componentLeftmenu()
		{
			$this->show_article_options = (bool) ($this->article instanceof PublishArticle);
			$this->links = TBGContext::getModule('publish')->getMenuItems();
			$this->user_drafts = TBGContext::getModule('publish')->getUserDrafts();
			$this->whatlinkshere = ($this->article instanceof PublishArticle) ? $this->article->getLinkingArticles() : null;
		}

	}