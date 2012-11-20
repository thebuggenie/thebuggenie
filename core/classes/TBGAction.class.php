<?php

	/**
	 * Action class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
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
	class TBGAction extends TBGParameterholder
	{
		
		/**
		 * Forward the user to a specified url
		 * 
		 * @param string $url The URL to forward to
		 * @param integer $code[optional] HTTP status code
		 * @param integer $method[optional] 2 for meta redirect instead of header
		 */
		public function forward($url, $code = 200)
		{
			if (TBGContext::getRequest()->isAjaxCall() || TBGContext::getRequest()->getRequestedFormat() == 'json')
			{
				$this->getResponse()->ajaxResponseText($code, TBGContext::getMessageAndClear('forward'));
			}
			TBGLogging::log("Forwarding to url {$url}");
			
			TBGLogging::log('Triggering header redirect function');
			$this->getResponse()->headerRedirect($url, $code);
		}

		/**
		 * Function that is executed before any actions in an action class
		 * 
		 * @param TBGRequest $request The request object
		 * @param string $action The action that is being triggered
		 */
		public function preExecute(TBGRequest $request, $action)
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
			$this->getResponse()->setTemplate(mb_strtolower($redirect_to) . '.' . TBGContext::getRequest()->getRequestedFormat() . '.php');
			if (method_exists($this, $actionName))
			{
				return $this->$actionName(TBGContext::getRequest());
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
		 * @param array $content The array to render
		 *  
		 * @return boolean
		 */
		public function renderJSON($text = array())
		{
			$this->getResponse()->setContentType('application/json');
			$this->getResponse()->setDecoration(TBGResponse::DECORATE_NONE);
			echo json_encode($text);
			return true;
		}
		
		/**
		 * Return the response object
		 * 
		 * @return TBGResponse
		 */
		protected function getResponse()
		{
			return TBGContext::getResponse();
		}
		
		/**
		 * Return the routing object
		 * 
		 * @return TBGRouting
		 */
		protected function getRouting()
		{
			return TBGContext::getRouting();
		}
		
		/**
		 * Return the i18n object
		 * 
		 * @return TBGI18n
		 */
		protected function getI18n()
		{
			return TBGContext::getI18n();
		}
		
		/**
		 * Return the current logged in user
		 * 
		 * @return TBGUser
		 */
		protected function getUser()
		{
			return TBGContext::getUser();
		}
		
		/**
		 * Sets the response to 404 and shows an error, with an optional message
		 * 
		 * @param string $message[optional] The message
		 */
		public function return404($message = null)
		{
			if (TBGContext::getRequest()->isAjaxCall() || TBGContext::getRequest()->getRequestedFormat() == 'json')
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
			if (!$condition)
			{
				$message = ($message === null) ? TBGContext::getI18n()->__("You are not allowed to access this page") : $message;
				if (TBGContext::getUser()->isGuest())
				{
					TBGContext::setMessage('login_message_err', $message);
					TBGContext::setMessage('login_force_redirect', true);
					TBGContext::setMessage('login_referer', TBGContext::getRouting()->generate(TBGContext::getRouting()->getCurrentRouteName(), TBGContext::getRequest()->getParameters()));
					$this->forward(TBGContext::getRouting()->generate('login_page'), 403);
				}
				else
				{
					$this->getResponse()->setHttpStatus(403);
					$this->getResponse()->setTemplate('main/forbidden', array('message' => $message));
				}
			}
		}
		
		public function forward403if($condition, $message = null)
		{
			$this->forward403unless(!$condition, $message);
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
			echo TBGActionComponent::includeTemplate($template, $params);
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
			echo TBGActionComponent::includeComponent($template, $params);
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
			(TBGContext::isCLI()) ? ob_start() : ob_start('mb_output_handler');
			echo TBGActionComponent::includeComponent($template, $params);
			$component_content = ob_get_clean();
			(TBGContext::isCLI()) ? ob_start() : ob_start('mb_output_handler');
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
			(TBGContext::isCLI()) ? ob_start() : ob_start('mb_output_handler');
			echo TBGActionComponent::includeTemplate($template, $params);
			$template_content = ob_get_clean();
			(TBGContext::isCLI()) ? ob_start() : ob_start('mb_output_handler');
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
		public function getTemplateHTML($template, $params = array())
		{
			return self::returnTemplateHTML($template, $params);
		}
		
	}
