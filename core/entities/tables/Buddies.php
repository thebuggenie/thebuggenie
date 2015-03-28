<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Buddies table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Buddies table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="buddies")
     */
    class Buddies extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'buddies';
        const ID = 'buddies.id';
        const SCOPE = 'buddies.scope';
        const UID = 'buddies.uid';
        const BID = 'buddies.bid';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::_addForeignKeyColumn(self::BID, Users::getTable(), Users::ID);
        }

        public function addFriend($user_id, $friend_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::UID, $user_id);
            $crit->addInsert(self::BID, $friend_id);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $this->doInsert($crit);
        }

        public function getFriendsByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $friends = array();
            if ($res = $this->doSelect($crit, false))
            {
                while ($row = $res->getNextRow())
                {
                    $friends[] = $row->get(self::BID);
                }
            }

            return $friends;
        }

        public function removeFriendByUserID($user_id, $friend_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            $crit->addWhere(self::BID, $friend_id);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $this->doDelete($crit);
        }

    }
