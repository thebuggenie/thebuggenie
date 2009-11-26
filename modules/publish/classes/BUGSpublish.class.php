<?php

	class BUGSpublish extends BUGSmodule 
	{

		public function __construct($m_id, $res = null)
		{
			parent::__construct($m_id, $res);
			$this->_module_version = '1.0';
			$this->setLongName(BUGScontext::getI18n()->__('Wiki'));
			$this->setMenuTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setConfigTitle(BUGScontext::getI18n()->__('Wiki'));
			$this->setDescription(BUGScontext::getI18n()->__('Enables Wiki-functionality'));
			$this->setConfigDescription(BUGScontext::getI18n()->__('Set up the Wiki module from this section'));
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
				BUGSTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
				BUGSTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
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
  			$scope = ($scope === null) ? BUGScontext::getScope()->getID() : $scope;

			$module = parent::_install('publish', 'BUGSpublish','1.0', true, true, false, $scope);

			BUGScontext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			BUGScontext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$module->saveSetting('allow_camelcase_links', 1);

			$module->enableListenerSaved('core', 'index_left_middle');
			$module->enableListenerSaved('core', 'index_right_middle');
			$module->enableListenerSaved('core', 'project_overview_item_links');
			$module->enableListenerSaved('core', 'project_menustrip_item_links');
  									  
			if ($scope == BUGScontext::getScope()->getID())
			{
				B2DB::getTable('B2tArticles')->create();
				B2DB::getTable('B2tArticleViews')->create();
				B2DB::getTable('B2tArticleLinks')->create();
				B2DB::getTable('B2tArticleCategories')->create();
				B2DB::getTable('B2tBillboardPosts')->create();
			}

			try
			{
				BUGScontext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
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
				B2DB::getTable('B2tArticles')->loadFixtures($scope);
				B2DB::getTable('B2tBillboardPosts')->loadFixtures($scope);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		
		public function uninstall()
		{
			if (BUGScontext::getScope()->getID() == 1)
			{
				B2DB::getTable('B2tArticles')->drop();
				B2DB::getTable('B2tBillboardPosts')->drop();
			}
			parent::_uninstall();
		}

		public function getRoute()
		{
			return BUGScontext::getRouting()->generate('publish');
		}

		public function hasProjectAwareRoute()
		{
			return true;
		}

		public function getProjectAwareRoute($project_key)
		{
			return BUGScontext::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key).":MainPage"));
		}

		public function postConfigSettings()
		{
			$settings = array('allow_camelcase_links', 'menu_title');
			foreach ($settings as $setting)
			{
				if (BUGScontext::getRequest()->hasParameter($setting))
				{
					$this->saveSetting($setting, BUGScontext::getRequest()->getParameter($setting));
				}
			}
		}

		public function getMenuTitle()
		{
			if (($menu_title = $this->getSetting('menu_title')) !== null)
			{
				$i18n = BUGScontext::getI18n();
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
			BUGScontext::loadLibrary('ui');
			$article_name = $matches[0];
			BUGSTextParser::getCurrentParser()->addInternalLinkOccurrence($article_name);
			$article_name = $this->getSpacedName($matches[0]);
			return link_tag(make_url('publish_article', array('article_name' => $matches[0])), $article_name);
		}


		public function getBillboardPosts($target_board = 0, $posts = 5)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tBillboardPosts::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tBillboardPosts::IS_DELETED, 0);
			$crit->setLimit($posts);
			$crit->addOrderBy(B2tBillboardPosts::DATE, 'desc');
			if (is_array($target_board))
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board, B2DBCriteria::DB_IN);
			}
			else
			{
				$crit->addWhere(B2tBillboardPosts::TARGET_BOARD, $target_board);
			}
	
			$posts = array();
	
			$res = B2DB::getTable('B2tBillboardPosts')->doSelect($crit);
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
			$crit->addOrderBy(B2tArticles::ORDER, 'asc');
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			$res = B2DB::getTable('B2tArticles')->doSelect($crit);
			$articles = array();
			while ($row = $res->getNextRow())
			{
				$articles[] = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return $articles;
		}
		
		public function getArticles($num_articles = 5, $news = false, $published = true)
		{
			$crit = new B2DBCriteria();
			$crit->addWhere(B2tArticles::SCOPE, BUGScontext::getScope()->getID());
			$crit->addWhere(B2tArticles::ARTICLE_NAME, 'Category:%', B2DBCriteria::DB_NOT_LIKE);
			
			$crit->addOrderBy(B2tArticles::DATE, 'desc');
			
			if ($published) $crit->addWhere(B2tArticles::IS_PUBLISHED, 1);
	
			$articles = array();
			
			if ($res = B2DB::getTable('B2tArticles')->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && (count($articles) < $num_articles))
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
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

		public function getMenuItems()
		{
			return array();
		}

		public function getUserDrafts()
		{
			$articles = array();

			if ($res = B2DB::getTable('B2tArticles')->getUnpublishedArticlesByUser(BUGScontext::getUser()->getID()))
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$article = PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
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
			if ($row = B2DB::getTable('B2tArticles')->getArticleByName('FrontpageArticle'))
			{
				return PublishFactory::articleLab($row->get(B2tArticles::ID), $row);
			}
			return null;
		}
		
		public function listen_frontpageArticle()
		{
			$index_article = $this->getFrontpageArticle();
			if ($index_article instanceof PublishArticle)
			{
				BUGSactioncomponent::includeComponent('publish/articledisplay', array('article' => $index_article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
			}
		}

		public function listen_latestArticles()
		{
			BUGSactioncomponent::includeComponent('publish/latestArticles');
		}

		public function listen_projectLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectlinks', $params);
		}

		public function listen_projectMenustripLinks($params)
		{
			BUGSactioncomponent::includeTemplate('publish/projectmenustriplinks', $params);
		}

		public function getTabKey()
		{
			return (BUGScontext::getCurrentProject() instanceof BUGSproject) ? parent::getTabKey() : 'wiki';
		}

	}

?>
