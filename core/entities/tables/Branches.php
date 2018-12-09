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

        protected function setupIndexes()
        {
            $this->addIndex('project', self::PROJECT_ID);
        }

        /**
         * Get all branches inside a project
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByProject(Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.is_deleted', false);
            $query->addOrderBy('branches.name', \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        /**
         * Get all branches inside a project
         * @param int[] $commit_ids
         * @param Project $project
         *
         * @return Branch[]
         */
        public function getByCommitsAndProject($commit_ids, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.latest_commit_id', $commit_ids, \b2db\Criterion::IN);
            $query->where('branches.is_deleted', false);

            $branches = [];
            foreach ($this->select($query) as $branch) {
                if (!isset($branches[$branch->getLatestCommitId()])) {
                    $branches[$branch->getLatestCommitId()] = [];
                }
                $branches[$branch->getLatestCommitId()][] = $branch;
            }

            return $branches;
        }

        public function getByBranchNameAndProject($name, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where('branches.name', $name);
            $query->where('branches.is_deleted', false);

            $branch = $this->selectOne($query);

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
