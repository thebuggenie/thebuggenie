<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::BID, Users::getTable(), Users::ID);
        }

        public function addFriend($user_id, $friend_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::UID, $user_id);
            $insertion->add(self::BID, $friend_id);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawInsert($insertion);
        }

        public function getFriendsByUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $friends = array();
            if ($res = $this->rawSelect($query, false))
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
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $query->where(self::BID, $friend_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

    }
