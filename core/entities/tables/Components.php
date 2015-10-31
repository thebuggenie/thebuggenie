<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Components table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Components table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="components")
     * @Entity(class="\thebuggenie\core\entities\Component")
     */
    class Components extends ScopedTable 
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'components';
        const ID = 'components.id';
        const SCOPE = 'components.scope';
        const NAME = 'components.name';
        const VERSION_MAJOR = 'components.version_major';
        const VERSION_MINOR = 'components.version_minor';
        const VERSION_REVISION = 'components.version_revision';
        const PROJECT = 'components.project';
        const LEAD_BY = 'components.leader';
        const LEAD_TYPE = 'components.leader_type';
        
        public function getByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT, $project_id);
            $res = $this->doSelect($crit, false);
            return $res;
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
