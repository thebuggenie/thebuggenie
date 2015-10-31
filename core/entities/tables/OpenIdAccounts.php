<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\User,
        b2db\Table;

    /**
     * OpenID accounts table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * OpenID accounts table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="openid_accounts")
     */
    class OpenIdAccounts extends Table
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'openid_accounts';
        const ID = 'openid_accounts.id';
        const IDENTITY = 'openid_accounts.identity';
        const IDENTITY_HASH = 'openid_accounts.identity_hash';
        const EMAIL = 'openid_accounts.email';
        const TYPE = 'openid_accounts.type';
        const UID = 'openid_accounts.uid';

        public static function getProviders()
        {
            $providers = array();
            $providers['google'] = 'google.com';
            $providers['myopenid'] = 'myopenid.com';
            $providers['yahoo'] = 'yahoo.com';
            $providers['livejournal'] = 'livejournal.com';
            $providers['wordpress'] = 'wordpress.com';
            $providers['blogger'] = 'blogspot.com';
            $providers['verisign'] = 'verisignlabs.com';
            $providers['claimid'] = 'claimid.com';
            $providers['clickpass'] = 'clickpass.com';
            $providers['launchpad'] = 'launchpad.net';

            return $providers;
        }

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::IDENTITY, 300);
            parent::_addVarchar(self::IDENTITY_HASH, 300);
            parent::_addVarchar(self::EMAIL, 300);
            parent::_addVarchar(self::TYPE, 300);
            parent::_addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
        }
        
        public function addIdentity($identity, $user_id)
        {
            $user = \thebuggenie\core\entities\User::getB2DBTable()->selectById($user_id);
            $crit = $this->getCriteria();
            $crit->addInsert(self::IDENTITY, $identity);
            $crit->addInsert(self::IDENTITY_HASH, User::hashPassword($identity, $user->getSalt()));
            $crit->addInsert(self::UID, $user_id);
            $type = 'openid';

            foreach (self::getProviders() as $provider => $string)
            {
                if (stripos($identity, $string) !== false)
                {
                    $type = $provider;
                    break;
                }
            }
            $crit->addInsert(self::TYPE, $type);

            $this->doInsert($crit);
        }
        
        public function getUserIDfromIdentity($identity)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::IDENTITY, $identity);
            if ($row = $this->doSelectOne($crit))
            {
                return (integer) $row->get(self::UID);
            }
            return null;
        }
        
        public function getUserIDfromIdentityHash($identity_hash)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::IDENTITY_HASH, $identity_hash);
            if ($row = $this->doSelectOne($crit))
            {
                return (integer) $row->get(self::UID);
            }
            return null;
        }
        
        public function getIdentityFromID($id)
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::IDENTITY);
            $row = $this->doSelectById($id, $crit);
            
            return ($row instanceof \b2db\Row) ? $row->get(self::IDENTITY) : null;
        }

        public function getIdentitiesForUserID($user_id)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::UID, $user_id);
            $identities = array();
            
            if ($res = $this->doSelect($crit))
            {
                while ($row = $res->getNextRow())
                {
                    $identities[$row->get(self::IDENTITY)] = array('identity' => $row->get(self::IDENTITY), 'email' => $row->get(self::EMAIL), 'type' => $row->get(self::TYPE), 'id' => $row->getID());
                }
            }
            
            return $identities;
        }

    }
