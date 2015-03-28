<?php

    namespace b2db;

    class Core
    {

        protected static $tablemocks = array();
        protected static $debug = false;

        public static function setTableMock($key, $mock) { if (self::$debug) var_dump($key); self::$tablemocks[$key] = $mock; }
        public static function resetMocks() { self::$tablemocks = array(); }

        public static function getTable($classname) {
            if (self::$debug) print(get_called_class().'::getTable('.$classname.')'."\n");
            if (array_key_exists($classname, self::$tablemocks)) {
                return self::$tablemocks[$classname]; 
            }

            throw new \Exception('Cannot find table ' . $classname);
        }

    }

    class Saveable
    {

        protected static $debug = false;

        public function __construct() {
        }

        public static function getB2DBTable() {
            if (self::$debug) print(get_called_class().'::getB2DBTable()'."\n");
            $classname = get_called_class();
            return Core::getTable($classname);
        }

        public function save()
        {
            if (self::$debug) print(get_called_class().'->save()'."\n");
            $this->_id = rand(0, 1000000);
            return Core::getTable(get_called_class())->saveObject($this, $this->_id);
        }

    }

    class Table
    {
        
        protected $saved_objects = array();

        protected $lastobject = null;

        protected static $debug = false;

        public function saveObject($object, $id) {
            if (self::$debug) print(get_called_class().'->saveObject()'."\n");
            if (self::$debug) print(get_class($object)."\n");
            if (self::$debug) print($id."\n");
            $this->saved_objects[$id] = $object;
            $this->lastobject = $id;
        }

        public function selectById($id) {
            if (self::$debug) print(get_called_class().'->selectById()'."\n");
            if (isset($this->saved_objects[$id])) {
                return $this->saved_objects[$id];
            }

            throw new \Exception('This object does not exist');
        }

        public function getLastMockObject() {
            if (self::$debug) print(get_called_class().'->getLastMockObject()'."\n");
            return $this->selectById($this->lastobject);
        }

        public static function getTable() {
            if (self::$debug) print(get_called_class().'::getTable()'."\n");
            return Core::getTable(get_called_class());
        }

        public function countUsers()
        {
            if (self::$debug) print(get_called_class().'->countUsers()'."\n");
            return count($this->saved_objects);
        }

    }
