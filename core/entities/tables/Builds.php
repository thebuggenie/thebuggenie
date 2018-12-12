<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Update;
    use thebuggenie\core\entities\Build;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Builds table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method static Builds getTable() Retrieves an instance of this table
     *
     * @method Build selectById($id)
     *
     * @Table(name="builds")
     * @Entity(class="\thebuggenie\core\entities\Build")
     */
    class Builds extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'builds';
        const ID = 'builds.id';
        const SCOPE = 'builds.scope';
        const NAME = 'builds.name';
        const VERSION_MAJOR = 'builds.version_major';
        const VERSION_MINOR = 'builds.version_minor';
        const VERSION_REVISION = 'builds.version_revision';
        const EDITION = 'builds.edition';
        const RELEASE_DATE = 'builds.release_date';
        const LOCKED = 'builds.locked';
        const PROJECT = 'builds.project';
        const MILESTONE = 'builds.milestone';
        const RELEASED = 'builds.isreleased';
        const FILE_ID = 'builds.file_id';
        const FILE_URL = 'builds.file_url';

        protected function migrateData(\b2db\Table $old_table)
        {
            $sqls = array();
            $tn = $this->_getTableNameSQL();
            switch ($old_table->getVersion())
            {
                case 1:
                    $query = $this->getQuery();
                    $query->where(self::EDITION, 0, \b2db\Criterion::NOT_EQUALS);
                    $res = $this->rawSelect($query);
                    $editions = array();
                    if ($res)
                    {
                        while ($row = $res->getNextRow())
                        {
                            $editions[$row->get(self::EDITION)] = 0;
                        }
                    }

                    $edition_projects = Editions::getTable()->getProjectIDsByEditionIDs(array_keys($editions));

                    foreach ($edition_projects as $edition => $project)
                    {
                        $query = $this->getQuery();
                        $update = new Update();

                        $update->add(self::PROJECT, $project);
                        $query->where(self::EDITION, $edition);

                        $res = $this->rawUpdate($update, $query);
                    }
                    break;
            }
        }

        public function preloadBuilds($build_ids)
        {
            if (!count($build_ids))
                return;

            $query = $this->getQuery();
            $query->where(self::ID, $build_ids, \b2db\Criterion::IN);
            $this->select($query);
        }

        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $query->addOrderBy(self::RELEASE_DATE, \b2db\QueryColumnSort::SORT_DESC);
            return $this->select($query);
        }

        public function getByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, $file_id);
            return $this->select($query);
        }

        public function getByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->addOrderBy(self::RELEASE_DATE, \b2db\QueryColumnSort::SORT_DESC);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query);
            return $row;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, \b2db\Criterion::IN);
            return $this->select($query);
        }

        /**
         * @return Build[]
         */
        public function selectAll()
        {
            $query = $this->getQuery();

            $query->join(Projects::getTable(), Projects::ID, self::PROJECT);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(Projects::NAME, \b2db\QueryColumnSort::SORT_ASC);
            $query->addOrderBy(self::NAME, \b2db\QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

    }
