<?php

    namespace thebuggenie\core\modules\livelink;

    use thebuggenie\core\entities\Module;
    use thebuggenie\core\entities\Project;
    use thebuggenie\core\entities\tables\Projects;
    use thebuggenie\core\framework,
        Github\Client as GithubClient;

    /**
     * TBG Live link module
     *
     * @author
     * @version 0.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package livelink
     * @subpackage core
     */

    /**
     * TBG Live link module
     *
     * @package livelink
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Modules")
     */
    class Livelink extends framework\CoreModule
    {

        /**
         * @var ConnectorProvider[]
         */
        protected $_connectors = [];

        /**
         * Return an instance of this module
         *
         * @return Livelink
         */
        public static function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        public function hasAccountSettings()
        {
            return true;
        }

        public function getAccountSettingsLogo()
        {
            return 'leaf tbg-livelink';
        }

        public function getAccountSettingsName()
        {
            return 'TBG Live Link';
        }

        /**
         * @Listener(module='core', identifier='get_backdrop_partial')
         * @param \thebuggenie\core\framework\Event $event
         */
        public function listen_get_backdrop_partial(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            $connector = $request['connector'];
            $connector_module = $this->getConnectorModule($connector);
            $options = ['connector' => $this->getConnector($connector)];

            if ($this->hasConnector($connector)) {
                switch ($event->getSubject())
                {
                    case 'livelink-import_project':
                        $template_name = $connector_module->getName() . "/import_project";
                        $options['project'] = null;
                        if ($request['project_id']) {
                            $options['project'] = Projects::getTable()->selectById($request['project_id']);
                        }

                        if (!$options['project'] instanceof Project) {
                            $options['project'] = new Project();
                        }

                        break;
                    case 'livelink-configure_connector':
                        $template_name = $connector_module->getName() . "/configureconnector";
                        break;
                    default:
                        return;
                }

                foreach ($options as $key => $value)
                {
                    $event->addToReturnList($value, $key);
                }
                $event->setReturnValue($template_name);
                $event->setProcessed();
            }
        }

        /**
         * @Listener(module='core', identifier='project/editproject::above_content')
         * @param framework\Event $event
         */
        public function listen_project_template(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            $options = [
                'project' => $event->getParameter('project'),
                'module' => $this,
                'partial_options' => $this->getCurrentPartialOptions()
            ];

            if ($request->hasParameter('connector')) {
                $options['connector'] = $this->getModule()->getConnectorModule($request['connector']);
                $options['display_name'] = $options['connector']->getImportDisplayNameForProjectEdit($request);
                $options['input'] = $options['connector']->getInputOptionsForProjectEdit($request);
                $event->getParameter('project')->setName($options['connector']->getImportProjectNameForProjectEdit($request));
            }

            include_component('livelink/projectconfig_template', $options);

        }

        /**
         * @Listener(module='core', identifier='project/editproject::additional_form_elements')
         * @param framework\Event $event
         */
        public function listen_project_template_additional_form_elements(framework\Event $event)
        {
            $request = framework\Context::getRequest();
            if ($request->hasParameter('connector')) {
                $options = [
                    'project' => $event->getParameter('project'),
                    'module' => $this,
                ];

                $options['connector'] = $this->getModule()->getConnectorModule($request['connector']);
                $options['input'] = $options['connector']->getInputOptionsForProjectEdit($request);

                include_component('livelink/projectconfig_template_additional_form_elements', $options);
            }

        }

        /**
         * @Listener(module='core', identifier='config_project_tabs_other')
         * @param framework\Event $event
         */
        public function listen_projectconfig_tab(framework\Event $event)
        {
            include_component('livelink/projectconfig_tab', array('selected_tab' => $event->getParameter('selected_tab')));
        }

        /**
         * @Listener(module='core', identifier='config_project_panes')
         * @param framework\Event $event
         */
        public function listen_projectconfig_panel(framework\Event $event)
        {
            $options = [
                'selected_tab' => $event->getParameter('selected_tab'),
                'access_level' => $event->getParameter('access_level'),
                'project' => $event->getParameter('project'),
                'connector' => $event->getParameter('connector')
            ];

            include_component('livelink/projectconfig_panel', $options);
        }

        public function postConnectorSettings(framework\Request $request)
        {

            return true;
        }

        public function addConnector($key, ConnectorProvider $connector)
        {
            $this->_connectors[$key] = $connector;
        }

        /**
         * @param $key
         * @return BaseConnector
         */
        public function getConnector($key)
        {
            return (isset($this->_connectors[$key])) ? $this->_connectors[$key]->getConnector() : null;
        }

        /**
         * @param $key
         * @return ConnectorProvider
         */
        public function getConnectorModule($key)
        {
            return (isset($this->_connectors[$key])) ? $this->_connectors[$key] : null;
        }

        public function hasConnector($key)
        {
            return array_key_exists($key, $this->_connectors);
        }

        /**
         * @return ConnectorProvider[]
         */
        public function getConnectorModules()
        {
            return $this->_connectors;
        }

        public function hasConnectors()
        {
            return (bool) count($this->_connectors);
        }

        public function getCurrentPartialOptions()
        {
            $partial_options = ['key' => 'project_config'];
            $request = framework\Context::getRequest();

            if ($request->hasParameter('assignee_type')) {
                $partial_options['assignee_type'] = $request['assignee_type'];
            }
            if ($request->hasParameter('assignee_id')) {
                $partial_options['assignee_id'] = $request['assignee_id'];
            }

            return $partial_options;
        }

    }
