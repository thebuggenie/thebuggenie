<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Userstate table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Userstate table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="userstate")
     * @Entity(class="\thebuggenie\core\entities\Userstate")
     */
    class UserStates extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'userstate';
        const ID = 'userstate.id';
        const SCOPE = 'userstate.scope';
        const NAME = 'userstate.name';
        const UNAVAILABLE = 'userstate.is_unavailable';
        const BUSY = 'userstate.is_busy';
        const ONLINE = 'userstate.is_online';
        const MEETING = 'userstate.is_in_meeting';
        const COLOR = 'userstate.itemdata';
        const ABSENT = 'userstate.is_absent';

        public function getAll()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($crit);
        }
        
    }
