<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable,
        thebuggenie\modules\publish\entities\Article,
        b2db\Criteria;

    /**
     * @method \thebuggenie\modules\publish\entities\tables\Articles getTable() Retrieves an instance of this table
     * @method \thebuggenie\modules\publish\entities\Article selectById(integer $id) Retrieves an article
     *
     * @Table(name="articles")
     * @Entity(class="\thebuggenie\modules\publish\entities\Article")
     */
    class Articles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'articles';
        const ID = 'articles.id';
        const NAME = 'articles.name';
        const CONTENT = 'articles.content';
        const IS_PUBLISHED = 'articles.is_published';
        const DATE = 'articles.date';
        const AUTHOR = 'articles.author';
        const SCOPE = 'articles.scope';

        public function _setupIndexes()
        {
            $this->_addIndex('name_scope', array(self::NAME, self::SCOPE));
        }

        public function getAllArticles()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy(self::NAME);

            return $this->select($crit);
        }

        public function getManualSidebarArticles(\thebuggenie\core\entities\Project $project = null, $filter = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere('articles.article_type', Article::TYPE_MANUAL);
            $crit->addWhere('articles.name', '%' . strtolower($filter) . '%', Criteria::DB_LIKE);
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $ctn = $crit->returnCriterion(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
                $ctn->addOr(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_LIKE);
                $crit->addWhere($ctn);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }

            $crit->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($crit);
            foreach ($articles as $i => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$i]);
            }

            return $articles;
        }

        public function getManualSidebarCategories(\thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere('articles.article_type', Article::TYPE_MANUAL);
            $crit->addWhere('articles.parent_article_id', 0);
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }

            $crit->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($crit);
            foreach ($articles as $i => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$i]);
            }

            return $articles;
        }

        public function getArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere('articles.article_type', Article::TYPE_WIKI);

            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_LIKE);
                $crit->addOr(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }

            $crit->addOrderBy(self::DATE, 'desc');

            $articles = $this->select($crit);
            foreach ($articles as $id => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$id]);
            }

            return $articles;
        }

        public function getArticleByName($name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            return $this->selectOne($crit, 'none');
        }

        public function doesArticleExist($name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->doCount($crit);
        }

        public function deleteArticleByName($name)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $name);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->setLimit(1);
            $row = $this->doDelete($crit);

            return $row;
        }

        public function doesNameConflictExist($name, $id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;

            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $name);
            $crit->addWhere(self::ID, $id, Criteria::DB_NOT_EQUALS);
            $crit->addWhere(self::SCOPE, $scope);

            return (bool) $this->doCount($crit);
        }

        public function findArticlesContaining($content, $project = null, $limit = 5, $offset = 0)
        {
            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $ctn = $crit->returnCriterion(self::NAME, "%{$content}%", Criteria::DB_LIKE);
                $ctn->addWhere(self::NAME, "category:" . $project->getKey() . "%", Criteria::DB_LIKE);
                $crit->addWhere($ctn);

                $ctn = $crit->returnCriterion(self::NAME, "%{$content}%", Criteria::DB_LIKE);
                $ctn->addWhere(self::NAME, $project->getKey() . "%", Criteria::DB_LIKE);
                $crit->addOr($ctn);

                $ctn = $crit->returnCriterion(self::CONTENT, "%{$content}%", Criteria::DB_LIKE);
                $ctn->addWhere(self::NAME, $project->getKey() . "%", Criteria::DB_LIKE);
                $crit->addOr($ctn);
            }
            else
            {
                $crit->addWhere(self::NAME, "%{$content}%", Criteria::DB_LIKE);
                $crit->addOr(self::CONTENT, "%{$content}%", Criteria::DB_LIKE);
            }

            $resultcount = $this->doCount($crit);

            if ($resultcount)
            {
                $crit->setLimit($limit);

                if ($offset)
                    $crit->setOffset($offset);

                return array($resultcount, $this->select($crit));
            }
            else
            {
                return array($resultcount, array());
            }
        }

        public function save($name, $content, $published, $author, $id = null, $scope = null)
        {
            $scope = ($scope !== null) ? $scope : framework\Context::getScope()->getID();
            $crit = $this->getCriteria();
            if ($id == null)
            {
                $crit->addInsert(self::NAME, $name);
                $crit->addInsert(self::CONTENT, $content);
                $crit->addInsert(self::IS_PUBLISHED, (bool) $published);
                $crit->addInsert(self::AUTHOR, $author);
                $crit->addInsert(self::DATE, NOW);
                $crit->addInsert(self::SCOPE, $scope);
                $res = $this->doInsert($crit);
                return $res->getInsertID();
            }
            else
            {
                $crit->addUpdate(self::NAME, $name);
                $crit->addUpdate(self::CONTENT, $content);
                $crit->addUpdate(self::IS_PUBLISHED, (bool) $published);
                $crit->addUpdate(self::AUTHOR, $author);
                $crit->addUpdate(self::DATE, NOW);
                $res = $this->doUpdateById($crit, $id);
                return $res;
            }
        }

        public function getDeadEndArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueArticleNames();

            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::NAME, $names, Criteria::DB_NOT_IN);
            $crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getAllByLinksToArticleName($article_name)
        {
            $names_res = ArticleLinks::getTable()->getLinkingArticles($article_name);
            if (empty($names_res))
                return array();

            $names = array();
            while ($row = $names_res->getNextRow())
            {
                $names[] = $row[ArticleLinks::ARTICLE_NAME];
            }

            $crit = $this->getCriteria();
            $crit->addWhere(self::NAME, $names, Criteria::DB_IN);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getUnlinkedArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueLinkedArticleNames();

            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::NAME, $names, Criteria::DB_NOT_IN);
            $crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getUncategorizedArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::NAME, "Category:%", Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::CONTENT, '%[Category:%', Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getUncategorizedCategories(\thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getAllArticlesSpecial(\thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        protected function _getAllInNamespace($namespace, \thebuggenie\core\entities\Project $project = null)
        {
            $crit = $this->getCriteria();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $crit->addWhere(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
            }
            else
            {
                $crit->addWhere(self::NAME, "{$namespace}:%", Criteria::DB_LIKE);
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $crit->addWhere(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
                    $crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
                }
            }
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }

        public function getAllCategories(\thebuggenie\core\entities\Project $project = null)
        {
            return $this->_getAllInNamespace('Category', $project);
        }

        public function getAllTemplates(\thebuggenie\core\entities\Project $project = null)
        {
            return $this->_getAllInNamespace('Template', $project);
        }

    }
