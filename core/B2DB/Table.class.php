<?php

	namespace b2db;
	
	/**
	 * Table class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * Table class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Table
	{
		protected $b2db_name;
		protected $id_column;
		protected $b2db_alias;
		protected $_columns;
		protected $_indexes = array();
		protected $_charset = 'utf8';
		protected $_autoincrement_start_at = 1;
		protected $_foreigntables = null;
		protected $_foreigncolumns = array();

		public function __clone()
		{
			$this->b2db_alias = $this->b2db_name . Core::addAlias();
		}
		
		final public function __construct()
		{
			if ($entity_class = Core::getCachedTableEntityClass(\get_called_class())) {
				if ($details = Core::getCachedTableDetails($entity_class)) {
					$this->_columns = $details['columns'];
					$this->_foreigncolumns = $details['foreign_columns'];
					$this->b2db_name = $details['name'];
					$this->b2db_alias = $details['name'] . Core::addAlias();
					$this->id_column = $details['id'];
				}
			} else {
				$this->_initialize();
			}
		}

		protected function _initialize()
		{
			throw new Exception('The table "'.\get_class($this).'" has no corresponding entity class. You must override the _initialize() method to set up the table details.');
		}

		protected function _setup($b2db_name, $id_column)
		{
			$this->b2db_name = $b2db_name;
			$this->b2db_alias = $b2db_name . Core::addAlias();
			$this->id_column = $id_column;
			$this->_addInteger($id_column, 10, 0, true, true, true);
		}

		/**
		 * Return an instance of this table
		 * 
		 * @return Table 
		 */
		public static function getTable()
		{
			$tablename = \get_called_class();
			return Core::getTable($tablename);
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
		
		protected function _addIndex($index_name, $columns, $index_type = null)
		{
			if (!is_array($columns)) $columns = array($columns);
			$this->_indexes[$index_name] = array('columns' => $columns, 'type' => $index_type);
		}

		/**
		 * Adds a foreign table
		 *
		 * @param string $column
		 * @param Table $table
		 * @param string $key
		 */
		protected function _addForeignKeyColumn($column, Table $table, $key = null)
		{
			$addtable = clone $table;
			$key = ($key !== null) ? $key : $addtable->getIdColumn();
			$foreign_column = $addtable->getColumn($key);
			switch ($foreign_column['type']) {
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
//					$this->_addText($column, $foreign_column['default_value'], false);
//					break;
				case 'boolean':
				case 'blob':
					throw new Exception('Cannot use a text, blob or boolean column as a foreign key');
			}
			//$this->_foreigntables[$addtable->getB2DBAlias()] = array('table' => $addtable, 'key' => $key, 'column' => $column);
			$this->_foreigncolumns[$column] = array('class' => \get_class($table), 'key' => $key, 'name' => $column);
		}

		public function getForeignTableByLocalColumn($column)
		{
			if (is_array($this->_foreigntables)) {
				foreach ($this->_foreigntables as $foreign_table) {
					if ($foreign_table['column'] == $column) {
						return $foreign_table;
					}
				}
			}
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
			switch (Core::getDBtype()) {
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
		
		protected function _initializeForeignTables()
		{
			$this->_foreigntables = array();
			foreach ($this->_foreigncolumns as $column) {
				$table_classname = $column['class'];
				$table = clone $table_classname::getTable();
				$key = ($column['key'] !== null) ? $column['key'] : $table->getIdColumn();
				$this->_foreigntables[$table->getB2DBAlias()] = array('table' => $table, 'key' => $key, 'column' => $column['name']);
			}
		}

		public function getForeignTables()
		{
			if ($this->_foreigntables === null) {
				$this->_initializeForeignTables();
			}
			return $this->_foreigntables;
		}
		
		public function getForeignColumns()
		{
			return $this->_foreigncolumns;
		}
		
		/**
		 * Returns a foreign table
		 *
		 * @param Table $table
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
			return mb_substr($column, mb_stripos($column, '.') + 1);
		}
		
		public function getAliasColumns()
		{
			$retcolumns = array();
			foreach ($this->_columns as $column => $col_data) {
				$column_name = explode('.', $column);
				$retcolumns[] = $this->b2db_alias . '.' . $column_name[1];
			}
			return $retcolumns;
		}

		/**
		 * Selects all records in this table
		 *
		 * @return Resultset
		 */
		public function doSelectAll()
		{
			$crit = new Criteria();
			$crit->setFromTable($this);
			$crit->generateSelectSQL(true);

			$statement = Statement::getPreparedStatement($crit);
			return $statement->performQuery();
		}

		public function selectAll()
		{
			$resultset = $this->doSelectAll();
			return $this->_populateFromResultset($resultset);
		}

		/**
		 * Returns one row from the current table based on a given id
		 *
		 * @param integer $id
		 * @param Criteria $crit
		 * @param mixed $join
		 * 
		 * @return \b2db\Row
		 */
		public function doSelectById($id, Criteria $crit = null, $join = 'all')
		{
			if ($crit == null) $crit = new Criteria();
			$crit->addWhere($this->id_column, $id);
			$crit->setLimit(1);
			return $this->doSelectOne($crit, $join);
		}

		/**
		 *
		 * @param type $id
		 * @param Criteria $crit
		 * @param type $join
		 *
		 * @return Saveable
		 */
		public function selectById($id, Criteria $crit = null, $join = 'all')
		{
			$row = $this->doSelectById($id, $crit, $join);
			return $this->_populateFromRow($row);
		}

		/**
		 * Counts rows
		 *
		 * @param Criteria $crit
		 * @return integer
		 */
		public function doCount(Criteria $crit)
		{
			$crit->setFromTable($this);
			$crit->generateCountSQL();
			$statement = Statement::getPreparedStatement($crit);

			$resultset = $statement->performQuery();
			return $resultset->getCount();
		}

		public function count(Criteria $crit)
		{
			return $this->doCount($crit);
		}

		/**
		 * Selects rows based on given criteria
		 *
		 * @param Criteria $crit
		 * 
		 * @return Resultset
		 */
		public function doSelect(Criteria $crit, $join = 'all')
		{
			if ($crit == null) $crit = new Criteria();
			$crit->setFromTable($this);
			$crit->setupJoinTables($join);
			$crit->generateSelectSQL();

			$statement = Statement::getPreparedStatement($crit);

			$resultset = $statement->performQuery();
			return ($resultset->count()) ? $resultset : null;
		}

		public function select(Criteria $crit, $join = 'all')
		{
			$resultset = $this->doSelect($crit, $join);
			return $this->_populateFromResultset($resultset);
		}

		/**
		 * Selects one row from the table based on the given criteria
		 *
		 * @param Criteria $crit
		 * 
		 * @return \b2db\Row
		 */
		public function doSelectOne(Criteria $crit, $join = 'all')
		{
			$crit->setFromTable($this);
			$crit->setupJoinTables($join);
			$crit->setLimit(1);
			$crit->generateSelectSQL();

			$statement = Statement::getPreparedStatement($crit);
			$resultset = $statement->performQuery();
			$resultset->next();
			
			return $resultset->getCurrentRow();
		}

		public function selectOne(Criteria $crit, $join = 'all')
		{
			$row = $this->doSelectOne($crit, $join);
			return $this->_populateFromRow($row);
		}

		/**
		 * Inserts a row into the table
		 *
		 * @param Criteria $crit
		 * 
		 * @return Resultset
		 */
		public function doInsert(Criteria $crit)
		{
			$crit->setFromTable($this);
			$crit->generateInsertSQL();

			$statement = Statement::getPreparedStatement($crit);
			return $statement->performQuery('insert');
		}

		/**
		 * Perform an SQL update
		 *
		 * @param Criteria $crit
		 * 
		 * @return Resultset
		 */
		public function doUpdate(Criteria $crit)
		{
			$crit->setFromTable($this);
			$crit->generateUpdateSQL();

			$statement = Statement::getPreparedStatement($crit);

			return $statement->performQuery('update');
		}

		/**
		 * Perform an SQL update
		 *
		 * @param Criteria $crit
		 * @param integer $id
		 * 
		 * @return Resultset
		 */
		public function doUpdateById(Criteria $crit, $id)
		{
			$crit->setFromTable($this);
			$crit->addWhere($this->id_column, $id);
			$crit->setLimit(1);
			$crit->generateUpdateSQL();

			$statement = Statement::getPreparedStatement($crit);

			return $statement->performQuery('update');
		}

		/**
		 * Perform an SQL delete
		 *
		 * @param Criteria $crit
		 * 
		 * @return Resultset
		 */
		public function doDelete(Criteria $crit)
		{
			$crit->setFromTable($this);
			$crit->generateDeleteSQL();

			$statement = Statement::getPreparedStatement($crit);

			return $statement->performQuery('delete');
		}

		/**
		 * Perform an SQL delete by an id
		 *
		 * @param integer $id
		 * 
		 * @return Resultset
		 */
		public function doDeleteById($id)
		{
			$crit = new Criteria();
			$crit->setFromTable($this);
			$crit->addWhere($this->id_column, $id);
			$crit->generateDeleteSQL();

			$statement = Statement::getPreparedStatement($crit);

			return $statement->performQuery('delete');
		}
		
		/**
		 * creates the table by executing the sql create statement
		 *
		 * @return Resultset
		 */
		public function create()
		{
			$sql = '';
			try {
				$res = $this->drop();
				
				$sql = $this->_createToSQL();
				$statement = Statement::getPreparedStatement($sql);
				return $statement->performQuery('create');
			} catch (\Exception $e) {
				throw new Exception('Error creating table ' . $this->getB2DBName() . ': ' . $e->getMessage(), $sql);
			}
		}
		
		protected function _setupIndexes() { }
		
		public function createIndexes()
		{
			try {
				$this->_setupIndexes();
				$qc = $this->getQC();

				foreach ($this->_indexes as $index_name => $details) {
					$sql = '';
					switch (Core::getDBtype()) {
						case 'pgsql':
							$sql .= " CREATE INDEX " . Core::getTablePrefix() . $this->b2db_name . "_{$index_name} ON " . $this->_getTableNameSQL() . " (";
							break;
						case 'mysql':
							$sql .= " ALTER TABLE " . $this->_getTableNameSQL() . " ADD INDEX " . Core::getTablePrefix() . $this->b2db_name . "_{$index_name}(";
							break;
					}
					$index_column_sqls = array();
					foreach ($details['columns'] as $column) {
						$index_column_sqls[] = "$qc" . $this->_getRealColumnFieldName($column) . "$qc";
					}
					$sql .= join (', ', $index_column_sqls);
					$sql .= ");";

					$statement = Statement::getPreparedStatement($sql);
					$res = $statement->performQuery('create index');
				}
			} catch (Exception $e) {
				throw new Exception('An error occured when trying to create indexes for table "'.$this->getB2DBName().'" (defined in "'.\get_class($this).')": '.$e->getMessage(), $e->getSQL());
			}
		}

		protected function _dropToSQL()
		{
			return 'DROP TABLE IF EXISTS ' . Core::getTablePrefix() . $this->b2db_name;
		}

		/**
		 * Drops a table
		 *  
		 * @return null
		 */
		public function drop()
		{
			try {
				$sql = $this->_dropToSQL();
				$statement = Statement::getPreparedStatement($sql);
				return $statement->performQuery('drop');
			} catch (\Exception $e) {
				throw new Exception('Error dropping table ' . $this->getB2DBName() . ': ' . $e->getMessage(), $sql);
			}
		}
		
		/**
		 * Return a new criteria with this table as the from-table
		 * 
		 * @param boolean $setupjointables[optional] Whether to auto-join all related tables by default
		 * 
		 * @return Criteria
		 */
		public function getCriteria($setupjointables = false)
		{
			$crit = new Criteria($this, $setupjointables);
			return $crit;
		}
		
		protected function formatify($value, $type)
		{
			switch ($type) {
				case 'float':
					return $value;
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
		
		public function saveObject(\b2db\Saveable $object)
		{
			$crit = $this->getCriteria();
			$id = $object->getB2DBID();
			foreach ($this->getColumns() as $column) {
				if (!array_key_exists('property', $column)) {
					throw new Exception('Could not match all columns to properties for object of type '.\get_class($object).". Make sure you're not mixing between initializing the table manually and using column (property) annotations");
				}
				$property = $column['property'];
				$value = $this->formatify($object->getB2DBSaveablePropertyValue(mb_strtolower($property)), $column['type']);
				if ($column['name'] == $this->getIdColumn()) $res_id = $value;
				if (is_object($value)) $value = (int) $value->getID();
				if (in_array($column['name'], $this->_foreigncolumns)) $value = ($value) ? (int) $value : null;
				if ($id) {
					$crit->addUpdate($column['name'], $value);
				} elseif ($column != $this->getIdColumn()) {
					$crit->addInsert($column['name'], $value);
				}
			}
			if ($id) {
				$res = $this->doUpdateById($crit, $id);
				$res_id = $id;
			} else {
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
			$fsql = '';
			switch ($column['type']) {
				case 'integer':
					if (Core::getDBtype() == 'pgsql' && isset($column['auto_inc']) && $column['auto_inc'] == true) {
						$fsql .= 'SERIAL';
					} elseif (Core::getDBtype() == 'pgsql') {
						$fsql .= 'INTEGER';
					} else {
						$fsql .= 'INTEGER(' . $column['length'] . ')';
					}
					if ($column['unsigned'] && Core::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
					break;
				case 'varchar':
					if (!$column['length']) throw new Exception("Column '{$column['name']}' (defined in ".\get_class($this).") is missing required 'length' property");
					$fsql .= 'VARCHAR(' . $column['length'] . ')';
					break;
				case 'float':
					$fsql .= 'FLOAT(' . $column['precision'] . ')';
					if ($column['unsigned'] && Core::getDBtype() != 'pgsql') $fsql .= ' UNSIGNED';
					break;
				case 'blob':
					if (Core::getDBtype() == 'mysql') {
						$fsql .= 'LONGBLOB';
					} elseif (Core::getDBtype() == 'pgsql') {
						$fsql .= 'BYTEA';
					} else {
						$fsql .= 'BLOB';
					}
					break;
				case 'text':
				case 'boolean':
					$fsql .= mb_strtoupper($column['type']);
					break;
			}
			if ($column['not_null']) $fsql .= ' NOT NULL';
			if ($column['type'] != 'text') {
				if (isset($column['auto_inc']) && $column['auto_inc'] == true && Core::getDBtype() != 'pgsql') {
					$fsql .= ' AUTO_INCREMENT';
				} elseif (isset($column['default_value']) && $column['default_value'] !== null && !(isset($column['auto_inc']) && $column['auto_inc'] == true && Core::getDBtype() == 'pgsql')) {
					if (is_int($column['default_value'])) {
						if ($column['type'] == 'boolean') {
							$fsql .= ' DEFAULT ';
							$fsql .= ($column['default_value']) ? 'true' : 'false';
						} else {
							$fsql .= ' DEFAULT ' . $column['default_value'];
						}
					} else {
						$fsql .= ' DEFAULT \'' . $column['default_value'] . '\'';
					}
				}
			}
			return $fsql;
		}

		protected function _getTableNameSQL()
		{
			$qc = $this->getQC();
			$sql = $qc . Core::getTablePrefix() . $this->b2db_name . $qc;

			return $sql;
		}

		protected function _createToSQL()
		{
			$sql = '';
			$qc = $this->getQC();
			$sql .= "CREATE TABLE " . $this->_getTableNameSQL() . " (\n";
			$field_sql = array();
			foreach ($this->_columns as $column) {
				$_sql = " $qc" . $this->_getRealColumnFieldName($column['name']) . "$qc ";
				$field_sql[] = $_sql . $this->_getColumnDefinitionSQL($column);
			}
			$sql .= join(",\n", $field_sql);
			$sql .= ", PRIMARY KEY ($qc" . $this->_getRealColumnFieldName($this->id_column) . "$qc) ";
			$sql .= ') ';
			if (Core::getDBtype() != 'pgsql') $sql .= 'AUTO_INCREMENT=' . $this->_autoincrement_start_at . ' ';
			if (Core::getDBtype() != 'pgsql') $sql .= 'CHARACTER SET ' . $this->_charset;

			return $sql;
		}

		protected function _getAddColumnSQL($column, $details)
		{
			$qc = $this->getQC();

			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$sql .= " ADD COLUMN $qc" . $this->_getRealColumnFieldName($details['name']) . "$qc " . $this->_getColumnDefinitionSQL($details);

			return $sql;
		}

		protected function _getAlterColumnSQL($column, $details)
		{
			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$qc = $this->getQC();
			switch (Core::getDBtype()) {
				case 'mysql':
					$sql .= " MODIFY $qc" . $this->_getRealColumnFieldName($details['name']) . "$qc ";
					break;
				case 'pgsql':
					$sql .= " ALTER COLUMN $qc" . $this->_getRealColumnFieldName($details['name']) . "$qc TYPE ";
					break;
			}
			$sql .= $this->_getColumnDefinitionSQL($details);
			
			return $sql;
		}

		protected function _getDropColumnSQL($column)
		{
			$sql = 'ALTER TABLE ' . $this->_getTableNameSQL();
			$sql .= ' DROP COLUMN ' . $this->_getRealColumnFieldName($column);

			return $sql;
		}

		protected function _migrateData(Table $old_table) { }

		/**
		 * Perform upgrade for a table, by comparing one table to an old version
		 * of the same table
		 *
		 * @param Table $old_table
		 */
		public function upgrade(Table $old_table)
		{
			if ($old_table->getVersion() != ($this->getVersion() - 1))
				throw new Exception('Cannot upgrade from ' . get_class($old_table) . ' version ' . $old_table->getVersion() . ', must be version ' . ($this->getVersion() - 1));
			
			$old_columns = $old_table->getColumns();
			$new_columns = $this->getColumns();
			
			$added_columns = \array_diff_key($new_columns, $old_columns);
			$altered_columns = Tools::array_diff_recursive($old_columns, $new_columns);
			$dropped_columns = \array_keys(array_diff_key($old_columns, $new_columns));

			$sqls = array();
			foreach ($added_columns as $column => $details) {
				$sqls[] = $this->_getAddColumnSQL($column, $details);
			}
			if (count($sqls)) {
				foreach ($sqls as $sqlStmt) {
					$statement = Statement::getPreparedStatement($sqlStmt);
					$res = $statement->performQuery('alter');
				}
			}

			$this->_migrateData($old_table);

			$sqls = array();
			foreach ($altered_columns as $column => $details) {
				if (in_array($column, $dropped_columns)) continue;
				$sqls[] = $this->_getAlterColumnSQL($column, $new_columns[$column]);
			}
			foreach ($dropped_columns as $details) {
				$sqls[] = $this->_getDropColumnSQL($details);
			}
			if (count($sqls)) {
				foreach ($sqls as $sqlStmt) {
					$statement = Statement::getPreparedStatement($sqlStmt);
					$res = $statement->performQuery('alter');
				}
			}
		}

		protected function _populateFromRow($row = null, $classname = null, $id_column = null)
		{
			$item = null;
			if ($row) {
				$classname = ($classname !== null) ? $classname : Core::getCachedTableEntityClass(\get_class($this));
				if (!$classname)
					throw new Exception("Classname '{$classname}' for table '{$this->getB2DBName()}' is not valid");

				$id_column = ($id_column !== null) ? $id_column : $row->getCriteria()->getTable()->getIdColumn();
				$row_id = $row->get($id_column);
				$item = new $classname($row_id, $row);
			}
			return $item;
		}

		protected function _populateFromResultset($resultset = null, $classname = null, $id_column = null, $index_column = null)
		{
			$items = array();
			if ($resultset instanceof Resultset) {
				$criteria = $resultset->getCriteria();
				$id_column = ($id_column !== null) ? $id_column : $criteria->getTable()->getIdColumn();
				if ($index_column === null) {
					$index_column = ($criteria->getIndexBy()) ? $criteria->getIndexBy() : $id_column;
				}
				$classnames = Core::getCachedTableEntityClasses(\get_class($this));
				if ($classname === null) {
					$classname = Core::getCachedTableEntityClass(\get_class($this));
				}
				while ($row = $resultset->getNextRow()) {
					if ($classnames) {
						$identifier = $row->get($this->getB2DBName() . '.' . $classnames['identifier']);
						$classname = (\array_key_exists($identifier, $classnames['classes'])) ? $classnames['classes'][$identifier] : null;
						if (!$classname) {
							throw new Exception("No classname has been specified in the @SubClasses annotation for identifier '{$identifier}'");
						}
					}
					$item = $this->_populateFromRow($row, $classname, $id_column);
					$items[$row->get($index_column)] = $item;
				}
			}
			return $items;
		}

		public function generateForeignItemsCriteria(Saveable $class, $relation_details)
		{
			$criteria = $this->getCriteria();
			$foreign_table = $class->getB2DBTable();
			$foreign_table_class = \get_class($foreign_table);
			$item_class = (array_key_exists('class', $relation_details)) ? $relation_details['class'] : null;
			$item_column = null;
			if ($relation_details['manytomany']) {
				$item_table_class = Core::getCachedB2DBTableClass($item_class);
			}
			if ($relation_details['foreign_column']) {
				$saveable_class = \get_class($class);
				$table_details = ($item_class) ? Core::getCachedTableDetails($item_class) : Core::getTableDetails($relation_details['joinclass']);
				$criteria->addWhere("{$table_details['name']}.".$relation_details['foreign_column'], $class->getB2DBSaveablePropertyValue(Core::getCachedColumnPropertyName($saveable_class, $foreign_table->getIdColumn())));
				if (array_key_exists('discriminator', $table_details) && $table_details['discriminator'] && array_key_exists($saveable_class, $table_details['discriminator']['discriminators'])) {
					$criteria->addWhere($table_details['discriminator']['column'], $table_details['discriminator']['discriminators'][$saveable_class]);
				}
			} else {
				foreach ($this->getForeignColumns() as $column => $details) {
					if ($details['class'] == $foreign_table_class) {
						$foreign_column = ($details['key']) ? $details['key'] : $foreign_table->getIdColumn();
						$property_name = Core::getCachedColumnPropertyName(Core::getCachedTableEntityClass($details['class']), $foreign_column);
						$value = $class->getB2DBSaveablePropertyValue($property_name);
						$criteria->addWhere($column, $value);
					} elseif ($item_class && $details['class'] == $item_table_class) {
						$item_column = $column;
					}
				}
			}
			return array($criteria, $item_class, $item_column);
		}

		public function getForeignItems(Saveable $class, $relation_details)
		{
			list ($criteria, $item_class, $item_column) = $this->generateForeignItemsCriteria($class, $relation_details);
			if (!$item_class) {
				$items = array();
				$resultset = $this->doSelect($criteria);
				if ($resultset) {
					$column = "{$this->getB2DBName()}.".$relation_details['column'];
					while ($row = $resultset->getNextRow()) {
						$items[] = $row->get($column);
					}
				}
				return $items;
			} elseif (!$relation_details['manytomany']) {
				return $this->select($criteria);
			} else {
				$resultset = $this->doSelect($criteria);
				return $this->_populateFromResultset($resultset, $item_class, $item_column, $item_column);
			}
		}

		public function countForeignItems(Saveable $class, $relation_details)
		{
			list ($criteria, $item_class, $item_column) = $this->generateForeignItemsCriteria($class, $relation_details);
			$result = $this->doCount($criteria);
			return $result;
		}

	}
