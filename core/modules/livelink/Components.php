<?php

    namespace thebuggenie\core\modules\livelink;

    use thebuggenie\core\entities\CommitFileDiff;
    use thebuggenie\core\entities\Project;
    use thebuggenie\core\framework;

    /**
     * actions for the livelink module
     *
     * @property Project $project
     * @property CommitFileDiff $diff
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

        public function componentTree()
        {
        }

        public function componentDirectory()
        {
        }

        public function componentDiff()
        {
            $this->too_long = false;

            if (!$this->diff->getDiff()) {
                $this->too_long = true;
            } else {
                $this->addlinecounter = $this->diff->getStartLineAdd();
                $this->removelinecounter = $this->diff->getStartLineRemove();
                $lines = explode("\n", $this->diff->getDiff());
                $this->lines = array_map(function ($line) {
                    $change = substr($line, 0, 1);
                    if ($change == '+') {
                        return [
                            'change' => 'add',
                            'text' => ' ' . substr($line, 1)
                        ];
                    } elseif ($change == '-') {
                        return [
                            'change' => 'remove',
                            'text' => ' ' . substr($line, 1)
                        ];
                    } else {
                        return [
                            'change' => '',
                            'text' => $line
                        ];
                    }
                }, $lines);
            }
        }

    }

