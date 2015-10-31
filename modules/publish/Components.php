<?php

    namespace thebuggenie\modules\publish;

    use thebuggenie\core\framework,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\tables\Articles;

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

    }
