<?php

    namespace thebuggenie\core\entities\tables;

    use thebuggenie\core\entities\tables\ScopedTable;

    /**
     * Application passwords table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Application passwords table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="application_password")
     * @Entity(class="\thebuggenie\core\entities\ApplicationPassword")
     */
    class ApplicationPasswords extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

    }
