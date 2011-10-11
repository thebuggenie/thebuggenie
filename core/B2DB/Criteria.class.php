<?php

	namespace b2db;
	
	/**
	 * Criteria class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * Criteria class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Criteria
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
		 * @var Table
		 */
		protected $fromtable;

		protected $limit = null;
		protected $offset = null;
		protected $customsel = false;
		public $action;
		public $sql;

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
		const DB_LOWER = 'LOWER';
		const DB_DISTINCT = 'DISTINCT';
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
		 * @param \b2db\Table $table The table
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
		 * @return Criterion
		 */
		public function returnCriterion($column, $value, $operator = self::DB_EQUALS)
		{
			$ret = new Criterion($column, $value, $operator);

			return $ret;
		}

		/**
		 * Get added values
		 *
		 * @return array
		 */
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
			if (!$this->fromtable instanceof Table)
			{
				throw new \Exception('You must set the from-table before adding selection columns');
			}
			$this->customsel = true;
			$column = $this->getSelectionColumn($column);
			$alias = ($alias === '') ? str_replace('.', '_', $column) : $alias;
			$this->_addSelectionColumn($column, $alias, $special, $variable, $additional);
			return $this;
		}

		protected function _addSelectionColumn($column, $alias = '', $special = '', $variable = '', $additional = '')
		{
			$this->selections[Core::getTablePrefix() . $column] = array('column' => $column, 'alias' => $alias, 'special' => $special, 'variable' => $variable, 'additional' => $additional);
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
		public function addOr($column, $value = null, $operator = self::DB_EQUALS)
		{
			if ($column instanceof Criterion)
			{
				$this->ors[] = $column;
			}
			else
			{
				$this->ors[] = new Criterion($column, $value, $operator);
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
				throw new Exception("Invalid value, can't be an object.");
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
			if ($column instanceof Criterion)
			{
				$this->criterias[] = $column;
			}
			else
			{
				$this->criterias[] = new Criterion($column, $value, $operator, $variable, $additional, $special);
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
			$this->criterias[$column] = array('column' => $column, 'value' => $value, 'operator' => $operator, 'variable' => $variable);
			return $this;
		}

		/**
		 * Join one table on another
		 *
		 * @param Table $jointable The table to join
		 * @param string $col1 The left matching column
		 * @param string $col2 The right matching column
		 * @param array $criterias An array of criteria (ex: array(array(DB_FLD_ISSUE_ID, 1), array(DB_FLD_ISSUE_STATE, 1));
		 * @param string $jointype Type of join
		 * @param Table $ontable If different than the main table, specify the left side of the join here
		 *
		 * @return Base2DBCriteria
		 */
		public function addJoin($jointable, $foreigncol, $tablecol, $criterias = array(), $jointype = self::DB_LEFT_JOIN, $ontable = null)
		{
			if (!$jointable instanceof Table)
			{
				throw new Exception('Cannot join table ' . $jointable . ' since it is not a table');
			}
			foreach ($this->jointables as $ajt)
			{
				if ($ajt['jointable']->getB2DBAlias() == $jointable->getB2DBAlias())
				{
					$jointable = clone $jointable;
					break;
				}
			}
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Cannot use ' . print_r($this->fromtable) . ' as a table. You need to call setTable() before trying to join a new table');
			}
			$col1 = $jointable->getB2DBAlias() . '.' . $this->getColumnName($foreigncol);
			if ($ontable === null)
			{
				$col2 = $this->fromtable->getB2DBAlias() . '.' . $this->getColumnName($tablecol);
			}
			else
			{
				$col2 = $ontable->getB2DBAlias() . '.' . $this->getColumnName($tablecol);
			}

			$this->jointables[$jointable->getB2DBAlias()] = array('jointable' => $jointable, 'col1' => $col1, 'col2' => $col2, 'original_column' => $this->getRealColumnName($tablecol), 'criterias' => $criterias, 'jointype' => $jointype);
			return $jointable;
		}

		public function getRealColumnName($column)
		{
			$column_details = explode('.', $column);
			$table_alias = $column_details[0];
			$column = $column_details[1];
			if ($table_alias == $this->fromtable->getB2DBAlias() || $table_alias == $this->fromtable->getB2DBName())
			{
				$real_table_name = $this->fromtable->getB2DBName();
			}
			else
			{
				foreach ($this->getForeignTables() as $alias => $join_details)
				{
					if ($table_alias == $alias || $table_alias == $join_details['jointable']->getB2DBName())
					{
						$real_table_name = $join_details['jointable']->getB2DBName();
						break;
					}
				}
			}
			return "{$real_table_name}.{$column}";
		}

		/**
		 * Generates "select all" SQL
		 *
		 * @return string
		 */
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

		/**
		 * Adds all select columns from all available tables in the query
		 */
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

		/**
		 * Retrieve a list of foreign tables
		 *
		 * @return array
		 */
		public function getForeignTables()
		{
			return $this->jointables;
		}

		/**
		 * Returns the table the criteria applies to
		 *
		 * @return Table
		 */
		public function getTable()
		{
			return $this->fromtable;
		}

		/**
		 * Get the column name part of a column
		 *
		 * @param string $column
		 *
		 * @return string
		 */
		public function getColumnName($column)
		{
			if (mb_stripos($column, '.') > 0)
			{
				return mb_substr($column, mb_stripos($column, '.') + 1);
			}
			else
			{
				return $column;
			}
		}

		/**
		 * Get all select columns
		 *
		 * @return array
		 */
		public function getSelectionColumns()
		{
			return $this->selections;
		}

		/**
		 * Return a select column
		 *
		 * @param string $column
		 * @param string $join_column[optional]
		 * @param boolean $debug[optional]
		 *
		 * @return string
		 */
		public function getSelectionColumn($column, $join_column = null, $throw_exceptions = true)
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
			
			if ($throw_exceptions)
			{
				throw new Exception("Couldn't find table name '{$table_name}' for column '{$column_name}', column was '{$column}'. If this is a column from a foreign table, make sure the foreign table is joined.");
			}
			else
			{
				return null;
			}
		}

		/**
		 * Get the selection alias for a specified column
		 *
		 * @param string $column
		 *
		 * @return string
		 */
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
					throw new Exception('Invalid column!');
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

		/**
		 * Add all available foreign tables
		 *
		 * @param array $join [optional]
		 */
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

		/**
		 * Generate a "select" query
		 *
		 * @param boolean $all [optional]
		 */
		public function generateSelectSQL($all = false)
		{
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Trying to run a query when no table is being used.');
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

		/**
		 * Generate a "count" query
		 */
		public function generateCountSQL()
		{
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'count';
			$sql = $this->_generateCountSQL();
			$sql .= $this->_generateJoinSQL();
			$sql .= $this->_generateWhereSQL();

			$this->sql = $sql;
		}

		/**
		 * Add a specified value
		 *
		 * @param mixed $value
		 */
		protected function _addValue($value)
		{
			if (is_bool($value))
			{
				if (Core::getDBtype() == 'mysql')
				{
					$this->values[] = (int) $value;
				}
				elseif (Core::getDBtype() == 'pgsql')
				{
					$this->values[] = ($value) ? 'true' : 'false';
				}
			}
			else
			{
				$this->values[] = $value;
			}
		}

		/**
		 * Generate an "update" query
		 */
		public function generateUpdateSQL()
		{
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'update';
			$sql = $this->_generateUpdateSQL();
			$sql .= $this->_generateWhereSQL(true);

			$this->sql = $sql;
		}

		/**
		 * Generate an "insert" query
		 */
		public function generateInsertSQL()
		{
			foreach ($this->criterias as $a_crit)
			{
				if ($a_crit instanceof Criterion)
				{
					throw new Exception('Please use \b2db\Criteria::addInsert() when inserting values into a table.');
				}
			}
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'insert';
			$sql = $this->_generateInsertSQL();

			$this->sql = $sql;
		}

		/**
		 * Generate a "delete" query
		 */
		public function generateDeleteSQL()
		{
			if (!$this->fromtable instanceof Table)
			{
				throw new Exception('Trying to run a query when no table is being used.');
			}
			$this->values = array();
			$this->sql = '';
			$this->action = 'delete';
			$sql = $this->_generateDeleteSQL();
			$sql .= $this->_generateWhereSQL(true);

			$this->sql = $sql;
		}

		/**
		 * Generate the "delete" part of the query
		 *
		 * @return string
		 */
		protected function _generateDeleteSQL()
		{
			$sql = 'DELETE FROM ';
			$sql .= Core::getTablePrefix() . $this->fromtable->getB2DBName();

			return $sql;
		}

		/**
		 * Generate the "insert" part of the query
		 *
		 * @return string
		 */
		protected function _generateInsertSQL()
		{
			$sql = 'INSERT INTO ';
			if (Core::getDBtype() == 'mysql') $sql .= "`";
			$sql .= Core::getTablePrefix() . $this->fromtable->getB2DBName();
			if (Core::getDBtype() == 'mysql') $sql .= "`";
			$sql .= ' (';
			$first_ins = true;
			foreach ($this->criterias as $a_crit)
			{
				$sql .= (!$first_ins) ? ', ' : '';
				if (Core::getDBtype() == 'pgsql')
				{
					$sql .= '"';
				}
				elseif (Core::getDBtype() == 'mysql')
				{
					$sql .= '`';
				}
				$sql .= mb_substr($a_crit['column'], mb_strpos($a_crit['column'], '.') + 1);
				if (Core::getDBtype() == 'pgsql')
				{
					$sql .= '"';
				}
				elseif (Core::getDBtype() == 'mysql')
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

		/**
		 * Generate the "update" part of the query
		 *
		 * @return string
		 */
		protected function _generateUpdateSQL()
		{
			$sql = 'UPDATE ';
			$sql .= Core::getTablePrefix() . $this->fromtable->getB2DBName();
			$sql .= ' SET ';
			$first_upd = true;
			foreach ($this->updates as $an_upd)
			{
				$sql .= (!$first_upd) ? ', ' : '';
				if (Core::getDBtype() == 'mysql') $sql .= '`';
				$sql .= mb_substr($an_upd['column'], mb_strpos($an_upd['column'], '.') + 1);
				if (Core::getDBtype() == 'mysql') $sql .= '`';
				$sql .= self::DB_EQUALS;
				$sql .= '?';
				$this->_addValue($an_upd['value']);
				$first_upd = false;
			}
			return $sql;
		}

		/**
		 * Generate the "select" part of the query
		 *
		 * @return string
		 */
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
				if ($this->distinct && Core::getDBtype() == 'pgsql')
				{
					foreach ($this->sort_orders as $a_sort)
					{
						$this->addSelectionColumn($a_sort['column']);
					}
				}
				foreach ($this->selections as $column => $a_sel)
				{
					if ($a_sel['special'] != '')
					{
						$sql .= (!$first_sel) ? ', ' : '';
						if ($a_sel['variable'] != '')
						{
							$sql .= ' @' . $a_sel['variable'] . ':=';
						}
						$sql .= mb_strtoupper($a_sel['special']) . '(' . $a_sel['column'] . ')';
						$sql .= ($a_sel['additional'] != '') ? ' ' . $a_sel['additional'] . ' ' : '';
						if (mb_strlen(mb_stristr($a_sel['special'], '(')) > 0)
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
						$sql .= $a_sel['column'];
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

		/**
		 * Generate the "count" part of the query
		 *
		 * @return string
		 */
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

		/**
		 * Set the query to distinct mode
		 */
		public function setDistinct()
		{
			$this->distinct = true;
		}

		/**
		 * Parses the given criterion and returns the SQL string
		 *
		 * @param Criterion $critn
		 * @param boolean $strip
		 * @return string
		 */
		protected function _parseCriterion($critn, $strip = false)
		{
			if (!$critn instanceof Criterion)
			{
				throw new Exception('The $critn parameter must be of type Criterion');
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
				if (!$a_crit['column'] instanceof Criterion)
				{
					if (!in_array($a_crit['operator'], array(self::DB_EQUALS, self::DB_GREATER_THAN, self::DB_GREATER_THAN_EQUAL, self::DB_ILIKE, self::DB_IN, self::DB_IS_NOT_NULL, self::DB_IS_NULL, self::DB_LESS_THAN, self::DB_LESS_THAN_EQUAL, self::DB_LIKE, self::DB_NOT_EQUALS, self::DB_NOT_ILIKE, self::DB_NOT_IN, self::DB_NOT_LIKE)))
						throw new Exception("Invalid operator");
					
					if (isset($a_crit['special']) && $a_crit['special'] != '')
					{
						$sql .= $a_crit['special'] . '(';
					}
					$sql .= ($strip) ? $this->getColumnName($a_crit['column']) : $this->getSelectionColumn($a_crit['column']);
					if (isset($a_crit['special']) && $a_crit['special'] != '')
					{
						$sql .= ')';
					}
					if (is_null($a_crit['value']) && !in_array($a_crit['operator'], array(self::DB_IS_NOT_NULL, self::DB_IS_NULL)))
					{
						$a_crit['operator'] = ($a_crit['operator'] == self::DB_EQUALS) ? self::DB_IS_NULL : self::DB_IS_NOT_NULL; 
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
				if (!$an_or['column'] instanceof Criterion)
				{
					if (!in_array($an_or['operator'], array(self::DB_EQUALS, self::DB_GREATER_THAN, self::DB_GREATER_THAN_EQUAL, self::DB_ILIKE, self::DB_IN, self::DB_IS_NOT_NULL, self::DB_IS_NULL, self::DB_LESS_THAN, self::DB_LESS_THAN_EQUAL, self::DB_LIKE, self::DB_NOT_EQUALS, self::DB_NOT_ILIKE, self::DB_NOT_IN, self::DB_NOT_LIKE)))
						throw new Exception("Invalid operator");
					
					$sql .= ($strip) ? $this->getColumnName($an_or['column']) : $this->getSelectionColumn($an_or['column']);
					if (is_null($an_or['value']) && !in_array($an_or['operator'], array(self::DB_IS_NOT_NULL, self::DB_IS_NULL)))
					{
						$an_or['operator'] = ($an_or['operator'] == self::DB_EQUALS) ? self::DB_IS_NULL : self::DB_IS_NOT_NULL; 
					}
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
					elseif ($an_or['operator'] == self::DB_IS_NULL || $an_or['operator'] == self::DB_IS_NOT_NULL)
					{
						// don't do anything, since that's taken care of by the operator
					}
					else
					{
						$sql .= '?';
						$this->_addValue($an_or['value']);
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

		/**
		 * Generate the "where" part of the query
		 *
		 * @param boolean $strip[optional]
		 *
		 * @return string
		 */
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
			if (count($this->sort_groups) > 0 || (count($this->sort_orders) > 0 && $this->action == 'count'))
			{
				$first_crit = true;
				$group_columns = array();
				$sql .= ' GROUP BY ';
				foreach ($this->sort_groups as $a_group)
				{
					if (!$first_crit)
					{
						$sql .= ', ';
					}
					$column_name = $this->getSelectionColumn($a_group['column']);
					$sql .= $column_name . ' ' . $a_group['sort'];
					$first_crit = false;
					if ($this->action == 'count')
					{
						$group_columns[$column_name] = $column_name;
					}
				}
				if ($this->action == 'count')
				{
					foreach ($this->sort_orders as $a_sort)
					{
						$column_name = $this->getSelectionColumn($a_sort['column']);
						if (!array_key_exists($column_name, $group_columns))
						{
							if (!$first_crit)
							{
								$sql .= ', ';
							}
							$sql .= $column_name . ' ';
							$first_crit = false;
						}
					}
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
					if (is_array($a_sort['sort']))
					{
						$sqls = array();
						foreach ($a_sort['sort'] as $sort_elm)
						{
							$sqls[] = $this->getSelectionColumn($a_sort['column']) . '=' . $sort_elm;
						}
						$sql .= join(',', $sqls);
					}
					else
					{
						$sql .= $this->getSelectionColumn($a_sort['column']) . ' ' . $a_sort['sort'];
					}
					$first_crit = false;
				}
			}
			if ($this->action == 'select')
			{
				if ($this->limit != null)
				{
					$sql .= ' LIMIT ' . (int) $this->limit;
				}
				if ($this->offset != null)
				{
					$sql .= ' OFFSET ' . (int) $this->offset;
				}
			}

			return $sql;
		}

		/**
		 * Generate the "join" part of the sql
		 *
		 * @return string
		 */
		protected function _generateJoinSQL()
		{
			$sql = ' FROM ' . Core::getTablePrefix() . $this->fromtable->getB2DBName() . ' ' . $this->fromtable->getB2DBAlias();
			foreach ($this->jointables as $a_jt)
			{
				$sql .= ' ' . $a_jt['jointype'] . ' ' . Core::getTablePrefix() . $a_jt['jointable']->getB2DBName() . ' ' . $a_jt['jointable']->getB2DBAlias();
				$sql .= ' ON (' . $a_jt['col1'] . self::DB_EQUALS . $a_jt['col2'];
				foreach ($a_jt['criterias'] as $a_crit)
				{
					$sql .= ' AND ';
					$a_crit = new Criterion($a_crit[0], $a_crit[1]);
					$sql .= $this->_parseCriterion($a_crit);
				}
				$sql .= ')';
			}

			return $sql;
		}

	}
