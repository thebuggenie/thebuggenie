<?php

	class publishActionComponents extends TBGActionComponent
	{
		
		public function componentLatestArticles()
		{
			$publish_module = TBGPublish::getModule();
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
			$this->show_article = (isset($this->show_article)) ? $this->show_article : true;
			$this->mode = (isset($this->mode)) ? $this->mode : 'view';
		}

		public function componentSettings()
		{
			$articles = array();
			$categories = array();
			$_path_handle = opendir(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS);
			while ($article_name = readdir($_path_handle))
			{
				if (mb_strpos($article_name, '.') === false)
				{
					if (mb_strpos($article_name, '%3A') !== false)
					{
						$article_elements = explode('%3A', $article_name);
						$category = array_shift($article_elements);
						$categories[mb_strtolower($category)] = $category;
					}
					else
					{
						$category = '';
					}
					
					$articles[$article_name] = array('exists' => TBGWikiArticle::doesArticleExist(urldecode($article_name)), 'category' => mb_strtolower($category));
				}
			}
			ksort($articles, SORT_STRING);
			$this->articles = $articles;
			$this->categories = $categories;
		}

		public function componentLeftmenu()
		{
			$this->show_article_options = (bool) ($this->article instanceof TBGWikiArticle);
			$this->links_target_id = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0;
			$this->links = TBGPublish::getModule()->getMenuItems($this->links_target_id);
			$this->user_drafts = TBGPublish::getModule()->getUserDrafts();
			$this->whatlinkshere = ($this->article instanceof TBGWikiArticle) ? $this->article->getLinkingArticles() : null;
		}

	}