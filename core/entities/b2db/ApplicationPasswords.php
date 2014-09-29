<?php

    namespace thebuggenie\core\entities\b2db;

    /**
     * Application passwords table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
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
    class ApplicationPasswords extends \TBGB2DBTable
    {

        const B2DB_TABLE_VERSION = 1;

    }
