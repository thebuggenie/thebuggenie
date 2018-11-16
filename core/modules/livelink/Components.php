<?php

    namespace thebuggenie\core\modules\livelink;

    use thebuggenie\core\framework;
    use thebuggenie\core\modules\project\Project;

    /**
     * actions for the livelink module
     *
     * @property Project $project
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

        public function componentProjectConfig_template()
        {
            if ($this->project->getID()) {
                $connector = $this->getModule()->getProjectConnector($this->project);
                $connector_module = ($connector) ? $this->getModule()->getConnectorModule($connector) : null;
                if ($connector_module instanceof ConnectorProvider) {
                    $this->connector = $connector_module;
                    $this->display_name = $connector_module->getRepositoryDisplayNameForProject($this->project);
                }
                $this->module = $this->getModule();
            }
        }

    }

