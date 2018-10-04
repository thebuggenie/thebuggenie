<?php

    namespace thebuggenie\core\modules\livelink;

    use thebuggenie\core\framework;

    /**
     * actions for the livelink module
     */
    class Components extends framework\ActionComponent
    {

        /**
         * Return an instance of this module
         *
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        public function componentConfigureConnector()
        {
            $this->connector = $this->getModule()->getConnector('connector_key');
        }

    }

