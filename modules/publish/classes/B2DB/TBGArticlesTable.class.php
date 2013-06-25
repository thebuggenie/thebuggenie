<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="articles")
	 * @Entity(class="TBGWikiArticle")
	 */
	class TBGArticlesTable extends TBGB2DBTable 
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
		
//		public function __construct()
//		{
//			parent::__construct(self::B2DBNAME, self::ID);
//			parent::_addVarchar(self::NAME, 255);
//			parent::_addText(self::CONTENT, false);
//			parent::_addBoolean(self::IS_PUBLISHED);
//			parent::_addInteger(self::DATE, 10);
//			parent::_addForeignKeyColumn(self::AUTHOR, TBGUsersTable::getTable(), TBGUsersTable::ID);
//			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
//		}

		public function _setupIndexes()
		{
			$this->_addIndex('name_scope', array(self::NAME, self::SCOPE));
		}

		public function getAllArticles()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::NAME);

			$res = $this->doSelect($crit);
			$articles = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$a_id = $row->get(self::ID);
					$articles[$a_id] = PublishFactory::article($a_id, $row);
				}
			}

			return $articles;
		}
		
		public function getManualSidebarArticles(TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere('articles.article_type', TBGWikiArticle::TYPE_MANUAL);
			$crit->addWhere('articles.parent_article_id', 0);
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}

			$crit->addOrderBy(self::NAME, 'asc');
			
			$articles = $this->select($crit);
			foreach ($articles as $i => $article)
			{
				if (!$article->hasAccess()) unset($articles[$i]);
			}
			
			return $articles;
		}
		
		public function getManualSidebarCategories(TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere('articles.article_type', TBGWikiArticle::TYPE_MANUAL);
			$crit->addWhere('articles.parent_article_id', 0);
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}

			$crit->addOrderBy(self::NAME, 'asc');
			
			$articles = $this->select($crit);
			foreach ($articles as $i => $article)
			{
				if (!$article->hasAccess()) unset($articles[$i]);
			}
			
			return $articles;
		}
		
		public function getArticles(TBGProject $project = null, $limit = 10)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_LIKE);
				$crit->addOr(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}

			$crit->addOrderBy(self::DATE, 'desc');
			
			$articles = array();
			
			if ($res = self::getTable()->doSelect($crit))
			{
				while (($row = $res->getNextRow()) && ($limit === null || count($articles) < $limit))
				{
					try
					{
						$article = PublishFactory::article($row->get(self::ID), $row);
					}
					catch (Exception $e) 
					{
						continue;
					}
					
					if ($article->hasAccess())
					{
						$articles[] = $article;
					}
				}
			}
	
			return $articles;
		}

		public function getArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			return $this->selectOne($crit, 'none');
		}

		public function doesArticleExist($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			return (bool) $this->doCount($crit);
		}

		public function deleteArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->setLimit(1);
			$row = $this->doDelete($crit);

			return $row;
		}

		public function getArticleByID($article_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectByID($article_id, $crit);

			return $row;
		}

		public function getUnpublishedArticlesByUser($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::IS_PUBLISHED, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			$res = $this->doSelect($crit);

			return $res;
		}

		public function doesNameConflictExist($name, $id, $scope = null)
		{
			$scope = ($scope === null) ? TBGContext::getScope()->getID() : $scope;

			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $name);
			$crit->addWhere(self::ID, $id, Criteria::DB_NOT_EQUALS);
			$crit->addWhere(self::SCOPE, $scope);

			return (bool) ($res = $this->doSelect($crit));
		}
		
		public function findArticlesContaining($content, $project = null, $limit = 5, $offset = 0)
		{
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
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

				return array($resultcount, $this->doSelect($crit));
			}
			else
			{
				return array($resultcount, array());
			}
		}

		public function save($name, $content, $published, $author, $id = null, $scope = null)
		{
			$scope = ($scope !== null) ? $scope : TBGContext::getScope()->getID();
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

		public function getDeadEndArticles(TBGProject $project = null)
		{
			$names = TBGArticleLinksTable::getTable()->getUniqueArticleNames();
			
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::NAME, $names, Criteria::DB_NOT_IN);
			$crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}

		public function getAllByLinksToArticleName($article_name)
		{
			$names_res = TBGArticleLinksTable::getTable()->getLinkingArticles($article_name);
			if (empty($names_res)) return array();

			$names = array();
			while ($row = $names_res->getNextRow())
			{
				$names[] = $row[TBGArticleLinksTable::ARTICLE_NAME];
			}
			
			$crit = $this->getCriteria();
			$crit->addWhere(self::NAME, $names, Criteria::DB_IN);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

			return $this->select($crit);
		}
		
		public function getUnlinkedArticles(TBGProject $project = null)
		{
			$names = TBGArticleLinksTable::getTable()->getUniqueLinkedArticleNames();
			
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::NAME, $names, Criteria::DB_NOT_IN);
			$crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}
		
		public function getUncategorizedArticles(TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::NAME, "Category:%", Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::CONTENT, '%[Category:%', Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}
		
		public function getUncategorizedCategories(TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::CONTENT, '#REDIRECT%', Criteria::DB_NOT_LIKE);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}
		
		public function getAllArticlesSpecial(TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "Category:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}
		
		protected function _getAllInNamespace($namespace, TBGProject $project = null)
		{
			$crit = $this->getCriteria();
			if ($project instanceof TBGProject)
			{
				$crit->addWhere(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . ":%", Criteria::DB_LIKE);
			}
			else
			{
				$crit->addWhere(self::NAME, "{$namespace}:%", Criteria::DB_LIKE);
				foreach (TBGProjectsTable::getTable()->getAllIncludingDeleted() as $project)
				{
					if (trim($project->getKey()) == '') continue;
					$crit->addWhere(self::NAME, "{$namespace}:" . ucfirst($project->getKey()) . "%", Criteria::DB_NOT_LIKE);
					$crit->addWhere(self::NAME, ucfirst($project->getKey()) . ":%", Criteria::DB_NOT_LIKE);
				}
			}
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			
			return $this->select($crit);
		}
		
		public function getAllCategories(TBGProject $project = null)
		{
			return $this->_getAllInNamespace('Category', $project);
		}
		
		public function getAllTemplates(TBGProject $project = null)
		{
			return $this->_getAllInNamespace('Template', $project);
		}
		
	}

