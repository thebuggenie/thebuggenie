<?php

    namespace thebuggenie\core\entities\common;

    /**
     * Changeable item class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Changeable item class
     * 
     * @method boolean revert*() revert*() Reverts a property change
     * @method boolean is*Changed() is*Changed() Checks to see whether a property is changed
     *
     * @package thebuggenie
     * @subpackage main
     */
    class Changeable extends Ownable
    {

        /**
         * List of changed properties
         * 
         * @var array
         */
        protected $_changed_items = array();
        
        /**
         * List of properties that has been changed somewhere else
         * 
         * @var array
         */
        protected $_unmergeable_items = array();
        
        /**
         * Whether the constructor is done merging changes with the original object
         *  
         * @var boolean
         */
        protected $_merged = false;
        
        /**
         * Whether there was any errors mergin
         * 
         * @var boolean
         */
        protected $_merge_error = false;
        
        /**
         * Returns a list of changed items with a specified class
         * 
         * @param string $class The class name
         * 
         * @return array
         */
        public static function getChangedItems($class)
        {
            $retarr = array();
            if (isset($_SESSION['changeableitems'][$class]) && is_array($_SESSION['changeableitems'][$class]))
            {
                foreach ($_SESSION['changeableitems'][$class] as $id => $changes)
                {
                    if ($changes)
                    {
                        try
                        {
                            $retarr[$id] = $class::getB2DBTable()->selectById($id);
                        }
                        catch (\Exception $e)
                        {
                            \thebuggenie\core\framework\Logging::log("Changed item of type {$class}, id {$id} is invalid - unsetting", 'main', \thebuggenie\core\framework\Logging::LEVEL_NOTICE);
                            unset($_SESSION['changeableitems'][$class][$id]);
                        }
                    }
                    else
                    {
                        unset($_SESSION['changeableitems'][$class][$id]);
                    }
                }
            }
            return $retarr;
        }
        
        public function __call($method, $parameters = null)
        {
            if (mb_strpos($method, 'is') == 0 && mb_strpos($method, 'Changed') == mb_strlen($method) - 7)
            {
                return $this->_isPropertyChanged('_' . mb_strtolower(mb_substr($method, 2, mb_strlen($method) - 9)));
            }
            if (mb_strpos($method, 'is') == 0 && mb_strpos($method, 'Merged') == mb_strlen($method) - 6)
            {
                return $this->_isPropertyMerged('_' . mb_strtolower(mb_substr($method, 2, mb_strlen($method) - 8)));
            }
            elseif (mb_strpos($method, 'revert') == 0)
            {
                return $this->_revertPropertyChange('_' . mb_strtolower(mb_substr($method, 6)));
            }
        }
        
        /**
         * Adds a property to list of changed properties
         * 
         * @param string $property The property key that was changed
         * @param mixed $value The new value
         */
        protected function _addChangedProperty($property, $value)
        {
            if ($this->_id)
            {
                if (property_exists($this, $property))
                {
                    if ($this->$property instanceof \thebuggenie\core\entities\common\Identifiable) $this->$property = $this->$property->getID();
                }
                else
                {
                    $this->$property = null;
                }
                if ($value instanceof \thebuggenie\core\entities\common\Identifiable) $value = $value->getID();
                if ($this->$property != $value)
                {
                    if (array_key_exists($property, $this->_changed_items))
                    {
                        if ($this->_changed_items[$property]['original_value'] == $value)
                        {
                            $this->_revertPropertyChange($property);
                        }
                        else
                        {
                            $this->_changed_items[$property]['current_value'] = $value;
                            if ($this->_merged)
                            {
                                $_SESSION['changeableitems'][get_class($this)][$this->getID()][$property]['current_value'] = $value;
                            }
                        }
                    }
                    else
                    {
                        $this->_changed_items[$property] = array('original_value' => $this->$property, 'current_value' => $value);
                        if ($this->_merged)
                        {
                            $_SESSION['changeableitems'][get_class($this)][$this->getID()][$property] = array('original_value' => $this->$property, 'current_value' => $value);
                        }
                    }
                    $this->$property = $value;
                }
            }
            else
            {
                $this->$property = $value;
            }
        }
        
        /**
         * Returns a list of changed properties:
         *         array('property_name' => 'old_value')
         * 
         * @return array
         */
        protected function _getChangedProperties()
        {
            return $this->_changed_items;
        }
        
        /**
         * Returns a single changed propertys original value
         * 
         * @param $property
         * @return unknown_type
         */
        protected function getChangedPropertyOriginal($property)
        {
            if ($this->_isPropertyChanged($property))
            {
                return $this->_changed_items[$property]['original_value'];
            }
            return null;
        }
        
        /**
         * Whether or not this item has any unsaved changes
         * 
         * @return boolean
         */
        public function hasUnsavedChanges()
        {
            return !empty($this->_changed_items);
        }
        
        /**
         * Whether or not this item has any unmergeable changes
         * 
         * @return boolean
         */
        public function hasMergeErrors()
        {
            return !empty($this->_unmergeable_items);
        }
        
        /**
         * Return the number of unsaved changes this item has
         * 
         * @return integer
         */
        public function getNumberOfUnsavedChanges()
        {
            return count($this->_changed_items);
        }
        
        /**
         * Revert all changes made to the item
         * 
         * @return boolean
         */
        public function revertAll()
        {
            foreach ($this->_changed_items as $key => $values)
            {
                $this->_revertPropertyChange($key);
            }
        }
        
        /**
         * Checks to see whether a property has unsaved changes
         * 
         * @param string $property The field key
         * 
         * @return boolean
         */
        protected function _isPropertyChanged($property)
        {
            if (empty($this->_changed_items)) return false;
            return array_key_exists($property, $this->_changed_items);
        }

        /**
         * Checks to see whether a property has unmerged changes
         * 
         * @param string $property The field key
         * 
         * @return boolean
         */
        protected function _isPropertyMerged($property)
        {
            if (empty($this->_changed_items)) return true;
            if (empty($this->_unmergeable_items)) return true;
            return !array_key_exists($property, $this->_unmergeable_items);
        }
        
        /**
         * Reverts an unsaved change made to a property
         * 
         * @param string $property The field key
         * 
         * @return boolean
         */
        protected function _revertPropertyChange($property)
        {
            if (!is_array($this->_changed_items)) return false;
            if (array_key_exists($property, $this->_changed_items))
            {
                $this->$property = $this->_changed_items[$property]['original_value'];
                unset($this->_changed_items[$property]);
                if (isset($_SESSION['changeableitems'][get_class($this)]) && isset($_SESSION['changeableitems'][get_class($this)][$this->getID()]) && is_array($_SESSION['changeableitems'][get_class($this)][$this->getID()]) && array_key_exists($property, $_SESSION['changeableitems'][get_class($this)][$this->getID()]))
                {
                    unset($_SESSION['changeableitems'][get_class($this)][$this->getID()][$property]);
                }
                return true;
            }
            return false;
        }
        
        /**
         * Clears the list of changed items
         */
        protected function _clearChangedProperties()
        {
            $this->_changed_items = array();
            if (isset($_SESSION['changeableitems'][get_class($this)]) && isset($_SESSION['changeableitems'][get_class($this)][$this->getID()]))
            {
                unset($_SESSION['changeableitems'][get_class($this)][$this->getID()]);
            }
        }
    
        protected function _mergeChangedProperties()
        {
            if (isset($_SESSION['changeableitems'][get_class($this)]) && isset($_SESSION['changeableitems'][get_class($this)][$this->getID()]))
            {
                foreach ($_SESSION['changeableitems'][get_class($this)][$this->getID()] as $property => $value)
                {
                    if (property_exists($this, $property))
                    {
                        if ($this->$property instanceof \thebuggenie\core\entities\common\Identifiable)
                        {
                            $unmergeable = (bool) ($this->$property->getID() != $value['original_value']);
                        }
                        else
                        {
                            $unmergeable = (bool) ($this->$property != $value['original_value']);
                        }
                        if ($unmergeable)
                        {
                            $this->_unmergeable_items[$property] = $value['current_value'];
                        }
                        $this->_addChangedProperty($property, $value['current_value']);
                    }
                }
            }
            $this->_merged = true;
        }
        
    }