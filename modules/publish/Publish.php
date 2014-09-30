<?php

    namespace thebuggenie\modules\publish;

    use TBGEvent,
        TBGContext,
        TBGActionComponent,
        TBGCliCommand,
        TBGTextParser,
        TBGLinksTable,
        TBGProject,
        TBGUser,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\b2db\UserArticles,
        thebuggenie\modules\publish\entities\b2db\Articles;

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
     * @Table(name="\TBGModulesTable")
     */
    class Publish extends \TBGModule
    {

        const VERSION = '2.0';

        const PERMISSION_READ_ARTICLE = 'readarticle';
        const PERMISSION_EDIT_ARTICLE = 'editarticle';
        const PERMISSION_DELETE_ARTICLE = 'deletearticle';

        protected $_longname = 'Wiki';
        protected $_description = 'Enables Wiki-functionality';
        protected $_module_config_title = 'Wiki';
        protected $_module_config_description = 'Set up the Wiki module from this section';
        protected $_has_config_settings = true;

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
            TBGEvent::listen('core', 'TBGUser::__getStarredArticles', array($this, 'TBGUser__getStarredArticles'));
            TBGEvent::listen('core', 'TBGUser::__isArticleStarred', array($this, 'TBGUser__isArticleStarred'));
            TBGEvent::listen('core', 'TBGUser::__addStarredArticle', array($this, 'TBGUser__addStarredArticle'));
            TBGEvent::listen('core', 'TBGUser::__removeStarredArticle', array($this, 'TBGUser__removeStarredArticle'));
            TBGEvent::listen('core', 'upload', array($this, 'listen_upload'));
            TBGEvent::listen('core', 'quicksearch_dropdown_firstitems', array($this, 'listen_quicksearchDropdownFirstItems'));
            TBGEvent::listen('core', 'quicksearch_dropdown_founditems', array($this, 'listen_quicksearchDropdownFoundItems'));
            TBGEvent::listen('core', 'rolepermissionsedit', array($this, 'listen_rolePermissionsEdit'));
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

        protected function _upgrade()
        {
            switch ($this->_version)
            {
                case '1.0':
                    \thebuggenie\modules\publish\entities\Article::getB2DBTable()->upgrade(\thebuggenie\modules\publish\upgrade_32\TBGArticlesTable::getTable());
                    \thebuggenie\modules\publish\entities\b2db\UserArticles::getTable()->create();
                    break;
            }
        }

        public function loadFixturesArticles($scope, $overwrite = true)
        {
            if (TBGContext::isCLI())
                TBGCliCommand::cli_echo("Loading default articles\n");
            $this->loadArticles('', $overwrite, $scope);
            if (TBGContext::isCLI())
                TBGCliCommand::cli_echo("... done\n");
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
                            TBGCliCommand::cli_echo('Saving ' . urldecode($original_article_name) . "\n");
                        }
                        if ($overwrite)
                        {
                            Articles::getTable()->deleteArticleByName(urldecode($original_article_name));
                        }
                        if (Articles::getTable()->getArticleByName(urldecode($original_article_name)) === null)
                        {
                            $content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $original_article_name);
                            Article::createNew(urldecode($original_article_name), $content, true, $scope, array('overwrite' => $overwrite, 'noauthor' => true));
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
                Articles::getTable()->drop();
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
            return TBGContext::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key) . ":MainPage"));
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
                    Articles::getTable()->deleteArticleByName(urldecode($article_name));
                    $content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $article_name);
                    Article::createNew(urldecode($article_name), $content, true, null, array('overwrite' => true, 'noauthor' => true));
                }
                TBGContext::setMessage('module_message', TBGContext::getI18n()->__('%number_of_articles articles imported successfully', array('%number_of_articles' => $cc)));
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
                    case 5: return ($project_context) ? $i18n->__('Project archive') : $i18n->__('Archive');
                    case 3: return ($project_context) ? $i18n->__('Project documentation') : $i18n->__('Documentation');
                    case 4: return ($project_context) ? $i18n->__('Project documents') : $i18n->__('Documents');
                    case 2: return ($project_context) ? $i18n->__('Project help') : $i18n->__('Help');
                }
            }
            return ($project_context) ? $i18n->__('Project wiki') : $i18n->__('Wiki');
        }

        public function getSpacedName($camelcased)
        {
            return preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $camelcased);
        }

        public function stripExclamationMark($matches, $parser)
        {
            return mb_substr($matches[0], 1);
        }

        public function getArticleLinkTag($matches, $parser)
        {
            $article_link = $matches[0];
            $parser->addInternalLinkOccurrence($article_link);
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

        public function getLatestArticles(TBGProject $project = null)
        {
            return Articles::getTable()->getArticles($project);
        }

        public function getMenuItems($target_id = 0)
        {
            return TBGLinksTable::getTable()->getLinks('wiki', $target_id);
        }

        public function getUserDrafts()
        {
            $articles = Articles::getTable()->getUnpublishedArticlesByUser(TBGContext::getUser()->getID());
            return $articles;
        }

        public function getFrontpageArticle($type)
        {
            $article_name = ($type == 'main') ? 'FrontpageArticle' : 'FrontpageLeftmenu';
            $article = Articles::getTable()->getArticleByName($article_name);
            return $article;
        }

        public function listen_frontpageArticle(TBGEvent $event)
        {
            $article = $this->getFrontpageArticle('main');
            if ($article instanceof Article)
            {
                TBGActionComponent::includeComponent('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
            }
        }

        public function listen_frontpageLeftmenu(TBGEvent $event)
        {
            $article = $this->getFrontpageArticle('menu');
            if ($article instanceof Article)
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
            TBGActionComponent::includeTemplate('configuration/rolepermissionseditlist', array('role' => $event->getSubject(), 'permissions_list' => $this->_getPermissionslist(), 'module' => 'publish', 'target_id' => '%project_key'));
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
                $article = new Article($article_id);
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
            $project_url = (TBGContext::isProjectContext()) ? TBGContext::getRouting()->generate('publish_article', array('article_name' => ucfirst(TBGContext::getCurrentProject()->getKey()) . ':MainPage')) : null;
            $wiki_url = (TBGContext::isProjectContext() && TBGContext::getCurrentProject()->hasWikiURL()) ? TBGContext::getCurrentProject()->getWikiURL() : null;
            $url = TBGContext::getRouting()->generate('publish');
            TBGActionComponent::includeTemplate('publish/menustriplinks', array('url' => $url, 'project_url' => $project_url, 'wiki_url' => $wiki_url, 'selected_tab' => $event->getParameter('selected_tab')));
        }

        public function listen_createNewProject(TBGEvent $event)
        {
            if (!Article::getByName(ucfirst($event->getSubject()->getKey()) . ':MainPage') instanceof Article)
            {
                $project_key = $event->getSubject()->getKey();
                $article = Article::createNew("{$project_key}:MainPage", "This is the wiki frontpage for {$event->getSubject()->getName()} \n\n[[Category:{$project_key}:About]]", true);
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
            list ($resultcount, $articles) = Article::findArticlesByContentAndProject($searchterm, TBGContext::getCurrentProject());
            TBGActionComponent::includeTemplate('publish/quicksearch_dropdown_founditems', array('searchterm' => $searchterm, 'articles' => $articles, 'resultcount' => $resultcount));
        }

        /**
         * Populate the array of starred articles
         */
        protected function TBGUser__populateStarredArticles(TBGUser $user)
        {
            if ($user->_isset('publish', 'starredarticles') === null)
            {
                $articles = UserArticles::getTable()->getUserStarredArticles($user->getID());
                $user->_store('publish', 'starredarticles', $articles);
            }
        }

        /**
         * Returns an array of articles ids which are "starred" by this user
         *
         * @return array
         */
        public function TBGUser__getStarredArticles(TBGEvent $event)
        {
            $user = $event->getSubject();
            $this->TBGUser__populateStarredArticles($user);
            $event->setProcessed();
            $event->setReturnValue($user->_retrieve('publish', 'starredarticles'));
            return;
        }

        /**
         * Returns whether or not an article is starred
         *
         * @return boolean
         */
        public function TBGUser__isArticleStarred(TBGEvent $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            if ($user->_isset('publish', 'starredarticles'))
            {
                $articles = $user->getStarredArticles();
                $event->setProcessed();
                $event->setReturnValue(array_key_exists($article_id, $articles));
                return;
            }
            else
            {
                $event->setProcessed();
                $event->setReturnValue(UserArticles::getTable()->hasStarredArticle($user->getID(), $article_id));
                return;
            }
        }

        /**
         * Adds an article to the list of articles "starred" by this user
         *
         * @return boolean
         */
        public function TBGUser__addStarredArticle(TBGEvent $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            if ($user->isLoggedIn() && !$user->isGuest())
            {
                if (UserArticles::getTable()->hasStarredArticle($user->getID(), $article_id))
                {
                    $event->setProcessed();
                    $event->setReturnValue(true);
                    return;
                }

                UserArticles::getTable()->addStarredArticle($user->getID(), $article_id);
                if ($user->_isset('publish', 'starredarticles'))
                {
                    $article = TBGArticlesTable::getTable()->selectById($article_id);
                    $articles = $user->_retrieve('publish', 'starredarticles');
                    $articles[$article->getID()] = $article;
                    $user->_store('publish', 'starredarticles', $articles);
                }
                $event->setProcessed();
                $event->setReturnValue(true);
                return;
            }

            $event->setProcessed();
            $event->setReturnValue(false);
            return;
        }

        /**
         * Removes an article from the list of flagged articles
         *
         * @param integer $article_id ID of article to remove
         */
        public function TBGUser__removeStarredArticle(TBGEvent $event)
        {
            $user = $event->getSubject();
            $arguments = $event->getParameters();
            $article_id = $arguments[0];
            UserArticles::getTable()->removeStarredArticle($user->getID(), $article_id);
            if (isset($user->_starredarticles))
            {
                $articles = $user->_retrieve('publish', 'starredarticles');
                unset($articles[$article_id]);
                $user->_store('publish', 'starredarticles', $articles);
            }
            $event->setProcessed();
            $event->setReturnValue(true);
        }

    }
