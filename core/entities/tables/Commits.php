<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Query;
    use thebuggenie\core\entities\Commit;
    use thebuggenie\core\entities\Project;
    use thebuggenie\core\entities\tables\ScopedTable;
    use b2db\Criteria;

    /**
     * Commits table
     *
     * @method static Commits getTable()
     * @method Commit selectById($id)
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Entity(class="\thebuggenie\core\entities\Commit")
     * @Table(name="commits")
     */
    class Commits extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'commits';
        const ID = 'commits.id';
        const SCOPE = 'commits.scope';
        const LOG = 'commits.log';
        const OLD_REV = 'commits.old_rev';
        const NEW_REV = 'commits.new_rev';
        const AUTHOR = 'commits.author';
        const DATE = 'commits.date';
        const DATA = 'commits.data';
        const PROJECT_ID = 'commits.project_id';

        protected function setupIndexes()
        {
            $this->addIndex('project', self::PROJECT_ID);
            $this->addIndex('project_commit', [self::PROJECT_ID, self::NEW_REV]);
        }

        /**
         * Get commit for a given commit id
         * @param string $hash
         * @param Project $project
         *
         * @return Commit
         */
        public function getCommitByHash($hash, Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::NEW_REV, $hash);
            $query->where(self::PROJECT_ID, $project->getID());

            return $this->selectOne($query);
        }

        /**
         * Get unlinked commits
         *
         * @param Project $project
         *
         * @return Commit[]
         */
        public function getUnprocessedCommitsByProject(Project $project)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $project->getID());
            $query->where(self::OLD_REV, '', \b2db\Criterion::NOT_EQUALS);
            $query->where('commits.previous_commit_id', 0);

            return $this->select($query);
        }

        /**
         * Whether a commit is already processed
         * @param string $id
         * @param integer $project
         */
        public function isProjectCommitProcessed($id, $project)
        {
            $query = $this->getQuery();

            $query->where(self::NEW_REV, $id);
            $query->where(self::PROJECT_ID, $project);

            return (bool) $this->count($query);
        }

    }
