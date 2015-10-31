<?php

    namespace thebuggenie\core\framework\exceptions;

    /**
     * Exception used when trying to download a module
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Exception used when trying to download a module
     *
     * @package thebuggenie
     * @subpackage core
     */
    class ModuleDownloadException extends \Exception
    {

        const JSON_NOT_FOUND = 1;
        const FILE_NOT_FOUND = 2;

    }

