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
	class B2DBTable
	{
		protected $b2db_name;
		protected $id_column;
		protected $b2db_alias;
		protected $_columns;
		protected $_charset = 'utf8';
		protected $_autoincrement_start_at = 1;
		protected $_foreigntables = array();
		protected $_foreigncolumns = array();

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

		/**
		 * Return an instance of this table
		 * 
		 * @return B2DBTable 
		 */
		public static function getTable()
		{
			$tablename = get_called_class();
			return B2DB::getTable($tablename);
		}
		
		protected function _addColumn($column, $details)
		{
			$this->_columns[$column] = $details;
		}

		protected function _addInteger($column, $length = 10, $default_value = 0, $not_null = false, $auto_inc = false, $unsigned = false)
		{
			$this->_addColumn($column, array('type' => 'integer', 'name' => $column, 'length' => $length, 'default_value' => $default_value, 'not_null' => $not_null, 'auto_inc' => $auto_inc, 'unsigned' => $unsigned));
		}
		
		protected function _addFloat($column, $precision = 2, $default_value = 0, $not_null = false, $auto_inc = false, $unsigned = false)
		{
			$this->_addColumn($column, array('type' => 'float', 'name' => $column, 'precision' => $precision, 'default_value' => $default_value, 'not_null' => $not_null, 'auto_inc' => $auto_inc, 'unsigned' => $unsigned));
		}
		
		protected function _addVarchar($column, $length = null, $default_value = null, $not_null = false)
		{
			$this->_addColumn($column, array('type' => 'varchar', 'name' => $column, 'length' => $length, 'default_value' => $default_value, 'not_null' => $not_null));
		}

		protected function _addText($column, $not_null = false)
		{
			$this->_addColumn($column, array('type' => 'text', 'name' => $column, 'not_null' => $not_null));
		}

		protected function _addBlob($column, $not_null = false)
		{
			$this->_addColumn($column, array('type' => 'blob', 'name' => $column, 'not_null' => $not_null));
		}

		protected function _addBoolean($column, $default_value = false, $not_null = false)
		{
			$this->_addColumn($column, array('type' => 'boolean', 'name' => $column, 'default_value' => ($default_value) ? 1 : 0, 'not_null' => $not_null));
		}

		/**
		 * Adds a foreign table
		 *
		 * @param string $column
		 * @param B2DBTable $table
		 * @param string $key
		 */
		protected function _addForeignKeyColumn($column, $table, $key)
		{
			$addtable = clone $table;
			$foreign_column = $addtable->getColumn($key);
			switch ($foreign_column['type'])
			{
				case 'integer':
					$this->_addInteger($column, $foreign_column['length'], $foreign_column['default_value'], false, false, $foreign_column['unsigned']);
					break;
				case 'float':
					$this->_addFloat($column, $foreign_column['precision'], $foreign_column['default_value'], false, false, $foreign_column['unsigned']);
					break;
				case 'varchar':
					$this->_addVarchar($column, $foreign_column['length'], $foreign_column['default_value'], false);
					break;
				case 'text':
					$this->_addText($column, $foreign_column['default_value'], false);
					break;
				case 'boolean':
				case 'blob':
					throw new B2DBException('Cannot use a blob or boolean column as a foreign key');
			}
			$this->_foreigntables[$addtable->getB2DBAlias()] = array('table' => $addtable, 'key' => $key, 'column' => $column);
			$this->_foreigncolumns[$column] = $column;
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

		protected function getQC()
		{
			$qc = '`';
			switch (B2DB::getDBtype())
			{
				case 'pgsql':
					$qc = '"';
					break;
			}
			return $qc;
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
		
		public function getForeignColumns()
		{
			return $this->_foreigncolumns;
		}
		
		/**
		 * Returns a foreign table
		 *
		 * @param B2DBTable $table
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
	
				$statement = B2DBStatement::getPreparedStatement($crit);
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
		 * @param B2DBCriteria $crit
		 * @param mixed $join
		 * 
		 * @return B2DBRow
		 */
		public function doSelectById($id, B2DBCriteria $crit = null, $join = 'all')
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
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
		public function doSelect(B2DBCriteria $crit, $join = 'all')
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
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
				$resultSet = $statement->performQuery();
			}
			catch (B2DBException $e)
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
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBRow
		 */
		public function doSelectOne(B2DBCriteria $crit, $join = 'all')
		{
			try
			{
				$crit->setFromTable($this);
				$crit->setupJoinTables($join);
				$crit->setLimit(1);
				$crit->generateSelectSQL();
				
				$statement = B2DBStatement::getPreparedStatement($crit);
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
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doInsert(B2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateInsertSQL();
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doUpdate(B2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateUpdateSQL();
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
		 * @param B2DBCriteria $crit
		 * @param integer $id
		 * 
		 * @return B2DBResultset
		 */
		public function doUpdateById(B2DBCriteria $crit, $id)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->addWhere($this->id_column, $id);
				$crit->setLimit(1);
				$crit->generateUpdateSQL();
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
		 * @param B2DBCriteria $crit
		 * 
		 * @return B2DBResultset
		 */
		public function doDelete(B2DBCriteria $crit)
		{
			try
			{
				$crit->setFromTable($this);
				$crit->generateDeleteSQL();
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
				
				$statement = B2DBStatement::getPreparedStatement($crit);
	
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
				$statement = B2DBStatement::getPreparedStatement($sql);
				$res = $statement->performQuery('create');
			}
			catch (Exception $e)
			{
				throw new B2DBException('Error creating table ' . $this->getB2DBName() . ': ' . $e->getMessage() . '. SQL was: ' . $sql);
			}
			return $res;			
		}

		protected function _dropToSQL()
		{
			$sql = '';
			$sql .= 'DROP TABLE IF EXISTS ' . B2DB::getTablePrefix() . $this->b2db_name;
			return $sql;
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
				$statement = B2DBStatement::getPreparedStatement($sql);
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
		 * @param boolean $setupjointables[optional] Whether to auto-join all related tables by default
		 * 
		 * @return B2DBCriteria
		 */
		public function getCriteria($setupjointables = false)
		{
			$crit = new B2DBCriteria($this, $setupjointables);
			return $crit;
		}
		
		protected function formatify($value, $type)
		{
			switch ($type)
			{
				case 'float':
					return settype(gmp_strval($value));
				case 'varchar':
				case 'text':
					return (string) $value;
				case 'integer':
					return (integer) $value;
				case 'boolean':
					return (boolean) $value;
				default:
					return $value;
			}
		}
		
		public function saveObject(B2DBSaveable $object)
		{
			$crit = $this->getCriteria();
			$id = $object->getB2DBID();
			foreach ($this->getColumns() as $property)
			{
				$property = $property['name'];
				$value = $this->formatify($object->getB2DBSaveablePropertyValue(strtolower($property)), $property['type']);
				if ($property == $this->getIdColumn())
				{
					$res_id = $value;
				}
				if (is_object($value))
				{
					$value = (int) $value->getID();
				}
				if (in_array($property, $this->_foreigncolumns))
				{
					$value = ($value) ? (int) $value : null;
				}
				if ($id)
				{
					$crit->addUpdate($property, $value);
				}
				elseif ($property != $this->getIdColumn())
				{
					$crit->addInsert($property, $value);
				}
			}
			if ($id)
			{
				$res = $this->doUpdateById($crit, $id);
				return $res_id;
			}
			else
			{
				$res = $this->doInsert($crit);
				$res_id = $res->getInsertID();
			}
			
			return $res_id;
		}

		final public function getVersion()
		{
			return static::B2DB_TABLE_VERSION;
		}

		protected function _getColumnDefinitionSQL($column)
		{
			$qc = $this->getQC();
			$fsql = " $qc" . $this->_getRealColumnFieldName($column['name']) . "$qc ";
			switch ($column['type'])
			{
				case 'integer':
					if (B2DB::getDBtype() == 'pgsql' && isset($column['auto_inc']) && $column['auto_inc'] == true)
					{
						$fsql .= 'SERIAL';
					}
					elseif (B2DB::getDBtype() == 'pgsql')
					{
						$fsql .= 'INTEGER';
					}
					else
					{
						$fsql .= 'INTEGER(' . $column['length'] . ')';
					}
					if ($column['unsigned'] && B2DB::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
					break;
				case 'varchar':
					$fsql .= 'VARCHAR(' . $column['length'] . ')';
					break;
				case 'float':
					$fsql .= 'FLOAT(' . $column['precision'] . ')';
					if ($column['unsigned'] && B2DB::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
					break;
				case 'blob':
					if (B2DB::getDBtype() == 'mysql')
					{
						$fsql .= 'LONGBLOB';
					}
					elseif (B2DB::getDBtype() == 'pgsql')
					{
						$fsql .= 'BYTEA';
					}
					else
					{
						$fsql .= 'BLOB';
					}
					break;
				case 'text':
				case 'boolean':
					$fsql .= strtoupper($column['type']);
					break;
			}
			if ($column['not_null']) $fsql .= ' NOT NULL';
			if ($column['type'] != 'text')
			{
				if (isset($column['auto_inc']) && $column['auto_inc'] == true && B2DB::getDBtype() != 'pgsql')
				{
					$fsql .= ' AUTO_INCREMENT';
				}
				elseif (isset($column['default_value']) && $column['default_value'] !== null && !(isset($column['auto_inc']) && $column['auto_inc'] == true && B2DB::getDBtype() == 'pgsql'))
				{
					if (is_int($column['default_value']))
					{
						if ($column['type'] == 'boolean')
						{
							$fsql .= ' DEFAULT ';
							$fsql .= ($column['default_value']) ? 'true' : 'false';
						}
						else
						{
							$fsql .= ' DEFAULT ' . $column['default_value'];
						}
					}
					else
					{
						$fsql .= ' DEFAULT \'' . $column['default_value'] . '\'';
					}
				}
			}
			return $fsql;
		}

		protected function _getTableNameSQL()
		{
			$qc = $this->getQC();
			$sql = $qc . B2DB::getTablePrefix() . $this->b2db_name . $qc;

			return $sql;
		}

		protected function _createToSQL()
		{
			$sql = '';
			$qc = $this->getQC();
			$sql .= "CREATE TABLE " . $this->_getTableNameSQL() . " (\n";
			$field_sql = array();
			foreach ($this->_columns as $column)
			{
				$field_sql[] = $this->_getColumnDefinitionSQL($column);
			}
			$sql .= join(",\n", $field_sql);
			$sql .= ", PRIMARY KEY ($qc" . $this->_getRealColumnFieldName($this->id_column) . "$qc) ";
			$sql .= ') ';
			if (B2DB::getDBtype() != 'pgsql') $sql .= 'AUTO_INCREMENT=' . $this->_autoincrement_start_at . ' ';
			if (B2DB::getDBtype() != 'pgsql') $sql .= 'CHARACTER SET ' . $this->_charset;

			return $sql;
		}

		protected function _getAddColumnSQL($column, $details)
		{
			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$sql .= ' ADD COLUMN ' . $this->_getColumnDefinitionSQL($details);

			return $sql;
		}

		protected function _getAlterColumnSQL($old_column_details, $new_column_details)
		{
			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$sql .= ' ALTER COLUMN ' . $this->_getColumnDefinitionSQL($new_column_details);
			
			return $sql;
		}

		protected function _getDropColumnSQL($column)
		{
			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$sql .= ' DROP COLUMN ' . $this->_getRealColumnFieldName($column);

			return $sql;
		}

		protected function _migrateData(B2DBTable $old_table)
		{
			
		}

		/**
		 * Perform upgrade for a table, by comparing one table to an old version
		 * of the same table
		 *
		 * @param B2DBTable $old_table
		 */
		public function upgrade(B2DBTable $old_table)
		{
			if ($old_table->getVersion() != ($this->getVersion() - 1))
				throw new B2DBException('Cannot upgrade from ' . get_class($old_table) . ' version ' . $old_table->getVersion() . ', must be version ' . ($this->getVersion() - 1));
			
			$old_columns = $old_table->getColumns();
			$new_columns = $this->getColumns();
			
			$added_columns = array_diff_key($new_columns, $old_columns);
			$altered_columns = array_diff($old_columns, $new_columns);
			$dropped_columns = array_keys(array_diff_key($old_columns, $new_columns));

			$sqls = array();
			foreach ($added_columns as $column => $details)
				$sqls[] = $this->_getAddColumnSQL($column, $details);

			if (count($sqls))
			{
				foreach ($sqls as $sqlStmt)
				{
					$statement = B2DBStatement::getPreparedStatement($sqlStmt);
					$res = $statement->performQuery('alter');
				}
			}

			$this->_migrateData($old_table);

			$sqls = array();
			foreach ($altered_columns as $column)
				$sqls[] = $this->_getAlterColumnSQL($column, $new_columns[$column]);

			foreach ($dropped_columns as $column)
				$sqls[] = $this->_getDropColumnSQL($column);

			if (count($sqls))
			{
				foreach ($sqls as $sqlStmt)
				{
					$statement = B2DBStatement::getPreparedStatement($sqlStmt);
					$res = $statement->performQuery('alter');
				}
			}
		}

	}
	
