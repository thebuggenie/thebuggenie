<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Permissions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Permissions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="permissions_32")
     */
    class TBGPermissionsTable extends ScopedTable
    {

        const B2DBNAME = 'permissions';
        const ID = 'permissions.id';
        const SCOPE = 'permissions.scope';
        const PERMISSION_TYPE = 'permissions.permission_type';
        const TARGET_ID = 'permissions.target_id';
        const UID = 'permissions.uid';
        const GID = 'permissions.gid';
        const TID = 'permissions.tid';
        const ALLOWED = 'permissions.allowed';
        const MODULE = 'permissions.module';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::PERMISSION_TYPE, 100);
            parent::addVarchar(self::TARGET_ID, 200, 0);
            parent::addBoolean(self::ALLOWED);
            parent::addVarchar(self::MODULE, 50);
            parent::addInteger(self::UID, 10);
            parent::addInteger(self::GID, 10);
            parent::addInteger(self::TID, 10);
            parent::addInteger(self::SCOPE, 10);
        }

    }
