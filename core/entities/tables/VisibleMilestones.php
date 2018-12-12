<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Visible milestones table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Visible milestones table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="visible_milestones")
     */
    class VisibleMilestones extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'visible_milestones';
        const ID = 'visible_milestones.id';
        const SCOPE = 'visible_milestones.scope';
        const PROJECT_ID = 'visible_milestones.project_id';
        const MILESTONE_ID = 'visible_milestones.milestone_id';
        
        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::MILESTONE_ID, Milestones::getTable(), Milestones::ID);
            parent::addForeignKeyColumn(self::PROJECT_ID, Projects::getTable(), Projects::ID);
        }
        
        public function getAllByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT_ID, $project_id);
            $query->addOrderBy(Milestones::SCHEDULED, \b2db\QueryColumnSort::SORT_ASC);
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
        
        public function addByProjectIDAndMilestoneID($project_id, $milestone_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::PROJECT_ID, $project_id);
            $insertion->add(self::MILESTONE_ID, $milestone_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);
            return true;
        }
        
    }
