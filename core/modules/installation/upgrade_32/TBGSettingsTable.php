<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Settings table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Settings table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="settings_32")
     */
    class TBGSettingsTable extends ScopedTable
    {

        const B2DBNAME = 'settings';
        const ID = 'settings.id';
        const SCOPE = 'settings.scope';
        const NAME = 'settings.name';
        const MODULE = 'settings.module';
        const VALUE = 'settings.value';
        const UID = 'settings.uid';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::NAME, 45);
            parent::_addVarchar(self::MODULE, 45);
            parent::_addVarchar(self::VALUE, 200);
            parent::_addInteger(self::UID, 10);
            parent::_addInteger(self::SCOPE, 10);
        }

    }
