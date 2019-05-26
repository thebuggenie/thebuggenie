<?php

    namespace thebuggenie\modules\vcs_integration\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;
    use b2db\Criteria;

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationCommitsTable
     *
     * @method static Commits getTable()
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Entity(class="\thebuggenie\modules\vcs_integration\entities\Commit")
     * @Table(name="vcsintegration_commits")
     */
    class Commits extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'vcsintegration_commits';
        const ID = 'vcsintegration_commits.id';
        const SCOPE = 'vcsintegration_commits.scope';
        const LOG = 'vcsintegration_commits.log';
        const OLD_REV = 'vcsintegration_commits.old_rev';
        const NEW_REV = 'vcsintegration_commits.new_rev';
        const AUTHOR = 'vcsintegration_commits.author';
        const DATE = 'vcsintegration_commits.date';
        const DATA = 'vcsintegration_commits.data';
        const PROJECT_ID = 'vcsintegration_commits.project_id';

        protected function _setupIndexes()
        {
            $this->_addIndex('project', self::PROJECT_ID);
        }

        /**
         * Get all commits relating to issues inside a project
         * @param integer $id
         * @param integer $limit
         * @param integer $offset
         */
        public function getCommitsByProject($id, $limit = 40, $offset = null, $branch = null, $gitlab_repos_nss = null)
        {
            $query = $this->getQuery();

            $query->where(self::PROJECT_ID, $id);
            $query->addOrderBy(self::DATE, \b2db\QueryColumnSort::SORT_DESC);

            if ($branch !== null)
            {
                if ($gitlab_repos_nss !== null)
                {
                    $query->where(self::DATA, 'branch:'.$branch.'|gitlab_repos_ns:'.$gitlab_repos_nss);
                }
                else
                {
                    $query->where(self::DATA, 'branch:'.$branch.'%', \b2db\Criterion::LIKE);
                }
            }
            else
            {
                if ($gitlab_repos_nss !== null)
                {
                    $query->where(self::DATA, '%|gitlab_repos_ns:'.$gitlab_repos_nss, \b2db\Criterion::LIKE);
                }
            }

            if ($limit !== null)
            {
                $query->setLimit($limit);
            }

            if ($offset !== null)
            {
                $query->setOffset($offset);
            }

            return $this->select($query);
        }

        /**
         * Get commit for a given commit id
         * @param string $id
         * @param integer $project
         */
        public function getCommitByCommitId($id, $project)
        {
            $query = $this->getQuery();

            $query->where(self::NEW_REV, $id);
            $query->where(self::PROJECT_ID, $project);

            return $this->selectOne($query);
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
