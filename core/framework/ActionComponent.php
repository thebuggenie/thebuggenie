<?php

    namespace thebuggenie\core\framework;

    /**
     * Action component class used in the MVC part of the framework
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Action component class used in the MVC part of the framework
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class ActionComponent extends Parameterholder
    {

        /**
         * Get module and template for a module/template combination
         *
         * @param string $template
         *
         * @return array
         */
        protected static function getModuleAndTemplate($template)
        {
            if (Context::getRouting()->hasComponentOverride($template))
            {
                $override_details = Context::getRouting()->getComponentOverride($template);
                $template = strtolower($override_details['module'] . '/' . $override_details['method']);
            }

            if ($separator_pos = mb_strpos($template, '/'))
            {
                $module = mb_substr($template, 0, $separator_pos);
                $template = mb_substr($template, $separator_pos + 1);
            }
            else
            {
                $module = Context::getRouting()->getCurrentRouteModule();
            }
            return array('module' => $module, 'file' => $template);
        }

        protected static function getFinalTemplateName($template, $module_file = null)
        {
            if (!isset($module_file))
                $module_file = self::getModuleAndTemplate($template);
            if (!Context::isReadySetup() || ($template_name = Context::getI18n()->hasTranslatedTemplate($template, true)) === false)
            {
                $template_basepath = (Context::isInternalModule($module_file['module'])) ? THEBUGGENIE_INTERNAL_MODULES_PATH : THEBUGGENIE_MODULES_PATH;
                $template_name = $template_basepath . $module_file['module'] . DS . 'templates' . DS . "_{$module_file['file']}.inc.php";
            }
            return $template_name;
        }

        protected static function _doesTemplateExist($template, $throw_exceptions = true, $module_file = null)
        {
            if (!isset($module_file))
                $module_file = self::getModuleAndTemplate($template);
            $template_name = self::getFinalTemplateName($template, $module_file);
            if (!file_exists($template_name))
            {
                if (!$throw_exceptions)
                    return false;
                throw new exceptions\TemplateNotFoundException("The template file <b>_{$module_file['file']}.inc.php</b> cannot be found in the template directory for module \"" . Context::getRouting()->getCurrentRouteModule() . '"');
            }
            if (!$throw_exceptions)
                return true;

            return $template_name;
        }

        protected static function _getComponentDetails($template)
        {
            $module_file = self::getModuleAndTemplate($template);
            $actionClassName = (Context::isInternalModule($module_file['module'])) ? "\\thebuggenie\\core\\modules\\".$module_file['module']."\\Components" : "\\thebuggenie\\modules\\".$module_file['module']."\\Components";
            $actionToRunName = 'component' . ucfirst($module_file['file']);

            return array($module_file, $actionClassName, $actionToRunName);
        }

        public static function doesComponentExist($template, $throw_exceptions = true)
        {
            return self::_doesComponentExist($template, $throw_exceptions);
        }

        protected static function _doesComponentExist($template, $throw_exceptions = true)
        {
            list ($module_file, $actionClassName, $actionToRunName) = self::_getComponentDetails($template);
            if (!class_exists($actionClassName))
            {
                if (!$throw_exceptions)
                    return false;
                throw new exceptions\ComponentNotFoundException('The component class ' . $actionClassName . ' could not be found');
            }
            $actionClass = new $actionClassName();
            if (!method_exists($actionClass, $actionToRunName))
            {
                if (!$throw_exceptions)
                    return false;
                throw new exceptions\ComponentNotFoundException("The component action {$actionToRunName} was not found in the {$actionClassName} class");
            }
            $retval = self::_doesTemplateExist($template, $throw_exceptions, $module_file);
            if (!$throw_exceptions)
                return $retval;

            return array($retval, $actionClass, $actionToRunName);
        }

        /**
         * Include a component from a module
         *
         * @param string $template
         * @param array $params
         */
        public static function includeComponent($template, $params = array())
        {
            $debug = Context::isDebugMode();
            if ($debug)
            {
                $time = explode(' ', microtime());
                $pretime = $time[1] + $time[0];
            }
            if (self::doesComponentExist($template, false))
            {
                list ($template_name, $actionClass, $actionToRunName) = self::_doesComponentExist($template);

                foreach ($params as $key => $val)
                {
                    $actionClass->$key = $val;
                }
                $actionClass->$actionToRunName();
                $parameters = $actionClass->getParameterHolder();
            }
            else
            {
                $template_name = self::getFinalTemplateName($template);
                $parameters = $params;
            }
            self::presentTemplate($template_name, $parameters);
            if ($debug)
            {
                $time = explode(' ', microtime());
                $posttime = $time[1] + $time[0];
                Context::visitPartial($template, $posttime - $pretime);
            }
        }

        /**
         * Present a template
         * @param string $template_file
         * @param array $params
         */
        public static function presentTemplate($template_file, $params = array())
        {
            Logging::log("configuring template variables for template {$template_file}");
            if (!file_exists($template_file))
                throw new exceptions\TemplateNotFoundException("The template file <b>{$template_file}</b> cannot be found.");

            foreach ($params as $key => $val)
            {
                $$key = $val;
            }
            if (array_key_exists('key', $params))
                $key = $params['key'];
            if (array_key_exists('val', $params))
                $val = $params['val'];

            /**
             * @global \thebuggenie\core\framework\Request The request object
             */
            $tbg_request = Context::getRequest();

            /**
             * @global \thebuggenie\core\framework\Response The response object
             */
            $tbg_response = Context::getResponse();

            /**
             * @global \thebuggenie\core\framework\Request The request object
             */
            $tbg_routing = Context::getRouting();

            /**
             * @global \thebuggenie\core\entities\User The user object
             */
            $tbg_user = Context::getUser();

            Context::loadLibrary('common');
            Context::loadLibrary('ui');

            Logging::log("rendering template '{$template_file}'");
            require $template_file;
        }

        /**
         * Returns the response object
         *
         * @return \thebuggenie\core\framework\Response
         */
        protected function getResponse()
        {
            return Context::getResponse();
        }

        /**
         * Return the routing object
         *
         * @return \thebuggenie\core\framework\Routing
         */
        protected function getRouting()
        {
            return Context::getRouting();
        }

        /**
         * Return the i18n object
         *
         * @return \thebuggenie\core\framework\I18n
         */
        protected function getI18n()
        {
            return Context::getI18n();
        }

        /**
         * Return the current logged in user
         *
         * @return \thebuggenie\core\entities\User
         */
        protected function getUser()
        {
            return Context::getUser();
        }

    }
