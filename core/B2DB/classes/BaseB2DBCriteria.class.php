<?php

	/**
	 * B2DB Criteria Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Criteria Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBCriteria
	{
		protected $criterias = array();
		protected $jointables = array();
		protected $sort_orders = array();
		protected $sort_groups = array();
		protected $selections = array();
		protected $values = array();
		protected $distinct = false;
		protected $ors = array();
		protected $updates = array();
		protected $aliases = array(); 
		protected $return_selections = array();
		
		/**
		 * Parent table
		 *
		 * @var BaseB2DBTable
		 */
		protected $fromtable;

		protected $limit = null;
		protected $offset = null;
		protected $customsel = false;
		public $action;
		protected $sql;

		const DB_EQUALS = '=';
		const DB_NOT_EQUALS = '!=';
		const DB_GREATER_THAN = '>';
		const DB_LESS_THAN = '<';
		const DB_GREATER_THAN_EQUAL = '>=';
		const DB_LESS_THAN_EQUAL = '<=';
		const DB_IS_NULL = 'IS NULL';
		const DB_IS_NOT_NULL = 'IS NOT NULL';
		const DB_LIKE = 'LIKE';
		const DB_ILIKE = 'ILIKE';
		const DB_NOT_LIKE = 'NOT LIKE';
		const DB_NOT_ILIKE = 'NOT ILIKE';
		const DB_IN = 'IN';
		const DB_NOT_IN = 'NOT IN';
		const DB_LEFT_JOIN = 'LEFT JOIN';
		const DB_INNER_JOIN = 'INNER JOIN';
		const DB_RIGHT_JOIN = 'RIGHT JOIN';
		const DB_COUNT = 'COUNT';
		const DB_MAX = 'MAX';
		const DB_SUM = 'SUM';
		const DB_CONCAT = 'CONCAT';
		const DB_COUNT_DISTINCT = 'COUNT(DISTINCT';
		
		const SORT_ASC = 'asc';
		const SORT_DESC = 'desc';
		
		/**
		 * Constructor
		 * 
		 * @param object $table [optional]
		 * 
		 * @return Base2DBCriteria 
		 */
		public function __construct($table = null, $setupjointable = false)
		{
			if ($table !== null)
			{
				$this->setFromTable($table, $setupjointable);
			}
			return $this;
		}
		
		/**
		 * Set the "from" table
		 * 
		 * @param BaseB2DBtable $table The table
		 * @param boolean $setupjointables [optional] Whether to automatically join other tables
		 * 
		 * @return Base2DBCriteria
		 */
		public function setFromTable($table, $setupjointables = false)
		{
			$this->fromtable = $table;
			if ($setupjointables)
			{
				$this->setupJoinTables();
			}
			return $this;
		}

		/**
		 * Returns a criterion, to use with more advanced SQL queries
		 *
		 * @param string $column
		 * @param mixed  $value
		 * @param string $operator
		 * 
		 * @return B2DBCriterion
		 */
		public function returnCriterion($column, $value, $operator = self::DB_EQUALS)
		{
			$ret = new B2DBCriterion($column, $value, $operator);

			return $ret;
		}
		
		public function getValues()
		{
			return $this->values;
		}
		
		/**
		 * Add a column to select
		 * 
		 * @param string $column The column
		 * @param string $alias [optional] An alias for the column
		 * @param string $special [optional] Whether to use a special method on the column
		 * @param string $variable [optional] An optional variable to assign it to 
		 * @param string $additional [optional] Additional parameter
		 * 
		 * @return Base2DBCriteria
		 */
		public function addSelectionColumn($column, $alias = '', $special = '', $variable = '', $additional = '')
		{
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new Exception('You must set the from-table before adding selection columns');
			}
			$this->customsel = true;
			$column = $this->getSelectionColumn($column);
			$this->_addSelectionColumn($column, $alias, $special, $variable, $additional);
			return $this;
		}

		protected function _addSelectionColumn($column, $alias = '', $special = '', $variable = '', $additional = '')
		{
			$this->selections[B2DB::getTablePrefix() . $column] = array('column' => $column, 'alias' => $alias, 'special' => $special, 'variable' => $variable, 'additional' => $additional);
		}
		
		/**
		 * Adds an "or" part to the query
		 * 
		 * @param string $column The column to update
		 * @param mixed $value The value
		 * @param mixed $operator [optional]
		 * 
		 * @return Base2DBCriteria
		 */
		public function addOr($column, $value, $operator = self::DB_EQUALS)
		{
			if ($column instanceof B2DBCriterion)
			{
				$this->ors[] = $column;
			}
			else
			{
				$this->ors[] = new B2DBCriterion($column, $value, $operator);
			}
			return $this;
		}
		
		/**
		 * Add a field to update
		 * 
		 * @param string $column The column name
		 * @param mixed $value The value to update
		 * 
		 * @return Base2DBCriteria 
		 */
		public function addUpdate($column, $value)
		{
			if (is_object($value))
			{
				throw new B2DBException("Invalid value, can't be an object.");
			}
			$this->updates[] = array('column' => $column, 'value' => $value);
			return $this;
		}

		/**
		 * Adds a "where" part to the criteria
		 *
		 * @param mixed  $column
		 * @param mixed  $value
		 * @param string $operator
		 * @param string $variable
		 * @param mixed  $additional
		 * @param string $special
		 * 
		 * @return Base2DBCriteria
		 */
		public function addWhere($column, $value = '', $operator = self::DB_EQUALS, $variable = '', $additional = '', $special = '')
		{
			if ($column instanceof B2DBCriterion)
			{
				$this->criterias[] = $column;
			}
			else
			{
				$this->criterias[] = new B2DBCriterion($column, $value, $operator, $variable, $additional, $special);
			}
			return $this;
		}
		
		/**
		 * Adds an "insert" part to the criteria
		 * 
		 * @param string $column The name of the column
		 * @param mixed $value The value to insert
		 * @param mixed $operator [optional] The operator
		 * @param mixed $variable [optional] assigns the inserted value a variable to use later in the transaction
		 * 
		 * @return Base2DBCriteria 
		 */
		public function addInsert($column, $value, $operator = self::DB_EQUALS, $variable = '')
		{
			if ($value === null)
			{
				throw new B2DBException('You must specify a value to insert');
			}
			elseif (is_bool($value))
			{
				$value = ($value) ? 1 : 0;
			}
			$this->criterias[$column] = array('column' => $column, 'value' => $value, 'operator' => $operator, 'variable' => $variable);
			return $this;
		}

		/**
		 * Join one table on another
		 *
		 * @param BaseB2DBTable $jointable The table to join
		 * @param string $col1 The left matching column
		 * @param string $col2 The right matching column
		 * @param array $criterias An array of criteria (ex: array(array(DB_FLD_ISSUE_ID, 1), array(DB_FLD_ISSUE_STATE, 1));
		 * @param string $jointype Type of join
		 * 
		 * @return Base2DBCriteria
		 */
		public function addJoin($jointable, $foreigncol, $tablecol, $criterias = array(), $jointype = self::DB_LEFT_JOIN)
		{
			if (!$jointable instanceof BaseB2DBTable)
			{
				throw new B2DBException('Cannot join table ' . $jointable . ' since it is not a table');
			}
			foreach ($this->jointables as $ajt)
			{
				if ($ajt['jointable']->getB2DBAlias() == $jointable->getB2DBAlias())
				{
					$jointable = clone $jointable;
					break;
				}
			}
			if (!$this->fromtable instanceof BaseB2DBTable)
			{
				throw new B2DBException('Cannot use ' . print_r($this->fromtable) . ' as a table. You need to call setTable() before trying to join a new table');
			}
			$col1 = $jointable->getB2DBAlias() . '.' . $this->getColumnName($foreigncol);
			$col2 = $this->fromtable->getB2DBAlias() . '.' . $this->getColumnName($tablecol);
			
			$this->jointables[$jointable->getB2DBAlias()] = array('jointable' => $jointable, 'col1' => $col1, 'col2' => $col2, 'criterias' => $criterias, 'jointype' => $jointype);
			return $this;
		}

		protected function _generateSelectAllSQL()
		{
			$sqls = array();
			foreach ($this->getSelectionColumns() as $column_name => $column_data)
			{
				$str = '';
				$str = $column_data['column'] . ' AS ' . $this->getSelectionAlias($column_data['column']);
				$sqls[] = $str;
			}
			return join(', ', $sqls);
		}
		
		protected function _addAllSelectColumns()
		{
			foreach ($this->fromtable->getAliasColumns() as $aColumn)
			{
				$this->_addSelectionColumn($aColumn);
			}
			foreach ($this->getForeignTables() as $table)
			{
				foreach ($table['jointable']->getAliasColumns() as $aColumn)
				{
					$this->_addSelectionColumn($aColumn);
				}
			}
		}
		
		public function getForeignTables()
		{
			return $this->jointables;
		}

		/**
		 * Returns the table the criteria applies to
		 *
		 * @return B2DBTable
		 */
		public function getTable()
		{
			return $this->fromtable;
		}
		
		public function getColumnName($column)
		{
			if (stripos($column, '.') > 0)
			{
				return substr($column, stripos($column, '.') + 1);
			}
			else
			{
				return $column;
			}
		}
		
		public function getSelectionColumns()
		{
			return $this->selections;
		}
		
		public function getSelectionColumn($column, $join_column = null)
		{
			if ($column instanceof B2DBCriterion)
			{
				throw new B2DBException('Invalid column type B2DBCriterion');
			}
			try
			{
				if (isset($this->selections[$column])) return $this->selections[$column];
				$retval = '';
				foreach ($this->selections as $a_sel)
				{
					if ($a_sel['alias'] == $column)
					{
						return $column;
					}
				}
				list($table_name, $column_name) = explode('.', $column);
				if ($join_column === null)
				{
					if ($this->fromtable->getB2DBAlias() == $table_name || $this->fromtable->getB2DBName() == $table_name)
					{
						$retval = $this->fromtable->getB2DBAlias() . '.' . $column_name;
						return $retval;
					}
					if (isset($this->jointables[$table_name])) return $this->jointables[$table_name]['jointable']->getB2DBAlias() . '.' . $column_name;
				}
				foreach ($this->jointables as $a_table)
				{
					if (($join_column !== null && $a_table['col2'] == $join_column) || ($join_column === null && $a_table['jointable']->getB2DBName() == $table_name))
					{
						$retval = $a_table['jointable']->getB2DBAlias() . '.' . $column_name;
						return $retval;
					}
				}
			}
			catch (Exception $e)
			{
				throw $e;
			}
			throw new B2DBException('Couldn\'t find table name \'' . $table_name . '\' for column \'' . $column_name . '\', column was \'' . $column . '\'. If this is a column from a foreign table, make sure the foreign table is joined.');
		}
		
		public function getSelectionAlias($column)
		{
			if (!is_numeric($column) && !is_string($column))
			{
				if (is_array($column) && array_key_exists('column', $column))
				{
					$column = $column['column'];
				}
				else
				{
					die(var_dump($column));
					throw new B2DBException('Invalid column!');
				}
			}
			if (!isset($this->aliases[$column]))
			{
				$this->aliases[$column] = str_replace('.', '_', $column);
			}
			return $this->aliases[$column];
		}

		/**
		 * Add an order by clause
		 * 
		 * @param string $column The column to order by
		 * @param string $sort [optional] The sort order
		 * 
		 * @return Base2DBCriteria
		 */
		public function addOrderBy($column, $sort = null, $join_column = null)
		{
			if ($join_column !== null)
			{
				$column = null;
				foreach ($this->jointables as $table_alias => $join_options)
				{
					if ($join_options['col2'] == $join_column)
					{
						$column = $join_options['jointable']->getSelectionAlias($column);
					}
				}
			}
			if (is_array($column))
			{
				foreach ($column as $a_sort)
				{
					$this->sort_orders[] = array('column' => $a_sort[0], 'sort' => $a_sort[1]);
				}
			}
			else
			{
				$this->sort_orders[] = array('column' => $column, 'sort' => $sort);
			}
			return $this;
		}
		
		/**
		 * Limit the query
		 *  
		 * @param integer $limit The number to limit
		 *
		 * @return Base2DBCriteria
		 */
		public function setLimit($limit)
		{
			$this->limit = (int) $limit;
			return $this;
		}
		
		/**
		 * Add a group by clause
		 * 
		 * @param string $column The column to group by
		 * @param string $sort [optional] The sort order
		 * 
		 * @return Base2DBCriteria
		 */
		public function addGroupBy($column, $sort = null)
		{
			if (is_array($column))
			{
				foreach ($column as $a_sort)
				{
					$this->sort_groups[] = array('column' => $a_sort[0], 'sort' => $a_sort[1]);
				}
			}
			else
			{
				$this->sort_groups[] = array('column' => $column, 'sort' => $sort);
			}
			return $this;
		}
		
		/**
		 * Offset the query
		 *
		 * @param integer $offset The number to offset by
		 *
		 * @return Base2DBCriteria
		 */
		public function setOffset($offset)
		{
			$this->offset = (int) $offset;
			return $this;
		}

		/**
		 * Returns the SQL string for the current criteria
		 *
		 * @return string
		 */
		public function getSQL()
		{
			return $this->sql;
		}

		public function setupJoinTables($join = 'all')
		{
			if (!is_array($join) && $join == 'all')
			{
				foreach ($this->fromtable->getForeignTables() as $aForeign)
				{
					$fTable = array_shift($aForeign);
					$fKey = $fTable->getB2DBAlias() . '.' . $this->getColumnName(array_shift($aForeign));
					$fColumn = $this->fromtable->getB2DBAlias() . '.' . $this->getColumnName(array_shift($aForeign));
					$this->addJoin($fTable, $fKey, $fColumn);
				}
			}
			elseif (is_array($join))
			{
				foreach ($join as $join_column)
				{
					$foreign = $this->fromtable->getForeignTableByLocalColumn($join_column);
					$this->addJoin($foreign['table'], $foreign['table']->getB2DBAlias() . '.' . $this->getColumnName($foreign['key']), $this->fromtable->getB2DBAlias() . '.' . $this->getColumnName($foreign['column']));
				}
			}
			
		}
		
		public function generateSelectSQL($all = false)
		{
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new B2DBException('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'select';
			$sql = $this->_generateSelectSQL();
			$sql .= $this->_generateJoinSQL();
			if (!$all)
			{
				$sql .= $this->_generateWhereSQL();
			}

			$this->sql = $sql;
		}

		public function generateCountSQL()
		{
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new B2DBException('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'count';
			$sql = $this->_generateCountSQL();
			$sql .= $this->_generateJoinSQL();
			$sql .= $this->_generateWhereSQL();

			$this->sql = $sql;
		}
		
		protected function _addValue($value)
		{
			if (is_bool($value))
			{
				//var_dump($value);
				if (B2DB::getDBtype() == 'mysql')
				{
					$this->values[] = (int) $value;
				}
				elseif (B2DB::getDBtype() == 'pgsql')
				{
					$this->values[] = ($value) ? 'true' : 'false';
				}
			}
			else
			{
				$this->values[] = $value;
			}
		}

		public function generateUpdateSQL()
		{
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new B2DBException('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'update';
			$sql = $this->_generateUpdateSQL();
			$sql .= $this->_generateWhereSQL(true);

			$this->sql = $sql;
		}

		public function generateInsertSQL()
		{
			foreach ($this->criterias as $a_crit)
			{
				if ($a_crit instanceof B2DBCriterion)
				{
					throw new B2DBException('Please use B2DBCriteria::addInsert() when inserting values into a table.');
				}
			}
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new B2DBException('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'insert';
			$sql = $this->_generateInsertSQL();
	
			$this->sql = $sql;
		}

		public function generateDeleteSQL()
		{
			if (!$this->fromtable instanceof B2DBTable)
			{
				throw new B2DBException('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'delete';
			$sql = $this->_generateDeleteSQL();
			$sql .= $this->_generateWhereSQL(true);

			$this->sql = $sql;
			return $sql;
		}

		protected function _generateDeleteSQL()
		{
			$sql = 'DELETE FROM ';
			$sql .= B2DB::getTablePrefix() . $this->fromtable->getB2DBName();

			return $sql;
		}

		protected function _generateInsertSQL()
		{
			$sql = 'INSERT INTO ';
			$sql .= B2DB::getTablePrefix() . $this->fromtable->getB2DBName();
			$sql .= '(';
			$first_ins = true;
			foreach ($this->criterias as $a_crit)
			{
				$sql .= (!$first_ins) ? ', ' : '';
				if (B2DB::getDBtype() == 'pgsql')
				{
					$sql .= '"';
				}
				elseif (B2DB::getDBtype() == 'mysql')
				{
					$sql .= '`';
				}
				$sql .= substr($a_crit['column'], strpos($a_crit['column'], '.') + 1);
				if (B2DB::getDBtype() == 'pgsql')
				{
					$sql .= '"';
				}
				elseif (B2DB::getDBtype() == 'mysql')
				{
					$sql .= '`';
				}
				$first_ins = false;
			}
			$sql .= ') values (';
			$first_ins = true;
			foreach ($this->criterias as $a_crit)
			{
				$sql .= (!$first_ins) ? ', ' : '';
				if ($a_crit['variable'] != '')
				{
					$sql .= '@' . $a_crit['variable'];
				}
				else
				{
					$sql .= '?';
					$this->_addValue($a_crit['value']);
				}
				$first_ins = false;
			}
			$sql .= ')';

			return $sql;
		}

		protected function _generateUpdateSQL()
		{
			$sql = 'UPDATE ';
			$sql .= B2DB::getTablePrefix() . $this->fromtable->getB2DBName();
			$sql .= ' SET ';
			$first_upd = true;
			foreach ($this->updates as $an_upd)
			{
				$sql .= (!$first_upd) ? ', ' : '';
				if (B2DB::getDBtype() == 'mysql') $sql .= '`';
				$sql .= substr($an_upd['column'], strpos($an_upd['column'], '.') + 1);
				if (B2DB::getDBtype() == 'mysql') $sql .= '`';
				$sql .= self::DB_EQUALS;
				$sql .= '?';
				$this->_addValue($an_upd['value']);
				$first_upd = false;
			}
			return $sql;
		}

		protected function _generateSelectSQL()
		{
			$sql = 'SELECT ';
			$first_sel = true;
			if ($this->distinct)
			{
				$sql .= ' DISTINCT ';
			}
			if ($this->customsel)
			{
				foreach ($this->selections as $column => $a_sel)
				{
					if ($a_sel['special'] != '')
					{
						$sql .= (!$first_sel) ? ', ' : '';
						if ($a_sel['variable'] != '')
						{
							$sql .= ' @' . $a_sel['variable'] . ':=';
						}
						$sql .= strtoupper($a_sel['special']) . '(' . $a_sel['column'] . ')';
						$sql .= ($a_sel['additional'] != '') ? ' ' . $a_sel['additional'] . ' ' : '';
						if (strlen(stristr($a_sel['special'], '(')) > 0)
						{
							$sql .= ')';
						}
						if ($a_sel['alias'] != '')
						{
							$sql .= ' AS ' . $a_sel['alias'];
						}
						else
						{
							$sql .= ' AS ' . $this->getSelectionAlias($column);
						}
					}
					else
					{
						$sql .= (!$first_sel) ? ', ' : '';
						if (isset($a_sel['variable']) && $a_sel['variable'] != '')
						{
							$sql .= ' @' . $a_sel['variable'] . ':=';
						}
						$sql .= $column;
						if ($a_sel['alias'] != '')
						{
							$sql .= ' AS ' . $a_sel['alias'];
						}
						else
						{
							$sql .= ' AS ' . $this->getSelectionAlias($column);
						}
					}
					if ($a_sel['alias'] == '')
					{
						$a_sel['alias'] = $this->getSelectionAlias($column);
					}
					$first_sel = false;
				}
			}
			else
			{
				$this->_addAllSelectColumns();
				$sql .= $this->_generateSelectAllSQL();
			}

			return $sql;
		}

		protected function _generateCountSQL()
		{
			$sql = 'SELECT COUNT(';
			if ($this->distinct)
			{
				$sql .= ' DISTINCT ';
			}
			$sql .= $this->getSelectionColumn($this->getTable()->getIdColumn());
			$sql .= ') as num_col';

			return $sql;
		}
		
		public function setDistinct()
		{
			$this->distinct = true;
		}

		/**
		 * Parses the given criterion and returns the SQL string
		 *
		 * @param B2DBCriterion $critn
		 * @param boolean $strip
		 * @return string
		 */
		protected function _parseCriterion($critn, $strip = false)
		{
			if (!$critn instanceof B2DBCriterion)
			{
				throw new B2DBException('The $critn parameter must be of type B2DBCriterion');
			}
			$sql = '';
			if (count($critn->ors) > 0)
			{
				$sql .= ' (';
			}
			if (count($critn->wheres) > 0)
			{
				$sql .= '(';
			}
			$first_crit = true;
			foreach ($critn->wheres as $a_crit)
			{
				if (!$first_crit)
				{
					$sql .= ' AND ';
				}
				if (!$a_crit['column'] instanceof B2DBCriterion)
				{
					if (isset($a_crit['special']) && $a_crit['special'] != '')
					{
						$sql .= $a_crit['special'] . '(';
					}
					$sql .= ($strip) ? $this->getColumnName($a_crit['column']) : $this->getSelectionColumn($a_crit['column']);
					if (isset($a_crit['special']) && $a_crit['special'] != '')
					{
						$sql .= ')';
					}
					$sql .= ' ' . $a_crit['operator'] . ' ';
					if (is_numeric($a_crit['value']) && $a_crit['operator'] != self::DB_IN)
					{
						$sql .= '?';
						$this->_addValue($a_crit['value']);
					}
					elseif (is_array($a_crit['value']) || $a_crit['operator'] == self::DB_IN)
					{
						$sql .= '(';
						$first_crit = true;
						if (is_numeric($a_crit['value']))
						{
							$a_crit['value'] = array($a_crit['value']);
						}
						foreach ($a_crit['value'] as $a_val)
						{
							if (!$first_crit)
							{
								$sql .= ', ';
							}
							$sql .= '?';
							$this->_addValue($a_val);
						   	$first_crit = false;
						}
						$sql .= ')';
					}
					elseif ($a_crit['operator'] == self::DB_IS_NULL || $a_crit['operator'] == self::DB_IS_NOT_NULL)
					{
						// don't do anything, since that's taken care of by the operator
					}
					else
					{
						$sql .= '?';
						$this->_addValue($a_crit['value']);
					}
				}
				else
				{
					$sql .= $this->_parseCriterion($a_crit['column']);
				}
				//var_dump($this->values);
				$first_crit = false;
			}
			if (count($critn->wheres) > 0)
			{
				$sql .= ')';
			}
			foreach ($critn->ors as $an_or)
			{
				$sql .= ' OR ';
				if (!$an_or['column'] instanceof B2DBCriterion)
				{
					$sql .= ($strip) ? $this->getColumnName($an_or['column']) : $this->getSelectionColumn($an_or['column']);
					$sql .= ' ' . $an_or['operator'] . ' ';
					if (is_numeric($an_or['value']))
					{
						$sql .= '?';
						$this->_addValue($an_or['value']);
					}
					elseif (is_array($an_or['value']))
					{
						$sql .= '(';
						$first_crit = true;
						foreach ($an_or['value'] as $a_val)
						{
							if (!$first_crit)
							{
								$sql .= ', ';
							}
							$sql .= '?';
							$this->_addValue($a_val);
							$first_crit = false;
						}
						$sql .= ')';
					}
					else
					{
						$sql .= '?';
						$this->_addValue('%' . $an_or['value'] . '%');
					}
				}
				else
				{
					$sql .= $this->_parseCriterion($an_or['column']);
				}
			}
			if (count($critn->ors) > 0)
			{
				$sql .= ') ';
			}

			return $sql;
		}

		protected function _generateWhereSQL($strip = false)
		{
			$sql = '';
			if (count($this->criterias) > 0)
			{
				$sql = ' WHERE ';
				$first_crit = true;
				if (count($this->ors) > 0)
				{
					$sql .= '(';
				}
				foreach ($this->criterias as $a_crit)
				{
					if (!$first_crit)
					{
						$sql .= ' AND ';
					}
					$sql .= $this->_parseCriterion($a_crit, $strip);
					$first_crit = false;
				}
				if (count($this->ors) > 0)
				{
					foreach ($this->ors as $a_crit)
					{
						$sql .= ' OR ';
						$sql .= $this->_parseCriterion($a_crit, $strip);
					}
				}
				if (count($this->ors) > 0)
				{
					$sql .= ')';
				}
			}
			if (count($this->sort_groups) > 0)
			{
				$first_crit = true;
				$sql .= ' GROUP BY ';
				foreach ($this->sort_groups as $a_group)
				{
					if (!$first_crit)
					{
						$sql .= ', ';
					}
					$sql .= '? ' . $a_group['sort'];
					$this->values[] = $this->getSelectionColumn($a_group['column']);
					$first_crit = false;
				}
			}
			if (count($this->sort_orders) > 0)
			{
				$first_crit = true;
				$sql .= ' ORDER BY ';
				foreach ($this->sort_orders as $a_sort)
				{
					if (!$first_crit)
					{
						$sql .= ', ';
					}
					$sql .= $this->getSelectionColumn($a_sort['column']) . ' ' . $a_sort['sort'];
					$first_crit = false;
				}
			}
			if ($this->limit != null && $this->action != 'update')
			{
				$sql .= ' LIMIT ' . (int) $this->limit;
			}
			if ($this->offset != null && $this->action != 'update')
			{
				$sql .= ' OFFSET ' . (int) $this->offset;
			}

			return $sql;
		}

		protected function _generateJoinSQL()
		{
			$sql = ' FROM ' . B2DB::getTablePrefix() . $this->fromtable->getB2DBName() . ' ' . $this->fromtable->getB2DBAlias();
			foreach ($this->jointables as $a_jt)
			{
				$sql .= ' ' . $a_jt['jointype'] . ' ' . B2DB::getTablePrefix() . $a_jt['jointable']->getB2DBName() . ' ' . $a_jt['jointable']->getB2DBAlias();
				$sql .= ' ON (' . $a_jt['col1'] . self::DB_EQUALS . $a_jt['col2'];
				foreach ($a_jt['criterias'] as $a_crit)
				{
					$sql .= ' AND ';
					$a_crit = new B2DBCriterion($a_crit[0], $a_crit[1]);
					$sql .= $this->_parseCriterion($a_crit);
				}
				$sql .= ')';
			}

			return $sql;
		}

	}
