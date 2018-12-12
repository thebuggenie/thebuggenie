<?php

    namespace thebuggenie\core\modules\installation\upgrade_415;

    use thebuggenie\core\entities\tables\ScopedTable;
    use thebuggenie\core\entities\tables\Issues;
    use thebuggenie\core\entities\tables\Users;

    /**
     * Issue estimates table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name='issue_estimates')
     */
    class IssueEstimatesTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issue_estimates';
        const ID = 'issue_estimates.id';
        const SCOPE = 'issue_estimates.scope';
        const ISSUE_ID = 'issue_estimates.issue_id';
        const EDITED_BY = 'issue_estimates.edited_by';
        const EDITED_AT = 'issue_estimates.edited_at';
        const ESTIMATED_MONTHS = 'issue_estimates.estimated_months';
        const ESTIMATED_WEEKS = 'issue_estimates.estimated_weeks';
        const ESTIMATED_DAYS = 'issue_estimates.estimated_days';
        const ESTIMATED_HOURS = 'issue_estimates.estimated_hours';
        const ESTIMATED_POINTS = 'issue_estimates.estimated_points';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::EDITED_BY, Users::getTable(), Users::ID);
            parent::addInteger(self::EDITED_AT, 10);
            parent::addInteger(self::ESTIMATED_MONTHS, 10);
            parent::addInteger(self::ESTIMATED_WEEKS, 10);
            parent::addInteger(self::ESTIMATED_DAYS, 10);
            parent::addInteger(self::ESTIMATED_HOURS, 10);
            parent::addFloat(self::ESTIMATED_POINTS);
        }

    }
