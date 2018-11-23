<?php

    namespace thebuggenie\core\entities\tables;

    use \thebuggenie\core\framework;

    /**
     * B2DB Table, vcs_integration -> VCSIntegrationFilesTable
     *
     * @method static CommitFiles getTable()
     *
     * @Entity(class="\thebuggenie\core\entities\CommitFile")
     * @Table(name="commitfiles")
     */
    class CommitFiles extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'commitfiles';
        const ID = 'commitfiles.id';
        const SCOPE = 'commitfiles.scope';
        const COMMIT_ID = 'commitfiles.commit_id';
        const FILE_NAME = 'commitfiles.file_name';
        const ACTION = 'commitfiles.action';

        protected function _setupIndexes()
        {
            $this->_addIndex('commit', self::COMMIT_ID);
        }

        /**
         * Get all affected files by commit
         * @param integer $id
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, $scope);
            $crit->addWhere(self::COMMIT_ID, $id);

            return $this->select($crit);
        }

    }
