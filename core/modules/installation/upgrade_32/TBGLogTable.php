<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Log table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Log table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="log_32")
     */
    class TBGLogTable extends ScopedTable
    {

        const B2DBNAME = 'log';
        const ID = 'log.id';
        const SCOPE = 'log.scope';
        const TARGET = 'log.target';
        const TARGET_TYPE = 'log.target_type';
        const CHANGE_TYPE = 'log.change_type';
        const PREVIOUS_VALUE = 'log.previous_value';
        const CURRENT_VALUE = 'log.current_value';
        const TEXT = 'log.text';
        const TIME = 'log.time';
        const UID = 'log.uid';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addInteger(self::TARGET, 10);
            parent::addInteger(self::TARGET_TYPE, 3);
            parent::addInteger(self::CHANGE_TYPE, 3);
            parent::addText(self::TEXT, false);
            parent::addText(self::PREVIOUS_VALUE, false);
            parent::addText(self::CURRENT_VALUE, false);
            parent::addInteger(self::TIME, 10);
            parent::addInteger(self::UID, 10);
            parent::addInteger(self::SCOPE, 10);
        }

    }
