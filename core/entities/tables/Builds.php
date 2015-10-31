<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Builds table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Builds table
     *
     * @package thebuggenie
     * @subpackage tables
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

        public function _migrateData(\b2db\Table $old_table)
        {
            $sqls = array();
            $tn = $this->_getTableNameSQL();
            switch ($old_table->getVersion())
            {
                case 1:
                    $crit = $this->getCriteria();
                    $crit->addWhere(self::EDITION, 0, Criteria::DB_NOT_EQUALS);
                    $res = $this->doSelect($crit);
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
                        $crit = $this->getCriteria();
                        $crit->addUpdate(self::PROJECT, $project);
                        $crit->addWhere(self::EDITION, $edition);
                        $res = $this->doUpdate($crit);
                    }
                    break;
            }
        }

        public function getByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT, $project_id);
            $crit->addOrderBy(self::RELEASE_DATE, Criteria::SORT_DESC);
            return $this->select($crit);
        }

        public function getByFileID($file_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::FILE_ID, $file_id);
            return $this->select($crit);
        }

        public function getByEditionID($edition_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EDITION, $edition_id);
            $crit->addOrderBy(self::RELEASE_DATE, Criteria::SORT_DESC);
            $res = $this->doSelect($crit);

            return $res;
        }

        public function getByID($id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->doSelectById($id, $crit);
            return $row;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ID, $ids, Criteria::DB_IN);
            return $this->select($crit);
        }

        public function selectAll()
        {
            $crit = $this->getCriteria();

            $crit->addJoin(Projects::getTable(), Projects::ID, self::PROJECT);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addOrderBy(Projects::NAME, Criteria::SORT_ASC);
            $crit->addOrderBy(self::NAME, Criteria::SORT_ASC);

            return $this->select($crit);
        }

    }
