<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Team members table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Team members table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="teammembers")
     */
    class TeamMembers extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'teammembers';
        const ID = 'teammembers.id';
        const SCOPE = 'teammembers.scope';
        const UID = 'teammembers.uid';
        const TID = 'teammembers.tid';
        
        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable());
            parent::addForeignKeyColumn(self::TID, Teams::getTable());
        }

        protected function setupIndexes()
        {
            $this->addIndex('scope_uid', array(self::UID, self::SCOPE));
        }

        public function getUIDsForTeamID($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TID, $team_id);

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
        
        public function clearTeamsByUserID($user_id)
        {
            $team_ids = array();
            
            $query = $this->getQuery();
            $query->where(self::UID, $user_id);
            $query->join(Teams::getTable(), Teams::ID, self::TID);
            $query->where(Teams::ONDEMAND, false);
            
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $team_ids[$row->get(self::TID)] = true;
                }
            }
            
            if (!empty($team_ids))
            {
                $query = $this->getQuery();
                $query->where(self::UID, $user_id);
                $query->where(self::TID, array_keys($team_ids), \b2db\Criterion::IN);
                $res = $this->rawDelete($query);
            }
        }

        public function getNumberOfMembersByTeamID($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TID, $team_id);
            $count = $this->count($query);

            return $count;
        }

        public function cloneTeamMemberships($cloned_team_id, $new_team_id)
        {
            $query = $this->getQuery();
            $query->where(self::TID, $cloned_team_id);
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
                $insertion->add(self::TID, $new_team_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }
        
        public function addUserToTeam($user_id, $team_id)
        {
            $insertion = new Insertion();
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $insertion->add(self::TID, $team_id);
            $insertion->add(self::UID, $user_id);
            $this->rawInsert($insertion);
        }
        
        public function removeUserFromTeam($user_id, $team_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TID, $team_id);
            $query->where(self::UID, $user_id);
            $this->rawDelete($query);
        }
        
        public function removeUsersFromTeam($team_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TID, $team_id);
            $this->rawDelete($query);
        }
        
    }
