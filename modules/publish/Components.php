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

            $username = $request->getParameter('user');
            $page_size = ceil($request->getParameter('page_size', $default_page_size));
            $page = floor($request->getParameter('page', $default_page));

            // All contributions made by the user will be stored within this
            // array.
            $contributions = array();

            // Try to get the user based on passed ID, or use currently
            // logged-in user if not provided.
            if ($username !== null)
            {
                $user = Users::getTable()->getByUsername($username);
            }
            else
            {
                $user = framework\Context::getUser();
            }

            if ($user !== null)
            {
                $history = ArticleHistory::getTable()->getByUser($user);

                if ($history)
                {
                    // Contributions by the user will be stored in this specific array.
                    $contributions = array();

                    while ($row = $history->getNextRow())
                    {
                        // Extract basic information.
                        $date = $row[ArticleHistory::DATE];
                        $revision = $row[ArticleHistory::REVISION];
                        $article_name = $row[ArticleHistory::ARTICLE_NAME];
                        $reason = $row[ArticleHistory::REASON];
                        $author = $row[ArticleHistory::AUTHOR];

                        // Ignore articles the currently logged-in user can't read.
                        $article = Article::getByName($article_name);
                        if (!$article->canRead() || $article->getProject() != $current_project)
                        {
                            continue;
                        }

                        // Calculated properties, primarily URLs.
                        $revision_url = make_url('publish_article_revision', array('article_name' => $article_name,
                                                                                   'revision' => $revision));
                        $article_url = make_url('publish_article', array('article_name' => $article_name));
                        if ($revision > 1)
                        {
                            $diff_url = make_url('publish_article_diff', array('article_name' => $article_name,
                                                                               'from_revision' => $revision-1,
                                                                               'to_revision' => $revision));
                        }
                        else
                        {
                            $diff_url = null;
                        }
                        $history_url = make_url('publish_article_history', array('article_name' => $article_name));

                        // Add contribution for consumption in template.
                        $contributions[] = array('date' => $date,
                                                 'revision' => $revision,
                                                 'article_name' => $article_name,
                                                 'reason' => $reason,
                                                 'author' => $author,
                                                 'revision_url' => $revision_url,
                                                 'article_url' => $article_url,
                                                 'diff_url' => $diff_url,
                                                 'history_url' => $history_url);
                    }
                }

                // Ensure the page size is a valid value (has to be whole number greated than 0).
                if ($page_size < 1 )
                {
                    $page_size = 1;
                }

                // Calculate number of contributions and how many pages we have.
                $contributions_size = sizeof($contributions);
                $total_pages = ceil($contributions_size / $page_size);

                // Ensure we are not out of bounds with page number.
                if ($page > $total_pages)
                {
                    $page = $total_pages;
                }
                elseif ($page < 1)
                {
                    $page = 1;
                }

                // Determine starting and ending contribution that fits on this specific page.
                $page_start_at = ($page - 1) * $page_size;
                $page_end_at = ($page - 1) * $page_size + $page_size - 1;

                // Make sure we don't go past the last contribution.
                if ($page_end_at > $contributions_size - 1)
                {
                    $page_end_at = $contributions_size - 1;
                }

                // Calculate pagination URLs.
                $navigation_urls = array();
                $page_size_urls = array();

                $base_url = make_url('publish_article', array('article_name' => "Special:{$this->projectnamespace}Contributions")) . '?user=' . $username;
                $navigation_url_base = $base_url . ($page_size != $default_page_size ? '&page_size=' . $page_size : '');

                $navigation_urls['newest'] = $navigation_url_base . '&page=' . '1';
                $navigation_urls['oldest'] = $navigation_url_base . '&page=' . $total_pages;
                $navigation_urls['newer']  = $navigation_url_base . '&page=' . ($page > 1 ? $page - 1 : 1);
                $navigation_urls['older']  = $navigation_url_base . '&page=' . ($page < $total_pages ? $page + 1 : $total_pages);

                foreach ($available_page_sizes as $available_page_size)
                {
                    $page_size_urls[$available_page_size] = $base_url . '&page_size=' . $available_page_size;
                }

                // Prepare context for template.
                $this->username = $username;
                $this->user = $user;
                $this->contributions = $contributions;

                $this->page = $page;
                $this->total_pages = $total_pages;
                $this->page_size = $page_size;
                $this->page_start_at = $page_start_at;
                $this->page_end_at = $page_end_at;
                $this->navigation_urls = $navigation_urls;
                $this->page_size_urls = $page_size_urls;
                $this->available_page_sizes = $available_page_sizes;
            }
            else
            {
                $this->user = $user;
                $this->username = $username;
            }
        }

        public function componentSpecialContributors()
        {
            $current_project = framework\Context::getCurrentProject();

            $user_ids = ArticleHistory::getTable()->getContributorIDsByProject($current_project);

            $this->contributors = Users::getTable()->getByUserIDs($user_ids);
            $this->contributions_base_url = make_url('publish_article', array('article_name' => "Special:{$this->projectnamespace}Contributions"));
        }
    }
