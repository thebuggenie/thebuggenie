<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\entities\common\IdentifiableScoped;
    use thebuggenie\core\framework;

    /**
     * Group class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage main
     */

    /**
     * Group class
     *
     * @package thebuggenie
     * @subpackage main
     *
     * @Table(name="\thebuggenie\core\entities\tables\Groups")
     */
    class Group extends IdentifiableScoped
    {

        protected static $_groups = null;

        protected $_members = null;

        protected $_num_members = null;

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        public static function doesGroupNameExist($group_name)
        {
            return tables\Groups::getTable()->doesGroupNameExist($group_name);
        }

        public static function getAll()
        {
            if (self::$_groups === null)
            {
                self::$_groups = tables\Groups::getTable()->getAll();
            }
            return self::$_groups;
        }

        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                if (self::$_groups !== null)
                {
                    self::$_groups[$this->getID()] = $this;
                }
            }
        }

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $scope_id = $scope->getID();

            $admin_group = new \thebuggenie\core\entities\Group();
            $admin_group->setName('Administrators');
            $admin_group->setScope($scope);
            $admin_group->save();
            \thebuggenie\core\framework\Settings::saveSetting('admingroup', $admin_group->getID(), 'core', $scope_id);

            $user_group = new \thebuggenie\core\entities\Group();
            $user_group->setName('Regular users');
            $user_group->setScope($scope);
            $user_group->save();
            \thebuggenie\core\framework\Settings::saveSetting('defaultgroup', $user_group->getID(), 'core', $scope_id);

            $guest_group = new \thebuggenie\core\entities\Group();
            $guest_group->setName('Guests');
            $guest_group->setScope($scope);
            $guest_group->save();

            // Set up initial users, and their permissions
            if ($scope->isDefault())
            {
                list($guestuser_id, $adminuser_id) = \thebuggenie\core\entities\User::loadFixtures($scope, $admin_group, $user_group, $guest_group);
                tables\UserScopes::getTable()->addUserToScope($guestuser_id, $scope->getID(), $guest_group->getID(), true);
                tables\UserScopes::getTable()->addUserToScope($adminuser_id, $scope->getID(), $admin_group->getID(), true);
            }
            else
            {
                $default_scope_id = \thebuggenie\core\framework\Settings::getDefaultScopeID();
                $default_user_id = (int) \thebuggenie\core\framework\Settings::get(\thebuggenie\core\framework\Settings::SETTING_DEFAULT_USER_ID, 'core', $default_scope_id);
                tables\UserScopes::getTable()->addUserToScope($default_user_id, $scope->getID(), $user_group->getID(), true);
                tables\UserScopes::getTable()->addUserToScope(1, $scope->getID(), $admin_group->getID());
                \thebuggenie\core\framework\Settings::saveSetting(\thebuggenie\core\framework\Settings::SETTING_DEFAULT_USER_ID, $default_user_id, 'core', $scope->getID());
            }
            tables\Permissions::getTable()->loadFixtures($scope, $admin_group->getID(), $guest_group->getID());
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

        public function isDefaultUserGroup()
        {
            return (bool) (\thebuggenie\core\framework\Settings::getDefaultUser()->getGroupID() == $this->getID());
        }

        protected function _preDelete()
        {
            tables\UserScopes::getTable()->clearUserGroups($this->getID());
        }

        /**
         * Return an array of all members in this group
         *
         * @return array
         */
        public function getMembers()
        {
            if ($this->_members === null)
            {
                $this->_members = tables\UserScopes::getTable()->getUsersByGroupID($this->getID());
            }
            return $this->_members;
        }

        public function getNumberOfMembers()
        {
            if ($this->_members !== null)
            {
                return count($this->_members);
            }
            elseif ($this->_num_members === null)
            {
                $this->_num_members = tables\UserScopes::getTable()->countUsersByGroupID($this->getID());
            }

            return $this->_num_members;
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
        }

    }
