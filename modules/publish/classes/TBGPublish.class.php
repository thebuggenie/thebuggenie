<?php

	/**
	 * The wiki class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage publish
	 */

	/**
	 * The wiki class
	 *
	 * @package thebuggenie
	 * @subpackage publish
	 * 
	 * @Table(name="TBGModulesTable")
	 */
	class TBGPublish extends TBGModule 
	{
		
		const PERMISSION_READ_ARTICLE = 'readarticle';
		const PERMISSION_EDIT_ARTICLE = 'editarticle';
		const PERMISSION_DELETE_ARTICLE = 'deletearticle';

		protected $_longname = 'Wiki';
		
		protected $_description = 'Enables Wiki-functionality';
		
		protected $_module_config_title = 'Wiki';
		
		protected $_module_config_description = 'Set up the Wiki module from this section';
		
		protected $_has_config_settings = true;
		
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

		protected function _initialize()
		{
			if ($this->isEnabled() && $this->getSetting('allow_camelcase_links'))
			{
				TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
				TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
			}
		}

		protected function _addListeners()
		{
			TBGEvent::listen('core', 'index_left', array($this, 'listen_frontpageLeftmenu'));
			TBGEvent::listen('core', 'index_right_top', array($this, 'listen_frontpageArticle'));
			if ($this->isWikiTabsEnabled())
			{
				TBGEvent::listen('core', 'project_overview_item_links', array($this, 'listen_projectLinks'));
				TBGEvent::listen('core', 'menustrip_item_links', array($this, 'listen_MenustripLinks'));
				TBGEvent::listen('core', 'breadcrumb_main_links', array($this, 'listen_BreadcrumbMainLinks'));
				TBGEvent::listen('core', 'breadcrumb_project_links', array($this, 'listen_BreadcrumbProjectLinks'));
			}
			TBGEvent::listen('core', 'TBGProject::_postSave', array($this, 'listen_createNewProject'));
			TBGEvent::listen('core', 'TBGFile::hasAccess', array($this, 'listen_fileHasAccess'));
			TBGEvent::listen('core', 'upload', array($this, 'listen_upload'));
			TBGEvent::listen('core', 'quicksearch_dropdown_firstitems', array($this, 'listen_quicksearchDropdownFirstItems'));
			TBGEvent::listen('core', 'quicksearch_dropdown_founditems', array($this, 'listen_quicksearchDropdownFoundItems'));
			TBGEvent::listen('core', 'rolepermissionsedit', array($this, 'listen_rolePermissionsEdit'));
		}

		protected function _addRoutes()
		{
		}

		protected function _install($scope)
		{
			TBGContext::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
			$this->saveSetting('allow_camelcase_links', 1);
			$this->saveSetting('require_change_reason', 1);

			TBGContext::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
			TBGTextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
			TBGTextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
		}
		
		public function loadFixturesArticles($scope, $overwrite = true)
		{
			if (TBGContext::isCLI()) TBGCliCommand::cli_echo("Loading default articles\n");
			$this->loadArticles('', $overwrite, $scope);
			if (TBGContext::isCLI()) TBGCliCommand::cli_echo("... done\n");
		}
		
		public function loadArticles($namespace = '', $overwrite = true, $scope = null)
		{
			$scope = TBGContext::getScope()->getID();
			$namespace = mb_strtolower($namespace);
			$_path_handle = opendir(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS);
			while ($original_article_name = readdir($_path_handle))
			{
				if (mb_strpos($original_article_name, '.') === false)
				{
					$article_name = mb_strtolower($original_article_name);
					$imported = false;
					$import = false;
					if ($namespace)
					{
						if (mb_strpos(urldecode($article_name), "{$namespace}:") === 0 || (mb_strpos(urldecode($article_name), "category:") === 0 && mb_strpos(urldecode($article_name), "{$namespace}:") === 9))
						{
							$import = true;
						}
					}
					else
					{
						if (mb_strpos(urldecode($article_name), "category:help:") === 0)
						{
							$name_test = mb_substr(urldecode($article_name), 14);
						}
						elseif (mb_strpos(urldecode($article_name), "category:") === 0)
						{
							$name_test = mb_substr(urldecode($article_name), 9);
						}
						else
						{
							$name_test = urldecode($article_name);
						}
						if (mb_strpos($name_test, ':') === false) 
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
							$content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $original_article_name);
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
			TBGContext::setPermission(self::PERMISSION_READ_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission(self::PERMISSION_EDIT_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
			TBGContext::setPermission(self::PERMISSION_DELETE_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
		}
		
		protected function _uninstall()
		{
			if (TBGContext::getScope()->getID() == 1)
			{
				TBGArticlesTable::getTable()->drop();
				\b2db\Core::getTable('TBGBillboardPostsTable')->drop();
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
				foreach ($request['import_article'] as $article_name => $import)
				{
					$cc++;
					TBGArticlesTable::getTable()->deleteArticleByName(urldecode($article_name));
					$content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $article_name);
					TBGWikiArticle::createNew(urldecode($article_name), $content, true, null, array('overwrite' => true, 'noauthor' => true));
				}
				TBGContext::setMessage('module_message', TBGContext::getI18n()->__('%number_of_articles% articles imported successfully', array('%number_of_articles%' => $cc)));
			}
			else
			{
				$settings = array('allow_camelcase_links', 'menu_title', 'hide_wiki_links', 'free_edit', 'require_change_reason');
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
			return mb_substr($matches[0], 1);
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

		public function getLatestArticles($limit = 5)
		{
			return TBGArticlesTable::getTable()->getArticles($limit, true);
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

					if ($article->hasAccess())
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

		protected function _getPermissionslist()
		{
			$permissions = array();
			$permissions['editwikimenu'] = array('description' => TBGContext::getI18n()->__('Can edit the wiki lefthand menu'), 'permission' => 'editwikimenu');
			$permissions['readarticle'] = array('description' => TBGContext::getI18n()->__('Can access the project wiki'), 'permission' => 'readarticle');
			$permissions['editarticle'] = array('description' => TBGContext::getI18n()->__('Can write articles in project wiki'), 'permission' => 'editarticle');
			$permissions['deletearticle'] = array('description' => TBGContext::getI18n()->__('Can delete articles from project wiki'), 'permission' => 'deletearticle');
			return $permissions;
		}

		public function getPermissionDetails($permission)
		{
			$permissions = $this->_getPermissionslist();
			if (array_key_exists($permission, $permissions))
			{
				return $permissions[$permission];
			}
		}

		public function listen_rolePermissionsEdit(TBGEvent $event)
		{
			TBGActionComponent::includeTemplate('configuration/rolepermissionseditlist', array('role' => $event->getSubject(), 'permissions_list' => $this->_getPermissionslist(), 'module' => 'publish', 'target_id' => '%project_key%'));
		}

		public function listen_BreadcrumbMainLinks(TBGEvent $event)
		{
			$link = array('url' => TBGContext::getRouting()->generate('publish'), 'title' => $this->getMenuTitle(TBGContext::isProjectContext()));
			$event->addToReturnList($link);
		}

		public function listen_fileHasAccess(TBGEvent $event)
		{
			$article_ids = TBGArticleFilesTable::getTable()->getArticlesByFileID($event->getSubject()->getID());

			foreach ($article_ids as $article_id)
			{
				$article = new TBGWikiArticle($article_id);
				if ($article->canRead())
				{
					$event->setProcessed();
					$event->setReturnValue(true);
					break;
				}
			}
			
		}

		public function listen_BreadcrumbProjectLinks(TBGEvent $event)
		{
			$link = array('url' => TBGContext::getRouting()->generate('publish_article', array('article_name' => TBGContext::getCurrentProject()->getKey() . ':MainPage')), 'title' => $this->getMenuTitle(true));
			$event->addToReturnList($link);
		}

		public function listen_MenustripLinks(TBGEvent $event)
		{
			$project_url = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('publish_article', array('article_name' => ucfirst(TBGContext::getCurrentProject()->getKey()).':MainPage')) : null;
			$wiki_url = (TBGContext::isProjectContext() && TBGContext::getCurrentProject()->hasWikiURL()) ? TBGContext::getCurrentProject()->getWikiURL() : null;
			$url = TBGContext::getRouting()->generate('publish');
			TBGActionComponent::includeTemplate('publish/menustriplinks', array('url' => $url, 'project_url' => $project_url, 'wiki_url' => $wiki_url, 'selected_tab' => $event->getParameter('selected_tab')));
		}

		public function listen_createNewProject(TBGEvent $event)
		{
			if (!TBGWikiArticle::getByName(ucfirst($event->getSubject()->getKey()).':MainPage') instanceof TBGWikiArticle)
			{
				$project_key = $event->getSubject()->getKey();
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
			$retval = $user->hasPermission($permission_name, $article_name, 'publish', true, $permissive);
			if ($retval !== null)
			{
				return $retval;
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
			$permissive = ($permission_name == self::PERMISSION_READ_ARTICLE) ? true : $permissive;
			$retval = $user->hasPermission($permission_name, 0, 'publish', true, $permissive);
			return ($retval !== null) ? $retval : $permissive;
		}
		
		public function canUserReadArticle($article_name)
		{
			return $this->_checkArticlePermissions($article_name, self::PERMISSION_READ_ARTICLE);
		}
		
		public function canUserEditArticle($article_name)
		{
			return $this->_checkArticlePermissions($article_name, self::PERMISSION_EDIT_ARTICLE);
		}
		
		public function canUserDeleteArticle($article_name)
		{
			return $this->_checkArticlePermissions($article_name, self::PERMISSION_DELETE_ARTICLE);
		}
		
		public function listen_quicksearchDropdownFirstItems(TBGEvent $event)
		{
			$searchterm = $event->getSubject();
			TBGActionComponent::includeTemplate('publish/quicksearch_dropdown_firstitems', array('searchterm' => $searchterm));
		}
		
		public function listen_quicksearchDropdownFoundItems(TBGEvent $event)
		{
			$searchterm = $event->getSubject();
			list ($resultcount, $articles) = TBGWikiArticle::findArticlesByContentAndProject($searchterm, TBGContext::getCurrentProject());
			TBGActionComponent::includeTemplate('publish/quicksearch_dropdown_founditems', array('searchterm' => $searchterm, 'articles' => $articles, 'resultcount' => $resultcount));
		}
		
	}
