<?php

	use b2db\Core,
		b2db\Criteria,
		b2db\Criterion;

	/**
	 * Issue spent times table
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage tables
	 */

	/**
	 * Issue spent times table
	 *
	 * @package thebuggenie
	 * @subpackage tables
	 *
	 * @Table(name="issue_spenttimes_32")
	 */
	class TBGIssueSpentTimesTable3dot2 extends TBGB2DBTable
	{

		const B2DB_TABLE_VERSION = 1;
		const B2DBNAME = 'issue_spenttimes';
		const ID = 'issue_spenttimes.id';
		const SCOPE = 'issue_spenttimes.scope';
		const ISSUE_ID = 'issue_spenttimes.issue_id';
		const EDITED_BY = 'issue_spenttimes.edited_by';
		const EDITED_AT = 'issue_spenttimes.edited_at';
		const SPENT_MONTHS = 'issue_spenttimes.spent_months';
		const SPENT_WEEKS = 'issue_spenttimes.spent_weeks';
		const SPENT_DAYS = 'issue_spenttimes.spent_days';
		const SPENT_HOURS = 'issue_spenttimes.spent_hours';
		const SPENT_POINTS = 'issue_spenttimes.spent_points';

		protected function _initialize()
		{
			parent::_setup(self::B2DBNAME, self::ID);
			parent::_addForeignKeyColumn(self::ISSUE_ID, TBGIssuesTable::getTable(), TBGIssuesTable::ID);
			parent::_addForeignKeyColumn(self::EDITED_BY, TBGUsersTable::getTable(), TBGUsersTable::ID);
			parent::_addInteger(self::EDITED_AT, 10);
			parent::_addInteger(self::SPENT_MONTHS, 10);
			parent::_addInteger(self::SPENT_WEEKS, 10);
			parent::_addInteger(self::SPENT_DAYS, 10);
			parent::_addInteger(self::SPENT_HOURS, 10);
			parent::_addFloat(self::SPENT_POINTS);
			parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
		}

	}
