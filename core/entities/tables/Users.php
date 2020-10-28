<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Table;
    use thebuggenie\core\entities\User;
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
     * @method static Users getTable()
     *
     * @Table(name="users")
     * @Entity(class="\thebuggenie\core\entities\User")
     */
    class Users extends Table
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

        protected $_username_lookup_cache = [];

        public function getAll()
        {
            $users = $this->selectAll();
            return $users;
        }

        protected function setupIndexes()
        {
            $this->addIndex('userstate', self::USERSTATE);
            $this->addIndex('username_password', array(self::UNAME, self::PASSWORD));
            $this->addIndex('username_deleted', array(self::UNAME, self::DELETED));
        }

        protected function getUserMigrationDetails()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn('users.id');
            $query->addSelectionColumn('users.scope');
            $query->addSelectionColumn('users.group_id');
            $res = $this->rawSelect($query);

            $users = array();
            while ($row = $res->getNextRow())
            {
                $users[$row->get('users.id')] = array('scope_id' => $row->get('users.scope'), 'group_id' => $row->get('users.group_id'));
            }

            return $users;
        }

        /**
         * @param $username
         * @return User
         */
        public function getByUsername($username)
        {
            if (!array_key_exists($username, $this->_username_lookup_cache)) {
                $query = $this->getQuery();
                $query->where(self::UNAME, strtolower($username), \b2db\Criterion::EQUALS, '', '', \b2db\Query::DB_LOWER);
                $query->where(self::DELETED, false);

                $user = $this->selectOne($query);
                $this->_username_lookup_cache[$username] = $user;
            }

            return $this->_username_lookup_cache[$username];
        }

        public function getByRealname($realname)
        {
            $query = $this->getQuery();
            $query->where(self::REALNAME, strtolower($realname), \b2db\Criterion::EQUALS, '', '', \b2db\Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        public function getByBuddyname($buddyname)
        {
            $query = $this->getQuery();
            $query->where(self::BUDDYNAME, strtolower($buddyname), \b2db\Criterion::EQUALS, '', '', \b2db\Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        public function getByEmail($email)
        {
            $query = $this->getQuery();
            $query->where(self::EMAIL, strtolower($email), \b2db\Criterion::EQUALS, '', '', \b2db\Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return $this->selectOne($query);
        }

        public function isUsernameAvailable($username)
        {
            $query = $this->getQuery();
            $query->where(self::UNAME, strtolower($username), \b2db\Criterion::EQUALS, '', '', \b2db\Query::DB_LOWER);
            $query->where(self::DELETED, false);

            return !(bool) $this->count($query);
        }

        public function isUserDeleted($userid)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, true);
            $query->where(self::ID, $userid);
            return (bool) $this->count($query);
        }

        public function getByUserIDs($userids)
        {
            if (empty($userids)) return array();

            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $userids, \b2db\Criterion::IN);

            return $this->select($query);
        }

        /**
         * @param $userid
         * @return User
         */
        public function getByUserID($userid)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            return $this->selectById($userid, $query);
        }

        public function doesIDExist($userid)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::ID, $userid);
            return $this->count($query);
        }

        public function getByDetails($details, $limit = null)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            if (mb_stristr($details, "@"))
            {
                $query->where(self::EMAIL, "%$details%", \b2db\Criterion::LIKE);
            }
            else
            {
                $query->where(self::UNAME, "%$details%", \b2db\Criterion::LIKE);
            }

            if ($limit)
            {
                $query->setLimit($limit);
            }
            if (!$res = $this->select($query))
            {
                $query = $this->getQuery();
                $query->where(self::DELETED, false);
                $query->where(self::UNAME, "%$details%", \b2db\Criterion::LIKE);
                $query->or(self::BUDDYNAME, "%$details%", \b2db\Criterion::LIKE);
                $query->or(self::REALNAME, "%$details%", \b2db\Criterion::LIKE);
                $query->or(self::EMAIL, "%$details%", \b2db\Criterion::LIKE);
                if ($limit)
                {
                    $query->setLimit($limit);
                }
                $res = $this->select($query);
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
            $query = $this->getQuery();

            switch ($details)
            {
                case 'unactivated':
                    if ($allow_keywords)
                    {
                        $query->where(self::ACTIVATED, false);
                        $limit = 500;
                        break;
                    }
                case 'newusers':
                    if ($allow_keywords)
                    {
                        $query->where(self::JOINED, NOW - 1814400, \b2db\Criterion::GREATER_THAN_EQUAL);
                        $limit = 500;
                        break;
                    }
                case '0-9':
                    if ($allow_keywords)
                    {
                        $criteria = new Criteria();
                        $criteria->where(self::UNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), \b2db\Criterion::IN);
                        $criteria->or(self::BUDDYNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), \b2db\Criterion::IN);
                        $criteria->or(self::REALNAME, array('0%', '1%', '2%', '3%', '4%', '5%', '6%', '7%', '8%', '9%'), \b2db\Criterion::IN);
                        $query->where($criteria);
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
                    $criteria = new Criteria();
                    $criteria->where(self::UNAME, $details, \b2db\Criterion::LIKE);
                    $criteria->or(self::BUDDYNAME, $details, \b2db\Criterion::LIKE);
                    $criteria->or(self::REALNAME, $details, \b2db\Criterion::LIKE);
                    $criteria->or(self::EMAIL, $details, \b2db\Criterion::LIKE);
                    $query->where($criteria);
                    break;
            }
            $query->join(UserScopes::getTable(), UserScopes::USER_ID, self::ID, array(), \b2db\Join::INNER);
            $query->where(UserScopes::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::DELETED, false);

            $users = array();
            $res = null;

            if ($details != '' && $res = $this->rawSelect($query))
            {
                while (($row = $res->getNextRow()) && count($users) < $limit)
                {
                    $user_id = (int) $row->get(self::ID);
                    $details = UserScopes::getTable()->getUserDetailsByScope($user_id, framework\Context::getScope()->getID());
                    if (!$details) continue;
                    $users[$user_id] = User::getB2DBTable()->selectById($user_id);
                    $users[$user_id]->setScopeConfirmed($details['confirmed']);
                }
            }

            return $users;
        }

        public function getAllUserIDs()
        {
            $query = $this->getQuery();

            $query->addSelectionColumn(self::ID, 'uid');
            $res = $this->rawSelect($query);

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
                $query = $this->getQuery();
                $query->where(self::ID, $user_ids, \b2db\Criterion::IN);
                $users = $this->select($query);
                unset($users);
            }

            return;
        }

    }
