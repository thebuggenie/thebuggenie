<?php

    namespace thebuggenie\core\framework;

    /**
     * Action class used in the MVC part of the framework
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Action class used in the MVC part of the framework
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class Action extends Parameterholder
    {

        const AUTHENTICATION_METHOD_CORE = 'core';
        const AUTHENTICATION_METHOD_DUMMY = 'dummy';
        const AUTHENTICATION_METHOD_CLI = 'cli';
        const AUTHENTICATION_METHOD_RSS_KEY = 'rss_key';
        const AUTHENTICATION_METHOD_APPLICATION_PASSWORD = 'application_password';
        const AUTHENTICATION_METHOD_ELEVATED = 'elevated';

        public function getAuthenticationMethodForAction($action)
        {
            if (Context::isCLI())
            {
                return self::AUTHENTICATION_METHOD_CLI;
            }
            else
            {
                return self::AUTHENTICATION_METHOD_CORE;
            }
        }

        /**
         * Forward the user to a specified url
         *
         * @param string $url The URL to forward to
         * @param integer $code [optional] HTTP status code
         */
        public function forward($url, $code = 200)
        {
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json')
            {
                $this->getResponse()->ajaxResponseText($code, Context::getMessageAndClear('forward'));
            }
            Logging::log("Forwarding to url {$url}");

            Logging::log('Triggering header redirect function');
            $this->getResponse()->headerRedirect($url, $code);
        }

        /**
         * Function that is executed before any actions in an action class
         *
         * @param \thebuggenie\core\framework\Request $request The request object
         * @param string $action The action that is being triggered
         */
        public function preExecute(Request $request, $action)
        {

        }

        /**
         * Redirect from one action method to another in the same action
         *
         * @param string $redirect_to The method to redirect to
         */
        public function redirect($redirect_to)
        {
            $actionName = 'run' . ucfirst($redirect_to);
            $this->getResponse()->setTemplate(mb_strtolower($redirect_to) . '.' . Context::getRequest()->getRequestedFormat() . '.php');
            if (method_exists($this, $actionName))
            {
                return $this->$actionName(Context::getRequest());
            }
            throw new exceptions\ActionNotFoundException("The action \"{$actionName}\" does not exist in ".get_class($this));
        }

        /**
         * Render a string
         *
         * @param string $text The text to render
         *
         * @return boolean
         */
        public function renderText($text)
        {
            echo $text;
            return true;
        }

        /**
         * Renders JSON output, also takes care of setting the correct headers
         *
         * @param mixed $text An array, or text, to serve as json
         *
         * @return boolean
         */
        public function renderJSON($text = array())
        {
            $this->getResponse()->setContentType('application/json');
            $this->getResponse()->setDecoration(Response::DECORATE_NONE);

            if (is_array($text))
                array_walk_recursive($text , function(&$item) { $item = iconv('UTF-8', 'UTF-8//IGNORE', $item); });
            else
                $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);

            echo json_encode($text);
            return true;
        }

        /**
         * Return the response object
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

        /**
         * Sets the response to 404 and shows an error, with an optional message
         *
         * @param string $message [optional] The message
         */
        public function return404($message = null)
        {
            if (Context::getRequest()->isAjaxCall() || Context::getRequest()->getRequestedFormat() == 'json')
            {
                $this->getResponse()->ajaxResponseText(404, $message);
            }

            $this->message = $message;
            $this->getResponse()->setHttpStatus(404);
            $this->getResponse()->setTemplate('main/notfound');
            return false;
        }

        /**
         * Forward the user with HTTP status code 403 and an (optional) message
         *
         * @param string $message [optional] The message
         */
        public function forward403($message = null)
        {
            $this->forward403unless(false, $message);
        }

        /**
         * Forward the user with HTTP status code 403 and an (optional) message
         * based on a boolean check
         *
         * @param boolean $condition
         * @param string $message [optional] The message
         */
        public function forward403unless($condition, $message = null)
        {
            if (!$condition)
            {
                $message = ($message === null) ? Context::getI18n()->__("You are not allowed to access this page") : htmlentities($message);
                if (Context::getUser()->isGuest())
                {
                    Context::setMessage('login_message_err', $message);
                    Context::setMessage('login_force_redirect', true);
                    Context::setMessage('login_referer', Context::getRouting()->generate(Context::getRouting()->getCurrentRouteName(), Context::getRequest()->getParameters()));
                    $this->forward(Context::getRouting()->generate('login_page'), 403);
                }
                elseif (Context::getRequest()->isAjaxCall())
                {
                    $this->getResponse()->setHttpStatus(403);
                    throw new \Exception(Context::getI18n()->__("You don't have access to perform this action"));
                }
                else
                {
                    $this->getResponse()->setHttpStatus(403);
                    $this->getResponse()->setTemplate('main/forbidden');
                }
            }
        }

        public function forward403if($condition, $message = null)
        {
            $this->forward403unless(!$condition, $message);
        }

        /**
         * Render a component
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public function renderComponent($template, $params = array())
        {
            echo ActionComponent::includeComponent($template, $params);
            return true;
        }

        /**
         * Returns the HTML output from a component, but doesn't render it
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public static function returnComponentHTML($template, $params = array())
        {
            $current_content = ob_get_clean();
            (Context::isCLI()) ? ob_start() : ob_start('mb_output_handler');
            echo ActionComponent::includeComponent($template, $params);
            $component_content = ob_get_clean();
            (Context::isCLI()) ? ob_start() : ob_start('mb_output_handler');
            echo $current_content;
            return $component_content;
        }

        /**
         * Returns the HTML output from a component, but doesn't render it
         *
         * @param string $template the component name
         * @param array $params component parameters
         *
         * @return boolean
         */
        public function getComponentHTML($template, $params = array())
        {
            return self::returnComponentHTML($template, $params);
        }

    }
