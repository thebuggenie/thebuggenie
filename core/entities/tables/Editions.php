<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Editions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Editions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="editions")
     * @Entity(class="\thebuggenie\core\entities\Edition")
     */
    class Editions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;
        const B2DBNAME = 'editions';
        const ID = 'editions.id';
        const SCOPE = 'editions.scope';
        const NAME = 'editions.name';
        const DESCRIPTION = 'editions.description';
        const PROJECT = 'editions.project';
        const LEAD_BY = 'editions.leader';
        const LEAD_TYPE = 'editions.leader_type';
        const OWNED_BY = 'editions.owner';
        const OWNED_TYPE = 'editions.owner_type';
        const DOC_URL = 'editions.doc_url';
        const QA = 'editions.qa_responsible';
        const QA_TYPE = 'editions.qa_responsible_type';
        const RELEASED = 'editions.isreleased';
        const PLANNED_RELEASED = 'editions.isplannedreleased';
        const RELEASE_DATE = 'editions.release_date';
        const LOCKED = 'editions.locked';
        
        public function getByProjectID($project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::PROJECT, $project_id);
            $res = $this->doSelect($crit);
            return $res;
        }

        public function getProjectIDsByEditionIDs($edition_ids)
        {
            if (count($edition_ids))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::ID, $edition_ids, Criteria::DB_IN);
                $edition_ids = array();
                if ($res = $this->doSelect($crit))
                {
                    while ($row = $res->getNextRow())
                    {
                        $edition_ids[$row->get(self::ID)] = $row->get(self::PROJECT);
                    }
                }
            }
            return $edition_ids;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return array();

            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::ID, $ids, Criteria::DB_IN);
            return $this->select($crit);
        }

    }
