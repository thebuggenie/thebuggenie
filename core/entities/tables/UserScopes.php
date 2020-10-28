<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Insertion;
    use b2db\Update;
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

        protected $_scope_confirmed_cache = [];

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'userscopes';
        const ID = 'userscopes.id';
        const SCOPE = 'userscopes.scope';
        const USER_ID = 'userscopes.user_id';
        const GROUP_ID = 'userscopes.group_id';
        const CONFIRMED = 'userscopes.confirmed';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addBoolean(self::CONFIRMED);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::GROUP_ID, Groups::getTable(), Groups::ID);
        }

        protected function setupIndexes()
        {
            $this->addIndex('uid_scope', array(self::USER_ID, self::SCOPE));
            $this->addIndex('groupid_scope', array(self::GROUP_ID, self::SCOPE));
        }

        public function countUsers()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function addUserToScope($user_id, $scope_id, $group_id = null, $confirmed = false)
        {
            $group_id = ($group_id === null) ? \thebuggenie\core\framework\Settings::get(\thebuggenie\core\framework\Settings::SETTING_USER_GROUP, 'core', $scope_id) : $group_id;
            $insertion = new Insertion();
            $insertion->add(self::USER_ID, $user_id);
            $insertion->add(self::SCOPE, $scope_id);
            $insertion->add(self::GROUP_ID, $group_id);
            $insertion->add(self::CONFIRMED, $confirmed);
            $this->rawInsert($insertion);
        }

        public function removeUserFromScope($user_id, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);
            $this->rawDelete($query);
        }

        public function confirmUserInScope($user_id, $scope_id)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::CONFIRMED, true);

            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);

            $this->rawUpdate($update, $query);
        }

        public function isUserInCurrentScope($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::USER_ID, $user_id);
            return $this->count($query);
        }

        public function clearUserScopes($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, \thebuggenie\core\framework\Settings::getDefaultScopeID(), \b2db\Criterion::NOT_EQUALS);
            $query->where(self::USER_ID, $user_id);
            $this->rawDelete($query);
        }

        public function clearUserGroups($group_id)
        {
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::GROUP_ID, null);

            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::GROUP_ID, $group_id);

            $this->rawUpdate($update, $query);
        }

        public function updateUserScopeGroup($user_id, $scope_id, $group_id)
        {
            $group_id = ($group_id instanceof \thebuggenie\core\entities\Group) ? $group_id->getID() : (int) $group_id;
            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::GROUP_ID, $group_id);

            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);

            $this->rawUpdate($update, $query);
        }

        public function getUserGroupIdByScope($user_id, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);
            $row = $this->rawSelectOne($query);

            return ($row) ? (int) $row->get(self::GROUP_ID) : null;
        }

        public function getUserConfirmedByScope($user_id, $scope_id)
        {
            if (!array_key_exists($scope_id, $this->_scope_confirmed_cache)) {
                $this->_scope_confirmed_cache[$scope_id] = [];
            }

            if (array_key_exists($user_id, $this->_scope_confirmed_cache[$scope_id])) {
                return $this->_scope_confirmed_cache[$scope_id][$user_id];
            }

            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);
            $row = $this->rawSelectOne($query);

            $value = ($row) ? (boolean) $row->get(self::CONFIRMED) : false;
            $this->_scope_confirmed_cache[$scope_id][$user_id] = $value;

            return $value;
        }

        public function getUserDetailsByScope($user_id, $scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, $scope_id);
            $row = $this->rawSelectOne($query);

            return ($row) ? array('confirmed' => (boolean) $row->get(self::CONFIRMED), 'group_id' => $row->get(self::GROUP_ID)) : null;
        }

        public function getScopeDetailsByUser($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);

            $scope_details = array();

            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    $scope_details[$row->get(self::SCOPE)] = array('confirmed' => (boolean) $row->get(self::CONFIRMED), 'group_id' => $row->get(self::GROUP_ID), 'internal_id' => $row->get(self::ID));
                }
            }
            if (count($scope_details)) {
                $scopes = Scopes::getTable()->getByIds(array_keys($scope_details));
                foreach ($scope_details as $id => $detail)
                {
                    if (array_key_exists($id, $scopes))
                    {
                        $scope_details[$id]['scope'] = $scopes[$id];
                    }
                    else
                    {
                        $this->rawDeleteById($detail['internal_id']);
                        unset($scope_details[$id]);
                    }
                }
            }

            return $scope_details;
        }

        public function getUsersByGroupID($group_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::GROUP_ID, $group_id);

            $users = array();
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    try
                    {
                        if (!Users::getTable()->isUserDeleted($row->get(self::USER_ID))) {
                            $user_id = (int) $row->get(self::USER_ID);
                            $users[$user_id] = new \thebuggenie\core\entities\User($user_id);
                        }
                    }
                    catch (\Exception $e) {}
                }
            }

            return $users;
        }

        public function countUsersByGroupID($group_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::GROUP_ID, $group_id);
            $query->where(self::CONFIRMED, true);
            $query->addSelectionColumn(self::USER_ID);
            $query->setIsDistinct();

            $count = 0;
            if ($res = $this->rawSelect($query))
            {
                while ($row = $res->getNextRow())
                {
                    if (!Users::getTable()->isUserDeleted($row->get(self::USER_ID))) {
                        ++$count;
                    }
                }
            }

            return $count; //$this->count($query);
        }

    }
