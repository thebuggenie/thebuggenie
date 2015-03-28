<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;

    /**
     * Issue tags table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Issue tags table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="issuetags")
     */
    class IssueTags extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'issuetags';
        const ID = 'issuetags.id';
        const ISSUE_ID = 'issuetags.issue_id';
        const TAG_NAME = 'issuetags.tag_name';
        const ADDED = 'issuetags.added';
        const SCOPE = 'issuetags.scope';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::_addVarchar(self::TAG_NAME, 50);
            parent::_addInteger(self::ADDED, 10);
        }
        
        public function getByIssueID($issue_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ISSUE_ID, $issue_id);
            $crit->addOrderBy(self::TAG_NAME, Criteria::SORT_ASC);
            $res = $this->doSelect($crit);
            return $res;
        }
        
    }
