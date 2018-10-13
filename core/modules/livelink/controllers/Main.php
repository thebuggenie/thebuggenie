<?php

    namespace thebuggenie\core\modules\livelink\controllers;

    use thebuggenie\core\entities\Project;
    use thebuggenie\core\entities\tables\CommitFiles;
    use thebuggenie\core\entities\tables\Commits;
    use thebuggenie\core\entities\tables\IssueCommits;
    use thebuggenie\core\entities\tables\Projects;
    use thebuggenie\core\framework,
        Github\Client as GithubClient,
        Github\Exception\RuntimeException as GithubException;
    use thebuggenie\core\modules\livelink\Livelink;

    /**
     * Main controller for the livelink module
     */
    class Main extends framework\Action
    {

        public function getAuthenticationMethodForAction($action)
        {
            switch ($action) {
                case 'webhook':
                    return framework\Action::AUTHENTICATION_METHOD_DUMMY;
                default:
                    return framework\Action::AUTHENTICATION_METHOD_CORE;
            }
        }

        /**
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Route(name="livelink_webhook", url="/livelink/hooks/:project_id/:secret")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runWebhook(framework\Request $request)
        {
            Commits::getTable()->create();
            IssueCommits::getTable()->create();
            CommitFiles::getTable()->create();
            $project = Projects::getTable()->selectById($request['project_id']);

            if (!$project instanceof Project) {
                $this->getResponse()->setHttpStatus(404);
                return $this->renderJSON(['error' => 'Project not found']);
            }

            $secret = $request['secret'];

            if ($secret != $this->getModule()->getProjectSecret($project)) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => 'Invalid secret']);
            }

            $connector = $this->getModule()->getProjectConnector($project);
            return $this->getModule()->getConnectorModule($connector)->webhook($request, $project);
        }

        /**
         * @Route(name="configure_livelink_connector", url="/livelink/connector/:connector")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runConfigureLivelinkConnector(framework\Request $request)
        {
            $connector = $request['connector'];
            try {
                $livelink = $this->getModule();
                $connector_module = $livelink->getConnectorModule($connector);

                return $this->renderJSON($connector_module->postConnectorSettings($request));
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
            }
        }

        /**
         * @Route(name="disconnect_livelink_connector", url="/livelink/disconnect")
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runDisconnectLivelinkConnector(framework\Request $request)
        {
            $connector = $request['connector'];
            try {
                $livelink = $this->getModule();
                $connector_module = $livelink->getConnectorModule($connector);

                return $this->renderJSON($connector_module->removeConnectorSettings($request));
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
            }
        }

    }

