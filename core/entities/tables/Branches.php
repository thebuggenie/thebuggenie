<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Criteria;
    use thebuggenie\core\entities\Branch;
    use thebuggenie\core\entities\Commit;
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
            $crit->addWhere('branches.is_deleted', false);

            return $this->select($crit);
        }

        /**
         * Get all branches inside a project
         * @param Commit[] $commits
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByCommitsAndProject($commits, Project $project)
        {
            $crit = new Criteria();
            $commit_ids = array_reduce($commits, function ($ids, $commit) {
                $ids[] = $commit->getID();
            }, []);

            $crit->addWhere(self::PROJECT_ID, $project->getID());
            $crit->addWhere('branches.latest_commit_id', $commit_ids, Criteria::DB_IN);
            $crit->addWhere('branches.is_deleted', false);

            $branches = [];
            foreach ($this->select($crit) as $branch) {
                if (!isset($branches[$branch->getLatestCommitId()])) {
                    $branches[$branch->getLatestCommitId()] = [];
                }
                $branches[$branch->getLatestCommitId()][] = $branch;
            }

            return $branches;
        }

        public function getByBranchNameAndProject($name, Project $project)
        {
            $crit = new Criteria();

            $crit->addWhere(self::PROJECT_ID, $project->getID());
            $crit->addWhere('branches.name', $name);
            $crit->addWhere('branches.is_deleted', false);

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
