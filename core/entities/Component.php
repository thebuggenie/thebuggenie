<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\QaLeadable;
    use thebuggenie\core\framework;

    /**
     * Class used for components
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Class used for components
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Components")
     */
    class Component extends QaLeadable
    {
        
        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * This components project
         *
         * @var unknown_type
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Project")
         */
        protected $_project = null;
        
        /**
         * @Relates(class="\thebuggenie\core\entities\User", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\ComponentAssignedUsers")
         */
        protected $_assigned_users;

        /**
         * @Relates(class="\thebuggenie\core\entities\Team", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\ComponentAssignedTeams")
         */
        protected $_assigned_teams;
        
        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                framework\Context::setPermission("canseecomponent", $this->getID(), "core", 0, framework\Context::getUser()->getGroup()->getID(), 0, true);
                \thebuggenie\core\framework\Event::createNew('core', 'Component::createNew', $this)->trigger();
            }
        }
        
        /**
         * Returns the parent project
         *
         * @return \thebuggenie\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyload('_project');
        }
        
        public function setProject($project)
        {
            $this->_project = $project;
        }
        
        protected function _preDelete()
        {
            tables\IssueAffectsComponent::getTable()->deleteByComponentID($this->getID());
            tables\EditionComponents::getTable()->deleteByComponentID($this->getID());
            tables\ComponentAssignedUsers::getTable()->deleteByComponentID($this->getID());
            tables\ComponentAssignedTeams::getTable()->deleteByComponentID($this->getID());
        }
        
        /**
         * Add an assignee to the component
         *
         * @param \thebuggenie\core\entities\common\Identifiable $assignee
         * @param integer $role
         *
         * @return boolean
         */
        public function addAssignee(\thebuggenie\core\entities\common\Identifiable $assignee, $role)
        {
            if ($assignee instanceof \thebuggenie\core\entities\User)
                $retval = tables\ComponentAssignedUsers::getTable()->addUserToComponent($this->getID(), $assignee, $role);
            elseif ($assignee instanceof \thebuggenie\core\entities\Team)
                $retval = tables\ComponentAssignedTeams::getTable()->addTeamToComponent($this->getID(), $assignee, $role);

            return $retval;
        }

        /**
         * Add an assignee to the component
         *
         * @param \thebuggenie\core\entities\common\Identifiable $assignee
         * @param integer $role
         *
         * @return boolean
         */
        public function removeAssignee(\thebuggenie\core\entities\common\Identifiable $assignee)
        {
            if ($assignee instanceof \thebuggenie\core\entities\User)
                $retval = tables\ComponentAssignedUsers::getTable()->removeUserFromComponent($this->getID(), $assignee, $role);
            elseif ($assignee instanceof \thebuggenie\core\entities\Team)
                $retval = tables\ComponentAssignedTeams::getTable()->removeTeamFromComponent($this->getID(), $assignee, $role);

            return $retval;
        }

        protected function _populateAssignees()
        {
            if ($this->_assigned_users === null)
                $this->_b2dbLazyload('_assigned_users');

            if ($this->_assigned_teams === null)
                $this->_b2dbLazyload('_assigned_teams');
        }

        public function getAssignedUsers()
        {
            $this->_populateAssignees();
            return $this->_assigned_users;
        }

        public function getAssignedTeams()
        {
            $this->_populateAssignees();
            return $this->_assigned_teams;
        }

        /**
         * Whether or not the current user can access the component
         * 
         * @return boolean
         */
        public function hasAccess()
        {
            return ($this->getProject()->canSeeAllComponents() || framework\Context::getUser()->hasPermission('canseecomponent', $this->getID()));
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

    }
