<?php

    namespace thebuggenie\core\framework;

    /**
     * The TBG event class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * The TBG event class
     *
     * @package thebuggenie
     * @subpackage core
     */
    final class Event
    {

        /**
         * List of registered event listeners
         *
         * @var array
         */
        protected static $_registeredlisteners = array();

        protected $_module = null;

        protected $_identifier = null;

        protected $_return_value = null;

        protected $_subject = null;

        protected $_processed = false;

        protected $_return_list = null;

        protected $_parameters = array();

        /**
         * Register a listener for a spesified trigger
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         * @param mixed $callback_function Which function to call
         */
        public static function listen($module, $identifier, $callback_function)
        {
            self::$_registeredlisteners[$module][$identifier][] = $callback_function;
        }

        /**
         * Remove all listeners from a module+identifier
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         */
        public static function clearListeners($module, $identifier = null)
        {
            if ($identifier !== null)
            {
                self::$_registeredlisteners[$module][$identifier] = array();
            }
            elseif (isset(self::$_registeredlisteners[$module]))
            {
                unset(self::$_registeredlisteners[$module]);
            }
        }

        /**
         * Whether or not there are any listeners to a specific trigger
         *
         * @param string $module The module for which the trigger is active
         * @param string $identifier The trigger identifier
         *
         * @return boolean
         */
        public static function isAnyoneListening($module, $identifier)
        {
            if (isset(self::$_registeredlisteners[$module]) && isset(self::$_registeredlisteners[$module][$identifier]) && !empty(self::$_registeredlisteners[$module][$identifier]))
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Invoke a trigger
         *
         * @param Event $event The event that is triggered
         * @param boolean $return_when_processed (optional) whether to return when processed or continue
         *
         * @return mixed
         */
        protected static function _trigger(Event $event, $return_when_processed = false)
        {
            $module = $event->getModule();
            $identifier = $event->getIdentifier();
            
            Logging::log("Triggering $module - $identifier");
            if (isset(self::$_registeredlisteners[$module][$identifier]))
            {
                foreach (self::$_registeredlisteners[$module][$identifier] as $trigger)
                {
                    try
                    {
                        $cb_string = (is_array($trigger)) ? get_class($trigger[0]).'::'.$trigger[1] : $trigger;
                        Logging::log('Running callback function '.$cb_string);
                        $retval = call_user_func($trigger, $event);
                        if ($return_when_processed && $event->isProcessed())
                        {
                            return true;
                        }
                        Logging::log('done (Running callback function '.$cb_string.')');
                    }
                    catch (\Exception $e)
                    {
                        throw $e;
                    }
                }
            }
            Logging::log("done (Triggering $module - $identifier)");
        }

        /**
         * Create a new event and return it
         *
         * @param string $module
         * @param string $identifier
         * @param mixed $subject
         * @param array $parameters
         * @param array $initial_list
         *
         * @return Event
         */
        public static function createNew($module, $identifier, $subject = null, $parameters = array(), $initial_list = array())
        {
            $event = new Event($module, $identifier, $subject, $parameters, $initial_list);
            return $event;
        }

        /**
         * Create a new event
         *
         * @param string $module
         * @param string $identifier
         * @param mixed $subject
         * @param array $parameters
         * @param array $initial_list
         */
        public function __construct($module, $identifier, $subject = null, $parameters = array(), $initial_list = array())
        {
            $this->_module = $module;
            $this->_identifier = $identifier;
            $this->_subject = $subject;
            $this->_parameters = $parameters;
            $this->_return_list = $initial_list;
        }

        /**
         * Return the event module
         *
         * @return string
         */
        public function getModule()
        {
            return $this->_module;
        }

        /**
         * Return the event identifier
         *
         * @return string
         */
        public function getIdentifier()
        {
            return $this->_identifier;
        }

        /**
         * Return the event subject
         *
         * @return mixed
         */
        public function getSubject()
        {
            return $this->_subject;
        }

        /**
         * Return the event parameters
         *
         * @return array
         */
        public function getParameters()
        {
            return $this->_parameters;
        }

        /**
         * Return a specific event parameter
         *
         * @param string $key
         *
         * @return mixed The parameter
         */
        public function getParameter($key)
        {
            return (array_key_exists($key, $this->_parameters)) ? $this->_parameters[$key] : null;
        }

        /**
         * Invoke a trigger
         *
         * @param array $parameters [optional] Parameters to pass to the registered listeners
         *
         * @return Event
         */
        public function trigger($parameters = null)
        {
            if ($parameters !== null)
            {
                $this->_parameters = $parameters;
            }
            self::_trigger($this, false);

            return $this;
        }

        /**
         * Invoke a trigger and return as soon as it is processed
         *
         * @param array $parameters [optional] Parameters to pass to the registered listeners
         *
         * @return Event
         */
        public function triggerUntilProcessed($parameters = null)
        {
            if ($parameters !== null)
            {
                $this->_parameters = $parameters;
            }
            self::_trigger($this, true);

            return $this;
        }

        /**
         * Mark the event as processed / unprocessed
         *
         * @param boolean $val
         */
        public function setProcessed($val = true)
        {
            $this->_processed = $val;
        }

        /**
         * Return whether or not the event has been processed
         *
         * @return boolean
         */
        public function isProcessed()
        {
            return $this->_processed;
        }

        /**
         * Set the event return value
         *
         * @param mixed $val
         */
        public function setReturnValue($val)
        {
            $this->_return_value = $val;
        }

        /**
         * Get the event return value
         *
         * @return mixed $val
         */
        public function getReturnValue()
        {
            return $this->_return_value;
        }

        /**
         * Add an element to the return list
         *
         * @param mixed $val The value to add to the list
         * @param mixed $key [optional] Specify the key
         */
        public function addToReturnList($val, $key = null)
        {
            ($key !== null) ? $this->_return_list[$key] = $val : $this->_return_list[] = $val;
        }

        /**
         * Get the return list
         *
         * @return array
         */
        public function getReturnList()
        {
            return $this->_return_list;
        }

        /**
         * Return a specific event return value
         *
         * @param mixed $key
         *
         * @return mixed The value
         */
        public function getReturnListValue($key)
        {
            return (is_array($this->_return_list) && array_key_exists($key, $this->_return_list)) ? $this->_return_list[$key] : null;
        }

        /**
         * Check if a specific event return value is set
         *
         * @param mixed $key
         *
         * @return mixed Whether the value is set
         */
        public function hasReturnListValue($key)
        {
            return (bool) (is_array($this->_return_list) && array_key_exists($key, $this->_return_list));
        }

    }