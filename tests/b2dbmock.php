<?php

    namespace b2db;

    class Core
    {

        protected static $tablemocks = array();

        public static function setTableMock($key, $mock) { self::$tablemocks[$key] = $mock; }
        public static function resetMocks() { self::$tablemocks = array(); }

        public static function getTable($classname) {
            if (array_key_exists($classname, self::$tablemocks)) {
                return self::$tablemocks[$classname]; 
            }
            throw new \Exception('Cannot find table ' . $classname);
        }

    }

    class Saveable
    {

        public function __construct() {
        }

        public static function getB2DBTable() {
            $classname = get_called_class();
            return Core::getTable($classname);
        }

        public function save()
        {
            $this->_id = rand(0, 1000000);
            return static::getB2DBTable()->saveObject($this, $this->_id);
        }

    }

    class Table
    {
        
        protected $saved_objects = array();

        protected $lastobject = null;

        public function saveObject($object, $id) {
            $this->saved_objects[$id] = $object;
            $this->lastobject = $id;
        }

        public function selectById($id) {
            if (isset($this->saved_objects[$id])) {
                return $this->saved_objects[$id];
            }

            throw new \Exception('This object does not exist');
        }

        public function getLastMockObject() {
            return $this->selectById($this->lastobject);
        }

        public function getTable() {
            return Core::getTable(get_called_class());
        }

    }
