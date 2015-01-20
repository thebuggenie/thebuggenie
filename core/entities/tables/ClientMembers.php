<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;

    /**
     * Client members table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Client members table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="clientmembers")
     */
    class ClientMembers extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'clientmembers';
        const ID = 'clientmembers.id';
        const SCOPE = 'clientmembers.scope';
        const UID = 'clientmembers.uid';
        const CID = 'clientmembers.cid';
        
        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::UID, Users::getTable());
            parent::_addForeignKeyColumn(self::CID, Clients::getTable());
        }

        public function getUIDsForClientID($client_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CID, $client_id);

            $uids = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $uids[$row->get(self::UID)] = $row->get(self::UID);
                }
            }

            return $uids;
        }
        
        public function clearClientsByUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            $res = $this->doDelete($crit);
        }

        public function getNumberOfMembersByClientID($client_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CID, $client_id);
            $count = $this->doCount($crit);

            return $count;
        }

        public function cloneClientMemberships($cloned_client_id, $new_client_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::CID, $cloned_client_id);
            $memberships_to_add = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $memberships_to_add[] = $row->get(self::UID);
                }
            }

            foreach ($memberships_to_add as $uid)
            {
                $crit = $this->getCriteria();
                $crit->addInsert(self::UID, $uid);
                $crit->addInsert(self::CID, $new_client_id);
                $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
                $this->doInsert($crit);
            }
        }

        public function getClientIDsForUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            return $this->doSelect($crit);
        }

        public function addUserToClient($user_id, $client_id)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addInsert(self::CID, $client_id);
            $crit->addInsert(self::UID, $user_id);
            $this->doInsert($crit);
        }
        
        public function removeUserFromClient($user_id, $client_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::CID, $client_id);
            $crit->addWhere(self::UID, $user_id);
            $this->doDelete($crit);
        }
        
        public function removeUsersFromClient($client_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::CID, $client_id);
            $this->doDelete($crit);
        }

    }
