<?php

    namespace thebuggenie\core\framework\exceptions;

    /**
     * Exception used in an action
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Exception used in an action
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class ConfigurationException extends \Exception
    {

        const NO_VERSION_INFO = 1;
        const UPGRADE_REQUIRED = 2;
        const UPGRADE_FILE_MISSING = 3;
        const NO_B2DB_CONFIGURATION = 4;
        const PERMISSION_DENIED = 5;

    }

