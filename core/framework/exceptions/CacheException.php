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
    class CacheException extends \Exception
    {

        const NO_FOLDER = 1;
        const NOT_WRITABLE = 2;

    }

