<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * User scopes table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * User scopes table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="userscopes")
     */
    class UserScopes extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'userscopes';
        const ID = 'userscopes.id';
        const SCOPE = 'userscopes.scope';
        const USER_ID = 'userscopes.user_id';
        const GROUP_ID = 'userscopes.group_id';
        const CONFIRMED = 'userscopes.confirmed';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addBoolean(self::CONFIRMED);
            parent::_addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
            parent::_addForeignKeyColumn(self::GROUP_ID, Groups::getTable(), Groups::ID);
        }

        public function _setupIndexes()
        {
            $this->_addIndex('uid_scope', array(self::USER_ID, self::SCOPE));
            $this->_addIndex('groupid_scope', array(self::GROUP_ID, self::SCOPE));
        }

        public function countUsers()
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            return $this->doCount($crit);
        }

        public function addUserToScope($user_id, $scope_id, $group_id = null, $confirmed = false)
        {
            $group_id = ($group_id === null) ? \thebuggenie\core\framework\Settings::get(\thebuggenie\core\framework\Settings::SETTING_USER_GROUP, 'core', $scope_id) : $group_id;
            $crit = $this->getCriteria();
            $crit->addInsert(self::USER_ID, $user_id);
            $crit->addInsert(self::SCOPE, $scope_id);
            $crit->addInsert(self::GROUP_ID, $group_id);
            $crit->addInsert(self::CONFIRMED, $confirmed);
            $this->doInsert($crit);
        }

        public function removeUserFromScope($user_id, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $this->doDelete($crit);
        }

        public function confirmUserInScope($user_id, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addUpdate(self::CONFIRMED, true);
            $this->doUpdate($crit);
        }

        public function isUserInCurrentScope($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::USER_ID, $user_id);
            return $this->doCount($crit);
        }

        public function clearUserScopes($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, \thebuggenie\core\framework\Settings::getDefaultScopeID(), Criteria::DB_NOT_EQUALS);
            $crit->addWhere(self::USER_ID, $user_id);
            $this->doDelete($crit);
        }

        public function clearUserGroups($group_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::GROUP_ID, $group_id);
            $crit->addUpdate(self::GROUP_ID, null);
            $this->doUpdate($crit);
        }

        public function updateUserScopeGroup($user_id, $scope_id, $group_id)
        {
            $group_id = ($group_id instanceof \thebuggenie\core\entities\Group) ? $group_id->getID() : (int) $group_id;
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $crit->addUpdate(self::GROUP_ID, $group_id);
            $this->doUpdate($crit);
        }

        public function getUserGroupIdByScope($user_id, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $row = $this->doSelectOne($crit);

            return ($row) ? (int) $row->get(self::GROUP_ID) : null;
        }

        public function getUserConfirmedByScope($user_id, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $row = $this->doSelectOne($crit);

            return ($row) ? (boolean) $row->get(self::CONFIRMED) : false;
        }

        public function getUserDetailsByScope($user_id, $scope_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);
            $crit->addWhere(self::SCOPE, $scope_id);
            $row = $this->doSelectOne($crit);

            return ($row) ? array('confirmed' => (boolean) $row->get(self::CONFIRMED), 'group_id' => $row->get(self::GROUP_ID)) : null;
        }

        public function getScopeDetailsByUser($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::USER_ID, $user_id);

            $scopes = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $scopes[$row->get(self::SCOPE)] = array('confirmed' => (boolean) $row->get(self::CONFIRMED), 'group_id' => $row->get(self::GROUP_ID));
                }
            }

            return $scopes;
        }

        public function getUsersByGroupID($group_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::GROUP_ID, $group_id);

            $users = array();
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    try
                    {
                        $user_id = (int) $row->get(self::USER_ID);
                        $users[$user_id] = new \thebuggenie\core\entities\User($user_id);
                    }
                    catch (\Exception $e) {}
                }
            }

            return $users;
        }

        public function countUsersByGroupID($group_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::GROUP_ID, $group_id);
            $crit->addWhere(self::CONFIRMED, true);
            return $this->doCount($crit);
        }

    }
