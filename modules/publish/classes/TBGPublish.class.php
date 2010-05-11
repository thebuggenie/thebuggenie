<?php

	class TBGPublish extends TBGModule 
	{

		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(TBGContext::getI18n()->__('Wiki'));
			$this->setMenuTitle(TBGContext::getI18n()->__('Wiki'));
			$this->setConfigTitle(TBGContext::getI18n()->__('Wiki'));
			$this->setDescription(TBGContext::getI18n()->__('Enables Wiki-functionality'));
			$this->setConfigDescription(TBGContext::getI18n()->__('Set up the Wiki module from this section'));
			$this->setHasConfigSettings();
			$this->addAvailablePermission('article_management', 'Can create and manage articles');
			$this->addAvailablePermission('manage_billboard', 'Can delete billboard posts');
			$this->addAvailablePermission('publish_postonglobalbillboard', 'Can post articles on global billboard');
			$this->addAvailablePermission('publish_postonteambillboard', 'Can post articles on team billboard');
			$this->addAvailableListener('core', 'index_left_middle', 'listen_latestArticles', 'Frontpage "Last news items"');
			$this->addAvailableListener('core', 'index_right_middle', 'listen_frontpageArticle', 'Frontpage article');
			$this->addAvailableListener('core', 'project_overview_item_links', 'listen_projectLinks', 'Project overview links');
			$this->addAvailableListener('core', 'project_menustrip_item_links', 'listen_projectMenustripLinks', 'Project menustrip links');

			$this->_addRoutes();

			if ($this->getSetting('allow_camelcase_links'))
			{
				TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
				TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
			}
		}

		public function initialize()
		{
		}

		protected function _addRoutes()
		{
			$this->addRoute('publish', '/wiki', 'showArticle', array('article_name' => 'MainPage'));
			$this->addRoute('publish_article_new', '/wiki/new', 'editArticle', array('article_name' => 'NewArticle'));
			$this->addRoute('publish_article', '/wiki/:article_name', 'showArticle');
			$this->addRoute('publish_article_edit', '/wiki/:article_name/edit', 'editArticle');
			$this->addRoute('publish_article_delete', '/wiki/:article_name/delete', 'deleteArticle');
			$this->addRoute('publish_article_save', '/wiki/savearticle', 'saveArticle');
			$this->addRoute('publish_article_history', '/wiki/:article_name/history', 'showArticle');
		}

		public static function install($scope = null)
		{
  			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;

			$module = parent::_install('publish', 'TBGPublish','1.0', true, true, false, $scope);

			TBGContext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$module->saveSetting('allow_camelcase_links', 1);

			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'index_right_middle');
			$module->enableListenerSaved('core', 'project_overview_item_links');
			$module->enableListenerSaved('core', 'project_menustrip_item_links');
  									  
			if ($scope == TBGContext::getScope()->getID())
			{
				B2DB::getTable('TBGArticlesTable')->create();
				B2DB::getTable('TBGArticleViewsTable')->create();
				B2DB::getTable('TBGArticleLinksTable')->create();
				B2DB::getTable('TBGArticleCategoriesTable')->create();
				B2DB::getTable('TBGBillboardPostsTable')->create();
			}

			try
			{
				TBGContext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
				self::loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}

			return true;
		}
		
		static function loadFixtures($scope)
		{
			try
			{
				$_path_handle = opendir(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR);
				while ($article_name = readdir($_path_handle))
				{
					if (strpos($article_name, '.') === false)
					{
						$content = file_get_contents(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $article_name);
						TBGWikiArticle::createNew($article_name, $content, true, $scope, array('ignore_vars' => true));
					}
				}

				$article_name = 'Category:Help';
				$content = "This is a list of all the available help articles in The Bug Genie. If you are stuck, look here for help.";
				TBGWikiArticle::createNew($article_name, $content, true, $scope, array('ignore_vars' => true));

				$article_name = 'Category:HowTo';
				$content = "[[Category:Help]]";
				TBGWikiArticle::createNew($article_name, $content, true, $scope, array('ignore_vars' => true));
				
				TBGLinksTable::getTable()->addLink('wiki', 0, 'MainPage', 'Wiki Frontpage', 1, $scope);
				TBGLinksTable::getTable()->addLink('wiki', 0, 'WikiFormatting', 'Formatting help', 2, $scope);
				TBGLinksTable::getTable()->addLink('wiki', 0, 'Category:Help', 'Help topics', 3, $scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				B2DB::getTable('TBGArticlesTable')->drop();
				B2DB::getTable('TBGBillboardPostsTable')->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('publish');
		}

		public function hasProjectAwareRoute()
		{
			return true;
		}

		public function getProjectAwareRoute($project_key)
		{
			return TBGContext::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key).":MainPage"));
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('allow_camelcase_links', 'menu_title');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
			}
			if ((bool) $request->getParameter('show_latest_article'))
			{
				$this->enableListenerSaved('core', 'index_left_middle');
			}
			else
			{
				$this->disableListenerSaved('core', 'index_left_middle');
			}
		}

		public function getMenuTitle()
		{
			if (($menu_title = $this->getSetting('menu_title')) !== null)
			{
				$i18n = TBGContext::getI18n();
				switch ($menu_title)
				{
					case 5: return $i18n->__('Archive');
					case 3: return $i18n->__('Documentation');
					case 4: return $i18n->__('Documents');
					case 2: return $i18n->__('Help');
					case 1: return $i18n->__('Wiki');
				}

			}
			return parent::getMenuTitle();
		}

		public function getSpacedName($camelcased)
		{
			return preg_replace('/(?<=[a-z])(?=[A-Z])/',' ', $camelcased);
		}

		public function stripExclamationMark($matches)
		{
			return substr($matches[0], 1);
		}

		public function getArticleLinkTag($matches)
		{
			TBGContext::loadLibrary('ui');
			$article_name = $matches[0];
			TBGTextParser::getCurrentParser()->addInternalLinkOccurrence($article_name);
			$article_name = $this->getSpacedName($matches[0]);
			return link_tag(make_url('publish_article', array('article_name' => $matches[0])), $article_name);
		}


		public function getBillboardPosts($target_board = 0, $posts = 5)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGBillboardPostsTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(TBGBillboardPostsTable::IS_DELETED, 0);
			$crit->setLimit($posts);
			$crit->addOrderBy(TBGBillboardPostsTable::DATE, 'desc');
			if (is_array($target_board))
			{
				$crit->addWhere(TBGBillboardPostsTable::TARGET_BOARD, $target_board, B2DBCriteria::DB_IN);
			}
			else
			{
				$crit->addWhere(TBGBillboardPostsTable::TARGET_BOARD, $target_board);
			}
	
			$posts = array();
	
			$res = B2DB::getTable('TBGBillboardPostsTable')->doSelect($crit);
			while ($row = $res->getNextRow())
			{
				$posts[] = new PublishBillboardPost($row);
			}
	
			return $posts;
		}
		
		public function getLatestArticles($limit = 5)
		{
			return $this->getArticles($limit, true);
		}
	
		public function getAllArticles()
		{
			$crit = new B2DBCriteria();
			$crit->addOrderBy(TBGArticlesTable::ORDER, 'asc');
			$crit->addOrderBy(TBGArticlesTable::DATE, 'desc');
			$res = B2DB::getTable('TBGArticlesTable')->doSelect($crit);
			$articles = array();
			while ($row = $res->getNextRow())
			{
				$articles[] = PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
			}
			return $articles;
		}
		
		public function getArticles($num_articles = 5, $news = false, $published = true)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(TBGArticlesTable::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(TBGArticlesTable::ARTICLE_NAME, 'Category:%', B2DBCriteria::DB_NOT_LIKE);
			
			$crit->addOrderBy(TBGArticlesTable::DATE, 'desc');
			
			if ($published) $crit->addWhere(TBGArticlesTable::IS_PUBLISHED, 1);
	
			$articles = array();
			
			if ($res = B2DB::getTable('TBGArticlesTable')->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && (count($articles) < $num_articles))
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
					}
					catch (Exception $e) 
					{
						continue;
					}
					
					if ($article->canRead())
					{
						$articles[] = $article;
					}
				}
			}
	
			return $articles;
		}

		public function getMenuItems($target_id = 0)
		{
			return TBGLinksTable::getTable()->getLinks('wiki', $target_id);
		}

		public function getUserDrafts()
		{
			$articles = array();

			if ($res = B2DB::getTable('TBGArticlesTable')->getUnpublishedArticlesByUser(TBGContext::getUser()->getID()))
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
					}
					catch (Exception $e)
					{
						continue;
					}

					if ($article->canRead())
					{
						$articles[] = $article;
					}
				}
			}

			return $articles;
		}
		
		public function getFrontpageArticle()
		{
			if ($row = B2DB::getTable('TBGArticlesTable')->getArticleByName('FrontpageArticle'))
			{
				return PublishFactory::articleLab($row->get(TBGArticlesTable::ID), $row);
			}
			return null;
		}
		
		public function listen_frontpageArticle(TBGEvent $event)
		{
			$index_article = $this->getFrontpageArticle();
			if ($index_article instanceof TBGWikiArticle)
			{
				TBGActionComponent::includeComponent('publish/articledisplay', array('article' => $index_article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
			}
		}

		public function listen_latestArticles(TBGEvent $event)
		{
			TBGActionComponent::includeComponent('publish/latestArticles', array('project' => $event->getSubject()));
		}

		public function listen_projectLinks(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('publish/projectlinks', array('project' => $event->getSubject()));
		}

		public function listen_projectMenustripLinks(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('publish/projectmenustriplinks', array('project' => $event->getSubject(), 'selected_tab' => $event->getParameter('selected_tab')));
		}

		public function getTabKey()
		{
			return (TBGContext::isProjectContext()) ? parent::getTabKey() : 'wiki';
		}

	}

?>
