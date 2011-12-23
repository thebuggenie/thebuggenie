<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Votes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Votes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="votes")
	 */
	class TBGVotesTable extends TBGB2DBTable 
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'votes';
		const ID = 'votes.id';
		const SCOPE = 'votes.scope';
		const TARGET = 'votes.target';
		const VOTE = 'votes.vote';
		const UID = 'votes.uid';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET, 10);
			parent::_addInteger(self::VOTE, 2);
			parent::_addForeignKeyColumn(self::UID, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getVoteSumForIssue($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addSelectionColumn(self::VOTE, 'votes_total', Criteria::DB_SUM);
			$crit->addWhere(self::TARGET, $issue_id);
			$res = $this->doSelectOne($crit);

			return ($res) ? $res->get('votes_total') : 0;
		}
		
		public function getByIssueId($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			$res = $this->doSelect($crit);
			return $res;
		}
		
		public function addByUserIdAndIssueId($user_id, $issue_id, $up = true)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doDelete($crit);
			
			$crit = $this->getCriteria();
			$crit->addInsert(self::TARGET, $issue_id);
			$crit->addInsert(self::UID, $user_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$crit->addInsert(self::VOTE, (($up) ? 1 : -1));
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
	}
