<?php

	namespace b2db;
	
	/**
	 * B2DB Saveable class, active record implementation for B2DB
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * B2DB Saveable class, active record implementation for B2DB
	 * Can be implemented by objects to allow them to be passed to a B2DB table 
	 * class for saving
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Saveable
	{

		/**
		 * Return the associated B2DBTable for this class
		 * 
		 * @return Table
		 */
		public static function getB2DBTable()
		{
			$b2dbtablename = Core::getCachedB2DBTableClass(\get_called_class());
			return $b2dbtablename::getTable();
		}

		protected function _b2dbLazycount($property)
		{
			$relation_details = Core::getCachedEntityRelationDetails(\get_class($this), $property);
			if (array_key_exists('manytomany', $relation_details) && $relation_details['manytomany']) {
				$table = $relation_details['joinclass'];
			} else {
				$table = Core::getCachedB2DBTableClass($relation_details['class']);
			}
			$count = $table::getTable()->countForeignItems($this, $relation_details);
			return $count;
		}

		protected function _b2dbLazyload($property)
		{
			$relation_details = Core::getCachedEntityRelationDetails(\get_class($this), $property);
			if ($relation_details['collection']) {
				if (array_key_exists('manytomany', $relation_details) && $relation_details['manytomany']) {
					$table = $relation_details['joinclass'];
				} elseif (array_key_exists('class', $relation_details) && $relation_details['class']) {
					$table = Core::getCachedB2DBTableClass($relation_details['class']);
				} elseif (array_key_exists('joinclass', $relation_details) && $relation_details['joinclass']) {
					$table = $relation_details['joinclass'];
				}
				$items = $table::getTable()->getForeignItems($this, $relation_details);
				$value = ($items !== null) ? $items : array();
				$this->$property = $value;
			} elseif (is_numeric($this->$property) && $this->$property > 0) {
				if ($relation_details && \class_exists($relation_details['class']))
				{
					$classname = $relation_details['class'];
					try
					{
						$this->$property = new $classname($this->$property);
					}
					catch (\Exception $e)
					{
						$this->$property = null;
					}
				}
				else
				{
					throw new \Exception("Unknown class definition for property {$property} in class ".\get_class($this));
				}
			}
			return $this->$property;
		}
		
		protected function _populatePropertiesFromRow(\b2db\Row $row, $traverse = true, $foreign_key = null)
		{
			$table = self::getB2DBTable();
			$table_name = $table->getB2DBName();
			$id_column = $table->getIdColumn();
			$this_class = \get_class($this);
			foreach ($table->getColumns() as $column)
			{
				if ($column['name'] == $id_column) continue;
				$property_name = $column['property']; //Core::getCachedColumnClassProperty($this_class, $column['name']);
				$property_type = $column['type'];
				if (!property_exists($this, $property_name))
				{
					throw new \Exception("Could not find class property {$property_name} in class ".$this_class.". The class must have all properties from the corresponding B2DB table class available");
				}
				if ($traverse && in_array($column['name'], $table->getForeignColumns()))
				{
					if ($row->get($column['name']) > 0)
					{
						$relation_details = Core::getCachedEntityRelationDetails($this_class, $property_name);
						if ($relation_details && class_exists($relation_details['class']))
						{
							$b2dbtablename = Core::getCachedB2DBTableClass($relation_details['class']);
							$b2dbtable = $b2dbtablename::getTable();
							foreach ($row->getJoinedTables() as $join_details)
							{
								if ($join_details['original_column'] == $column['name'])
								{
									$property_type = 'class';
									break;
								}
							}
						}
					}
				}
				switch ($property_type)
				{
					case 'class':
						$value = (int) $row->get($column['name']);
						$this->$property_name = new $type_name($value, $row, false, $column['name']);
						break;
					case 'boolean':
						$this->$property_name = (boolean) $row->get($column['name'], $foreign_key);
						break;
					case 'integer':
						$this->$property_name = (integer) $row->get($column['name'], $foreign_key);
						break;
					case 'float':
						$this->$property_name = floatval($row->get($column['name'], $foreign_key));
						break;
					case 'text':
					case 'varchar':
						$this->$property_name = (string) $row->get($column['name'], $foreign_key);
						break;
					default:
						$this->$property_name = $row->get($column['name'], $foreign_key);
				}
			}
		}
		
		protected function _preInitialize() {}
		
		protected function _construct(\b2db\Row $row, $foreign_key = null) {}

		protected function _clone() {}
		
		protected function _preSave($is_new) {}
		
		protected function _postSave($is_new) {}
		
		protected function _preDelete() {}
		
		protected function _postDelete() {}

		public function getB2DBSaveablePropertyValue($property_name)
		{
//			$column = explode('.', $column);
//			if (!array_key_exists(1, $column)) {
//				throw new \Exception("Could not find class property");
//			}
			if (!property_exists($this, $property_name))
			{
				throw new \Exception("Could not find class property '{$property_name}' in class ".get_class($this).". The class must have all properties from the corresponding B2DB table class available");
			}
			if (is_object($this->$property_name))
			{
				return (int) $this->$property_name->getID();
			}
			elseif (!is_object($this->$property_name))
			{
				return $this->$property_name;
			}
		}

		public function getB2DBID()
		{
			$column = self::getB2DBTable()->getIdColumn();
			$property = explode('.', $column);
			$property_name = "_{$property[1]}";
			return $this->$property_name;
		}

		final public function __construct($id = null, $row = null, $traverse = true, $foreign_key = null)
		{
			if ($id != null)
			{
				if (!is_numeric($id))
				{
					throw new \Exception('Please specify a valid id for object of type ' . get_class($this));
				}
				if ($row === null)
				{
					$row = static::getB2DBTable()->getByID($id);
				}

				if (!$row instanceof Row)
				{
					throw new \Exception('The specified id ('.$id.') does not exist in table ' . self::getB2DBTable()->getB2DBName());
				}
				try
				{
					$this->_preInitialize();
					$this->_id = (integer) $id;
					$this->_populatePropertiesFromRow($row, $traverse, $foreign_key);
					$this->_construct($row, $foreign_key);
				}
				catch (\Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				$this->_preInitialize();
			}
		}
		
		final public function __clone()
		{
			$this->_id = null;
			$this->_clone();
		}

		final public function save()
		{
			$is_new = !(bool) $this->_id;
			$this->_preSave($is_new);
			$res_id = self::getB2DBTable()->saveObject($this);
			$this->_id = $res_id;
			$this->_postSave($is_new);
		}
		
		final public function delete()
		{
			$this->_preDelete();
			self::getB2DBTable()->doDeleteById($this->getB2DBID());
			$this->_postDelete();
		}
		
	}