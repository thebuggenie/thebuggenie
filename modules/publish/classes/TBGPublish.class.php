<?php

	/**
	 * The wiki class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage publish
	 */

	/**
	 * The wiki class
	 *
	 * @package thebuggenie
	 * @subpackage publish
	 */
	class TBGPublish extends TBGModule 
	{
		
		protected $_module_version = '1.0';

		/**
		 * Return an instance of this module
		 *
		 * @return TBGPublish
		 */
		public static function getModule()
		{
			return TBGContext::getModule('publish');
		}

		protected function _initialize(TBGI18n $i18n)
		{
			$this->setLongName($i18n->__('Wiki'));
			$this->setMenuTitle($i18n->__('Wiki'));
			$this->setConfigTitle($i18n->__('Wiki'));
			$this->setDescription($i18n->__('Enables Wiki-functionality'));
			$this->setConfigDescription($i18n->__('Set up the Wiki module from this section'));
			$this->setHasConfigSettings();
			if ($this->isEnabled())
			{
				if ($this->isWikiTabsEnabled())
				{
					$this->showInMenu();
				}
				if ($this->getSetting('allow_camelcase_links'))
				{
					TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
					TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
				}
			}
		}

		protected function _addAvailablePermissions()
		{
			$this->addAvailablePermission('article_management', 'Can create and manage articles');
			$this->addAvailablePermission('manage_billboard', 'Can delete billboard posts');
			$this->addAvailablePermission('publish_postonglobalbillboard', 'Can post articles on global billboard');
			$this->addAvailablePermission('publish_postonteambillboard', 'Can post articles on team billboard');
		}
		
		protected function _addAvailableListeners()
		{
			$this->addAvailableListener('core', 'index_left_middle', 'listen_frontpageLeftmenu', 'Frontpage left menu');
			$this->addAvailableListener('core', 'index_right_middle', 'listen_frontpageArticle', 'Frontpage article');
			if ($this->isWikiTabsEnabled())
			{
				$this->addAvailableListener('core', 'project_overview_item_links', 'listen_projectLinks', 'Project overview links');
				$this->addAvailableListener('core', 'project_menustrip_item_links', 'listen_projectMenustripLinks', 'Project menustrip links');
			}
			$this->addAvailableListener('core', 'TBGProject::createNew', 'listen_createNewProject', 'Create basic project wiki page');
		}

		protected function _addAvailableRoutes()
		{
			$this->addRoute('publish', '/wiki', 'showArticle', array('article_name' => 'MainPage'));
			$this->addRoute('publish_article_new', '/wiki/new', 'editArticle', array('article_name' => 'NewArticle'));
			$this->addRoute('publish_article', '/wiki/:article_name', 'showArticle');
			$this->addRoute('publish_article_revision', '/wiki/:article_name/revision/:revision', 'showArticle');
			$this->addRoute('publish_article_edit', '/wiki/:article_name/edit', 'editArticle');
			$this->addRoute('publish_article_delete', '/wiki/:article_name/delete', 'deleteArticle');
			$this->addRoute('publish_article_save', '/wiki/savearticle', 'saveArticle');
			$this->addRoute('publish_article_history', '/wiki/:article_name/history', 'articleHistory', array('history_action' => 'list'));
			$this->addRoute('publish_article_diff', '/wiki/:article_name/diff', 'articleHistory', array('history_action' => 'diff'));
			$this->addRoute('publish_article_restore', '/wiki/:article_name/revert/to/revision/:revision', 'articleHistory', array('history_action' => 'revert'));
		}

		protected function _install($scope)
		{
			TBGContext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$this->saveSetting('allow_camelcase_links', 1);

			$this->enableListenerSaved('core', 'index_left_middle');
			$this->enableListenerSaved('core', 'index_right_middle');
			$this->enableListenerSaved('core', 'project_overview_item_links');
			$this->enableListenerSaved('core', 'project_menustrip_item_links');
			$this->enableListenerSaved('core', 'TBGProject::createNew');
  									  
			TBGContext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
			TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
			TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
		}
		
		public function loadFixturesArticles($scope, $overwrite = true)
		{
			$_path_handle = opendir(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR);
			while ($article_name = readdir($_path_handle))
			{
				if (strpos($article_name, '.') === false)
				{
					$imported = false;
					if ($overwrite)
					{
						TBGArticlesTable::getTable()->deleteArticleByName(urldecode($article_name));
					}
					if (TBGArticlesTable::getTable()->getArticleByName(urldecode($article_name)) === null)
					{
						$content = file_get_contents(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $article_name);
						TBGWikiArticle::createNew(urldecode($article_name), $content, true, $scope, array('overwrite' => $overwrite));
						$imported = true;
					}
					TBGEvent::createNew('publish', 'fixture_article_loaded', urldecode($article_name), array('imported' => $imported))->trigger();
				}
			}
		}

		protected function _loadFixtures($scope)
		{
			$this->loadFixturesArticles($scope);

			TBGLinksTable::getTable()->addLink('wiki', 0, 'MainPage', 'Wiki Frontpage', 1, $scope);
			TBGLinksTable::getTable()->addLink('wiki', 0, 'TheBugGenie:WikiFormatting', 'Formatting help', 2, $scope);
			TBGLinksTable::getTable()->addLink('wiki', 0, 'Category:Help', 'Help topics', 3, $scope);
		}
		
		protected function _uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				TBGArticlesTable::getTable()->drop();
				B2DB::getTable('TBGBillboardPostsTable')->drop();
			}
			TBGLinksTable::getTable()->removeByTargetTypeTargetIDandLinkID('wiki', 0);
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

		public function isWikiTabsEnabled()
		{
			return (bool) ($this->getSetting('hide_wiki_links') != 1);
		}

		public function postConfigSettings(TBGRequest $request)
		{
			$settings = array('allow_camelcase_links', 'menu_title', 'hide_wiki_links');
			foreach ($settings as $setting)
			{
				if ($request->hasParameter($setting))
				{
					$this->saveSetting($setting, $request->getParameter($setting));
				}
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
			$article_name = $matches[0];
			TBGTextParser::getCurrentParser()->addInternalLinkOccurrence($article_name);
			$article_name = $this->getSpacedName($matches[0]);
			if (!TBGContext::isCLI())
			{
				TBGContext::loadLibrary('ui');
				return link_tag(make_url('publish_article', array('article_name' => $matches[0])), $article_name);
			}
			else
			{
				return $matches[0];
			}
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
			$res = TBGArticlesTable::getTable()->doSelect($crit);
			$articles = array();
			while ($row = $res->getNextRow())
			{
				$articles[] = PublishFactory::article($row->get(TBGArticlesTable::ID), $row);
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
			
			if ($res = TBGArticlesTable::getTable()->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && (count($articles) < $num_articles))
				{
					try
					{
						$article = PublishFactory::article($row->get(TBGArticlesTable::ID), $row);
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

			if ($res = TBGArticlesTable::getTable()->getUnpublishedArticlesByUser(TBGContext::getUser()->getID()))
			{
				while ($row = $res->getNextRow())
				{
					try
					{
						$article = PublishFactory::article($row->get(TBGArticlesTable::ID), $row);
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
		
		public function getFrontpageArticle($type)
		{
			$article_name = ($type == 'main') ? 'FrontpageArticle' : 'FrontpageLeftmenu';
			if ($row = TBGArticlesTable::getTable()->getArticleByName($article_name))
			{
				return PublishFactory::article($row->get(TBGArticlesTable::ID), $row);
			}
			return null;
		}
		
		public function listen_frontpageArticle(TBGEvent $event)
		{
			$article = $this->getFrontpageArticle('main');
			if ($article instanceof TBGWikiArticle)
			{
				TBGActionComponent::includeComponent('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
			}
		}

		public function listen_frontpageLeftmenu(TBGEvent $event)
		{
			$article = $this->getFrontpageArticle('menu');
			if ($article instanceof TBGWikiArticle)
			{
				TBGActionComponent::includeComponent('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
			}
		}

		public function listen_projectLinks(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('publish/projectlinks', array('project' => $event->getSubject()));
		}

		public function listen_projectMenustripLinks(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('publish/projectmenustriplinks', array('project' => $event->getSubject(), 'selected_tab' => $event->getParameter('selected_tab')));
		}

		public function listen_createNewProject(TBGEvent $event)
		{
			if (!TBGWikiArticle::getByName(ucfirst($event->getSubject()->getKey()).':MainPage') instanceof TBGWikiArticle)
			{
				$article = TBGWikiArticle::createNew(ucfirst($event->getSubject()->getKey()).':MainPage', "[[Category:{$event->getSubject()->getName()}:About]]This is the wiki frontpage for the {$event->getSubject()->getName()} project", true);
			}
		}

		public function getTabKey()
		{
			return (TBGContext::isProjectContext()) ? parent::getTabKey() : 'wiki';
		}

	}
