<?php

	/**
	 * Action component class used in the MVC part of the framework
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
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
	class BUGSactioncomponent extends BUGSparameterholder
	{
		
		protected static function getModuleAndTemplate($template)
		{
			if ($separator_pos = strpos($template, '/'))
			{
				$module = substr($template, 0, $separator_pos);
				$template = substr($template, $separator_pos + 1);
			}
			else
			{
				$module = BUGScontext::getRouting()->getCurrentRouteModule();
			}
			return array('module' => $module, 'file' => $template);
		}
		
		public static function includeComponent($template, $params)
		{
			BUGScontext::loadLibrary('ui');
			$module_file = self::getModuleAndTemplate($template);
			$template_name = BUGScontext::getIncludePath() . "modules/{$module_file['module']}/templates/_{$module_file['file']}.inc.php";
			$actionClassName = $module_file['module'].'ActionComponents';
			$actionToRunName = 'component' . ucfirst($module_file['file']);
			if (!class_exists($actionClassName))
			{
				BUGScontext::addClasspath(BUGScontext::getIncludePath() . 'modules/' . $module_file['module'] . '/classes/');
			}
			$actionClass = new $actionClassName();
			if (!method_exists($actionClass, $actionToRunName))
			{
				throw new BUGSComponentNotFoundException("The component action {$actionToRunName} was not found in the {$actionClassName} class");
			}
			if (!file_exists($template_name))
			{
				throw new BUGSTemplateNotFoundException("The template file for the {$module_file['file']} component was not found in the {$module_file['module']} module");
			}
				
			foreach ($params as $key => $val)
			{
				$actionClass->$key = $val;
			}
			$actionClass->$actionToRunName();
			self::presentTemplate($template_name, $actionClass->getParameterHolder());
		}
		
		public static function includeTemplate($template, $params)
		{
			BUGScontext::loadLibrary('ui');
			$module_file = self::getModuleAndTemplate($template);
			$template_name = BUGScontext::getIncludePath() . "modules/{$module_file['module']}/templates/_{$module_file['file']}.inc.php";
			if (!file_exists($template_name))
			{
				throw new BUGSTemplateNotFoundException("The template file <b>_{$module_file['file']}.inc.php</b> cannot be found in the template directory for module \"" . BUGScontext::getRouting()->getCurrentRouteModule() . '"');
			}
			self::presentTemplate($template_name, $params);
		}
		
		protected static function presentTemplate($template_file, $params)
		{
			foreach ($params as $key => $val)
			{
				$$key = $val;
			}
			
			/**
			 * @global BUGSrequest The request object
			 */
			$bugs_request = BUGScontext::getRequest();
			
			/**
			 * @global BUGSresponse The response object
			 */
			$bugs_response = BUGScontext::getResponse();
			
			/**
			 * @global BUGSuser The user object
			 */
			$bugs_user = BUGScontext::getUser();
			
			require $template_file;
		}

		protected function getResponse()
		{
			return BUGScontext::getResponse();
		}
		
	}
