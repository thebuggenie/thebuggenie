<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\Commit;
    use thebuggenie\core\entities\Project;
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

        protected function _setupIndexes()
        {
            $this->_addIndex('project', self::PROJECT_ID);
        }

        /**
         * Get commit for a given commit id
         * @param string $id
         * @param integer $project
         */
        public function getCommitByCommitId($id, $project)
        {
            $crit = new Criteria();

            $crit->addWhere(self::NEW_REV, $id);
            $crit->addWhere(self::PROJECT_ID, $project);

            return $this->selectOne($crit);
        }

        /**
         * Get commit for a given ref hash
         * @param string $ref
         * @param Project $project
         */
        public function getCommitByRef($ref, Project $project)
        {
            $crit = new Criteria();

            $ctn = $crit->returnCriterion('commits.new_rev', $ref);
            $ctn->addOr('commits.new_rev', $ref);
            $crit->addWhere($ctn);
            $crit->addWhere(self::PROJECT_ID, $project->getID());

            $commits = $this->doCount($crit);

            if ($commits == 1) {
                return $this->selectOne($crit);
            }
        }

        /**
         * Whether a commit is already processed
         * @param string $id
         * @param integer $project
         */
        public function isProjectCommitProcessed($id, $project)
        {
            $crit = new Criteria();

            $crit->addWhere(self::NEW_REV, $id);
            $crit->addWhere(self::PROJECT_ID, $project);

            return (bool) $this->count($crit);
        }

    }
