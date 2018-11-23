<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\Branch;
    use thebuggenie\core\entities\Commit;
    use thebuggenie\core\entities\IssueCommit;
    use \thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Branch commits table
     *
     * @method static BranchCommits getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\BranchCommit")
     * @Table(name="branchcommits")
     */
    class BranchCommits extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'branchcommits';
        const ID = 'branchcommits.id';
        const SCOPE = 'branchcommits.scope';
        const BRANCH_ID = 'branchcommits.branch_id';
        const COMMIT_ID = 'branchcommits.commit_id';
        const COMMIT_SHA = 'branchcommits.commit_sha';

        protected function _setupIndexes()
        {
            $this->_addIndex('commit', self::COMMIT_ID);
            $this->_addIndex('branch', self::BRANCH_ID);
        }

        public function hasBranchCommitSha(Branch $branch, $commit_sha)
        {
            $crit = $this->getCriteria();
            $crit->addWhere('branchcommits.branch_id', $branch->getID());
            $crit->addWhere('branchcommits.commit_sha', $commit_sha);

            return (bool) $this->doCount($crit);
        }

        public function hasCommitInDifferentBranch(Commit $commit, Branch $branch)
        {
            $crit = $this->getCriteria();
            $crit->addWhere('branchcommits.branch_id', $branch->getID(), Criteria::DB_NOT_EQUALS);
            $crit->addWhere('branchcommits.commit_id', $commit->getID());

            return (bool) $this->doCount($crit);
        }

    }
