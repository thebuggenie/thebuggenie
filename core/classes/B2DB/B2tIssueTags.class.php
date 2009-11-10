<?php

	/**
	 * Issue tags table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue tags table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 */
	class B2tIssueTags extends B2DBTable
	{

		const B2DBNAME = 'issuetags';
		const ID = 'issuetags.id';
		const ISSUE_ID = 'issuetags.issue_id';
		const TAG_NAME = 'issuetags.tag_name';
		const ADDED = 'issuetags.added';
		const SCOPE = 'issuetags.scope';
		
		public function __construct()
		{
			parent::__construct(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, B2DB::getTable('B2tIssues'), B2tIssues::ID);
			parent::_addVarchar(self::TAG_NAME, 50);
			parent::_addInteger(self::ADDED, 10);
			parent::_addForeignKeyColumn(self::SCOPE, B2DB::getTable('B2tScopes'), B2tScopes::ID);
		}
		
		public function getByIssueID($issue_id)
		{
			$crit = $this->getCriteria();
			$crit->addWhere(self::ISSUE_ID, $issue_id);
			$crit->addOrderBy(self::TAG_NAME, B2DBCriteria::SORT_ASC);
			$res = $this->doSelect($crit);
			return $res;
		}
		
	}
