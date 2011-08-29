<?php

	/**
	 * Action component class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Action component class used in the MVC part of the framework
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class TBGActionComponent extends TBGParameterholder
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
			if ($separator_pos = mb_strpos($template, '/'))
			{
				$module = mb_substr($template, 0, $separator_pos);
				$template = mb_substr($template, $separator_pos + 1);
			}
			else
			{
				$module = TBGContext::getRouting()->getCurrentRouteModule();
			}
			return array('module' => $module, 'file' => $template);
		}

		protected static function getFinalTemplateName($template, $module_file = null)
		{
			if (!isset($module_file)) $module_file = self::getModuleAndTemplate($template);
			if (($template_name = TBGContext::getI18n()->hasTranslatedTemplate($template, true)) === false)
			{
				$template_name = THEBUGGENIE_MODULES_PATH . $module_file['module'] . DS . 'templates' . DS . "_{$module_file['file']}.inc.php";
			}
			return $template_name;
		}

		protected static function _doesTemplateExist($template, $throw_exceptions = true, $module_file = null)
		{
			if (!isset($module_file)) $module_file = self::getModuleAndTemplate($template);
			$template_name = self::getFinalTemplateName($template, $module_file);
			if (!file_exists($template_name))
			{
				if (!$throw_exceptions) return false;
				throw new TBGTemplateNotFoundException("The template file <b>_{$module_file['file']}.inc.php</b> cannot be found in the template directory for module \"" . TBGContext::getRouting()->getCurrentRouteModule() . '"');
			}
			if (!$throw_exceptions) return true;

			return $template_name;
		}

		protected static function _getComponentDetails($template)
		{
			$module_file = self::getModuleAndTemplate($template);
			$actionClassName = $module_file['module'].'ActionComponents';
			$actionToRunName = 'component' . ucfirst($module_file['file']);

			return array($module_file, $actionClassName, $actionToRunName);
		}

		protected static function _doesComponentExist($template, $throw_exceptions = true)
		{
			list ($module_file, $actionClassName, $actionToRunName) = self::_getComponentDetails($template);
			if (!class_exists($actionClassName))
			{
				TBGContext::addAutoloaderClassPath(THEBUGGENIE_MODULES_PATH . $module_file['module'] . DS . 'classes' . DS);
			}
			if (!class_exists($actionClassName))
			{
				if (!$throw_exceptions) return false;
				throw new TBGComponentNotFoundException('The component class ' . $actionClassName . ' could not be found');
			}
			$actionClass = new $actionClassName();
			if (!method_exists($actionClass, $actionToRunName))
			{
				if (!$throw_exceptions) return false;
				throw new TBGComponentNotFoundException("The component action {$actionToRunName} was not found in the {$actionClassName} class");
			}
			$retval = self::_doesTemplateExist($template, $throw_exceptions, $module_file);
			if (!$throw_exceptions) return $retval;

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
			$debug = TBGContext::isDebugMode();
			if ($debug)
			{
				$time = explode(' ', microtime());
				$pretime = $time[1] + $time[0];
			}
			list ($template_name, $actionClass, $actionToRunName) = self::_doesComponentExist($template);

			foreach ($params as $key => $val)
			{
				$actionClass->$key = $val;
			}
			$actionClass->$actionToRunName();
			self::presentTemplate($template_name, $actionClass->getParameterHolder());
			if ($debug)
			{
				$time = explode(' ', microtime());
				$posttime = $time[1] + $time[0];
				TBGContext::visitPartial($template, $posttime - $pretime);
			}
		}

		/**
		 * Include a template from a module
		 *
		 * @param string $template
		 * @param array $params
		 */
		public static function includeTemplate($template, $params = array())
		{
			$debug = TBGContext::isDebugMode();
			if ($debug)
			{
				$time = explode(' ', microtime());
				$pretime = $time[1] + $time[0];
			}
			$template_name = self::getFinalTemplateName($template);
			self::presentTemplate($template_name, $params);
			if ($debug)
			{
				$time = explode(' ', microtime());
				$posttime = $time[1] + $time[0];
				TBGContext::visitPartial($template, $posttime - $pretime);
			}
		}

		/**
		 * Present a template
		 * @param string $template_file
		 * @param array $params
		 */
		public static function presentTemplate($template_file, $params = array())
		{
			TBGLogging::log("configuring template variables for template {$template_file}");
			foreach ($params as $key => $val)
			{
				$$key = $val;
			}
			if (array_key_exists('key', $params)) $key = $params['key'];
			if (array_key_exists('val', $params)) $val = $params['val'];
			
			/**
			 * @global TBGRequest The request object
			 */
			$tbg_request = TBGContext::getRequest();
			
			/**
			 * @global TBGResponse The response object
			 */
			$tbg_response = TBGContext::getResponse();
			
			/**
			 * @global TBGRequest The request object
			 */
			$tbg_routing = TBGContext::getRouting();
			
			/**
			 * @global TBGUser The user object
			 */
			$tbg_user = TBGContext::getUser();

			TBGContext::loadLibrary('common');
			TBGContext::loadLibrary('ui');

			TBGLogging::log('rendering template output');
			require $template_file;
		}

		/**
		 * Returns the response object
		 *
		 * @return TBGResponse
		 */
		protected function getResponse()
		{
			return TBGContext::getResponse();
		}
		
	}
