<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
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
        
        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable());
            parent::addForeignKeyColumn(self::CID, Clients::getTable());
        }

        public function getUIDsForClientID($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CID, $client_id);

            $uids = array();
            if ($res = $this->rawSelect($query))
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
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $res = $this->rawDelete($query);
        }

        public function getNumberOfMembersByClientID($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CID, $client_id);
            $count = $this->count($query);

            return $count;
        }

        public function cloneClientMemberships($cloned_client_id, $new_client_id)
        {
            $query = $this->getQuery();
            $query->where(self::CID, $cloned_client_id);
            $memberships_to_add = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $memberships_to_add[] = $row->get(self::UID);
                }
            }

            foreach ($memberships_to_add as $uid)
            {
                $insertion = new Insertion();
                $insertion->add(self::UID, $uid);
                $insertion->add(self::CID, $new_client_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function getClientIDsForUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            return $this->rawSelect($query);
        }

        public function addUserToClient($user_id, $client_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::CID, $client_id);
            $insertion->add(self::UID, $user_id);
            $this->rawInsert($insertion);
        }
        
        public function removeUserFromClient($user_id, $client_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CID, $client_id);
            $query->where(self::UID, $user_id);
            $this->rawDelete($query);
        }
        
        public function removeUsersFromClient($client_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CID, $client_id);
            $this->rawDelete($query);
        }

    }
