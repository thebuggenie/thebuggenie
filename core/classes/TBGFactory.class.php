<?php

    /**
     * Static factory class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * Static factory class
     *
     * This factory class manufactures and returns objects without instantiating
     * new objects whenever this is needed.
     *
     * @package thebuggenie
     * @subpackage core
     */
    final class TBGFactory
    {

        public function __call($name, $arguments)
        {
            if (class_exists($name))
            {
                array_unshift($arguments, $name);
                return call_user_func_array(array($this, "manufacture"), $arguments);
            }
            else
            {
                throw new Exception("The class $name doesn't exist");
            }
        }

        public function manufacture($classname, $id, $row = null)
        {
            // Check that the id is valid
            if ((int) $id == 0) throw new Exception('Invalid id');

            // Set up the name for the factory array
            $factory_array_name = "_{$classname}s";

            // Set up the manufactured array if it doesn't exist
            if (!isset($this->$factory_array_name))
            {
                TBGLogging::log("Setting up manufactured array for $classname");
                $this->$factory_array_name = array();
            }

            // If the current id doesn't exist in the manufactured array, manufacture it
            if (!array_key_exists($id, $this->$factory_array_name))
            {
                // Initialize a position for the item in the manufactured array
                $this->{$factory_array_name}[$id] = null;

                try
                {
                    // Add the manufactured item to the manufactured array
                    $this->{$factory_array_name}[$id] = new $classname($id, $row);
                }
                catch (Exception $e)
                {
                    throw $e;
                }
            }
            else
            {
                TBGLogging::log("Using previously manufactured $classname with id $id");
            }

            // Return the item at that id in the manufactured array
            return $this->{$factory_array_name}[$id];
        }

    }
