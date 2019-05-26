<?php

    namespace thebuggenie\modules\publish\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable,
        thebuggenie\modules\publish\entities\Article,
        b2db\Criteria;

    /**
     * @static @method Articles getTable() Retrieves an instance of this table
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
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::NAME);

            return $this->select($query);
        }

        public function getManualSidebarArticles(\thebuggenie\core\entities\Project $project = null, $filter = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.article_type', Article::TYPE_MANUAL);
            $query->where('articles.name', '%' . strtolower($filter) . '%', \b2db\Criterion::LIKE);
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $criteria = new Criteria();
                $criteria->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
                $criteria->or(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::LIKE);
                $query->where($criteria);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }

            $query->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($query);
            foreach ($articles as $i => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$i]);
            }

            return $articles;
        }

        public function getManualSidebarCategories(\thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.article_type', Article::TYPE_MANUAL);
            $query->where('articles.parent_article_id', 0);
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }

            $query->addOrderBy(self::NAME, 'asc');

            $articles = $this->select($query);
            foreach ($articles as $i => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$i]);
            }

            return $articles;
        }

        public function getArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where('articles.article_type', Article::TYPE_WIKI);

            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::LIKE);
                $query->or(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }

            $query->addOrderBy(self::DATE, 'desc');

            $articles = $this->select($query);
            foreach ($articles as $id => $article)
            {
                if (!$article->hasAccess())
                    unset($articles[$id]);
            }

            return $articles;
        }

        public function getArticleByName($name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            return $this->selectOne($query, 'none');
        }

        public function getArticleByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->rawSelectById($id, $query);
        }

        public function doesArticleExist($name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool) $this->count($query);
        }

        public function deleteArticleByName($name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setLimit(1);
            $row = $this->rawDelete($query);

            return $row;
        }

        public function doesNameConflictExist($name, $id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;

            $query = $this->getQuery();
            $query->where(self::NAME, $name);
            $query->where(self::ID, $id, \b2db\Criterion::NOT_EQUALS);
            $query->where(self::SCOPE, $scope);

            return (bool) $this->count($query);
        }

        public function findArticlesContaining($content, $project = null, $limit = 5, $offset = 0)
        {
            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", \b2db\Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where(self::NAME, "category:" . $project->getKey() . "%", \b2db\Criterion::LIKE);
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", \b2db\Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $criteria->where(self::NAME, $project->getKey() . "%", \b2db\Criterion::LIKE);
                $query->or($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", \b2db\Criterion::LIKE);
                $criteria->where(self::NAME, $project->getKey() . "%", \b2db\Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->or($criteria);
            }
            else
            {
                $criteria = new Criteria();
                $criteria->where(self::NAME, "%{$content}%", \b2db\Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->where($criteria);

                $criteria = new Criteria();
                $criteria->where(self::CONTENT, "%{$content}%", \b2db\Criterion::LIKE);
                $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
                $query->or($criteria);
            }

            $resultcount = $this->count($query);

            if ($resultcount)
            {
                $query->setLimit($limit);

                if ($offset) {
                    $query->setOffset($offset);
                }

                return [$resultcount, $this->select($query)];
            }
            else
            {
                return [$resultcount, []];
            }
        }

        public function save($name, $content, $published, $author, $id = null, $scope = null)
        {
            $scope = ($scope !== null) ? $scope : framework\Context::getScope()->getID();
            if ($id == null)
            {
                $insertion = new Insertion();
                $insertion->add(self::NAME, $name);
                $insertion->add(self::CONTENT, $content);
                $insertion->add(self::IS_PUBLISHED, (bool) $published);
                $insertion->add(self::AUTHOR, $author);
                $insertion->add(self::DATE, NOW);
                $insertion->add(self::SCOPE, $scope);
                $res = $this->rawInsert($insertion);
                return $res->getInsertID();
            }
            else
            {
                $update = new Update();
                $update->add(self::NAME, $name);
                $update->add(self::CONTENT, $content);
                $update->add(self::IS_PUBLISHED, (bool) $published);
                $update->add(self::AUTHOR, $author);
                $update->add(self::DATE, NOW);
                $res = $this->rawUpdateById($update, $id);
                return $res;
            }
        }

        public function getDeadEndArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueArticleNames();

            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, $names, \b2db\Criterion::NOT_IN);
            $query->where(self::CONTENT, '#REDIRECT%', \b2db\Criterion::NOT_LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
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

            $query = $this->getQuery();
            $query->where(self::NAME, $names, \b2db\Criterion::IN);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUnlinkedArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $names = ArticleLinks::getTable()->getUniqueLinkedArticleNames();

            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, $names, \b2db\Criterion::NOT_IN);
            $query->where(self::CONTENT, '#REDIRECT%', \b2db\Criterion::NOT_LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUncategorizedArticles(\thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::NAME, "Category:%", \b2db\Criterion::NOT_LIKE);
            $query->where(self::CONTENT, '#REDIRECT%', \b2db\Criterion::NOT_LIKE);
            $query->where(self::CONTENT, '%[Category:%', \b2db\Criterion::NOT_LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getUncategorizedCategories(\thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::CONTENT, '#REDIRECT%', \b2db\Criterion::NOT_LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllArticlesSpecial(\thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        protected function _getAllInNamespace($namespace, \thebuggenie\core\entities\Project $project = null)
        {
            $query = $this->getQuery();
            if ($project instanceof \thebuggenie\core\entities\Project)
            {
                $query->where(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . ":%", \b2db\Criterion::LIKE);
            }
            else
            {
                $query->where(self::NAME, "{$namespace}:%", \b2db\Criterion::LIKE);
                foreach (\thebuggenie\core\entities\tables\Projects::getTable()->getAllIncludingDeleted() as $project)
                {
                    if (trim($project->getKey()) == '')
                        continue;
                    $query->where(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . "%", \b2db\Criterion::NOT_LIKE);
                    $query->where(self::NAME, ucfirst($project->getKey()) . ":%", \b2db\Criterion::NOT_LIKE);
                }
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

        public function getAllCategories(\thebuggenie\core\entities\Project $project = null)
        {
            return $this->_getAllInNamespace('Category', $project);
        }

        public function getAllTemplates(\thebuggenie\core\entities\Project $project = null)
        {
            return $this->_getAllInNamespace('Template', $project);
        }

        public function fixArticleTypes()
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add('articles.article_type', Article::TYPE_WIKI);

            $query->where('articles.article_type', 0);

            $this->rawUpdate($update, $query);
        }

    }
