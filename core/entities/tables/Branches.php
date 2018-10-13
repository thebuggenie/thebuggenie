<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;
    use thebuggenie\core\entities\Branch;
    use thebuggenie\core\entities\Project;

    /**
     * Branches table
     *
     * @method static Branches getTable()
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Entity(class="\thebuggenie\core\entities\Branch")
     * @Table(name="branches")
     */
    class Branches extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'branches';
        const ID = 'branches.id';
        const SCOPE = 'branches.scope';
        const PROJECT_ID = 'branches.project_id';

        protected function _setupIndexes()
        {
            $this->_addIndex('project', self::PROJECT_ID);
        }

        /**
         * Get all branches inside a project
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByProject(Project $project)
        {
            $crit = new Criteria();

            $crit->addWhere(self::PROJECT_ID, $project->getID());
            $crit->addOrderBy('branches.name', Criteria::SORT_ASC);

            return $this->select($crit);
        }

        public function getByBranchNameAndProject($name, Project $project)
        {
            $crit = new Criteria();

            $crit->addWhere(self::PROJECT_ID, $project->getID());
            $crit->addWhere('branches.name', $name);

            $branch = $this->selectOne($crit);

            return $branch;
        }

        public function getOrCreateByBranchNameAndProject($name, Project $project)
        {
            $branch = $this->getByBranchNameAndProject($name, $project);

            if (!$branch instanceof Branch) {
                $branch = new Branch();
                $branch->setName($name);
                $branch->setProject($project);
                $branch->save();
            }

            return $branch;
        }

    }
