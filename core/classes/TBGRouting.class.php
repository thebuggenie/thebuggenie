<?php

	/**
	 * Routing class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 3.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage mvc
	 */

	/**
	 * Routing class
	 *
	 * @package thebuggenie
	 * @subpackage mvc
	 */
	class TBGRouting
	{
		protected $routes = array();
		protected $current_route_name = null;
		protected $current_route_module = null;
		protected $current_route_action = null;
		protected $current_route_csrf_enabled = null;

		public function __construct($current_module = null, $current_action = null, $current_name = null)
		{
			if ($current_module !== null) $this->current_route_module = $current_module;
			if ($current_action !== null) $this->current_route_action = $current_action;
			if ($current_name !== null) $this->current_route_name = $current_name;
		}

		/**
		 * Set all routes manually (used by cache functions)
		 * 
		 * @param array $routes
		 */
		public function setRoutes($routes)
		{
			$this->routes = $routes;
		}
		
		/**
		 * Get all the routes
		 * 
		 * @return array
		 */
		public function getRoutes()
		{
			return $this->routes;
		}
		
		public function addRoute($name, $route, $module, $action, $params = array(), $csrf_enabled = false)
		{
			$names = array();
			$names_hash = array();
			$r = null;
			
			if (($route == '') || ($route == '/'))
			{
				$regexp = '/^[\/]*$/';
				$this->routes[$name] = array($route, $regexp, array(), array(), $module, $action, $params, $csrf_enabled);
			}
			else
			{
				$elements = array();
				foreach (explode('/', $route) as $element)
				{
					if (trim($element)) $elements[] = $element;
				}
	
				if (!isset($elements[0])) return false;
	
				// specific suffix for this route?
				// or /$ directory
				$suffix = '';
				if (preg_match('/^(.+)(\.\w*)$/i', $elements[count($elements) - 1], $matches))
				{
					$suffix = ($matches[2][0] == '.') ? $matches[2] : '';
					$elements[count($elements) - 1] = $matches[1];
					$route = '/'.implode('/', $elements);
				}
				else if ($route{mb_strlen($route) - 1} == '/')
				{
					$suffix = '/';
				}
				
				$route = $route.$suffix;
	
				$regexp_suffix = preg_quote($suffix);

				foreach ($elements as $element)
				{
					if (preg_match('/^:(.+)$/', $element, $r))
					{
						$element = $r[1];
	
						// regex is [^\/]+ or the requirement regex
						if (isset($requirements[$element]))
						{
							$regex = $requirements[$element];
							if (0 === mb_strpos($regex, '^'))
							{
								$regex = mb_substr($regex, 1);
							}
							if (mb_strlen($regex) - 1 === mb_strpos($regex, '$'))
							{
								$regex = mb_substr($regex, 0, -1);
							}
						}
						else
						{
							$regex = '[^\/]+';
						}
	
						$parsed[] = '(?:\/('.$regex.'))?';
						$names[] = $element;
						$names_hash[$element] = 1;
					}
					elseif (preg_match('/^\*$/', $element, $r))
					{
						$parsed[] = '(?:\/(.*))?';
					}
					else
					{
						$parsed[] = '/'.$element;
					}
				}
				$regexp = '#^'.join('', $parsed).$regexp_suffix.'$#';
					
				$this->routes[$name] = array($route, $regexp, $names, $names_hash, $module, $action, $params, $csrf_enabled);
			}
		}

		/**
		 * Get route details from a given url
		 *
		 * @param string $url The url to retrieve details from
		 * 
		 * @return array Route details
		 */
		public function getRouteFromUrl($url)
		{
			TBGLogging::log('URL is ' . $url, 'routing');
			// an URL should start with a '/', mod_rewrite doesn't respect that, but no-mod_rewrite version does.
			if (mb_strlen($url) == 0 || '/' != $url[0])
			{
				$url = '/'.$url;
			}
			TBGLogging::log('URL is now ' . $url, 'routing');
	
			// we remove the query string
			if ($pos = mb_strpos($url, '?'))
			{
				$url = mb_substr($url, 0, $pos);
			}
	
			$break = false;
			
			// we remove multiple /
			$url = preg_replace('#/+#', '/', $url);
			TBGLogging::log('URL is now ' . $url, 'routing');
			foreach ($this->routes as $route_name => $route)
			{
				$out = array();
				$r = null;
	
				list($route, $regexp, $names, $names_hash, $module, $action, $params, $csrf_enabled) = $route;
	
				$break = false;
				
				if (preg_match($regexp, $url, $r))
				{
					$break = true;
	
					// remove the first element, which is the url
					array_shift($r);
	
					// hack, pre-fill the default route names
					foreach ($names as $name)
					{
						$out[$name] = null;
					}
	
					// parameters
					$params['module'] = $module;
					$params['action'] = $action;
					foreach ($params as $name => $value)
					{
						if (preg_match('#[a-z_\-]#i', $name))
						{
							$out[$name] = urldecode($value);
						}
						else
						{
							$out[$value] = true;
						}
					}
	
					$pos = 0;
					foreach ($r as $found)
					{
						// if $found is a named url element (i.e. ':action')
						if (isset($names[$pos]))
						{
							$out[$names[$pos]] = urldecode($found);
						}
						// unnamed elements go in as 'pass'
						else
						{
							$pass = explode('/', $found);
							$found = '';
							for ($i = 0, $max = count($pass); $i < $max; $i += 2)
							{
								if (!isset($pass[$i + 1])) continue;
	
								$found .= $pass[$i].'='.$pass[$i + 1].'&';
							}
	
							parse_str($found, $pass);
	
							foreach ($pass as $key => $value)
							{
								// we add this parameters if not in conflict with named url element (i.e. ':action')
								if (!isset($names_hash[$key]))
								{
									$out[$key] = $value;
								}
							}
						}
						$pos++;
					}
	
					// we must have found all :var stuffs in url? except if default values exists
					foreach ($names as $name)
					{
						if ($out[$name] == null)
						{
							$break = false;
						}
					}
	
					if ($break)
					{
						// we store route name
						$this->_setCurrentRouteDetails($route_name, $out['module'], $out['action'], $csrf_enabled);
	
						TBGLogging::log('match route ['.$route_name.'] "'.$route.'"', 'routing');
	
						break;
					}
				}
			}
	
			// no route found
			if (!$break)
			{
				TBGLogging::log('no matching route found', 'routing');
	
				return null;
			}
	
			foreach ($out as $key => $val)
			{
				TBGContext::getRequest()->setParameter($key, $val);
			}
			return $out;

		}

		/**
		 * Set the route details for the current route
		 * 
		 * @param string $name Current route name
		 * @param string $module Current route module
		 * @param string $action Current route action
		 */
		protected function _setCurrentRouteDetails($name, $module, $action, $csrf_enabled)
		{
			$this->current_route_name = $name;
			$this->current_route_module = $module;
			$this->current_route_action = $action;
			$this->current_route_csrf_enabled = $csrf_enabled;
		}

		/**
		 * Set the current route name
		 *
		 * @param string $current_route_name
		 */
		public function setCurrentRouteName($current_route_name)
		{
			$this->current_route_name = $current_route_name;
		}

		/**
		 * Returns the current route name
		 * 
		 * @return string The current route name
		 */
		public function getCurrentRouteName()
		{
			if ($this->current_route_name === null)
			{
				$this->getRouteFromUrl(TBGContext::getRequest()->getParameter('url', null, false));
			}
			return $this->current_route_name;
		}

		/**
		 * Set the current route module
		 *
		 * @param string $current_route_module
		 */
		public function setCurrentRouteModule($current_route_module)
		{
			$this->current_route_module = $current_route_module;
		}

		/**
		 * Returns the current route module
		 *
		 * @return string The current route module
		 */
		public function getCurrentRouteModule()
		{
			if ($this->current_route_module === null)
			{
				$this->getRouteFromUrl(TBGContext::getRequest()->getParameter('url', null, false));
			}
			return $this->current_route_module;
		}

		/**
		 * Set the current route csrf enabled/disabled
		 *
		 * @param boolean $current_route_module
		 */
		public function setCurrentRouteCSRFenabled($csrf_enabled = true)
		{
			$this->current_route_csrf_enabled = $csrf_enabled;
		}

		/**
		 * Returns whether the current route has csrf protection enabled
		 *
		 * @return boolean
		 */
		public function isCurrentRouteCSRFenabled()
		{
			if ($this->current_route_csrf_enabled === null)
			{
				$this->getRouteFromUrl(TBGContext::getRequest()->getParameter('url', null, false));
			}
			return $this->current_route_csrf_enabled;
		}

		/**
		 * Set the current route action
		 *
		 * @param string $current_route_action
		 */
		public function setCurrentRouteAction($current_route_action)
		{
			$this->current_route_action = $current_route_action;
		}

		/**
		 * Returns the current route action
		 *
		 * @return string The current route action
		 */
		public function getCurrentRouteAction()
		{
			if ($this->current_route_module === null)
			{
				$this->getRouteFromUrl(TBGContext::getRequest()->getParameter('url', null, false));
			}
			return $this->current_route_action;
		}
		
		/**
		 * Generate a url based on a route
		 * 
		 * @param string $name The route key
		 * @param array $params key=>value pairs of route parameters
		 * 
		 * @return string
		 */
		public function generate($name, $params = array(), $relative = true, $querydiv = '/', $divider = '/', $equals = '/')
		{
			if (mb_substr($name, 0, 1) == '@')
			{
				$name = mb_substr($name, 1);
				$details = explode('?', $name);
				$name = array_shift($details);
				if (count($details))
				{
					$param_details = array_shift($details);
					$param_details = explode('&', $param_details);
					foreach ($param_details as $detail)
					{
						$param_detail = explode('=', $detail);
						if (count($param_detail) > 1)
						$params[$param_detail[0]] = $param_detail[1];
					}
				}
			}
			if (!isset($this->routes[$name]))
			{
				TBGLogging::log("The route '$name' does not exist", 'routing', TBGLogging::LEVEL_FATAL);
				throw new Exception("The route '$name' does not exist");
			}

			list($url, $regexp, $names, $names_hash, $action, $module, $defaults, $csrf_enabled) = $this->routes[$name];

			$defaults = array('action' => $action, 'module' => $module);
			
			// all params must be given
			foreach ($names as $tmp)
			{
				if (!isset($params[$tmp]) && !isset($defaults[$tmp]))
				{
					throw new Exception(sprintf('Route named "%s" have a mandatory "%s" parameter', $name, $tmp));
				}
			}
	
			$params = self::arrayDeepMerge($defaults, $params);
			if ($csrf_enabled)
			{
				$params['csrf_token'] = TBGContext::generateCSRFtoken();
			}

			$real_url = preg_replace('/\:([^\/]+)/e', 'urlencode($params["\\1"])', $url);
	
			// we add all other params if *
			if (mb_strpos($real_url, '*'))
			{
				$tmp = array();
				foreach ($params as $key => $value)
				{
					if (isset($names_hash[$key]) || isset($defaults[$key])) continue;
	
					if (is_array($value))
					{
						foreach ($value as $k => $v)
						{
							if (is_array($v))
							{
								foreach ($v as $vk => $vv)
								{
									if (is_array($vv))
									{
										foreach ($vv as $vvk => $vvv)
										{
											$tmp[] = "{$key}[{$k}][{$vk}][{$vvk}]".$equals.urlencode($vvv);
										}
									}
									else
									{
										$tmp[] = "{$key}[{$k}][{$vk}]".$equals.urlencode($vv);
									}
								}
							}
							else
							{
								$tmp[] = "{$key}[{$k}]".$equals.urlencode($v);
							}
						}
					}
					else
					{
						$tmp[] = urlencode($key).$equals.urlencode($value);
					}
				}
				$tmp = implode($divider, $tmp);
				if (mb_strlen($tmp) > 0)
				{
					$tmp = $querydiv.$tmp;
				}
				$real_url = preg_replace('/\/\*(\/|$)/', "$tmp$1", $real_url);
			}
	
			// strip off last divider character
			if (mb_strlen($real_url) > 1)
			{
				$real_url = rtrim($real_url, $divider);
			}
			if (!$relative)
			{
				return TBGContext::getURLhost() . TBGContext::getStrippedTBGPath() . $real_url;
			}
			return TBGContext::getStrippedTBGPath() . $real_url;
		}
		
		
		// code from php at moechofe dot com (array_merge comment on php.net)
		/*
		 * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
		 *
		 * Like array_merge
		 *
		 *	arrayDeepMerge() merges the elements of one or more arrays together so
		 * that the values of one are appended to the end of the previous one. It
		 * returns the resulting array.
		 *	If the input arrays have the same string keys, then the later value for
		 * that key will overwrite the previous one. If, however, the arrays contain
		 * numeric keys, the later value will not overwrite the original value, but
		 * will be appended.
		 *	If only one array is given and the array is numerically indexed, the keys
		 * get reindexed in a continuous way.
		 *
		 * Different from array_merge
		 *	If string keys have arrays for values, these arrays will merge recursively.
		 */
		public static function arrayDeepMerge()
		{
			switch (func_num_args())
			{
				case 0:
					return false;
				case 1:
					return func_get_arg(0);
				case 2:
					$args = func_get_args();
					$args[2] = array();
					if (is_array($args[0]) && is_array($args[1]))
					{
						foreach (array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $key)
						{
							$isKey0 = array_key_exists($key, $args[0]);
							$isKey1 = array_key_exists($key, $args[1]);
							if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key]))
							{
								$args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
							}
							else if ($isKey0 && $isKey1)
							{
								$args[2][$key] = $args[1][$key];
							}
							else if (!$isKey1)
							{
								$args[2][$key] = $args[0][$key];
							}
							else if (!$isKey0)
							{
								$args[2][$key] = $args[1][$key];
							}
						}
						return $args[2];
					}
					else
					{
						return $args[1];
					}
				default :
					$args = func_get_args();
					$args[1] = sfToolkit::arrayDeepMerge($args[0], $args[1]);
					array_shift($args);
					return call_user_func_array(array('self', 'arrayDeepMerge'), $args);
					break;
			}
		}
		
	}
