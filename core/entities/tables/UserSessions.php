<?php

    namespace thebuggenie\core\entities\tables;

    use b2db\Table;
    use thebuggenie\core\framework;
    use b2db\Core,
        b2db\Criteria,
        b2db\Criterion;

    /**
     * Userstate table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Userstate table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="usersession")
     * @Entity(class="\thebuggenie\core\entities\UserSession")
     */
    class UserSessions extends Table
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'usersession';

    }
