<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * @Table(name="articlehistory")
	 */
	class TBGArticleHistoryTable extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'articlehistory';
		const ID = 'articlehistory.id';
		const ARTICLE_NAME = 'articlehistory.article_name';
		const OLD_CONTENT = 'articlehistory.old_content';
		const NEW_CONTENT = 'articlehistory.new_content';
		const REASON = 'articlehistory.reason';
		const REVISION = 'articlehistory.revision';
		const DATE = 'articlehistory.date';
		const AUTHOR = 'articlehistory.author';
		const SCOPE = 'articlehistory.scope';
		
		public function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addVarchar(self::ARTICLE_NAME, 255);
			parent::_addText(self::OLD_CONTENT, false);
			parent::_addText(self::NEW_CONTENT, false);
			parent::_addVarchar(self::REASON, 255);
			parent::_addInteger(self::DATE, 10);
			parent::_addInteger(self::REVISION, 10);
			parent::_addForeignKeyColumn(self::AUTHOR, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		protected function _getNextRevisionNumberForArticle($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::REVISION, 'next_revision', Criteria::DB_MAX, '', '+1');
			$crit->addWhere(self::ARTICLE_NAME, $article_name);

			$row = $this->doSelectOne($crit);
			return ($row->get('next_revision')) ? $row->get('next_revision') : 1;
		}

		public function addArticleHistory($article_name, $old_content, $new_content, $user_id, $reason = null)
		{
			$transaction = Core::startTransaction();
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE_NAME, $article_name);
			$crit->addInsert(self::AUTHOR, $user_id);
			$revision_number = $this->_getNextRevisionNumberForArticle($article_name);
			$crit->addInsert(self::REVISION, $revision_number);

			if (!($revision_number == 1 && $old_content == $new_content))
			{
				$crit->addInsert(self::OLD_CONTENT, $old_content);
			}
			else
			{
				$crit->addInsert(self::OLD_CONTENT, '');
			}
			$crit->addInsert(self::NEW_CONTENT, $new_content);

			if ($reason !== null)
			{
				$crit->addInsert(self::REASON, $reason);
			}
			
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::DATE, time());

			$res = $this->doInsert($crit);
			$transaction->commitAndEnd();

			return $res->getInsertID();
		}

		public function getHistoryByArticleName($article_name)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addOrderBy(self::REVISION, 'desc');

			$res = $this->doSelect($crit);

			return $res;
		}

		public function getRevisionContentFromArticleName($article_name, $from_revision, $to_revision = null)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$ctn = $crit->returnCriterion(self::REVISION, $from_revision);
			if ($to_revision !== null)
			{
				$ctn->addOr(self::REVISION, $to_revision);
			}
			$crit->addWhere($ctn);

			$res = $this->doSelect($crit);

			if ($res)
			{
				$retval = array();
				while ($row = $res->getNextRow())
				{
					$author = ($row->get(self::AUTHOR)) ? TBGContext::factory()->TBGUser($row->get(self::AUTHOR)) : null;
					$retval[$row->get(self::REVISION)] = array('old_content' => $row->get(self::OLD_CONTENT), 'new_content' => $row->get(self::NEW_CONTENT), 'date' => $row->get(self::DATE), 'author' => $author);
				}

				return ($to_revision !== null) ? $retval : $retval[$from_revision];
			}
			else
			{
				return null;
			}
		}

		public function removeArticleRevisionsSince($article_name, $revision)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE_NAME, $article_name);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addWhere(self::REVISION, $revision, Criteria::DB_GREATER_THAN);
			$res = $this->doDelete($crit);
		}

		

	}