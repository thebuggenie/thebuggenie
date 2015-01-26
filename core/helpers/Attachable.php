<?php

    namespace thebuggenie\core\helpers;

    /**
     * Common interface for objects that can have files attached
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Common interface for objects that can have files attached
     *
     * @package thebuggenie
     * @subpackage core
     */
    interface Attachable
    {

        /**
         * Attaches a file
         *
         * @param \thebuggenie\core\entities\File $file
         */
        public function attachFile(\thebuggenie\core\entities\File $file, $file_comment = '', $description = '');
        
        /**
         * Detaches a file
         *
         * @param \thebuggenie\core\entities\File $file
         */
        public function detachFile(\thebuggenie\core\entities\File $file);

    }

