<?php

	/**
	 * An identifiable class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage core
	 */

	/**
	 * An identifiable class
	 *
	 * @package thebuggenie
	 * @subpackage core
	 */
	abstract class TBGIdentifiableClass implements TBGIdentifiable, B2DBSaveable
	{
		
		const TYPE_USER = 1;
		const TYPE_TEAM = 2;
	
		/**
		 * The id for this item, usually identified by a record in the database
		 *
		 * @var integer
		 */
		protected $_id;
		
		/**
		 * The name of the object
		 *
		 * @var string
		 */
		protected $_name;
		
		/**
		 * The item type (if needed)
		 *
		 * @var string|integer
		 */
		protected $_itemtype;
		
		protected $_b2dbtablename;
		
		/**
		 * Return the items id
		 * 
		 * @return integer
		 */
		public function getID()
		{
			return $this->_id;
		}

		/**
		 * Set the items id
		 *
		 * @param integer $id
		 */
		public function setID($id)
		{
			$this->_id = $id;
		}

		/**
		 * Return the items name
		 * 
		 * @return string
		 */
		public function getName()
		{
			return $this->_name;
		}

		/**
		 * Set the edition name
		 *
		 * @param string $name
		 */
		public function setName($name)
		{
			$this->_name = $name;
		}

		public function getType()
		{
			return 0;
		}
		
		public function getB2DBTableName()
		{
			if (!isset($this->_b2dbtablename))
			{
				throw new Exception('You must specify a B2DB table name for class ' . get_class($this));
			}
			return $this->_b2dbtablename;
		}
		
		/**
		 * Return the associated B2DBTable for this class
		 * 
		 * @return TBGB2DBTable
		 */
		public function getB2DBTable()
		{
			$b2dbtablename = $this->getB2DBTableName();
			return $b2dbtablename::getTable();
		}
		
		protected function _populatePropertiesFromB2DBRow(B2DBRow $row)
		{
			$id_column = $this->getB2DBTable()->getIdColumn();
			foreach ($this->getB2DBTable()->getColumns() as $column)
			{
				if ($column['name'] == $id_column) 
				{
					$property_name = "_id";
					$property_type = 'integer';
				}
				else
				{
					$property = explode('.', $column['name']);
					if ($property[1] == 'scope') continue;
					$property_type = $column['type'];
					$property_name = "_".strtolower($property[1]);
					if (!property_exists($this, $property_name))
					{
						throw new Exception("Could not find class property {$property_name} in class ".get_class($this).". The class must have all properties from the corresponding B2DB table class available, except scope and id");
					}
				}
				if ($property_type == 'boolean')
				{
					$this->$property_name = (boolean) $row->get($column['name']);
				}
				else
				{
					$this->$property_name = $row->get($column['name']);
				}
			}
		}
		
		protected function _preInitialize() {}
		
		protected function _construct(B2DBRow $row) {}
		
		final public function __construct($id = null, $row = null)
		{
			if ($id !== null)
			{
				if (!is_numeric($id))
				{
					throw new Exception('Please specify a valid id for object of type ' . get_class($this));
				}
				if ($row === null)
				{
					$row = $this->getB2DBTable()->getByID($id);
				}

				if (!$row instanceof B2DBRow)
				{
					throw new Exception('The specified id does not exist in table ' . $this->getB2DBTableName());
				}
				try
				{
					$this->_preInitialize();
					$this->_populatePropertiesFromB2DBRow($row);
					$this->_construct($row);
				}
				catch (Exception $e)
				{
					var_dump($e);
					die();
					throw $e;
				}
			}
			else
			{
				$this->_preInitialize();
			}
		}
		
		public function getB2DBSaveablePropertyValue($property)
		{
			$property_name = "_{$property}";
			if (is_object($this->$property_name))
			{
				return $this->$property_name->getID();
			}
			elseif (!is_object($this->$property_name))
			{
				$this->$property_name;
			}
		}

		public function getB2DBID()
		{
			return $this->getID();
		}

		final public function save()
		{
			$this->_preSave();
			$is_new = (bool) $this->_id;
			$res_id = $this->getB2DBTable()->saveObject($this);
			$this->_id = $res_id;
			$this->_postSave();
		}

	}
