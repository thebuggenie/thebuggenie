<?php

	/**
	 * Action class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Action class used in the MVC part of the framework
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class BUGSaction extends BUGSparameterholder
	{
		
		const FORWARD_HEADER = 1;
		const FORWARD_META = 2;
		
		/**
		 * Forward the user to a specified url
		 * 
		 * @param string $url The URL to forward to
		 * @param integer $code[optional] HTTP status code
		 * @param integer $method[optional] 2 for meta redirect instead of header
		 */
		public function forward($url, $code = 200, $method = null)
		{
			BUGSlogging::log("Forwarding to url {$url}");
			$method = ($method === null) ? self::FORWARD_HEADER : $method;
			
			if ($method == self::FORWARD_HEADER)
			{
				BUGSlogging::log('Triggering header redirect function');
				$this->getResponse()->headerRedirect($url, $code);
			}
			elseif ($method == self::FORWARD_META)
			{
				throw new Exception('Not implemented yet');
				//self::metaForward($url, $code);
			}
		}

		/**
		 * Function that is executed before any actions in an action class
		 * 
		 * @param BUGSrequest $request The request object
		 * @param string $action The action that is being triggered
		 */
		public function preExecute($request, $action)
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
			$this->getResponse()->setTemplate(strtolower($redirect_to) . '.php');
			if (method_exists($this, $actionName))
			{
				return $this->$actionName(BUGScontext::getRequest());
			}
			throw new Exception("The action \"{$actionName}\" does not exist in ".get_class($this));
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
		 * @param string $text The array / text / object to render
		 *  
		 * @return boolean
		 */
		public function renderJSON($text)
		{
			$this->getResponse()->setContentType('application/json');
			echo json_encode($text);
			return true;
		}
		
		/**
		 * Return the response object
		 * 
		 * @return BUGSresponse
		 */
		protected function getResponse()
		{
			return BUGScontext::getResponse();
		}
		
		/**
		 * Sets the response to 404 and shows an error, with an optional message
		 * 
		 * @param string $message[optional] The message
		 */
		public function return404($message = null)
		{
			$this->message = $message;
			$this->getResponse()->setHttpStatus(404);
			BUGScontext::
			$this->getResponse()->setTemplate('main/notfound.php');
			return;
		}
		
		/**
		 * Forward the user with HTTP status code 403 and an (optional) message
		 * 
		 * @param string $message[optional] The message
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
		 * @param string $message[optional] The message
		 */
		public function forward403unless($condition, $message = null)
		{
			$message = ($message === null) ? BUGScontext::getI18n()->__("You don't have access to this page") : $message;
			BUGScontext::setMessage('forward', $message);
			
			if (!$condition)
			{
				$this->forward(BUGScontext::getRouting()->generate('login'), 403);
			}
		}
		
		/**
		 * Render a template
		 * 
		 * @param string $template the template name
		 * @param array $params template parameters
		 * 
		 * @return boolean 
		 */
		public function renderTemplate($template, $params = array())
		{
			echo BUGSactioncomponent::includeTemplate($template, $params);
			return true;
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
			echo BUGSactioncomponent::includeComponent($template, $params);
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
		public function getComponentHTML($template, $params = array())
		{
			$current_content = ob_get_clean();
			ob_start();
			$this->renderComponent($template, $params);
			$component_content = ob_get_clean();
			ob_start();
			echo $current_content;
			return $component_content;
		}

		/**
		 * Returns the HTML output from a template, but doesn't render it
		 * 
		 * @param string $template the template name
		 * @param array $params template parameters
		 * 
		 * @return boolean
		 */
		public function getTemplateHTML($template, $params = array())
		{
			$current_content = ob_get_clean();
			ob_start();
			$this->renderTemplate($template, $params);
			$template_content = ob_get_clean();
			ob_start();
			echo $current_content;
			return $template_content;
		}
		
		/**
		 * Returns the HTML output from a template, but doesn't render it
		 *
		 * @param string $template the template name
		 * @param array $params template parameters
		 *
		 * @return boolean
		 */
		public static function returnTemplateHTML($template, $params = array())
		{
			$current_content = ob_get_clean();
			ob_start();
			echo BUGSactioncomponent::includeTemplate($template, $params);
			$template_content = ob_get_clean();
			ob_start();
			echo $current_content;
			return $template_content;
		}

	}
