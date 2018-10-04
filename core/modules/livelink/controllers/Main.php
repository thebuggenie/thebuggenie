<?php

    namespace thebuggenie\core\modules\livelink\controllers;

    use thebuggenie\core\framework,
        Github\Client as GithubClient,
        Github\Exception\RuntimeException as GithubException;
    use thebuggenie\core\modules\livelink\Livelink;

    /**
     * Main controller for the livelink module
     */
    class Main extends framework\Action
    {

        /**
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Route(name="configure_livelink_settings", url="/configure/project/:project_id/livelink"
         * @Parameters(config_module="core", section=15)
         *
         * @param framework\Request $request
         * @return bool
         */
        public function runConfigureProjectSettings(framework\Request $request)
        {
            try {
                if (isset($request['setup-step'])) {
                    switch ($request['setup-step']) {
                        case 1:
                            $url = str_replace(['git@github.com:', 'https://github.com/'], ['', ''], $request['repository_url']);
                            $pieces = parse_url($url);
                            $pieces = explode('/', $pieces['path']);
                            if (count($pieces) == 2) {
                                list($repository_user, $repository_name) = $pieces;
                                if (substr($repository_name, -4) == '.git') {
                                    $repository_name = substr($repository_name, 0, -4);
                                }
                            } else {
                                $this->getResponse()->setHttpStatus(400);
                                return $this->renderJSON(['error' => $this->getI18n()->__("Sorry, that did not make sense")]);
                            }

                            $client = $this->getModule()->getGithubClient();
                            $repository = $client->api('repo')->show($repository_user, $repository_name);

                            break;
                    }
                }
            } catch (GithubException $e) {
                if ($e->getCode() == 404) {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => $this->getI18n()->__("That repository does not exist")]);
                } else {
                    $this->getResponse()->setHttpStatus(400);
                    return $this->renderJSON(['error' => $this->getI18n()->__("Woops, there was an error trying to connect to Github")]);
                }
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__("Woops, there was an error trying to connect to Github")]);
            }

            return $this->renderJSON($repository);
        }

    }

