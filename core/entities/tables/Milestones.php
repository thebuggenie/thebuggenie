<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Milestones table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Milestones table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Milestones getTable() Retrieves an instance of this table
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

        public function _migrateData(\b2db\Table $old_table)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate('milestones.visible_issues', true);
            $crit->addUpdate('milestones.visible_roadmap', true);
            
            $this->doUpdate($crit);
        }
        
        public function getByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT, $project_id);
            $crit->addOrderBy(self::NAME, Criteria::SORT_ASC);
            return $this->select($crit);
        }

        public function doesIDExist($userid)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, $userid);
            return $this->doCount($crit);
        }

        public function setReached($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::REACHED, NOW);
            $this->doUpdateById($crit, $milestone_id);
        }

        public function clearReached($milestone_id)
        {
            $crit = $this->getCriteria();
            $crit->addUpdate(self::REACHED, null);
            $this->doUpdateById($crit, $milestone_id);
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ID, $ids, Criteria::DB_IN);
            return $this->select($crit);
        }

        public function selectAll()
        {
            $crit = $this->getCriteria();

            $crit->addJoin(Projects::getTable(), Projects::ID, self::PROJECT);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy(Projects::NAME, Criteria::SORT_ASC);
            $crit->addOrderBy(self::NAME, Criteria::SORT_ASC);

            return $this->select($crit);
        }

    }
