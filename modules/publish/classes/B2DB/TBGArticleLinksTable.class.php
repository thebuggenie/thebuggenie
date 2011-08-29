<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	class TBGArticleLinksTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'articlelinks';
		const ID = 'articlelinks.id';
		const ARTICLE_NAME = 'articlelinks.article_name';
		const LINK_ARTICLE_NAME = 'articlelinks.link_article_name';
		const SCOPE = 'articlelinks.scope';

		/**
		 * Return an instance of this table
		 *
		 * @return TBGArticleLinksTable
		 */
		public static function getTable()
		{
			return Core::getTable('TBGArticleLinksTable');
		}

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::ARTICLE_NAME, 300);
			parent::_addVarchar(self::LINK_ARTICLE_NAME, 300);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function deleteLinksByArticle($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
		}

		public function addArticleLink($article_name, $linked_article_name)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, $article_name);
			$crit->addInsert(self::LINK_ARTICLE_NAME, $linked_article_name);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
		}

		public function getArticleLinks($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);

			return $res;
		}

		public function getLinkingArticles($linked_article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::LINK_ARTICLE_NAME, $linked_article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doSelect($crit);

			return $res;
		}

	}

