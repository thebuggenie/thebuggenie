<?php

	/**
	 * The wiki class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
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
			$this->setConfigTitle($i18n->__('Wiki'));
			$this->setDescription($i18n->__('Enables Wiki-functionality'));
			$this->setConfigDescription($i18n->__('Set up the Wiki module from this section'));
			$this->setHasConfigSettings();
			if ($this->isEnabled())
			{
				if ($this->getSetting('allow_camelcase_links'))
				{
					TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
					TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
				}
			}
		}

		protected function _addAvailableListeners()
		{
			$this->addAvailableListener('core', 'index_left', 'listen_frontpageLeftmenu', 'Frontpage left menu');
			$this->addAvailableListener('core', 'index_right_top', 'listen_frontpageArticle', 'Frontpage article');
			if ($this->isWikiTabsEnabled())
			{
				$this->addAvailableListener('core', 'project_overview_item_links', 'listen_projectLinks', 'Project overview links');
				$this->addAvailableListener('core', 'menustrip_item_links', 'listen_MenustripLinks', 'Menustrip links');
			}
			$this->addAvailableListener('core', 'TBGProject::createNew', 'listen_createNewProject', 'Create basic project wiki page');
			$this->addAvailableListener('core', 'upload', 'listen_upload', 'File is uploaded');
		}

		protected function _addAvailableRoutes()
		{
			$this->addRoute('publish', '/wiki', 'showArticle', array('article_name' => 'MainPage'));
			$this->addRoute('publish_article_new', '/wiki/new', 'editArticle', array('article_name' => 'NewArticle'));
			$this->addRoute('publish_article', '/wiki/:article_name', 'showArticle');
			$this->addRoute('publish_article_revision', '/wiki/:article_name/revision/:revision', 'showArticle');
			$this->addRoute('publish_article_edit', '/wiki/:article_name/edit', 'editArticle');
			$this->addRoute('publish_article_permissions', '/wiki/:article_name/permissions', 'articlePermissions');
			$this->addRoute('publish_article_attachments', '/wiki/:article_name/attachments', 'articleAttachments');
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

			$this->enableListenerSaved('core', 'index_left');
			$this->enableListenerSaved('core', 'index_right_top');
			$this->enableListenerSaved('core', 'project_overview_item_links');
			$this->enableListenerSaved('core', 'menustrip_item_links');
			$this->enableListenerSaved('core', 'TBGProject::createNew');
  									  
			TBGContext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
			TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
			TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
		}
		
		public function loadFixturesArticles($scope, $overwrite = true)
		{
			if (TBGContext::isCLI()) TBGCliCommand::cli_echo("Loading default articles\n");
			$this->loadArticles('', $overwrite);
			if (TBGContext::isCLI()) TBGCliCommand::cli_echo("... done\n");
		}
		
		public function loadArticles($namespace = '', $overwrite = true)
		{
			$scope = TBGContext::getScope()->getID();
			$namespace = strtolower($namespace);
			$_path_handle = opendir(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR);
			while ($original_article_name = readdir($_path_handle))
			{
				if (strpos($original_article_name, '.') === false)
				{
					$article_name = strtolower($original_article_name);
					$imported = false;
					$import = false;
					if ($namespace)
					{
						if (strpos(urldecode($article_name), "{$namespace}:") === 0 || (strpos(urldecode($article_name), "category:") === 0 && strpos(urldecode($article_name), "{$namespace}:") === 9))
						{
							$import = true;
						}
					}
					else
					{
						if (strpos(urldecode($article_name), "category:help:") === 0)
						{
							$name_test = substr(urldecode($article_name), 14);
						}
						elseif (strpos(urldecode($article_name), "category:") === 0)
						{
							$name_test = substr(urldecode($article_name), 9);
						}
						else
						{
							$name_test = urldecode($article_name);
						}
						if (strpos($name_test, ':') === false) 
							$import = true;
					}
					if ($import)
					{
						if (TBGContext::isCLI())
						{
							TBGCliCommand::cli_echo('Saving '.urldecode($original_article_name)."\n");
						}
						if ($overwrite)
						{
							TBGArticlesTable::getTable()->deleteArticleByName(urldecode($original_article_name));
						}
						if (TBGArticlesTable::getTable()->getArticleByName(urldecode($original_article_name)) === null)
						{
							$content = file_get_contents(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $original_article_name);
							TBGWikiArticle::createNew(urldecode($original_article_name), $content, true, $scope, array('overwrite' => $overwrite, 'noauthor' => true));
							$imported = true;
						}
						TBGEvent::createNew('publish', 'fixture_article_loaded', urldecode($original_article_name), array('imported' => $imported))->trigger();
					}
				}
			}
		}

		protected function _loadFixtures($scope)
		{
			$this->loadFixturesArticles($scope);

			TBGLinksTable::getTable()->addLink('wiki', 0, 'MainPage', 'Wiki Frontpage', 1, $scope);
			TBGLinksTable::getTable()->addLink('wiki', 0, 'WikiFormatting', 'Formatting help', 2, $scope);
			TBGLinksTable::getTable()->addLink('wiki', 0, 'Category:Help', 'Help topics', 3, $scope);
			TBGContext::setPermission('editarticle', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('deletearticle', 0, 'publish', 0, 1, 0, true, $scope);
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
			if ($request->hasParameter('import_articles'))
			{
				$cc = 0;
				foreach ($request->getParameter('import_article') as $article_name => $import)
				{
					$cc++;
					TBGArticlesTable::getTable()->deleteArticleByName(urldecode($article_name));
					$content = file_get_contents(TBGContext::getIncludePath() . 'modules' . DIRECTORY_SEPARATOR . 'publish' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . $article_name);
					TBGWikiArticle::createNew(urldecode($article_name), $content, true, null, array('overwrite' => true, 'noauthor' => true));
				}
				TBGContext::setMessage('module_message', TBGContext::getI18n()->__('%number_of_articles% articles imported successfully', array('%number_of_articles%' => $cc)));
			}
			else
			{
				$settings = array('allow_camelcase_links', 'menu_title', 'hide_wiki_links', 'free_edit');
				foreach ($settings as $setting)
				{
					if ($request->hasParameter($setting))
					{
						$this->saveSetting($setting, $request->getParameter($setting));
					}
				}
			}
		}

		public function getMenuTitle($project_context = null)
		{
			$project_context = ($project_context !== null) ? $project_context : TBGContext::isProjectContext();
			$i18n = TBGContext::getI18n();
			if (($menu_title = $this->getSetting('menu_title')) !== null)
			{
				switch ($menu_title)
				{
					case 5: return ($project_context) ? $i18n->__('Project archive') : $i18n->__('Archive') ;
					case 3: return ($project_context) ? $i18n->__('Project documentation') : $i18n->__('Documentation');
					case 4: return ($project_context) ? $i18n->__('Project documents') : $i18n->__('Documents');
					case 2: return ($project_context) ? $i18n->__('Project help') : $i18n->__('Help');
				}

			}
			return ($project_context) ? $i18n->__('Project wiki') : $i18n->__('Wiki');
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
			if (TBGTextParser::getCurrentParser() instanceof TBGTextParser)
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
			$crit->addWhere(TBGArticlesTable::NAME, 'Category:%', B2DBCriteria::DB_NOT_LIKE);
			
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

		public function listen_MenustripLinks(TBGEvent $event)
		{
			$project_url = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('publish_article', array('article_name' => ucfirst(TBGContext::getCurrentProject()->getKey()).':MainPage')) : null;
			$url = TBGContext::getRouting()->generate('publish');
			TBGActionComponent::includeTemplate('publish/menustriplinks', array('url' => $url, 'project_url' => $project_url, 'selected_tab' => $event->getParameter('selected_tab')));
		}

		public function listen_createNewProject(TBGEvent $event)
		{
			if (!TBGWikiArticle::getByName(ucfirst($event->getSubject()->getKey()).':MainPage') instanceof TBGWikiArticle)
			{
				$project_key = $event->getSubject()->getStrippedProjectName();
				$article = TBGWikiArticle::createNew("{$project_key}:MainPage", "This is the wiki frontpage for {$event->getSubject()->getName()} \n\n[[Category:{$project_key}:About]]", true);
				$this->loadArticles($project_key);
			}
		}

		public function getTabKey()
		{
			return (TBGContext::isProjectContext()) ? parent::getTabKey() : 'wiki';
		}

		protected function _checkArticlePermissions($article_name, $permission_name)
		{
			$user = TBGContext::getUser();
			switch ($this->getSetting('free_edit'))
			{
				case 1:
					$permissive = !$user->isGuest();
					break;
				case 2:
					$permissive = true;
					break;
				case 0:
				default:
					$permissive = false;
					break;
			}
			if ($user->hasPermission($permission_name, $article_name, 'publish', true, $permissive))
			{
				return true;
			}
			$namespaces = explode(':', $article_name);
			if (count($namespaces) > 1)
			{
				array_pop($namespaces);
				$composite_ns = '';
				foreach ($namespaces as $namespace)
				{
					$composite_ns .= ($composite_ns != '') ? ":{$namespace}" : $namespace;
					if ($user->hasPermission($permission_name, $composite_ns, 'publish', true, $permissive))
					{
						return true;
					}
				}
			}
			return $user->hasPermission($permission_name, 0, 'publish', false, $permissive);
		}
		
		public function canUserEditArticle($article_name)
		{
			return $this->_checkArticlePermissions($article_name, 'editarticle');
		}
		
		public function canUserDeleteArticle($article_name)
		{
			return $this->_checkArticlePermissions($article_name, 'deletearticle');
		}
		
	}
