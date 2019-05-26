<?php

    namespace thebuggenie\core\helpers;

    /**
     * Interface for items implementing diffable content
     *
     * @package thebuggenie
     * @subpackage core
     */
    interface Diffable
    {

        /**
         * @return int
         */
        public function getLinesAdded();

        /**
         * @return int
         */
        public function getLinesRemoved();

    }

