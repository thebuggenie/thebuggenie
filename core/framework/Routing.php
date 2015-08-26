<?php

    namespace thebuggenie\core\framework;

    use b2db\AnnotationSet,
        b2db\Annotation;

    /**
     * Routing class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage mvc
     */

    /**
     * Routing class
     *
     * @package thebuggenie
     * @subpackage mvc
     */
    class Routing
    {
        protected $routes = array();
        protected $component_override_map = array();
        protected $annotation_listeners = array();
        protected $has_cached_routes = null;
        protected $has_cached_component_override_map = null;
        protected $has_cached_annotation_listeners = null;
        protected $current_route_name = null;
        protected $current_route_module = null;
        protected $current_route_action = null;
        protected $current_route_options = null;

        public function __construct($current_module = null, $current_action = null, $current_name = null)
        {
            if ($current_module !== null) $this->current_route_module = $current_module;
            if ($current_action !== null) $this->current_route_action = $current_action;
            if ($current_name !== null) $this->current_route_name = $current_name;
        }

        public function hasCachedRoutes()
        {
            if ($this->has_cached_routes === null)
            {
                if (Context::isInstallmode())
                {
                    $this->has_cached_routes = false;
                }
                else
                {
                    $this->has_cached_routes = Context::getCache()->has(Cache::KEY_ROUTES_CACHE);
                    if ($this->has_cached_routes)
                    {
                        Logging::log('Routes are cached', 'routing');
                    }
                    else
                    {
                        Logging::log('Routes are not cached', 'routing');
                    }
                }
            }
            return $this->has_cached_routes;
        }

        public function hasCachedComponentOverrideMap()
        {
            if ($this->has_cached_component_override_map === null)
            {
                if (Context::isInstallmode())
                {
                    $this->has_cached_component_override_map = false;
                }
                else
                {
                    $this->has_cached_component_override_map = Context::getCache()->has(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE);
                    if ($this->has_cached_component_override_map)
                    {
                        Logging::log('Component override mappings are cached', 'routing');
                    }
                    else
                    {
                        Logging::log('Component override mappings are not cached', 'routing');
                    }
                }
            }
            return $this->has_cached_component_override_map;
        }

        public function hasCachedAnnotationListeners()
        {
            if ($this->has_cached_annotation_listeners === null)
            {
                if (Context::isInstallmode())
                {
                    $this->has_cached_annotation_listeners = false;
                }
                else
                {
                    $this->has_cached_annotation_listeners = Context::getCache()->has(Cache::KEY_ANNOTATION_LISTENERS_CACHE);
                    if ($this->has_cached_annotation_listeners)
                    {
                        Logging::log('Annotation listeners are cached', 'routing');
                    }
                    else
                    {
                        Logging::log('Annotation listeners are not cached', 'routing');
                    }
                }
            }
            return $this->has_cached_annotation_listeners;
        }

        public function cache()
        {
            Context::getCache()->fileAdd(Cache::KEY_ROUTES_CACHE, $this->getRoutes());
            Context::getCache()->add(Cache::KEY_ROUTES_CACHE, $this->getRoutes());
            Context::getCache()->fileAdd(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, $this->getComponentOverrideMap());
            Context::getCache()->add(Cache::KEY_COMPONENT_OVERRIDE_MAP_CACHE, $this->getComponentOverrideMap());
            Context::getCache()->fileAdd(Cache::KEY_ANNOTATION_LISTENERS_CACHE, $this->getAnnotationListeners());
            Context::getCache()->add(Cache::KEY_ANNOTATION_LISTENERS_CACHE, $this->getAnnotationListeners());
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
         * Set component override map manually (used by cache functions)
         *
         * @param array $component_override_map
         */
        public function setComponentOverrideMap($component_override_map)
        {
            $this->component_override_map = $component_override_map;
        }

        /**
         * Set component override map manually (used by cache functions)
         *
         * @param array $annotation_listeners
         */
        public function setAnnotationListeners($annotation_listeners)
        {
            $this->annotation_listeners = $annotation_listeners;
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

        /**
         * Get all component override mappings
         *
         * @return array
         */
        public function getComponentOverrideMap()
        {
            return $this->component_override_map;
        }

        /**
         * Get all registered annotation module listeners
         *
         * @return array
         */
        public function getAnnotationListeners()
        {
            return $this->annotation_listeners;
        }

        public function hasRoute($route)
        {
            return array_key_exists($route, $this->routes);
        }

        public function hasComponentOverride($component)
        {
            return array_key_exists($component, $this->component_override_map);
        }

        public function getComponentOverride($component)
        {
            return $this->component_override_map[$component];
        }

        public function loadRoutes($module_name, $module_type = null)
        {
            if ($module_type === null) $module_type = (Context::isInternalModule($module_name)) ? 'internal' : 'external';

            $module_routes_filename = (($module_type == 'internal') ? \THEBUGGENIE_INTERNAL_MODULES_PATH : \THEBUGGENIE_MODULES_PATH) . $module_name . DS . 'configuration' . DS . 'routes.yml';
            if (file_exists($module_routes_filename))
            {
                $this->loadYamlRoutes($module_routes_filename, $module_name);
            }

            $this->loadAnnotationRoutes($module_name);
            $this->loadAnnotationListeners($module_name);
        }

        public function loadYamlRoutes($yaml_filename, $module_name = null)
        {
            $routes = \Spyc::YAMLLoad($yaml_filename);

            foreach ($routes as $route => $details)
            {
                if (!isset($details['module'])) $details['module'] = $module_name;
                $this->addYamlRoute($route, $details);
            }
        }

        public function loadAnnotationRoutes($module_name)
        {
            $is_internal = Context::isInternalModule($module_name);
            $namespace = ($is_internal) ? '\\thebuggenie\\core\\modules\\' : '\\thebuggenie\\modules\\';

            // Point the annotated routes to the right module controller
            $this->loadModuleAnnotationRoutes($namespace . $module_name . '\\controllers\\Main', $module_name);

            if (!$is_internal)
            {
                $this->loadModuleOverrideMappings($namespace . $module_name . '\\Components', $module_name);
            }
        }

        public function loadAnnotationListeners($module_name)
        {
            $is_internal = Context::isInternalModule($module_name);
            $namespace = ($is_internal) ? '\\thebuggenie\\core\\modules\\' : '\\thebuggenie\\modules\\';
            $this->loadModuleAnnotationListeners($namespace . $module_name . '\\' . ucfirst($module_name), $module_name);
        }

        protected function loadModuleOverrideMappings($classname, $module)
        {
            if (!class_exists($classname))
                return;

            $reflection = new \ReflectionClass($classname);
            foreach ($reflection->getMethods() as $method)
            {
                $annotationset = new AnnotationSet($method->getDocComment());
                if ($annotationset->hasAnnotation('Overrides'))
                {
                    $overridden_component = $annotationset->getAnnotation('Overrides')->getProperty('name');
                    $component = array('module' => $module, 'method' => substr($method->name, 9));
                    $this->component_override_map[$overridden_component] = $component;
                }
            }
        }

        protected function loadModuleAnnotationListeners($classname, $module)
        {
            if (!class_exists($classname))
                return;

            $reflection = new \ReflectionClass($classname);
            foreach ($reflection->getMethods() as $method)
            {
                $annotationset = new AnnotationSet($method->getDocComment());
                if ($annotationset->hasAnnotation('Listener'))
                {
                    $listener_annotation = $annotationset->getAnnotation('Listener');
                    $event_module = $listener_annotation->getProperty('module');
                    $event_identifier = $listener_annotation->getProperty('identifier');
                    $this->annotation_listeners[] = array($event_module, $event_identifier, $module, $method->name);
                }
            }
        }

        protected function loadModuleAnnotationRoutes($classname, $module)
        {
            if (!class_exists($classname))
                return;

            $internal = Context::isInternalModule($module);
            $reflection = new \ReflectionClass($classname);
            $docblock = $reflection->getDocComment();
            $annotationset = new AnnotationSet($docblock);

            $route_url_prefix = '';
            $route_name_prefix = '';
            $default_route_name_prefix = ($internal) ? '' : $module . '_';
            if ($annotationset->hasAnnotation('Routes'))
            {
                $routes = $annotationset->getAnnotation('Routes');
                if ($routes->hasProperty('url_prefix'))
                {
                    $route_url_prefix = $routes->getProperty('url_prefix');
                }
                if ($routes->hasProperty('name_prefix'))
                {
                    $route_name_prefix = $routes->getProperty('name_prefix', $default_route_name_prefix);
                }
            }
            else
            {
                $route_name_prefix = $default_route_name_prefix;
            }

            foreach ($reflection->getMethods() as $method)
            {
                $annotationset = new AnnotationSet($method->getDocComment());
                if ($annotationset->hasAnnotation('Route'))
                {
                    if (substr($method->name, 0, 3) != 'run')
                    {
                        throw new exceptions\InvalidRouteException('A @Route annotation can only be used on methods prefixed with "run"');
                    }
                    $options = array();
                    $route_annotation = $annotationset->getAnnotation('Route');
                    $action = substr($method->name, 3);
                    $name = $route_name_prefix . (($route_annotation->hasProperty('name')) ? $route_annotation->getProperty('name') : strtolower($action));
                    $route = $route_url_prefix . $route_annotation->getProperty('url');
                    $options['csrf_enabled'] = $annotationset->hasAnnotation('CsrfProtected');
                    $options['anonymous_route'] = $annotationset->hasAnnotation('AnonymousRoute');
                    $http_methods = $route_annotation->getProperty('methods', array());
                    $params = ($annotationset->hasAnnotation('Parameters')) ? $annotationset->getAnnotation('Parameters')->getProperties() : array();

                    if ($annotationset->hasAnnotation('Overrides'))
                    {
                        $name = $annotationset->getAnnotation('Overrides')->getProperty('name');
                        $this->overrideRoute($name, $module, $action);
                    }
                    elseif ($this->hasRoute($name))
                    {
                        throw new exceptions\RoutingException('A route that overrides another route must have an @Override annotation');
                    }
                    else
                    {
                        $this->addRoute($name, $route, $module, $action, $params, $options, $http_methods);
                    }
                }
            }
        }

        public function addYamlRoute($key, $details)
        {
            $name = $key;
            $module = $details['module'];
            $action = $details['action'];
            if (array_key_exists('overrides', $details))
            {
                $this->overrideRoute($name, $module, $details['overrides']);
            }
            else
            {
                $options = array();
                $route = $details['route'];
                $params = (array_key_exists('parameters', $details)) ? $details['parameters'] : array();
                $options['csrf_enabled'] = (array_key_exists('csrf_enabled', $details)) ? $details['csrf_enabled'] : array();
                $options['anonymous_route'] = (array_key_exists('anonymous_route', $details)) ? $details['anonymous_route'] : array();
                $methods = (array_key_exists('methods', $details)) ? $details['methods'] : array();

                $this->addRoute($name, $route, $module, $action, $params, $options, $methods);
            }
        }

        public function overrideRoute($name, $module, $action)
        {
            $this->routes[$name][4] = $module;
            $this->routes[$name][5] = $action;
            $this->routes[$name][9] = true;
        }

        public function addRoute($name, $route, $module, $action, $params = array(), $options = array(), $allowed_methods = array(), $overridden = false)
        {
            if ($this->hasRoute($name))
            {
                if ($this->routes[$name][9]) 
                {
                    Logging::log("Skipping overridden route {$name}", 'routing');
                    return;
                }
            }

            $names = array();
            $names_hash = array();
            $r = null;
            $methods = (!is_array($allowed_methods)) ? array_filter(explode(',', $allowed_methods), function($element) { return trim(strtolower($element)); }) : $allowed_methods;

            if (($route == '') || ($route == '/'))
            {
                $regexp = '/^[\/]*$/';
                $this->routes[$name] = array($route, $regexp, array(), array(), $module, $action, $params, $options, $methods, $overridden);
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
                $parsed = array();

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

                $this->routes[$name] = array($route, $regexp, $names, $names_hash, $module, $action, $params, $options, $methods, $overridden);
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
            Logging::log("URL is '".htmlentities($url, ENT_COMPAT, 'utf-8')."'", 'routing');
            // an URL should start with a '/', mod_rewrite doesn't respect that, but no-mod_rewrite version does.
            if (mb_strlen($url) == 0 || '/' != $url[0])
            {
                $url = '/'.$url;
            }
            if (mb_strlen($url) > 1 && mb_substr($url, -1) == '/')
            {
                $url = mb_substr($url, 0, -1);
            }
            Logging::log("URL is now '".htmlentities($url, ENT_COMPAT, 'utf-8')."'", 'routing');

            // we remove the query string
            if ($pos = mb_strpos($url, '?'))
            {
                $url = mb_substr($url, 0, $pos);
            }

            $break = false;

            // we remove multiple /
            $url = preg_replace('#/+#', '/', $url);
            Logging::log("URL is now '".htmlentities($url, ENT_COMPAT, 'utf-8')."'", 'routing');
            foreach ($this->routes as $route_name => $route)
            {
                $out = array();
                $r = null;
                list($route, $regexp, $names, $names_hash, $module, $action, $params, $options, $allowed_methods, ) = $route;

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
                            $out[$name] = $value;
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
                            $out[$names[$pos]] = $found;
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
                    if (!empty($allowed_methods) && !in_array(Context::getRequest()->getMethod(), $allowed_methods))
                    {
                        $break = false;
                    }

                    if ($break)
                    {
                        // we store route name
                        $this->_setCurrentRouteDetails($route_name, $out['module'], $out['action'], $options);

                        Logging::log('match route ['.$route_name.'] "'.$route.'"', 'routing');

                        break;
                    }
                }
            }

            // no route found
            if (!$break)
            {
                Logging::log('no matching route found', 'routing');

                return null;
            }

            foreach ($out as $key => $val)
            {
                Context::getRequest()->setParameter($key, $val);
            }
            return $out;

        }

        /**
         * Set the route details for the current route
         *
         * @param string $name Current route name
         * @param string $module Current route module
         * @param string $action Current route action
         * @param array $options Current route options
         */
        protected function _setCurrentRouteDetails($name, $module, $action, $options)
        {
            $this->current_route_name = $name;
            $this->current_route_module = $module;
            $this->current_route_action = $action;
            $this->current_route_options = $options;
        }

        /**
         * Set current route's option to value
         *
         * @param string $option_name Option name
         * @param mixed $value Value for option
         */
        protected function _setCurrentRouteOption($option_name, $value)
        {
            if (!is_array($this->current_route_options))
            {
                $this->current_route_options = array();
            }
            $this->current_route_options[$option_name] = $value;
        }

        /**
         * Get value of current route's option
         *
         * @param string $option_name Option name
         * @return mixed Value for option. If option is not set return null.
         */
        protected function _getCurrentRouteOption($option_name)
        {
            if (!is_array($this->current_route_options) || !array_key_exists($option_name, $this->current_route_options))
            {
                return null;
            }
            return $this->current_route_options[$option_name];
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
                $this->getRouteFromUrl(Context::getRequest()->getParameter('url', null, false));
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
                $this->getRouteFromUrl(Context::getRequest()->getParameter('url', null, false));
            }
            return $this->current_route_module;
        }

        /**
         * Set the current route csrf enabled/disabled
         *
         * @param boolean $csrf_enabled
         */
        public function setCurrentRouteCSRFenabled($csrf_enabled = true)
        {
            $this->_setCurrentRouteOption('csrf_enabled', $csrf_enabled);
        }

        /**
         * Returns whether the current route has csrf protection enabled
         *
         * @return boolean
         */
        public function isCurrentRouteCSRFenabled()
        {
            if ($this->_getCurrentRouteOption('csrf_enabled') === null)
            {
                $this->getRouteFromUrl(Context::getRequest()->getParameter('url', null, false));
            }
            return (bool)$this->_getCurrentRouteOption('csrf_enabled');
        }

        /**
         * Set the current route as anonymous route
         *
         * @param boolean $anonymous_route
         */
        public function setCurrentRouteAnonymousRoute($anonymous_route = true)
        {
            $this->_setCurrentRouteOption('anonymous_route', $anonymous_route);
        }

        /**
         * Returns whether the current route has anonymous route enabled
         *
         * @return boolean
         */
        public function isCurrentRouteAnonymousRoute()
        {
            if ($this->_getCurrentRouteOption('anonymous_route') === null)
            {
                $this->getRouteFromUrl(Context::getRequest()->getParameter('url', null, false));
            }
            return (bool)$this->_getCurrentRouteOption('anonymous_route');
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
                $this->getRouteFromUrl(Context::getRequest()->getParameter('url', null, false));
            }
            return $this->current_route_action;
        }

        /**
         * Generate a url based on a route
         *
         * @param string $name The route key
         * @param array $params key=>value pairs of route parameters
         * @param boolean $relative Whether to generate an url relative to web root or an absolute
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
                Logging::log("The route '$name' does not exist", 'routing', Logging::LEVEL_FATAL);
                throw new \Exception("The route '$name' does not exist");
            }

            list($url, , $names, $names_hash, $action, $module, , $options, , ) = $this->routes[$name];

            $defaults = array('action' => $action, 'module' => $module);

            $params = self::arrayDeepMerge($defaults, $params);
            if (array_key_exists('csrf_enabled', $options) && $options['csrf_enabled'])
            {
                $params['csrf_token'] = Context::generateCSRFtoken();
            }

            // all params must be given
            foreach ($names as $tmp)
            {
                if (!isset($params[$tmp]) && !isset($defaults[$tmp]))
                {
                    throw new \Exception(sprintf('Route named "%s" have a mandatory "%s" parameter', $name, $tmp));
                }
            }

            // in PHP 5.5, preg_replace with /e modifier is deprecated; preg_replace_callback is recommended
            $callback = function($matches) use($params)
            {
                return (array_key_exists($matches[1], $params)) ? urlencode($params[$matches[1]]) : $matches[0];
            };

            $real_url = preg_replace_callback('/\:([^\/]+)/', $callback, $url);

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
                return Context::getURLhost() . Context::getStrippedWebroot() . $real_url;
            }
            return Context::getStrippedWebroot() . $real_url;
        }


        // code from php at moechofe dot com (array_merge comment on php.net)
        /*
         * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
         *
         * Like array_merge
         *
         *    arrayDeepMerge() merges the elements of one or more arrays together so
         * that the values of one are appended to the end of the previous one. It
         * returns the resulting array.
         *    If the input arrays have the same string keys, then the later value for
         * that key will overwrite the previous one. If, however, the arrays contain
         * numeric keys, the later value will not overwrite the original value, but
         * will be appended.
         *    If only one array is given and the array is numerically indexed, the keys
         * get reindexed in a continuous way.
         *
         * Different from array_merge
         *    If string keys have arrays for values, these arrays will merge recursively.
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
