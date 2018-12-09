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

        public function preloadComponents($component_ids)
        {
            if (!count($component_ids))
                return;

            $query = $this->getQuery();
            $query->where(self::ID, $component_ids, \b2db\Criterion::IN);
            $this->select($query);
        }

        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $res = $this->rawSelect($query, false);
            return $res;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, \b2db\Criterion::IN);
            return $this->select($query);
        }

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
