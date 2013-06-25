<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * User articles table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * User articles table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="userarticles")
	 */
	class TBGUserArticlesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'userarticles';
		const ID = 'userarticles.id';
		const SCOPE = 'userarticles.scope';
		const ARTICLE = 'userarticles.article';
		const UID = 'userarticles.uid';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ARTICLE, TBGArticlesTable::getTable(), TBGArticlesTable::ID);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

		public function _setupIndexes()
		{
			$this->_addIndex('uid_scope', array(self::UID, self::SCOPE));
		}

		public function getUserIDsByArticleID($article_id)
		{
			$uids = array();
			$crit = $this->getCriteria();
			
			$crit->addWhere(self::ARTICLE, $article_id);
			
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$uid = $row->get(self::UID);
					$uids[$uid] = $uid;
				}
			}
			
			return $uids;
		}

		public function copyStarrers($from_article_id, $to_article_id)
		{
			$old_watchers = $this->getUserIDsByIssueID($from_article_id);
			$new_watchers = $this->getUserIDsByIssueID($to_article_id);

			if (count($old_watchers))
			{
				$crit = $this->getCriteria();
				$crit->addInsert(self::ARTICLE, $to_article_id);
				$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
				foreach ($old_watchers as $uid)
				{
					if (!in_array($uid, $new_watchers))
					{
						$crit->addInsert(self::UID, $uid);
						$this->doInsert($crit);
					}
				}
			}
		}
		
		public function getUserStarredArticles($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addJoin(TBGArticlesTable::getTable(), TBGArticlesTable::ID, self::ARTICLE);
			$crit->addWhere(TBGArticlesTable::DELETED, 0);
			
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function addStarredArticle($user_id, $article_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::ARTICLE, $article_id);
			$crit->addInsert(self::UID, $user_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());

			$this->doInsert($crit);
		}
		
		public function removeStarredArticle($user_id, $article_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ARTICLE, $article_id);
			$crit->addWhere(self::UID, $user_id);
				
			$this->doDelete($crit);
			return true;
		}
	}
