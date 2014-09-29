<?php

    namespace thebuggenie\modules\publish;

    use TBGContext,
        thebuggenie\modules\publish\entities\Article,
        thebuggenie\modules\publish\entities\b2db\Articles;

    class Components extends \TBGActionComponent
    {

        public function componentLatestArticles()
        {
            $this->latest_articles = TBGContext::getModule('publish')->getLatestArticles(TBGContext::getCurrentProject());
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
            $this->links_target_id = (TBGContext::isProjectContext()) ? TBGContext::getCurrentProject()->getID() : 0;
            $this->links = TBGContext::getModule('publish')->getMenuItems($this->links_target_id);
            $this->user_drafts = TBGContext::getModule('publish')->getUserDrafts();
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
            $this->articles = Articles::getTable()->getDeadEndArticles(TBGContext::getCurrentProject());
        }

        public function componentSpecialOrphanedPages()
        {
            $this->articles = Articles::getTable()->getUnlinkedArticles(TBGContext::getCurrentProject());
        }

        public function componentSpecialUncategorizedPages()
        {
            $this->articles = Articles::getTable()->getUncategorizedArticles(TBGContext::getCurrentProject());
        }

        public function componentSpecialAllPages()
        {
            $this->articles = Articles::getTable()->getAllArticlesSpecial(TBGContext::getCurrentProject());
        }

        public function componentSpecialAllCategories()
        {
            $this->articles = Articles::getTable()->getAllCategories(TBGContext::getCurrentProject());
        }

        public function componentSpecialAllTemplates()
        {
            $this->articles = Articles::getTable()->getAllTemplates(TBGContext::getCurrentProject());
        }

        public function componentSpecialWhatLinksHere()
        {
            $this->linked_article_name = TBGContext::getRequest()->getParameter('linked_article_name');
            $this->articles = Articles::getTable()->getAllByLinksToArticleName($this->linked_article_name);
        }

    }
