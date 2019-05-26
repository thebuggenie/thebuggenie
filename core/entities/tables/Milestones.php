<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Update;
    use thebuggenie\core\entities\Milestone;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Milestones table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static Milestones getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\Milestone selectById(integer $id) Retrieves a milestone
     *
     * @Table(name="milestones")
     * @Entity(class="\thebuggenie\core\entities\Milestone")
     */
    class Milestones extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'milestones';
        const ID = 'milestones.id';
        const SCOPE = 'milestones.scope';
        const NAME = 'milestones.name';
        const PROJECT = 'milestones.project';
        const DESCRIPTION = 'milestones.description';
        const MILESTONE_TYPE = 'milestones.itemtype';
        const REACHED = 'milestones.reacheddate';
        const STARTING = 'milestones.startingdate';
        const SCHEDULED = 'milestones.scheduleddate';
        const PERCENTAGE_TYPE = 'milestones.percentage_type';

        protected function migrateData(\b2db\Table $old_table)
        {
            $update = new Update();
            $update->add('milestones.visible_issues', true);
            $update->add('milestones.visible_roadmap', true);
            
            $this->rawUpdate($update);
        }
        
        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);
            return $this->select($query);
        }

        public function doesIDExist($userid)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $userid);
            return $this->count($query);
        }

        public function setReached($milestone_id)
        {
            $update = new Update();
            $update->add(self::REACHED, NOW);
            $this->rawUpdateById($update, $milestone_id);
        }

        public function clearReached($milestone_id)
        {
            $update = new Update();
            $update->add(self::REACHED, null);
            $this->rawUpdateById($update, $milestone_id);
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, \b2db\Criterion::IN);
            return $this->select($query);
        }

        /**
         * @return Milestone[]
         */
        public function selectAll()
        {
            $query = $this->getQuery();

            $query->join(Projects::getTable(), Projects::ID, self::PROJECT);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(Projects::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

    }
