<?php

    namespace thebuggenie\core\entities\b2db;

    /**
     * Agile boards table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Agile boards table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method \thebuggenie\core\entities\b2db\AgileBoards getTable() Retrieves an instance of this table
     * @method \thebuggenie\core\entities\AgileBoard selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboards")
     * @Entity(class="\thebuggenie\core\entities\AgileBoard")
     */
    class AgileBoards extends \TBGB2DBTable
    {

        const B2DB_TABLE_VERSION = 1;

        public function getAvailableProjectBoards($user_id, $project_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere('agileboards.project_id', $project_id);
            $ctn = $crit->returnCriterion('agileboards.user_id', $user_id);
            $ctn->addOr('agileboards.is_private', false);
            $crit->addWhere($ctn);

            return $this->select($crit);
        }

    }
