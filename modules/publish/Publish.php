<?php

    namespace thebuggenie\modules\publish;

    use thebuggenie\core\framework,
        thebuggenie\core\helpers\TextParser,
        thebuggenie\core\entities\tables\Links,
        thebuggenie\core\entities\Project,
        thebuggenie\core\entities\User,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\tables\UserArticles,
        thebuggenie\modules\publish\entities\tables\Articles;

    /**
     * The wiki class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage publish
     */

    /**
     * The wiki class
     *
     * @package thebuggenie
     * @subpackage publish
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    class Publish extends \thebuggenie\core\entities\Module
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
                TextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
                TextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
            }
        }

        protected function _addListeners()
        {
            framework\Event::listen('core', 'index_left', array($this, 'listen_frontpageLeftmenu'));
            framework\Event::listen('core', 'index_right_top', array($this, 'listen_frontpageArticle'));
            if ($this->isWikiTabsEnabled())
            {
                framework\Event::listen('core', 'project_overview_item_links', array($this, 'listen_projectLinks'));
                framework\Event::listen('core', 'breadcrumb_main_links', array($this, 'listen_BreadcrumbMainLinks'));
                framework\Event::listen('core', 'breadcrumb_project_links', array($this, 'listen_BreadcrumbProjectLinks'));
            }
            framework\Event::listen('core', 'thebuggenie\core\entities\Project::_postSave', array($this, 'listen_createNewProject'));
            framework\Event::listen('core', 'thebuggenie\core\entities\File::hasAccess', array($this, 'listen_fileHasAccess'));
            framework\Event::listen('core', 'thebuggenie\core\entities\User::__getStarredArticles', array($this, 'User__getStarredArticles'));
            framework\Event::listen('core', 'thebuggenie\core\entities\User::__isArticleStarred', array($this, 'User__isArticleStarred'));
            framework\Event::listen('core', 'thebuggenie\core\entities\User::__addStarredArticle', array($this, 'User__addStarredArticle'));
            framework\Event::listen('core', 'thebuggenie\core\entities\User::__removeStarredArticle', array($this, 'User__removeStarredArticle'));
            framework\Event::listen('core', 'upload', array($this, 'listen_upload'));
            framework\Event::listen('core', 'quicksearch_dropdown_firstitems', array($this, 'listen_quicksearchDropdownFirstItems'));
            framework\Event::listen('core', 'quicksearch_dropdown_founditems', array($this, 'listen_quicksearchDropdownFoundItems'));
            framework\Event::listen('core', 'rolepermissionsedit', array($this, 'listen_rolePermissionsEdit'));
        }

        protected function _install($scope)
        {
            framework\Context::setPermission('article_management', 0, 'publish', 0, 1, 0, true, $scope);
            framework\Context::setPermission('publish_postonglobalbillboard', 0, 'publish', 0, 1, 0, true, $scope);
            framework\Context::setPermission('publish_postonteambillboard', 0, 'publish', 0, 1, 0, true, $scope);
            framework\Context::setPermission('manage_billboard', 0, 'publish', 0, 1, 0, true, $scope);
            $this->saveSetting('allow_camelcase_links', 1);
            $this->saveSetting('require_change_reason', 1);

            framework\Context::getRouting()->addRoute('publish_article', '/wiki/:article_name', 'publish', 'showArticle');
            TextParser::addRegex('/(?<![\!|\"|\[|\>|\/\:])\b[A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'getArticleLinkTag'));
            TextParser::addRegex('/(?<!")\![A-Z]+[a-z]+[A-Z][A-Za-z]*\b/', array($this, 'stripExclamationMark'));
        }

        protected function _upgrade()
        {
            switch ($this->_version)
            {
                case '1.0':
                    \thebuggenie\modules\publish\entities\Article::getB2DBTable()->upgrade(\thebuggenie\modules\publish\upgrade_32\TBGArticlesTable::getTable());
                    \thebuggenie\modules\publish\entities\tables\UserArticles::getTable()->create();
                    break;
            }
        }

        public function loadFixturesArticles($scope, $overwrite = true)
        {
            if (framework\Context::isCLI())
                \thebuggenie\core\framework\cli\Command::cli_echo("Loading default articles\n");
            $this->loadArticles('', $overwrite, $scope);
            if (framework\Context::isCLI())
                \thebuggenie\core\framework\cli\Command::cli_echo("... done\n");
        }

        public function loadArticles($namespace = '', $overwrite = true, $scope = null)
        {
            $scope = framework\Context::getScope()->getID();
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
                        if (framework\Context::isCLI())
                        {
                            \thebuggenie\core\framework\cli\Command::cli_echo('Saving ' . urldecode($original_article_name) . "\n");
                        }
                        if ($overwrite)
                        {
                            Articles::getTable()->deleteArticleByName(urldecode($original_article_name));
                        }
                        if (Articles::getTable()->getArticleByName(urldecode($original_article_name)) === null)
                        {
                            $content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $original_article_name);
                            Article::createNew(urldecode($original_article_name), $content, $scope, array('overwrite' => $overwrite, 'noauthor' => true));
                            $imported = true;
                        }
                        framework\Event::createNew('publish', 'fixture_article_loaded', urldecode($original_article_name), array('imported' => $imported))->trigger();
                    }
                }
            }
        }

        protected function _loadFixtures($scope)
        {
            $this->loadFixturesArticles($scope);

            Links::getTable()->addLink('wiki', 0, 'MainPage', 'Wiki Frontpage', 1, $scope);
            Links::getTable()->addLink('wiki', 0, 'WikiFormatting', 'Formatting help', 2, $scope);
            Links::getTable()->addLink('wiki', 0, 'Category:Help', 'Help topics', 3, $scope);
            framework\Context::setPermission(self::PERMISSION_READ_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
            framework\Context::setPermission(self::PERMISSION_EDIT_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
            framework\Context::setPermission(self::PERMISSION_DELETE_ARTICLE, 0, 'publish', 0, 1, 0, true, $scope);
        }

        protected function _uninstall()
        {
            if (framework\Context::getScope()->getID() == 1)
            {
                Articles::getTable()->drop();
            }
            Links::getTable()->removeByTargetTypeTargetIDandLinkID('wiki', 0);
            parent::_uninstall();
        }

        public function hasProjectAwareRoute()
        {
            return true;
        }

        public function getProjectAwareRoute($project_key)
        {
            return framework\Context::getRouting()->generate('publish_article', array('article_name' => ucfirst($project_key) . ":MainPage"));
        }

        public function isWikiTabsEnabled()
        {
            return (bool) ($this->getSetting('hide_wiki_links') != 1);
        }

        public function postConfigSettings(\thebuggenie\core\framework\Request $request)
        {
            if ($request->hasParameter('import_articles'))
            {
                $cc = 0;
                foreach ($request['import_article'] as $article_name => $import)
                {
                    $cc++;
                    Articles::getTable()->deleteArticleByName(urldecode($article_name));
                    $content = file_get_contents(THEBUGGENIE_MODULES_PATH . 'publish' . DS . 'fixtures' . DS . $article_name);
                    Article::createNew(urldecode($article_name), $content, null, array('overwrite' => true, 'noauthor' => true));
                }
                framework\Context::setMessage('module_message', framework\Context::getI18n()->__('%number_of_articles articles imported successfully', array('%number_of_articles' => $cc)));
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
            $project_context = ($project_context !== null) ? $project_context : framework\Context::isProjectContext();
            $i18n = framework\Context::getI18n();
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
            if (!framework\Context::isCLI())
            {
                framework\Context::loadLibrary('ui');
                return link_tag(make_url('publish_article', array('article_name' => $matches[0])), $article_name);
            }
            else
            {
                return $matches[0];
            }
        }

        public function getLatestArticles(Project $project = null)
        {
            return Articles::getTable()->getArticles($project);
        }

        public function getMenuItems($target_id = 0)
        {
            return Links::getTable()->getLinks('wiki', $target_id);
        }

        public function getFrontpageArticle($type)
        {
            $article_name = ($type == 'main') ? 'FrontpageArticle' : 'FrontpageLeftmenu';
            $article = Articles::getTable()->getArticleByName($article_name);
            return $article;
        }

        public function listen_frontpageArticle(framework\Event $event)
        {
            $article = $this->getFrontpageArticle('main');
            if ($article instanceof Article)
            {
                framework\ActionComponent::includeComponent('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
            }
        }

        public function listen_frontpageLeftmenu(framework\Event $event)
        {
            $article = $this->getFrontpageArticle('menu');
            if ($article instanceof Article)
            {
                framework\ActionComponent::includeComponent('publish/articledisplay', array('article' => $article, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true));
            }
        }

        public function listen_projectLinks(framework\Event $event)
        {
            framework\ActionComponent::includeComponent('publish/projectlinks', array('project' => $event->getSubject()));
        }

        protected function _getPermissionslist()
        {
            $permissions = array();
            $permissions['editwikimenu'] = array('description' => framework\Context::getI18n()->__('Can edit the wiki lefthand menu'), 'permission' => 'editwikimenu');
            $permissions['readarticle'] = array('description' => framework\Context::getI18n()->__('Can access the project wiki'), 'permission' => 'readarticle');
            $permissions['editarticle'] = array('description' => framework\Context::getI18n()->__('Can write articles in project wiki'), 'permission' => 'editarticle');
            $permissions['deletearticle'] = array('description' => framework\Context::getI18n()->__('Can delete articles from project wiki'), 'permission' => 'deletearticle');
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

        public function listen_rolePermissionsEdit(framework\Event $event)
        {
            framework\ActionComponent::includeComponent('configuration/rolepermissionseditlist', array('role' => $event->getSubject(), 'permissions_list' => $this->_getPermissionslist(), 'module' => 'publish', 'target_id' => '%project_key%'));
        }

        public function listen_BreadcrumbMainLinks(framework\Event $event)
        {
            $link = array('url' => framework\Context::getRouting()->generate('publish'), 'title' => $this->getMenuTitle(false));
            $event->addToReturnList($link);
        }

        public function listen_fileHasAccess(framework\Event $event)
        {
            $article_ids = \thebuggenie\modules\publish\entities\tables\ArticleFiles::getTable()->getArticlesByFileID($event->getSubject()->getID());

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

        public function listen_BreadcrumbProjectLinks(framework\Event $event)
        {
            $link = array('url' => framework\Context::getRouting()->generate('publish_article', array('article_name' => framework\Context::getCurrentProject()->getKey() . ':MainPage')), 'title' => $this->getMenuTitle(true));
            $event->addToReturnList($link);
        }

        /**
         * Header wiki menu and search dropdown / list
         *
         * @Listener(module="core", identifier="templates/headermainmenu::projectmenulinks")
         *
         * @param \thebuggenie\core\framework\Event $event
         */
        public function listen_MenustripLinks(framework\Event $event)
        {
            $project_url = (framework\Context::isProjectContext()) ? framework\Context::getRouting()->generate('publish_article', array('article_name' => ucfirst(framework\Context::getCurrentProject()->getKey()) . ':MainPage')) : null;
            $wiki_url = (framework\Context::isProjectContext() && framework\Context::getCurrentProject()->hasWikiURL()) ? framework\Context::getCurrentProject()->getWikiURL() : null;
            $url = framework\Context::getRouting()->generate('publish');
            framework\ActionComponent::includeComponent('publish/menustriplinks', array('url' => $url, 'project_url' => $project_url, 'wiki_url' => $wiki_url, 'selected_tab' => $event->getParameter('selected_tab')));
        }

        public function listen_createNewProject(framework\Event $event)
        {
            if (!Article::getByName(ucfirst($event->getSubject()->getKey()) . ':MainPage') instanceof Article)
            {
                $project_key = $event->getSubject()->getKey();
                $article = Article::createNew("{$project_key}:MainPage", "This is the wiki frontpage for {$event->getSubject()->getName()} \n\n[[Category:{$project_key}:About]]");
                $this->loadArticles($project_key);
            }
        }

        public function getTabKey()
        {
            return (framework\Context::isProjectContext()) ? parent::getTabKey() : 'wiki';
        }

        protected function _checkArticlePermissions($article_name, $permission_name)
        {
            $user = framework\Context::getUser();
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
            $retval = $user->hasPermission($permission_name, $article_name, 'publish');
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
                    $retval = $user->hasPermission($permission_name, $composite_ns, 'publish');
                    if ($retval !== null)
                    {
                        return $retval;
                    }
                }
            }
            $permissive = ($permission_name == self::PERMISSION_READ_ARTICLE) ? false : $permissive;
            $retval = $user->hasPermission($permission_name, 0, 'publish');
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

        public function listen_quicksearchDropdownFirstItems(framework\Event $event)
        {
            $searchterm = $event->getSubject();
            framework\ActionComponent::includeComponent('publish/quicksearch_dropdown_firstitems', array('searchterm' => $searchterm));
        }

        public function listen_quicksearchDropdownFoundItems(framework\Event $event)
        {
            $searchterm = $event->getSubject();
            list ($resultcount, $articles) = Article::findArticlesByContentAndProject($searchterm, framework\Context::getCurrentProject());
            framework\ActionComponent::includeComponent('publish/quicksearch_dropdown_founditems', array('searchterm' => $searchterm, 'articles' => $articles, 'resultcount' => $resultcount));
        }

        /**
         * Populate the array of starred articles
         */
        protected function User__populateStarredArticles(User $user)
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
        public function User__getStarredArticles(framework\Event $event)
        {
            $user = $event->getSubject();
            $this->User__populateStarredArticles($user);
            $event->setProcessed();
            $event->setReturnValue($user->_retrieve('publish', 'starredarticles'));
            return;
        }

        /**
         * Returns whether or not an article is starred
         *
         * @return boolean
         */
        public function User__isArticleStarred(framework\Event $event)
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
        public function User__addStarredArticle(framework\Event $event)
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
                    $article = Articles::getTable()->selectById($article_id);
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
         * @param framework\Event $event
         */
        public function User__removeStarredArticle(framework\Event $event)
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
