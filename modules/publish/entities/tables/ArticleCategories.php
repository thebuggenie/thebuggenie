<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable,
        b2db\Criteria;

    /**
     * @Table(name="articlecategories")
     */
    class ArticleCategories extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articlecategories';
        const ID = 'articlecategories.id';
        const ARTICLE_NAME = 'articlecategories.article_name';
        const ARTICLE_IS_CATEGORY = 'articlecategories.article_is_category';
        const CATEGORY_NAME = 'articlecategories.category_name';
        const SCOPE = 'articlecategories.scope';

        protected function _initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::ARTICLE_NAME, 300);
            parent::addBoolean(self::ARTICLE_IS_CATEGORY);
            parent::addVarchar(self::CATEGORY_NAME, 300);
        }

        public function deleteCategoriesByArticle($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function addArticleCategory($article_name, $category_name, $is_category)
        {
            $insertion = new Insertion();
            $insertion->add(self::ARTICLE_NAME, $article_name);
            $insertion->add(self::ARTICLE_IS_CATEGORY, $is_category);
            $insertion->add(self::CATEGORY_NAME, $category_name);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);
        }

        public function getArticleCategories($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::CATEGORY_NAME, \b2db\QueryColumnSort::SORT_ASC);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getCategoryArticles($category_name)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_NAME, $category_name);
            $query->where(self::ARTICLE_IS_CATEGORY, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::ARTICLE_NAME, \b2db\QueryColumnSort::SORT_ASC);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getSubCategories($category_name)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_NAME, $category_name);
            $query->where(self::ARTICLE_IS_CATEGORY, true);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::CATEGORY_NAME, \b2db\QueryColumnSort::SORT_ASC);
            $res = $this->rawSelect($query);

            return $res;
        }

    }
