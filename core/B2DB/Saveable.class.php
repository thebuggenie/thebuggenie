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
		 * Return the B2DB table name for this class
		 * 
		 * @return string
		 */
		public function getB2DBTableName()
		{
			if (!isset(static::$_b2dbtablename))
			{
				$b2dbtablename = get_class($this).'Table';
				if (!class_exists($b2dbtablename))
				{
					throw new \Exception("Cannot find B2DB table class. Please create or generate the '{$b2dbtablename}' class, or specify a B2DB table name for class " . get_class($this) . ' via the static \$_b2dbtablename property');
				}
			}
			else
			{
				$b2dbtablename = static::$_b2dbtablename;
			}
			return $b2dbtablename;
		}
		
		/**
		 * Return the associated B2DBTable for this class
		 * 
		 * @return Table
		 */
		public function getB2DBTable()
		{
			$b2dbtablename = $this->getB2DBTableName();
			return $b2dbtablename::getTable();
		}

		protected function _getForeignClassForProperty($property_name)
		{
			if (!$foreign_type = Core::getCachedClassPropertyForeignClass(\get_class($this), $property_name))
			{
				$reflection = new \ReflectionProperty(\get_class($this), $property_name);
				$docblock = $reflection->getDocComment();
				if ($docblock)
				{
					$has_b2dbtype = \mb_strpos($docblock, '@Class', 3);
					$no_autopopulate = \mb_strpos($docblock, '@NoAutoPopulation', 3);

					if ($has_b2dbtype !== false && !$no_autopopulate)
					{
						$type_details = \mb_substr($docblock, $has_b2dbtype + 7);
						$type_details = \explode(' ', $type_details);
						$foreign_type = \trim($type_details[0]);
						Core::addCachedClassPropertyForeignClass(\get_class($this), $property_name, $foreign_type);
					}
				}
			}
			return $foreign_type;
		}
		
		protected function _getColumnProperty($column_name)
		{
			if (!$property_name = Core::getCachedColumnClassProperty($column_name, \get_class($this)))
			{
				$property = explode('.', $column_name);
				$property_name = "_".\mb_strtolower($property[1]);
				Core::addCachedColumnClassProperty($column_name, \get_class($this), $property_name);
			}
			return $property_name;
		}

		protected function _getPopulatedObjectFromProperty($property)
		{
			if (is_numeric($this->$property) && $this->$property > 0)
			{
				$type_name = $this->_getForeignClassForProperty($property);
				if ($type_name && \class_exists($type_name))
				{
					$this->$property = \TBGContext::factory()->$type_name($this->$property);
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
			\TBGLogging::log('Populating ' . get_class($this) . ' with id ' . $this->_id, 'B2DB');
			$id_column = $this->getB2DBTable()->getIdColumn();
			foreach ($this->getB2DBTable()->getColumns() as $column)
			{
				if ($column['name'] == $this->getB2DBTable()->getIdColumn()) continue;
				$property_name = $this->_getColumnProperty($column['name']);
				$property_type = $column['type'];
				if (!property_exists($this, $property_name))
				{
					throw new \Exception("Could not find class property {$property_name} in class ".get_class($this).". The class must have all properties from the corresponding B2DB table class available");
				}
				if ($traverse && in_array($column['name'], $this->getB2DBTable()->getForeignColumns()))
				{
					if ($row->get($column['name']) > 0)
					{
						$type_name = $this->_getForeignClassForProperty($property_name);
						if ($type_name && class_exists($type_name))
						{
							$b2dbtablename = $type_name::$_b2dbtablename;
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
						\TBGLogging::log('Populating foreign object of type ' . $type_name . ' with value ' . $value . ' for property ' . $property_name, 'B2DB');
						//if (!$row->get($column))
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
			\TBGLogging::log('Done populating ' . get_class($this) . ' with id ' . $this->_id, 'B2DB');
		}
		
		protected function _preInitialize() {}
		
		protected function _construct(\b2db\Row $row, $foreign_key = null) {}

		protected function _clone() {}
		
		protected function _preSave($is_new) {}
		
		protected function _postSave($is_new) {}
		
		protected function _preDelete() {}
		
		protected function _postDelete() {}
		
		public function getB2DBSaveablePropertyValue($property)
		{
			$property = explode('.', $property);
			$property_name = "_{$property[1]}";
			if (!property_exists($this, $property_name))
			{
				throw new \Exception("Could not find class property {$property_name} in class ".get_class($this).". The class must have all properties from the corresponding B2DB table class available");
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
			$column = $this->getB2DBTable()->getIdColumn();
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
					$row = $this->getB2DBTable()->getByID($id);
				}

				if (!$row instanceof Row)
				{
					throw new \Exception('The specified id ('.$id.') does not exist in table ' . $this->getB2DBTableName());
				}
				try
				{
					$this->_preInitialize();
					$this->_id = $id;
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
			$res_id = $this->getB2DBTable()->saveObject($this);
			$this->_id = $res_id;
			$this->_postSave($is_new);
		}
		
		final public function delete()
		{
			$this->_preDelete();
			$this->getB2DBTable()->doDeleteById($this->getB2DBID());
			$this->_postDelete();
		}
		
	}