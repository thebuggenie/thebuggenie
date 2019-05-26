<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use b2db\Table;

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
     * @Table(name="users_32")
     */
    class TBGUsersTable extends Table
    {

        const B2DBNAME = 'users';
        const ID = 'users.id';
        const UNAME = 'users.username';
        const PASSWORD = 'users.password';
        const BUDDYNAME = 'users.buddyname';
        const REALNAME = 'users.realname';
        const EMAIL = 'users.email';
        const SALT = 'users.salt';
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::UNAME, 50);
            parent::addVarchar(self::PASSWORD, 100);
            parent::addVarchar(self::BUDDYNAME, 50);
            parent::addVarchar(self::REALNAME, 100);
            parent::addVarchar(self::EMAIL, 200);
            parent::addInteger(self::USERSTATE, 10);
            parent::addBoolean(self::CUSTOMSTATE);
            parent::addVarchar(self::HOMEPAGE, 250, '');
            parent::addVarchar(self::LANGUAGE, 100, '');
            parent::addInteger(self::LASTSEEN, 10);
            parent::addInteger(self::QUOTA);
            parent::addBoolean(self::ACTIVATED);
            parent::addBoolean(self::ENABLED);
            parent::addBoolean(self::DELETED);
            parent::addVarchar(self::AVATAR, 30, '');
            parent::addBoolean(self::USE_GRAVATAR, true);
            parent::addBoolean(self::PRIVATE_EMAIL);
            parent::addBoolean(self::OPENID_LOCKED);
            parent::addInteger(self::JOINED, 10);
        }

        public function getAdminUsername()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID);
            $query->addSelectionColumn(self::UNAME);
            $query->where(self::ID, 1);

            $row = $this->rawSelectOne($query);
            return $row[self::UNAME];
        }

    }
