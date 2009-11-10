<?php

	/**
	 * User issues table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * User issues table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tUserIssues extends B2DBTable 
	{

		const B2DBNAME = 'userissues';
		const ID = 'userissues.id';
		const SCOPE = 'userissues.scope';
		const ISSUE = 'userissues.issue';
		const UID = 'userissues.uid';

		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE, B2DB::getTable('B2tIssues'), B2tIssues::ID);
			parent::_addForeignKeyColumn(self::UID, B2DB::getTable('B2tUsers'), B2tUsers::ID);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}

		public function getUserIDsByIssueID($issue_id)
		{
			$uids = array();
			$crit = $this->getCriteria();
			
			$crit->addWhere(self::ISSUE, $issue_id);
			
			if ($res = $this->doSelect($crit))
			{
				while ($row = $res->getNextRow())
				{
					$uids[] = $row->get(B2tUserIssues::UID);
				}
			}
			
			return $uids;
		}
		
		public function getUserStarredIssues($user_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::UID, $user_id);
			$crit->addWhere(B2tIssues::DELETED, 0);
			
			$res = $this->doSelect($crit);
			return $res;
		}
		
	}
