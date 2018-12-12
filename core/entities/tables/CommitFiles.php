<?php

    namespace thebuggenie\core\entities\tables;

    use \thebuggenie\core\framework;

    /**
     * Commit files table
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

        protected function setupIndexes()
        {
            $this->addIndex('commit', self::COMMIT_ID);
        }

        /**
         * Get all affected files by commit
         * @param integer $id
         */
        public function getByCommitID($id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::COMMIT_ID, $id);

            return $this->select($query);
        }

    }
