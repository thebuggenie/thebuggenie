<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Team class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Team class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Teams")
     */
    class Team extends IdentifiableScoped
    {

        protected static $_teams = null;

        protected static $_num_teams = null;

        protected $_members = null;

        protected $_num_members = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="boolean")
         */
        protected $_ondemand = false;

        /**
         * List of team's dashboards
         *
         * @var array|\thebuggenie\core\entities\Dashboard
         * @Relates(class="\thebuggenie\core\entities\Dashboard", collection=true, foreign_column="team_id", orderby="name")
         */
        protected $_dashboards = null;

        protected $_associated_projects = null;

        public static function doesTeamNameExist($team_name)
        {
            return tables\Teams::getTable()->doesTeamNameExist($team_name);
        }

        public static function getAll()
        {
            if (self::$_teams === null)
            {
                self::$_teams = \thebuggenie\core\entities\tables\Teams::getTable()->getAll();
            }
            return self::$_teams;
        }

        public static function doesIDExist($id)
        {
            return (bool) static::getB2DBTable()->doesIDExist($id);
        }

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $staff_members = new \thebuggenie\core\entities\Team();
            $staff_members->setName('Staff members');
            $staff_members->save();

            $developers = new \thebuggenie\core\entities\Team();
            $developers->setName('Developers');
            $developers->save();

            $team_leaders = new \thebuggenie\core\entities\Team();
            $team_leaders->setName('Team leaders');
            $team_leaders->save();

            $testers = new \thebuggenie\core\entities\Team();
            $testers->setName('Testers');
            $testers->save();

            $translators = new \thebuggenie\core\entities\Team();
            $translators->setName('Translators');
            $translators->save();
        }

        public static function countAll()
        {
            if (self::$_num_teams === null)
            {
                if (self::$_teams !== null)
                    self::$_num_teams = count(self::$_teams);
                else
                    self::$_num_teams = tables\Teams::getTable()->countTeams();
            }

            return self::$_num_teams;
        }

        public function __toString()
        {
            return "" . $this->_name;
        }

        /**
         * Adds a user to the team
         *
         * @param \thebuggenie\core\entities\User $user
         */
        public function addMember(\thebuggenie\core\entities\User $user)
        {
            if (!$user->getID()) throw new \Exception('Cannot add user object to team until the object is saved');

            tables\TeamMembers::getTable()->addUserToTeam($user->getID(), $this->getID());

            if (is_array($this->_members))
                $this->_members[$user->getID()] = $user->getID();
        }

        public function getMembers()
        {
            if ($this->_members === null)
            {
                $this->_members = array();
                foreach (tables\TeamMembers::getTable()->getUIDsForTeamID($this->getID()) as $uid)
                {
                    $this->_members[$uid] = \thebuggenie\core\entities\User::getB2DBTable()->selectById($uid);
                }
            }
            return $this->_members;
        }

        public function removeMember(\thebuggenie\core\entities\User $user)
        {
            if ($this->_members !== null)
            {
                unset($this->_members[$user->getID()]);
            }
            if ($this->_num_members !== null)
            {
                $this->_num_members--;
            }
            tables\TeamMembers::getTable()->removeUserFromTeam($user->getID(), $this->getID());
        }

        protected function _preDelete()
        {
            tables\TeamMembers::getTable()->removeUsersFromTeam($this->getID());
        }

        public static function findTeams($details)
        {
            $crit = new \b2db\Criteria();
            $crit->addWhere(tables\Teams::NAME, "%$details%", \b2db\Criteria::DB_LIKE);
            $crit->addWhere(tables\Teams::ONDEMAND, false);

            $teams = array();
            if ($res = tables\Teams::getTable()->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $teams[$row->get(tables\Teams::ID)] = new \thebuggenie\core\entities\Team($row->get(tables\Teams::ID), $row);
                }
            }
            return $teams;
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null)
            {
                return count($this->_members);
            }
            elseif ($this->_num_members === null)
            {
                $this->_num_members = tables\TeamMembers::getTable()->getNumberOfMembersByTeamID($this->getID());
            }

            return $this->_num_members;
        }

        /**
         * Get all the projects a team is associated with
         *
         * @return array
         */
        public function getAssociatedProjects()
        {
            if ($this->_associated_projects === null)
            {
                $this->_associated_projects = array();

                $project_ids = tables\ProjectAssignedTeams::getTable()->getProjectsByTeamID($this->getID());
                foreach ($project_ids as $project_id)
                {
                    $this->_associated_projects[$project_id] = \thebuggenie\core\entities\Project::getB2DBTable()->selectById($project_id);
                }
            }

            return $this->_associated_projects;
        }

        public function isOndemand()
        {
            return $this->_ondemand;
        }

        public function setOndemand($val = true)
        {
            $this->_ondemand = $val;
        }

        public function hasAccess()
        {
            return (bool) (framework\Context::getUser()->hasPageAccess('teamlist') || framework\Context::getUser()->isMemberOfTeam($this));
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        /**
         * Returns an array of team dashboards
         *
         * @return array|\thebuggenie\core\entities\Dashboard
         */
        public function getDashboards()
        {
            $this->_b2dbLazyload('_dashboards');
            return $this->_dashboards;
        }

        public function toJSON()
        {
            return array(
                'id' => $this->getID(),
                'name' => $this->getName()
            );
        }

    }
