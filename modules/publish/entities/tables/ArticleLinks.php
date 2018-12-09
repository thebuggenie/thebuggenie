<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable;

    /**
     * @Table(name="articlelinks")
     */
    class ArticleLinks extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articlelinks';
        const ID = 'articlelinks.id';
        const ARTICLE_NAME = 'articlelinks.article_name';
        const LINK_ARTICLE_NAME = 'articlelinks.link_article_name';
        const SCOPE = 'articlelinks.scope';

        protected function _initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::ARTICLE_NAME, 300);
            parent::addVarchar(self::LINK_ARTICLE_NAME, 300);
        }

        public function deleteLinksByArticle($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function addArticleLink($article_name, $linked_article_name)
        {
            $insertion = new Insertion();
            $insertion->add(self::ARTICLE_NAME, $article_name);
            $insertion->add(self::LINK_ARTICLE_NAME, $linked_article_name);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);
        }

        public function getArticleLinks($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getLinkingArticles($linked_article_name)
        {
            $query = $this->getQuery();
            $query->where(self::LINK_ARTICLE_NAME, $linked_article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getUniqueArticleNames()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ARTICLE_NAME);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setIsDistinct();

            $names = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $article_name = $row->get(self::ARTICLE_NAME);
                    $names[$article_name] = $article_name;
                }
            }

            return $names;
        }

        public function getUniqueLinkedArticleNames()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::LINK_ARTICLE_NAME);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setIsDistinct();

            $names = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $article_name = $row->get(self::LINK_ARTICLE_NAME);
                    $names[$article_name] = $article_name;
                }
            }

            return $names;
        }

    }
