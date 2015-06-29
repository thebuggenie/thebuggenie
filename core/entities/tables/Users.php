<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\framework,
        b2db\Criteria;

    /**
     * Users table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Users table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @method Users getTable()
     *
     * @Table(name="users")
     * @Entity(class="\thebuggenie\core\entities\User")
     */
    class Users extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 3;
        const B2DBNAME = 'users';
        const ID = 'users.id';
        const UNAME = 'users.username';
        const PASSWORD = 'users.password';
        const BUDDYNAME = 'users.buddyname';
        const REALNAME = 'users.realname';
        const EMAIL = 'users.email';
        const USERSTATE = 'users.userstate';
        const CUSTOMSTATE = 'users.customstate';
        const HOMEPAGE = 'users.homepage';
        const LANGUAGE = 'users.language';
        const LASTSEEN = 'users.lastseen';
        const QUOTA = 'users.quota';
        const ACTIVATED = 'users.activated';
        const ENABLED = 'users.enabled';
        const DELETED = 'users.deleted';
        const AVATAR = 'users.avatar';
        const USE_GRAVATAR = 'users.use_gravatar';
        const PRIVATE_EMAIL = 'users.private_email';
        const JOINED = 'users.joined';
        const GROUP_ID = 'users.group_id';
        const OPENID_LOCKED = 'users.openid_locked';

        public function getAll()
        {
            $users = $this->selectAll();
            return $users;
        }

        protected function _setupIndexes()
        {
            $this->_addIndex('userstate', self::USERSTATE);
            $this->_addIndex('username_password', array(self::UNAME, self::PASSWORD));
            $this->_addIndex('username_deleted', array(self::UNAME, self::DELETED));
        }

        protected function getUserMigrationDetails()
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn('users.id');
            $crit->addSelectionColumn('users.scope');
            $crit->addSelectionColumn('users.group_id');
            $res = $this->doSelect($crit);

            $users = array();
            while ($row = $res->getNextRow())
            {
                $users[$row->get('users.id')] = array('scope_id' => $row->get('users.scope'), 'group_id' => $row->get('users.group_id'));
            }

            return $users;
        }

        /**
         * @param $username
         * @return \thebuggenie\core\entities\User
         */
        public function getByUsername($username)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UNAME, strtolower($username), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(self::DELETED, false);

            return $this->selectOne($crit);
        }

        public function getByRealname($realname)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::REALNAME, strtolower($realname), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(self::DELETED, false);

            return $this->selectOne($crit);
        }

        public function getByBuddyname($buddyname)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::BUDDYNAME, strtolower($buddyname), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(self::DELETED, false);

            return $this->selectOne($crit);
        }

        public function getByEmail($email)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::EMAIL, strtolower($email), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(self::DELETED, false);

            return $this->selectOne($crit);
        }

        public function isUsernameAvailable($username)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UNAME, strtolower($username), Criteria::DB_EQUALS, '', '', Criteria::DB_LOWER);
            $crit->addWhere(self::DELETED, false);

            return !(bool) $this->doCount($crit);
        }

        public function getByUserIDs($userids)
        {
            if (empty($userids)) return array();

            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ID, $userids, Criteria::DB_IN);

            return $this->select($crit);
        }

        public function getByUserID($userid)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            return $this->selectById($userid, $crit);
        }

        public function doesIDExist($userid)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            $crit->addWhere(self::ID, $userid);
            return $this->doCount($crit);
        }

        public function getByDetails($details, $limit = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::DELETED, false);
            if (mb_stristr($details, "@"))
            {
                $crit->addWhere(self::EMAIL, "%$details%", Criteria::DB_LIKE);
            }
            else
            {
                $crit->addWhere(self::UNAME, "%$details%", Criteria::DB_LIKE);
            }

            if ($limit)
            {
                $crit->setLimit($limit);
            }
            if (!$res = $this->select($crit))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::DELETED, false);
                $crit->addWhere(self::UNAME, "%$details%", Criteria::DB_LIKE);
                $crit->addOr(self::BUDDYNAME, "%$details%", Criteria::DB_LIKE);
                $crit->addOr(self::REALNAME, "%$details%", Criteria::DB_LIKE);
                $crit->addOr(self::EMAIL, "%$details%", Criteria::DB_LIKE);
                if ($limit)
                {
                    $crit->setLimit($limit);
                }
                $res = $this->select($crit);
            }

            $users = array();
            if ($res)
            {
                foreach ($res as $key => $user)
                {
                    if ($user->isScopeConfirmed())
                    {
                        $users[$key] = $user;
                    }
                }
            }

            return $users;
        }

        public function findInConfig($details, $limit = 50, $allow_keywords = true)
        {
            $crit = $this->getCriteria();

            switch ($details)
            {
                case 'unactivated':
                    if ($allow_keywords)
                    {
                        $crit->addWhere(self::ACTIVATED, false);
                        $limit = 500;
                        break;
                    }
                case 'newusers':
                    if ($allow_keywords)
                    {
                        $crit->addWhere(self::JOINED, NOW - 1814400, Criteria::DB_GREATER_THAN_EQUAL);
                        $limit = 500;
                        break;
                    }
                case '0-9':
                    if ($allow_keywords)
                    {
                        $ctn = $crit->returnCriterion(self::UNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
                        $ctn->addOr(self::BUDDYNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
                        $ctn->addOr(self::REALNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), Criteria::DB_IN);
                        $crit->addWhere($ctn);
                        $limit = 500;
                        break;
                    }
                case 'all':
                    if ($allow_keywords)
                    {
                        $limit = 500;
                        break;
                    }
                default:
                    if (mb_strlen($details) == 1) $limit = 500;
                    $details = (mb_strlen($details) == 1) ? mb_strtolower("$details%") : mb_strtolower("%$details%");
                    $ctn = $crit->returnCriterion(self::UNAME, $details, Criteria::DB_LIKE);
                    $ctn->addOr(self::BUDDYNAME, $details, Criteria::DB_LIKE);
                    $ctn->addOr(self::REALNAME, $details, Criteria::DB_LIKE);
                    $ctn->addOr(self::EMAIL, $details, Criteria::DB_LIKE);
                    $crit->addWhere($ctn);
                    break;
            }
            $crit->addJoin(UserScopes::getTable(), UserScopes::USER_ID, self::ID, array(), Criteria::DB_INNER_JOIN);
            $crit->addWhere(UserScopes::SCOPE, framework\Context::getScope()->getID());
            $crit->addWhere(self::DELETED, false);

            $users = array();
            $res = null;

            if ($details != '' && $res = $this->doSelect($crit))
            {
                while (($row = $res->getNextRow()) && count($users) < $limit)
                {
                    $user_id = (int) $row->get(self::ID);
                    $details = UserScopes::getTable()->getUserDetailsByScope($user_id, framework\Context::getScope()->getID());
                    if (!$details) continue;
                    $users[$user_id] = \thebuggenie\core\entities\User::getB2DBTable()->selectById($user_id);
                    $users[$user_id]->setScopeConfirmed($details['confirmed']);
                }
            }

            return $users;
        }

        public function getAllUserIDs()
        {
            $crit = $this->getCriteria();

            $crit->addSelectionColumn(self::ID, 'uid');
            $res = $this->doSelect($crit);

            $uids = array();
            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $uid = $row->get('uid');
                    $uids[$uid] = $uid;
                }
            }

            return $uids;
        }

        public function preloadUsers($user_ids)
        {
            if (!empty($user_ids))
            {
                $crit = $this->getCriteria();
                $crit->addWhere(self::ID, $user_ids, Criteria::DB_IN);
                $users = $this->select($crit);
                unset($users);
            }

            return;
        }

    }
