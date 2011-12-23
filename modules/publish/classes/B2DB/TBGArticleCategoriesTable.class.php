<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="articlecategories")
	 */
	class TBGArticleCategoriesTable extends TBGB2DBTable
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
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::ARTICLE_NAME, 300);
			parent::_addBoolean(self::ARTICLE_IS_CATEGORY);
			parent::_addVarchar(self::CATEGORY_NAME, 300);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function deleteCategoriesByArticle($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
		}

		public function addArticleCategory($article_name, $category_name, $is_category)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, $article_name);
			$crit->addInsert(self::ARTICLE_IS_CATEGORY, $is_category);
			$crit->addInsert(self::CATEGORY_NAME, $category_name);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
		}

		public function getArticleCategories($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::CATEGORY_NAME, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);

			return $res;
		}

		public function getCategoryArticles($category_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CATEGORY_NAME, $category_name);
			$crit->addWhere(self::ARTICLE_IS_CATEGORY, false);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::ARTICLE_NAME, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);

			return $res;
		}

		public function getSubCategories($category_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::CATEGORY_NAME, $category_name);
			$crit->addWhere(self::ARTICLE_IS_CATEGORY, true);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addOrderBy(self::CATEGORY_NAME, Criteria::SORT_ASC);
			$res = $this->doSelect($crit);

			return $res;
		}


	}