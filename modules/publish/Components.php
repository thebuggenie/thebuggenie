<?php

    namespace thebuggenie\modules\publish;

    use thebuggenie\core\framework,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\tables\Articles,
        thebuggenie\modules\publish\entities\tables\ArticleHistory,
        thebuggenie\core\entities\tables\Users;

    class Components extends framework\ActionComponent
    {

        public function componentLatestArticles()
        {
            $this->latest_articles = framework\Context::getModule('publish')->getLatestArticles(framework\Context::getCurrentProject());
        }

        public function componentWhatlinkshere()
        {
            $this->whatlinkshere = ($this->article instanceof Article) ? $this->article->getLinkingArticles() : array();
        }

        public function componentTools()
        {

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

                    $articles[$article_name] = array('exists' => Article::doesArticleExist(urldecode($article_name)), 'category' => mb_strtolower($category));
                }
            }
            ksort($articles, SORT_STRING);
            $this->articles = $articles;
            $this->categories = $categories;
        }

        public function componentLeftmenu()
        {
            $this->show_article_options = (bool) ($this->article instanceof Article);
            $this->links_target_id = (framework\Context::isProjectContext()) ? framework\Context::getCurrentProject()->getID() : 0;
            $this->links = framework\Context::getModule('publish')->getMenuItems($this->links_target_id);
        }

        public function componentManualSidebar()
        {
            $parents = array();
            $article = $this->article;
            do
            {
                $parent = $article->getParentArticle();
                if ($parent instanceof Article)
                {
                    $parents[$parent->getId()] = $parent->getId();
                    $article = $parent;
                }
            }
            while ($parent instanceof Article);

            $this->main_article = $article;
            $this->parents = $parents;
        }

        public function componentSpecialSpecialPages()
        {

        }

        public function componentSpecialDeadEndPages()
        {
            $this->articles = Articles::getTable()->getDeadEndArticles(framework\Context::getCurrentProject());
        }

        public function componentSpecialOrphanedPages()
        {
            $this->articles = Articles::getTable()->getUnlinkedArticles(framework\Context::getCurrentProject());
        }

        public function componentSpecialUncategorizedPages()
        {
            $this->articles = Articles::getTable()->getUncategorizedArticles(framework\Context::getCurrentProject());
        }

        public function componentSpecialAllPages()
        {
            $this->articles = Articles::getTable()->getAllArticlesSpecial(framework\Context::getCurrentProject());
        }

        public function componentSpecialAllCategories()
        {
            $this->articles = Articles::getTable()->getAllCategories(framework\Context::getCurrentProject());
        }

        public function componentSpecialAllTemplates()
        {
            $this->articles = Articles::getTable()->getAllTemplates(framework\Context::getCurrentProject());
        }

        public function componentSpecialWhatLinksHere()
        {
            $this->linked_article_name = framework\Context::getRequest()->getParameter('linked_article_name');
            $this->articles = Articles::getTable()->getAllByLinksToArticleName($this->linked_article_name);
        }

        public function componentSpecialContributions()
        {
            $request = framework\Context::getRequest();
            $current_project = framework\Context::getCurrentProject();

            $available_page_sizes = array(20, 50, 100, 250, 500);
            $default_page_size = 50;
            $default_page = 1;

            //Author ID associated with articles created automatically during
            //installation.
            $fixtures_user = 0;

            $username = $request->getParameter('user');
            $page_size = ceil($request->getParameter('page_size', $default_page_size));
            $page = floor($request->getParameter('page', $default_page));

            // Determine full username and whether the user is invalid or not.
            if ($username === "")
            {
                $invalid_user = false;
                $user = null;
                $user_full_name = null;
            }
            elseif ($username === null)
            {
                $invalid_user = false;
                $user = null;
                $user_full_name = null;
            }
            else
            {
                $user = Users::getTable()->getByUsername($username);
                if ($user === null)
                {
                    $invalid_user = true;
                    $user_full_name = null;
                }
                else
                {
                    $invalid_user = false;
                    $user_full_name = $user->getNameWithUsername();
                }
            }

            // Grab author contributions with current user's access rights in mind.
            $contributions = ArticleHistory::getTable()->getByAuthorUsernameAndCurrentUserAccess($username);

            // Ensure the page size is a valid value (has to be whole number greated than 0).
            if ($page_size < 1 )
            {
                $page_size = $default_page_size;
            }

            // Calculate number of contributions and how many pages we have.
            $contributions_size = count($contributions);
            $total_pages = ceil($contributions_size / $page_size);

            // Ensure we are not out of bounds with page number.
            if ($page > $total_pages)
            {
                $page = $total_pages;
            }
            elseif ($page < 1)
            {
                $page = $default_page;
            }

            // Narrow down list of contributions to current page.
            $contributions = array_slice($contributions,
                                         ($page - 1) * $page_size,
                                         ($page - 1) * $page_size + $page_size);

            // Calculate pagination URLs.
            $navigation_urls = array();
            $page_size_urls = array();

            $base_url = make_url('publish_article', array('article_name' => "Special:{$this->projectnamespace}Contributions"));
            $base_url_user_prefix = ($username !== null ? "?user={$username}&" : '?');
            $navigation_url_base = $base_url . $base_url_user_prefix . ($page_size != $default_page_size ? 'page_size=' . $page_size : '');

            $navigation_urls['newest'] = $navigation_url_base . '&page=' . '1';
            $navigation_urls['oldest'] = $navigation_url_base . '&page=' . $total_pages;
            $navigation_urls['newer']  = $navigation_url_base . '&page=' . ($page > 1 ? $page - 1 : 1);
            $navigation_urls['older']  = $navigation_url_base . '&page=' . ($page < $total_pages ? $page + 1 : $total_pages);

            foreach ($available_page_sizes as $available_page_size)
            {
                $page_size_urls[$available_page_size] = $base_url . $base_url_user_prefix . 'page_size='  . $available_page_size;
            }

            // Prepare context for template.
            $this->username = $username;
            $this->user = $user;
            $this->user_full_name = $user_full_name;
            $this->invalid_user = $invalid_user;
            $this->contributions = $contributions;

            $this->page = $page;
            $this->total_pages = $total_pages;
            $this->page_size = $page_size;
            $this->navigation_urls = $navigation_urls;
            $this->page_size_urls = $page_size_urls;
            $this->available_page_sizes = $available_page_sizes;
        }

        public function componentSpecialContributors()
        {
            $current_project = framework\Context::getCurrentProject();

            $user_ids = ArticleHistory::getTable()->getContributorIDsByProject($current_project);

            $this->contributors = Users::getTable()->getByUserIDs($user_ids);
            $this->contributions_base_url = make_url('publish_article', array('article_name' => "Special:{$this->projectnamespace}Contributions"));
        }
    }
