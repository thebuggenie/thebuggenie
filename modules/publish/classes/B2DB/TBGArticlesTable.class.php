<?php

	class TBGArticlesTable extends B2DBTable 
	{
		const B2DBNAME = 'articles';
		const ID = 'articles.id';
		const TITLE = 'articles.title';
		const ARTICLE_NAME = 'articles.article_name';
		const CONTENT = 'articles.content';
		const LINK = 'articles.link';
		const DELETED = 'articles.deleted';
		const IS_PUBLISHED = 'articles.is_published';
		const DATE = 'articles.date';
		const INTRO_TEXT = 'articles.intro_text';
		const AUTHOR = 'articles.author';
		const ORDER = 'articles.order';
		const ICON = 'articles.icon';
		const SCOPE = 'articles.scope';
		
		/**
		 * Return an instance of this table
		 *
		 * @return TBGArticlesTable
		 */
		public static function getTable()
		{
			return B2DB::getTable('TBGArticlesTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::TITLE, 255);
			parent::_addVarchar(self::ARTICLE_NAME, 255);
			parent::_addText(self::INTRO_TEXT, false);
			parent::_addText(self::CONTENT, false);
			parent::_addText(self::LINK, false);
			parent::_addBoolean(self::IS_PUBLISHED);
			parent::_addBoolean(self::DELETED);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::ORDER, 5);
			parent::_addVarchar(self::ICON, 50, '');
			parent::_addForeignKeyColumn(self::AUTHOR, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function getAllArticles()
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ARTICLE_NAME);

			$res = $this->doSelect($crit);
			$articles = array();

			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$a_id = $row->get(self::ID);
					$articles[$a_id] = PublishFactory::articleLab($a_id, $row);
				}
			}

			return $articles;
		}

		public function getArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$row = $this->doSelectOne($crit);

			return $row;
		}

		public function deleteArticleByName($name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $name);
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

		public function doesNameConflictExist($name, $id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $name);
			$crit->addWhere(self::ID, $id, B2DBCriteria::DB_NOT_EQUALS);

			return (bool) ($res = $this->doSelect($crit));
		}

		public function save($name, $content, $published, $author, $id = null, $scope = null)
		{
			$scope = ($scope !== null) ? $scope : TBGContext::getScope()->getID();
			$crit = $this->getCriteria();
			if ($id == null)
			{
				$crit->addInsert(self::ARTICLE_NAME, $name);
				$crit->addInsert(self::CONTENT, $content);
				$crit->addInsert(self::IS_PUBLISHED, (bool) $published);
				$crit->addInsert(self::AUTHOR, $author);
				$crit->addInsert(self::DATE, time());
				$crit->addInsert(self::SCOPE, $scope);
				$res = $this->doInsert($crit);
				return $res->getInsertID();
			}
			else
			{
				$crit->addUpdate(self::ARTICLE_NAME, $name);
				$crit->addUpdate(self::CONTENT, $content);
				$crit->addUpdate(self::IS_PUBLISHED, (bool) $published);
				$crit->addUpdate(self::AUTHOR, $author);
				$crit->addUpdate(self::DATE, time());
				$res = $this->doUpdateById($crit, $id);
				return $res;
			}
		}

	}

?>