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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::UNAME, 50);
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
