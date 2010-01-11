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
	class TBGActionComponent extends TBGParameterholder
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
				$module = TBGContext::getRouting()->getCurrentRouteModule();
			}
			return array('module' => $module, 'file' => $template);
		}
		
		public static function includeComponent($template, $params = array())
		{
			TBGContext::loadLibrary('ui');
			$module_file = self::getModuleAndTemplate($template);
			$template_name = TBGContext::getIncludePath() . "modules/{$module_file['module']}/templates/_{$module_file['file']}.inc.php";
			$actionClassName = $module_file['module'].'ActionComponents';
			$actionToRunName = 'component' . ucfirst($module_file['file']);
			if (!class_exists($actionClassName))
			{
				TBGContext::addClasspath(TBGContext::getIncludePath() . 'modules/' . $module_file['module'] . '/classes/');
			}
			if (!class_exists($actionClassName))
			{
				throw new TBGComponentNotFoundException('The component class ' . $actionClassName . ' could not be found');
			}
			$actionClass = new $actionClassName();
			if (!method_exists($actionClass, $actionToRunName))
			{
				throw new TBGComponentNotFoundException("The component action {$actionToRunName} was not found in the {$actionClassName} class");
			}
			if (!file_exists($template_name))
			{
				throw new TBGTemplateNotFoundException("The template file for the {$module_file['file']} component was not found in the {$module_file['module']} module");
			}
				
			foreach ($params as $key => $val)
			{
				$actionClass->$key = $val;
			}
			$actionClass->$actionToRunName();
			self::presentTemplate($template_name, $actionClass->getParameterHolder());
		}
		
		public static function includeTemplate($template, $params = array())
		{
			TBGContext::loadLibrary('ui');
			$module_file = self::getModuleAndTemplate($template);
			if (($template_name = TBGContext::getI18n()->hasTranslatedTemplate($template, true)) === false)
			{
				$template_name = TBGContext::getIncludePath() . "modules/{$module_file['module']}/templates/_{$module_file['file']}.inc.php";
			}
			if (!file_exists($template_name))
			{
				throw new TBGTemplateNotFoundException("The template file <b>_{$module_file['file']}.inc.php</b> cannot be found in the template directory for module \"" . TBGContext::getRouting()->getCurrentRouteModule() . '"');
			}
			self::presentTemplate($template_name, $params);
		}
		
		protected static function presentTemplate($template_file, $params = array())
		{
			foreach ($params as $key => $val)
			{
				$$key = $val;
			}
			if (array_key_exists('key', $params)) $key = $params['key'];
			if (array_key_exists('val', $params)) $val = $params['val'];
			
			/**
			 * @global TBGRequest The request object
			 */
			$bugs_request = TBGContext::getRequest();
			
			/**
			 * @global TBGResponse The response object
			 */
			$bugs_response = TBGContext::getResponse();
			
			/**
			 * @global TBGUser The user object
			 */
			$bugs_user = TBGContext::getUser();
			
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
