<?php

    /**
     * Exception used in an action
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Exception used in an action
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class TBGConfigurationException extends Exception
    {

        const NO_VERSION_INFO = 1;
        const UPGRADE_REQUIRED = 2;
        const UPGRADE_FILE_MISSING = 3;
        const NO_B2DB_CONFIGURATION = 4;

    }

