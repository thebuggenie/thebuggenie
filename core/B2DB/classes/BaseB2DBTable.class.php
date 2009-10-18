<?php

	/**
	 * B2DB Table Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Table Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBTable
	{
		protected $b2db_name;
		protected $id_column;
		protected $b2db_alias;
		protected $_columns;
		protected $_charset = 'latin1';
		protected $_autoincrement_start_at = 1;
		protected $_foreigntables = array();

		public function __clone()
		{
			$this->b2db_alias = $this->b2db_name . B2DB::addAlias();
		}
		
		public function __construct($b2db_name, $id_column)
		{
			$this->b2db_name = $b2db_name;
			$this->b2db_alias = $b2db_name . B2DB::addAlias();
			$this->id_column = $id_column;
			$this->_addInteger($id_column, 10, 0, true, true, true);
		}
		
		protected function _addInteger($column, $length = 10, $default_value = 0, $not_null = true, $auto_inc = false, $unsigned = false)
		{
			$this->_columns[$column] = array('type' => 'integer', 'name' => $column, 'length' => $length, 'default_value' => $default_value, 'not_null' => $not_null, 'auto_inc' => $auto_inc, 'unsigned' => $unsigned);
		}
		
		protected function _addVarchar($column, $length = null, $default_value = null, $not_null = true)
		{
			$this->_columns[$column] = array('type' => 'varchar', 'name' => $column, 'length' => $length, 'default_value' => $default_value, 'not_null' => $not_null);
		}

		protected function _addText($column, $not_null = true)
		{
			$this->_columns[$column] = array('type' => 'text', 'name' => $column, 'not_null' => $not_null);
		}

		protected function _addBoolean($column, $default_value = false, $not_null = true)
		{
			$this->_columns[$column] = array('type' => 'boolean', 'name' => $column, 'default_value' => ($default_value) ? 1 : 0, 'not_null' => $not_null);
		}
		
		/**
		 * Adds a foreign table
		 *
		 * @param string $column
		 * @param BaseB2DBTable $table
		 * @param string $key
		 */
		protected function _addForeignKeyColumn($column, $table, $key)
		{
			$addtable = clone $table;
			$foreign_column = $addtable->getColumn($key);
			switch ($foreign_column['type'])
			{
				case 'integer':
					$this->_addInteger($column, $foreign_column['length'], $foreign_column['default_value'], $foreign_column['not_null'], false, $foreign_column['unsigned']);
					break;
				case 'varchar':
					$this->_addVarchar($column, $foreign_column['length'], $foreign_column['default_value'], $foreign_column['not_null']);
					break;
				case 'text':
					$this->_addText($column, $foreign_column['default_value'], $foreign_column['not_null']);
					break;
				case 'boolean':
					$this->_addBoolean($column, $foreign_column['length'], $foreign_column['default_value'], $foreign_column['not_null']);
					break;
			}
			$this->_foreigntables[$addtable->getB2DBAlias()] = array('table' => $addtable, 'key' => $key, 'column' => $column);
		}

		public function getForeignTableByLocalColumn($column)
		{
			foreach ($this->_foreigntables as $foreign_table)
			{
				if ($foreign_table['column'] == $column)
				{
					return $foreign_table;
				}
			}
			return null;
		}
		
		public function __toString()
		{
			return $this->b2db_name;
		}
		
		/**
		 * Sets the charset to something other than "latin1" which is the default
		 *
		 * @param string $charset
		 */
		public function setCharset($charset)
		{
			$this->_charset = $charset;
		}
		
		/**
		 * Sets the initial auto_increment value to something else than 1
		 *
		 * @param integer $start_at
		 */
		public function setAutoIncrementStart($start_at)
		{
			$this->_autoincrement_start_at = $start_at;
		}

		/**
		 * Returns the table name
		 *
		 * @return string
		 */
		public function getB2DBName()
		{
			return $this->b2db_name;
		}
		
		/**
		 * Returns the shortname / prefix of the table
		 *
		 * @return string
		 */
		public function getB2DBAlias()
		{
			return $this->b2db_alias;
		}
		
		public function getForeignTables()
		{
			return $this->_foreigntables;
		}
		
		/**
		 * Returns a foreign table
		 *
		 * @param BaseB2DBTable $table
		 * 
		 * @return array
		 */
		public function getForeignTable($table)
		{
			return $this->_foreigntables[$table->getB2DBAlias()];
		}

		/**
		 * Returns the id column for this table
		 *
		 * @return string
		 */
		public function getIdColumn()
		{
			return $this->id_column;
		}
		
		public function getColumns()
		{
			return $this->_columns;
		}
		
		public function getColumn($column)
		{
			return $this->_columns[$column];
		}
		
		protected function _getRealColumnFieldName($column)
		{
			return substr($column, stripos($column, '.') + 1);
		}
		
		public function getAliasColumns()
		{
			$retcolumns = array();
			foreach ($this->_columns as $column => $col_data)
			{
				$column_name = explode('.', $column);
				$retcolumns[] = $this->b2db_alias . '.' . $column_name[1];
			}
			return $retcolumns;
		}

		/**
		 * Selects all records in this table
		 *
		 * @return B2DBResultset
		 */
		public function doSelectAll()
		{
			try
			{
				$crit = new B2DBCriteria();
				$crit->setFromTable($this);
				$crit->generateSelectSQL(true);
	
				$statement = B2DB::prepareStatement($crit);
				$resultSet = $statement->performQuery();
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $resultSet;
		}

		/**
		 * Returns one row from the current table based on a given id
		 *
		 * @param integer $id
		 * @param BaseB2DBCriteria $crit
		 * @param mixed $selects
		 * 
		 * @return B2DBRow
		 */
		public function doSelectById($id, BaseB2DBCriteria $crit = null, $join = 'all')
		{
			try
			{
				if ($crit == null)
				{
					$crit = new B2DBCriteria();
				}
				$crit->addWhere($this->id_column, $id);
				$crit->setLimit(1);
				return $this->doSelectOne($crit, $join);
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}
			
			
			if ($resultSet->count() == 0)
			{
				throw new B2DBException("The row $id does not exist");
			}

			return $resultSet->getCurrentRow();
		}

		/**
		 * Counts rows
		 *
		 * @param B2DBCriteria $crit
		 * @return integer
		 */
		public function doCount(B2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateCountSQL();
				$statement = B2DB::prepareStatement($crit);
	
				$resultSet = $statement->performQuery();
				$cnt = $resultSet->getCount();
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $cnt;
		}

		/**
		 * Selects rows based on given criteria
		 *
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doSelect(B2DBCriteria $crit = null, $join = 'all')
		{
			try
			{
				if ($crit == null)
				{
					$crit = new B2DBCriteria();
				}
				$crit->setFromTable($this);
				$crit->setupJoinTables($join);
				$crit->generateSelectSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$resultSet = $statement->performQuery();
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			if ($resultSet->count())
			{
				return $resultSet;
			}
			else
			{
				return null;
			}
		}

		/**
		 * Selects one row from the table based on the given criteria
		 *
		 * @param BaseB2DBCriteria $crit
		 * 
		 * @return B2DBRow
		 */
		public function doSelectOne(BaseB2DBCriteria $crit, $join = 'all')
		{
			try
			{
				$crit->setFromTable($this);
				$crit->setupJoinTables($join);
				$crit->setLimit(1);
				$crit->generateSelectSQL();
				
				$statement = B2DB::prepareStatement($crit);
				$resultset = $statement->performQuery();
				$resultset->next();
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $resultset->getCurrentRow();
		}

		/**
		 * Inserts a row into the table
		 *
		 * @param BaseB2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doInsert(BaseB2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateInsertSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$resultset = $statement->performQuery('insert');
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $resultset;
		}

		/**
		 * Perform an SQL update
		 *
		 * @param BaseB2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doUpdate(B2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateUpdateSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$res = $statement->performQuery('update');
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $res;
		}

		/**
		 * Perform an SQL update
		 *
		 * @param BaseB2DBCriteria $crit
		 * @param integer $id
		 * 
		 * @return B2DBResultset
		 */
		public function doUpdateById(BaseB2DBCriteria $crit, $id)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->addWhere($this->id_column, $id);
				$crit->setLimit(1);
				$crit->generateUpdateSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$resultset = $statement->performQuery('update');
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}

			return $resultset;
		}

		/**
		 * Perform an SQL delete
		 *
		 * @param BaseB2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doDelete(BaseB2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateDeleteSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$resultset = $statement->performQuery('delete');
				return $resultset;
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}
		}

		/**
		 * Perform an SQL delete by an id
		 *
		 * @param integer $id
		 * 
		 * @return B2DBResultset
		 */
		public function doDeleteById($id)
		{
			try
			{
				$crit = new B2DBCriteria();
				$crit->setFromTable($this);
				$crit->addWhere($this->id_column, $id);
				$crit->generateDeleteSQL();
				
				$statement = B2DB::prepareStatement($crit);
	
				$resultset = $statement->performQuery('delete');
				return $resultset;
			}
			catch (Exception $e)
			{
				if (B2DB::throwExceptionAsHTML())
				{
					B2DB::fatalError($e);
					exit();
				}
				else
				{
					throw $e;
				}
			}
		}
		
		/**
		 * creates the table by executing the sql create statement
		 *
		 * @return B2DBResultset
		 */
		public function create($debug = false)
		{
			$sql = '';
			try
			{
				$res = $this->drop();
				
				$sql = $this->_createToSQL();
				if ($debug)
				{
					echo $sql;
				}
				$statement = B2DB::prepareStatement($sql);
				$res = $statement->performQuery('create');
			}
			catch (Exception $e)
			{
				throw new B2DBException('Error creating table ' . $this->getB2DBName() . ': ' . $e->getMessage() . '. SQL was: ' . $sql);
			}
			return $res;			
		}
		
		/**
		 * Drops a table
		 *  
		 * @return null
		 */
		public function drop()
		{
			try
			{
				$sql = $this->_dropToSQL();
				$statement = B2DB::prepareStatement($sql);
				$res = $statement->performQuery('drop');
			}
			catch (Exception $e)
			{
				throw new B2DBException('Error dropping table ' . $this->getB2DBName() . ': ' . $e->getMessage() . '. SQL was: ' . $sql);
			}
			return $res;			
		}
		
		/**
		 * Return a new criteria with this table as the from-table
		 * 
		 * @return B2DBCriteria
		 */
		public function getCriteria()
		{
			$crit = new B2DBCriteria($this);
			return $crit;
		}

	}
	