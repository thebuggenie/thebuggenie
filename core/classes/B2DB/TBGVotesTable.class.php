<?php

	/**
	 * Votes table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Votes table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class TBGVotesTable extends B2DBTable 
	{

		const B2DBNAME = 'votes';
		const ID = 'votes.id';
		const SCOPE = 'votes.scope';
		const TARGET = 'votes.target';
		const UID = 'votes.uid';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addInteger(self::TARGET, 10);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('TBGUsersTable'), TBGUsersTable::ID);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}
		
		public function getNumberOfVotesForIssue($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			return $this->doCount($crit);			
		}
		
		public function getByUserIdAndIssueId($user_id, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::TARGET, $issue_id);
			$crit->addWhere(self::UID, $user_id);
			$res = $this->doSelectOne($crit);
			return $res;
		}
		
		public function addByUserIdAndIssueId($user_id, $issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addInsert(self::TARGET, $issue_id);
			$crit->addInsert(self::UID, $user_id);
			$crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());
			$res = $this->doInsert($crit);
			return $res->getInsertID();
		}
		
	}
