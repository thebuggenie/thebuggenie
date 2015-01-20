<?php

    namespace thebuggenie\core\helpers;

    /**
     * Common interface for objects providing a list of related users
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Common interface for objects providing a list of related users
     *
     * @package thebuggenie
     * @subpackage core
     */
    interface MentionableProvider
    {
        
        /**
         * Returns an array of users
         * 
         * @return array|\thebuggenie\core\entities\User
         */
        public function getMentionableUsers();
        
    }

