<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Visible issue types table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Visible issue types table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="visible_issue_types")
     */
    class VisibleIssueTypes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'visible_issue_types';
        const ID = 'visible_issue_types.id';
        const SCOPE = 'visible_issue_types.scope';
        const PROJECT_ID = 'visible_issue_types.project_id';
        const ISSUETYPE_ID = 'visible_issue_types.issuetype_id';
        
        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::ISSUETYPE_ID, IssueTypes::getTable(), IssueTypes::ID);
            parent::addForeignKeyColumn(self::PROJECT_ID, Projects::getTable(), Projects::ID);
        }
        
        public function getAllByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->addOrderBy(IssueTypes::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $res = $this->rawSelect($query);
            return $res;
        }
        
        public function clearByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $this->rawDelete($query);
            return true;
        }
        
        public function addByProjectIDAndIssuetypeID($project_id, $issuetype_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::PROJECT_ID, $project_id);
            $insertion->add(self::ISSUETYPE_ID, $issuetype_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);
            return true;
        }
        
        public function deleteByIssuetypeID($issuetype_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUETYPE_ID, $issuetype_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
            return true;
        }
        
    }
