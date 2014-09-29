<?php

    namespace thebuggenie\core\entities;

    /**
     * A class with event container storage for extending objects via events
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * A class with event container storage for extending objects via events
     *
     * @package thebuggenie
     * @subpackage core
     */
    abstract class IdentifiableEventContainerClass extends \TBGIdentifiableClass
    {

        /**
         * The storage container
         *
         * @var array
         */
        protected $_storage = array();

        public function _store($module, $name, $value)
        {
            if (!isset($this->_storage[$module])) $this->_storage[$module] = array();
            $this->_storage[$module][$name] = $value;
        }

        public function _retrieve($module, $name)
        {
            return (isset($this->_storage[$module]) && isset($this->_storage[$module][$name])) ? $this->_storage[$module][$name] : null;
        }

        public function _isset($module, $name)
        {
            return isset($this->_storage[$module]) && isset($this->_storage[$module][$name]);
        }

        public function _unset($module, $name)
        {
            if (isset($this->_storage[$module]) && isset($this->_storage[$module][$name]))
                unset($this->_storage[$module][$name]);
        }

        public function __call($name, $arguments)
        {
            $event = \TBGEvent::createNew('core', get_called_class().'::__'.$name, $this, $arguments);
            $event->triggerUntilProcessed();

            return $event->getReturnValue();
        }

    }
