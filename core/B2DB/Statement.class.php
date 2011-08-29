<?php

	namespace b2db;
	
	/**
	 * Statement class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package b2db
	 * @subpackage core
	 */

	/**
	 * Statement class
	 *
	 * @package b2db
	 * @subpackage core
	 */
	class Statement
	{

		/**
		 * Current Criteria
		 *
		 * @var Criteria
		 */
		protected $crit;
		
		/**
		 * PDO statement
		 *
		 * @var \PDOStatement
		 */
		public $statement;

		public $values = array();

		public $params = array();

		protected $insert_id = null;
		
		public $custom_sql = '';

		/**
		 * Returns a statement
		 *
		 * @param Criteria $crit
		 *
		 * @return Statement
		 */
		public static function getPreparedStatement($crit)
		{
			try
			{
				$statement = new Statement($crit);
			}
			catch (\Exception $e)
			{
				throw $e;
			}

			return $statement;
		}

		public function __construct($crit)
		{
			try
			{
				if ($crit instanceof Criteria)
					$this->crit = $crit;
				else
					$this->custom_sql = $crit;
				
				$this->_prepare();
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		}

		/**
		 * Performs a query, then returns a resultset
		 *
		 * @param string $action[optional] The crud action performed (select, insert, update, delete, create, alter)
		 *
		 * @return Resultset
		 */
		public function performQuery($action = '')
		{
			try
			{
				$values = ($this->getCriteria() instanceof Criteria) ? $this->getCriteria()->getValues() : array();
				\TBGLogging::log('executing PDO query (' . Core::getSQLCount() . ') - ' . (($this->getCriteria() instanceof Criteria) ? $this->getCriteria()->action : 'unknown'), 'B2DB');

				$time = explode(' ', microtime());
				$pretime = $time[1] + $time[0];
				$res = $this->statement->execute($values);

				if (!$res)
				{
					$error = $this->statement->errorInfo();
					if (Core::isDebugMode())
					{
						$time = explode(' ', microtime());
						$posttime = $time[1] + $time[0];
						Core::sqlHit($this->printSQL(), implode(', ', $values), $posttime - $pretime);
					}
					throw new Exception($error[2], $this->printSQL());
				}
				if (Core::isDebugMode())
				{
					\TBGLogging::log('done', 'B2DB');
				}
				if ($this->getCriteria() instanceof Criteria && $this->getCriteria()->action == 'insert')
				{
					if (Core::getDBtype() == 'mysql')
					{
						$this->insert_id = Core::getDBLink()->lastInsertId();
					}
					elseif (Core::getDBtype() == 'pgsql')
					{
						\TBGLogging::log('sequence: ' . Core::getTablePrefix() . $this->getCriteria()->getTable()->getB2DBName() . '_id_seq', 'b2db');
						$this->insert_id = Core::getDBLink()->lastInsertId(Core::getTablePrefix() . $this->getCriteria()->getTable()->getB2DBName() . '_id_seq');
						\TBGLogging::log('id is: ' . $this->insert_id, 'b2db');
					}
				}
				$action = ($this->getCriteria() instanceof Criteria) ? $this->getCriteria()->action : '';
				$retval = new Resultset($this);
				if (Core::isDebugMode())
				{
					$time = explode(' ', microtime());
					$posttime = $time[1] + $time[0];
					Core::sqlHit($this->printSQL(), implode(', ', $values), $posttime - $pretime);
				}
				if (!$this->getCriteria() || $this->getCriteria()->action != 'select')
				{
					$this->statement->closeCursor();
				}
				return $retval;
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		}
		
		/**
		 * Returns the criteria object
		 *
		 * @return Criteria
		 */
		public function getCriteria()
		{
			return $this->crit;
		}

		/**
		 * Return the ID for the inserted record
		 */
		public function getInsertID()
		{
			return $this->insert_id;
		}

		public function getColumnValuesForCurrentRow()
		{
			return $this->values;
		}
		
		/**
		 * Return the number of affected rows
		 */
		public function getNumRows()
		{
			return $this->statement->rowCount();
		}

		/**
		 * Fetch the resultset
		 */
		public function fetch()
		{
			try
			{
				if ($this->values = $this->statement->fetch(\PDO::FETCH_ASSOC))
				{
					return $this->values;
				}
				else
				{
					return false;
				}
			}
			catch (\PDOException $e)
			{
				throw new Exception('An error occured while trying to fetch the result: "' . $e->getMessage() . '"');
			}
		}

		/**
		 * Prepare the statement
		 */
		protected function _prepare()
		{
			try
			{
				if (!Core::getDBLink() instanceof \PDO)
				{
					throw new Exception('Connection not up, can\'t prepare the statement');
				}
				if ($this->crit instanceof Criteria)
				{
					$this->statement = Core::getDBLink()->prepare($this->crit->getSQL());
				}
				else
				{
					$this->statement = Core::getDBLink()->prepare($this->custom_sql);
				}
			}
			catch (\Exception $e)
			{
				throw $e;
			}
		}
		
		public function resetPtr()
		{
			$this->statement->reset();
		}
		
		public function printSQL()
		{
			$str = '';
			if ($this->getCriteria() instanceof Criteria)
			{
				$str .= $this->crit->getSQL();
				foreach ($this->crit->getValues() as $val)
				{
					if (is_int($val))
					{
						$val = $val;
					}
					elseif (is_null($val))
					{
						$val = 'null';
					}
					else
					{
						$val = '\'' . $val . '\'';
					}
					$str = substr_replace($str, $val, mb_strpos($str, '?'), 1);
				}
			}
			return $str;
		}
		
	}
