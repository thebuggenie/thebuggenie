<?php

    namespace thebuggenie\core\modules\installation\upgrade_4112;

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
     * @method static UsersTable getTable()
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="users")
     */
    class UsersTable extends Table
    {

        const B2DBNAME = 'users';
        const ID = 'users.id';
        const UNAME = 'users.username';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::UNAME, 50);
        }

        public function getAdminUsername()
        {
            $crit = $this->getCriteria();
            $crit->addSelectionColumn(self::ID);
            $crit->addSelectionColumn(self::UNAME);
            $crit->addWhere(self::ID, 1);

            $row = $this->doSelectOne($crit);
            return $row[self::UNAME];
        }

    }
