<?php

    namespace thebuggenie\modules\publish\entities\tables;

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
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::ARTICLE_NAME, 300);
            parent::_addVarchar(self::LINK_ARTICLE_NAME, 300);
        }

        public function deleteLinksByArticle($article_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_NAME, $article_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doDelete($crit);
        }

        public function addArticleLink($article_name, $linked_article_name)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::ARTICLE_NAME, $article_name);
            $crit->addInsert(self::LINK_ARTICLE_NAME, $linked_article_name);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doInsert($crit);
        }

        public function getArticleLinks($article_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ARTICLE_NAME, $article_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit);

            return $res;
        }

        public function getLinkingArticles($linked_article_name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::LINK_ARTICLE_NAME, $linked_article_name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->doSelect($crit);

            return $res;
        }

        public function getUniqueArticleNames()
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ARTICLE_NAME);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->setDistinct();

            $names = array();
            if ($res = $this->doSelect($crit))
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
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::LINK_ARTICLE_NAME);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->setDistinct();

            $names = array();
            if ($res = $this->doSelect($crit))
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
